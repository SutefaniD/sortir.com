<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Participant;
use App\Entity\Site;
use App\Form\CityForm;
use App\Form\ParticipantForm;
use App\Form\SiteForm;
use App\Repository\CityRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
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

    #[Route('/participant/all', name: 'all_users')]
    public function allUsers(ParticipantRepository $participantRepository): Response
    {
        $participants = $participantRepository->findAll();

        return $this->render('admin/all_participants.html.twig', ['participants' => $participants]);
    }

    #[Route('/participant/create', name: 'create_user')]
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

    #[Route('/participant/delete/{id}', name: 'delete_user')]
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

    #[Route('/participant/disable/{id}', name: 'disable_user')]
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

    #[Route('/city/all', name: 'city_all')]
    public function all_cities(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAll();

        return $this->render('admin/city/all.html.twig', ['cities' => $cities]);
    }

    #[Route('/city/create', name: 'create_city')]
    public function create_city(EntityManagerInterface $entityManager, Request $request): Response
    {
        $city = new City();
        $form = $this->createForm(CityForm::class, $city);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('admin_city_all');
        }

        return $this->render('admin/city/create.html.twig', ['cityForm' => $form]);
    }

    #[Route('/city/update/{id}', name: 'update_city')]
    public function update_city(int $id, EntityManagerInterface $entityManager, Request $request, CityRepository $cityRepository): Response
    {
        $city = $cityRepository->find($id);
        $form = $this->createForm(CityForm::class, $city);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('admin_city_all');
        }

        return $this->render('admin/city/create.html.twig', ['cityForm' => $form]);
    }

    #[Route('/city/delete/{id}', name: 'delete_city')]
    public function delete_city(int $id, EntityManagerInterface $entityManager, CityRepository $cityRepository): Response
    {
        $city = $cityRepository->find($id);

        if (!$city) {
            throw $this->createNotFoundException('Ville non trouvé');
        }

        $entityManager->remove($city);
        $entityManager->flush();

        return $this->redirectToRoute('admin_city_all');
    }

    #[Route('/site/all', name: 'site_all')]
    public function all_site(SiteRepository $siteRepository): Response
    {
        $sites = $siteRepository->findAll();

        return $this->render('admin/site/all.html.twig', ['sites' => $sites]);
    }

    #[Route('/site/create', name: 'create_site')]
    public function create_site(EntityManagerInterface $entityManager, Request $request): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteForm::class, $site);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('admin_site_all');
        }

        return $this->render('admin/site/create.html.twig', ['siteForm' => $form]);
    }

    #[Route('/site/update/{id}', name: 'update_site')]
    public function update_site(int $id, EntityManagerInterface $entityManager, Request $request, SiteRepository $siteRepository): Response
    {
        $site = $siteRepository->find($id);
        $form = $this->createForm(SiteForm::class, $site);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('admin_site_all');
        }

        return $this->render('admin/site/create.html.twig', ['siteForm' => $form]);
    }

    #[Route('/site/delete/{id}', name: 'delete_site')]
    public function delete_site(int $id, EntityManagerInterface $entityManager, SiteRepository $siteRepository): Response
    {
        $site = $siteRepository->find($id);

        if (!$site) {
            throw $this->createNotFoundException('Site non trouvé');
        }

        $entityManager->remove($site);
        $entityManager->flush();

        return $this->redirectToRoute('admin_site_all');
    }
}
