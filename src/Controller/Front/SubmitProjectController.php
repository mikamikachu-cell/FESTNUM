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

        /* Le formulaire a été soumis (deuxième étape) : 
           enregistrement des données et redirection    */
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Récupération des données depuis le formulaire et enregistrement en BDD
            $video = new Video();
            $video->setTitle($request->request->get('upload_video_form')['title']);
            $video->setDescription($request->request->get('upload_video_form')['description']);
            $video->setFile($request->files->get('upload_video_form')['file']['file']);
            $video->setFilename($request->files->get('upload_video_form')['file']['file']->getFilename());
            // $video->setFilename($request->files->get('upload_video_form')['file']->getFilename());
            $entityManager->persist($video);
            $entityManager->flush();

            // Changement données de User et enregistrement en BDD
            $user->setHasVideo(true);
            $user->setVideoId($video->getId());
            $entityManager->persist($user);
            $entityManager->flush();

            $msg = 'Votre participation a bien été prise en compte.';
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('front_profile');
        }

        /* Le formulaire doit être affiché (première étape)
           Récupération des données s'il y'en a et affichage */
        if ($user->getHasVideo() == true) {
            $videoRepo = $this->getDoctrine()->getRepository(Video::class);
            $video = $videoRepo->find($user->getVideoId());
            return $this->render('front/page/submit.html.twig', [
                'form' => $form->createView(),
                'videoFile' => $video,
            ]);
        } else {
            return $this->render('front/page/submit.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/delete/{id}", name="delete_project")
     */
    public function delete(Request $request, User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $videoId = $user->getVideoId();
        // Changement infos utilisateur
        $user->setHasVideo(false);
        $user->setVideoId(null);
        $em->persist($user);
        $em->flush();
        // Suppression video
        $video = $em->getRepository('App:Video')->find($videoId);
        $em->remove($video);
        $em->flush();

        $msg = 'Votre participation précédente a été supprimée';
        $this->addFlash('danger', $msg);

        return $this->redirectToRoute('submit_project', array(
            'id' => $user->getId()
        ));
    }
}
