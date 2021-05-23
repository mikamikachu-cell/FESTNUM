<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Entity\Video;
use App\Form\Front\UploadVideoFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubmitProjectController extends AbstractController
{
    /**
     * @Route("/submit/{id}", name="submit_project")
     */
    public function submit(Request $request, User $user): Response
    {
        $form = $this->createForm(UploadVideoFormType::class);
        $form->handleRequest($request);
        // Le formulaire a été soumis (deuxième étape)
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Récupération données Video transmise et enregistrement en BDD
            $video = new Video();
            $video->setTitle($request->request->get('upload_video_form')['title']);
            $video->setDescription($request->request->get('upload_video_form')['description']);
            // $video->setFilename($request->files->get('upload_video_form')['file']->getFilename());
            $video->setFilename($request->files->get('upload_video_form')['file']['file']->getFilename());
            $entityManager->persist($video);
            $entityManager->flush();

            // Changement données User et enregistrement en BDD
            $user->setHasVideo(true);
            $user->setVideoId($video->getId());
            $entityManager->persist($user);
            $entityManager->flush();

            $msg = 'Video transmise';
            $this->addFlash('info', $msg);
            return $this->redirectToRoute('front_home');
        }
        // Le formulaire doit être affiché (première étape)
        return $this->render('front/page/submit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="delete_project")
     */
    public function delete(Request $request, User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $videoId = $user->getVideoId();

        $user->setHasVideo(false);
        $user->setVideoId(null);
        $em->persist($user);
        $em->flush();


        $video = $em->getRepository('App:Video')->find($videoId);
        $em->remove($video);
        $em->flush();

        return $this->redirectToRoute('submit_project', array(
            'id' => $user->getId()
        ));
    }
}
