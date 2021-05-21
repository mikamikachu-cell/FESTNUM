<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\Front\UploadVideoFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubmitProjectController extends AbstractController
{
    /**
     * @Route("/{id}/submit", name="submit_project")
     */
    public function submit(Request $request, User $user): Response
    {
        $form = $this->createForm(UploadVideoFormType::class);
        $form->handleRequest($request);

        // Le formulaire a été soumis (deuxième étape)
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération infos video transmise
            $user->setVideoTitle($request->request->get('upload_video_form')['video_title']);
            $user->setVideoDescription($request->request->get('upload_video_form')['video_description']);
            $user->setVideoFilename($request->files->get('upload_video_form')['video_file']->getFilename());
            // Enregistrement en BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $msg = 'Video transmise';
            $this->addFlash('info', $msg);
            return $this->redirectToRoute('front_home');
        }
        // Le formulaire doit être affiché (première étape)
        return $this->render('front/submit_project/submit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/delete", name="delete_project")
     */
    public function delete(Request $request, User $user): Response
    {
        $user->setVideoTitle('');
        $user->setVideoDescription('');
        $user->setVideoFilename('');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $msg = 'Projet supprimé';
        $this->addFlash('info', $msg);
        return $this->redirectToRoute('submit_project', array(
            'id' => $user->getId()
        ));
    }
}
