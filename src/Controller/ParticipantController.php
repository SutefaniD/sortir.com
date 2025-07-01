<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CreateParticipantForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

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
        $form = $this->createForm(CreateParticipantForm::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $hashedPassword = $this->passwordHasher->hashPassword($participant, $plainPassword);

            $participant->setPassword($hashedPassword);

            $participant->setAdministrator(false);
            $participant->setActive(true);

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('main_home');
        }

        return $this->render('participant/create.html.twig', [ 'createParticipantForm' => $form]);
    }
}
