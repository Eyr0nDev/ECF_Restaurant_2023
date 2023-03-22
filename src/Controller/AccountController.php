<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class AccountController extends AbstractController
{
    #[Route('/account', name: 'account')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $bookings = $bookingRepository->findBy(['user' => $user]);
        $form = $this->createForm(ChangePasswordType::class, $user);

        return $this->render('account/index.html.twig', [
            'bookings' => $bookings,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/change-password', name: 'account_change_password', methods: ['POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new AccessDeniedException('Access denied.');
        }

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('old_password')->getData();
            $newPassword = $form->get('new_password')->getData();

            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                //$entityManager->persist($newPassword);
                $entityManager->flush();

                $this->addFlash('success', 'Le mot de passe a bien été changé');
                return $this->redirectToRoute('account');
            } else {
                $this->addFlash('danger', 'L\'ancien mot de passe est incorrect');
            }
        }

        return $this->redirectToRoute('account');
    }

    #[Route('/delete-account', name: 'account_delete', methods: ['POST'])]
    public function deleteAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        CsrfTokenManagerInterface $csrfTokenManager,
        TokenStorageInterface $tokenStorage
    ): Response
    {
        /** @var User|null */
        $user = $this->getUser();

        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new AccessDeniedException('Access denied.');
        }

        // CSRF check
        $csrfToken = new CsrfToken('delete_account', $request->request->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            throw new AccessDeniedException('Invalid CSRF token.');
        }

        // Log the user out
        $tokenStorage->setToken(null);
        $session->invalidate();

        // Delete the user account
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}