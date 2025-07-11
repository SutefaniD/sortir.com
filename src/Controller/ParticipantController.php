<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantForm;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/participant', name: 'participant_')]
final class ParticipantController extends AbstractController
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/create', name: 'create')]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantForm::class, $participant, ['include_profile' => true, 'include_password' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError("Les mots de passe ne correspondent pas."));
            } else {
                $hashedPassword = $this->passwordHasher->hashPassword($participant, $plainPassword);

                $participant->setPassword($hashedPassword);

                $participant->setAdministrator(false);
                $participant->setActive(true);

                $profileImage = $form->get('profileImageFile')->getData();
                if ($profileImage) {
                    $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profiles';
                    $newFilename = uniqid() . '.' . $profileImage->guessExtension();

                    $profileImage->move($uploadsDir, $newFilename);

                    $participant->setProfilePicture('/uploads/profiles/' . $newFilename);
                } else {
                    $participant->setProfilePicture('assets/images/mood.svg');
                }

                $entityManager->persist($participant);
                $entityManager->flush();

                return $this->redirectToRoute('main_home');
            }
        }

        return $this->render('participant/create.html.twig', ['createParticipantForm' => $form]);
    }

    #[Route('/update/profiles', name: 'update_profile')]
    public function update_profile(EntityManagerInterface $entityManager, Request $request): Response {
        $participant = $this->getUser();

        if (!$participant instanceof Participant) {
            return $this->redirectToRoute('main_home');
        }

        $form = $this->createForm(ParticipantForm::class, $participant, ['include_profile' => true, 'include_password' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileImage = $form->get('profileImageFile')->getData();
            if ($profileImage) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profiles';
                $newFilename = uniqid() . '.' . $profileImage->guessExtension();

                $profileImage->move($uploadsDir, $newFilename);

                $participant->setProfilePicture('/uploads/profiles/' . $newFilename);
            }

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('main_home');
        }

        return $this->render('participant/update.html.twig', ['createParticipantForm' => $form]);
    }

    #[Route('/update/password', name: 'update_password')]
    public function update_password(EntityManagerInterface $entityManager, Request $request): Response {
        $participant = $this->getUser();

        if (!$participant instanceof Participant) {
            return $this->redirectToRoute('main_home');
        }

        $form = $this->createForm(ParticipantForm::class, $participant, ['include_profile' => false, 'include_password' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError("Les mots de passe ne correspondent pas."));
            } else {
                $hashedPassword = $this->passwordHasher->hashPassword($participant, $plainPassword);

                $participant->setPassword($hashedPassword);

                $entityManager->persist($participant);
                $entityManager->flush();

                return $this->redirectToRoute('main_home');
            }
        }

        return $this->render('participant/update_password.html.twig', ['createParticipantForm' => $form]);
    }

    #[Route('/delete', name: 'delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, TokenStorageInterface $tokenStorage, Request $request): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $participant = $participantRepository->find($user->getId());

        if (!$participant) {
            return $this->redirectToRoute('main_home');
        }

        try {
            $entityManager->remove($participant);
            $entityManager->flush();

            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            return $this->redirectToRoute('app_login');
        } catch (Exception $exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du compte : ' . $exception->getMessage());
        }

        return $this->redirectToRoute('main_home');
    }

    #[Route('/profile/{id}', name: 'show_profile', methods: ['GET'])]
    public function showProfile(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, TokenStorageInterface $tokenStorage, Request $request): Response {
        $user = $participantRepository->find($id);

        if ($user) {
            return $this->render('participant/show_profile.html.twig', ['user' => $user]);
        }

        return $this->redirectToRoute('main_home');
    }
}
