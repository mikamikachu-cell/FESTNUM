<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="front_home")
     */
    public function index()
    {
        return $this->render('front/page/accueil.html.twig', []);
    }
    // /**
    //  * @Route("/videos", name="front_videos")
    //  */
    // public function videos()
    // {
    //     return $this->render('front/page/videos.html.twig', []);
    // }
    /**
     * @Route("/contact", name="front_contact")
     */
    public function contact()
    {
        return $this->render('front/page/contact.html.twig', []);
    }
    /**
     * @Route("/jury", name="front_jury")
     */
    public function jury()
    {
        return $this->render('front/page/jury.html.twig', []);
    }
    /**
     * @Route("/archives", name="front_archives")
     */
    public function archives()
    {
        return $this->render('front/page/archives.html.twig', []);
    }
    /**
     * @Route("/prix", name="front_prix")
     */
    public function prix()
    {
        return $this->render('front/page/prix.html.twig', []);
    }

    /**
     * @Route("/profile", name="front_profile")
     */
    public function profile()
    {
        return $this->render('front/page/profile.html.twig', []);
    }
}
