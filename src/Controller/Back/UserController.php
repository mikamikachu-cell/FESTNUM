<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Mailer\Mailer;
use App\Form\Back\UserType;
use App\Form\Back\UserBatchType;
use App\Form\Back\UserFilterType;
use App\Form\Back\UserUpdateType;
use App\Manager\Back\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/back/user")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository     */
    private $userRepository;

    /**
     * @var UserManager     */
    private $userManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(UserRepository $userRepository, UserManager $userManager, TranslatorInterface $translator)
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/search/{page}", name="back_user_search", methods="GET|POST")
     */
    public function search(Request $request, Session $session, $page = null)
    {
        $page ?: $page = $session->get('back_user_page', 1);
        $formFilter = $this->createForm(UserFilterType::class, null, ['action' => $this->generateUrl('back_user_search', ['page' => 1])]);
        $formFilter->handleRequest($request);
        $data = $this->userManager->configFormFilter($formFilter)->getData();
        $this->denyAccessUnlessGranted('back_user_search', $data);
        $users = $this->userRepository->searchBack($request, $session, $data, $page);
        $queryData = $this->userManager->getQueryData($data);
        $formBatch = $this->createForm(UserBatchType::class, null, [
            'action' => $this->generateUrl('back_user_search', array_merge(['page' => $page], $queryData)),
            'users' => $users,
        ]);
        $formBatch->handleRequest($request);
        if ($formBatch->isSubmitted() && $formBatch->isValid()) {
            $url = $this->userManager->dispatchBatchForm($formBatch);
            if ($url) {
                return $this->redirect($url);
            }
        }

        return $this->render('back/user/search/index.html.twig', [
            'users' => $users,
            'form_filter' => $formFilter->createView(),
            'form_batch' => $formBatch->createView(),
            'form_delete' => $this->createFormBuilder()->getForm()->createView(),
            'number_page' => ceil(count($users) / $formFilter->get('number_by_page')->getData()) ?: 1,
            'page' => $page,
            'query_data' => $queryData,
        ]);
    }

    /**
     * @Route("/create", name="back_user_create", methods="GET|POST")
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder, Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted('back_user_create');
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Génère un mdp aléatoire
            $password = bin2hex(random_bytes(4));
            // Encodage du mdp + affectation à l'utilisateur
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $password
                )
            );
            // désactive l’utilisateur et génère un jeton pour l’identifier lorsqu’il clique sur le lien d’activation;
            $user->setEnabled(false);
            $user->setConfirmationToken(utf8_encode(random_bytes(24)));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // envoie le mail à l'utilisateur avec la méthode créée auparavant;
            $mailer->sendInvitation($user, $password);

            $msg = $this->translator->trans('user.create.flash.success', ['%identifier%' => $user,], 'back_messages');
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('back_user_search');
        }

        return $this->render('back/user/create.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/read/{id}", name="back_user_read", methods="GET")
     */
    public function read(User $user): Response
    {
        $this->denyAccessUnlessGranted('back_user_read', $user);

        return $this->render('back/user/read.html.twig', [
            'user' => $user,
            'form_delete' => $this->createFormBuilder()->getForm()->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="back_user_update", methods="GET|POST")
     */
    public function update(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('back_user_update', $user);
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $msg = $this->translator->trans('user.update.flash.success', [], 'back_messages');
            $this->addFlash('success', $msg);

            return $this->redirectToRoute('back_user_search');
        }

        return $this->render('back/user/update.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete", name="back_user_delete", methods="GET|POST")
     */
    public function delete(Request $request): Response
    {
        $users = $this->userManager->getUsers();
        $this->denyAccessUnlessGranted('back_user_delete', $users);
        $formBuilder = $this->createFormBuilder();
        $formBuilder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($users) {
            $result = $this->userManager->validationDelete($users);
            if (true !== $result) {
                $event->getForm()->addError(new FormError($result));
            }
        });
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($users as $user) {
                $em->remove($user);
            }
            try {
                $em->flush();
                $this->addFlash('success', $this->translator->trans('user.delete.flash.success', [], 'back_messages'));
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->addFlash('warning', $e->getMessage());
            }

            return $this->redirectToRoute('back_user_search');
        }

        return $this->render('back/user/delete.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/permute/enabled", name="back_user_permute_enabled", methods="GET")
     */
    public function permuteEnabled(Request $request): Response
    {
        $users = $this->userManager->getUsers(); // récupére la liste d’utilisateurs à partir des identifiants passés en GET à la requête.

        $this->denyAccessUnlessGranted('back_user_permute_enabled', $users); // denyAccessUnlessGranted(clé, sujet)
        foreach ($users as $user) { // permute la valeur enabled de chaque utilisateur
            $permute = $user->getEnabled() ? false : true;
            $user->setEnabled($permute);
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('back_user_search');
    }
}