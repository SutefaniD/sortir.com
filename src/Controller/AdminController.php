<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    #[Route('/all-users', name: 'app_admin')]
    public function allUsers(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $participants = $participantRepository->findAll();

        return $this->render('admin/all_participants.html.twig', ['participants' => $participants]);
    }

    #[Route('/delete/{id}', name: 'delete_user')]
    public function delete_user(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
//        $participants = $participantRepository->findAll();

        return $this->render('admin/all_participants.html.twig');
    }

    #[Route('/disable-user/{id}', name: 'disable_user')]
    public function disable_user(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
//        $participants = $participantRepository->findAll();

        return $this->render('admin/all_participants.html.twig');
    }
}
