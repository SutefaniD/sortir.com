<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LocationController extends AbstractController
{

    #[Route('/create/location', name: 'create_location')]
    public function create_location(EntityManagerInterface $entityManager, Request $request): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationForm::class, $location);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('main_home');
        }

        return $this->render('outing/create_location.html.twig', ['form' => $form]);
    }

    #[Route('/location/{id}', name: 'location_details', methods: ['GET'])]
    public function getLocationDetails(Location $location): JsonResponse
    {
        return $this->json([
            'name' => $location->getName(),
            'street' => $location->getStreet(),
            'postalCode' => $location->getCity()->getZipCode(),
            'city' => $location->getCity()->getName(),
            'latitude' => $location->getLatitude(),
            'longitude' => $location->getLongitude(),
        ]);
    }
}
