<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantForm;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/all-users', name: 'all_users')]
    public function allUsers(ParticipantRepository $participantRepository): Response
    {
        $participants = $participantRepository->findAll();

        return $this->render('admin/all_participants.html.twig', ['participants' => $participants]);
    }

    #[Route('/create-user', name: 'create_user')]
    public function createUser(EntityManagerInterface $entityManager, Request $request): Response
    {
//        return $this->redirectToRoute('participant_create');
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

                $entityManager->persist($participant);
                $entityManager->flush();

                return $this->redirectToRoute('main_home');
            }
        }

        return $this->render('participant/create.html.twig', ['createParticipantForm' => $form]);
    }

    #[Route('/delete/{id}', name: 'delete_user')]
    public function delete_user(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant non trouvé');
        }

        $entityManager->remove($participant);
        $entityManager->flush();

        return $this->redirectToRoute('admin_all_users');
    }

    #[Route('/disable-user/{id}', name: 'disable_user')]
    public function disable_user(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant non trouvé');
        }

        if ($participant->isActive()) {
            $participant->setActive(false);

            $entityManager->persist($participant);
            $entityManager->flush();
        } else {
            $participant->setActive(true);

            $entityManager->persist($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_all_users');
    }
}
