<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\FilterForm;
use App\Form\OutingTypeForm;
use App\Repository\OutingRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/outing', name: "outing_")]
final class OutingController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ParticipantRepository $participantRepo
    ): Response {
        $outing = new Outing();

        // Si l'organisateur est l'utilisateur connecté
        $organizer = $this->getUser(); // supposé que tu as un système d'auth
        if ($organizer) {
            $outing->setOrganizer($organizer);
        }

        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($outing);
            $em->flush();

            $this->addFlash('success', 'Sortie créée avec succès !');
            return $this->redirectToRoute('outing_create');
        }

        return $this->render('outing/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list', name: 'list')]
    public function list(
        OutingRepository $outingRepository,
        Request $request,
    ) : Response {

        $filterForm = $this->createForm(FilterForm::class);

        $filterForm->handleRequest($request);

        $outings = [];

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData();

            $outings = $outingRepository->findByFilter($filters);
        }

        return $this->render("outing/list.html.twig", [
            'filterForm' => $filterForm->createView(),
            'outings' => $outings
        ]);

    }


    /* TO DO :
     * list(): afficher toutes les sorties

    show($id): afficher une sortie en détail

    edit($id): modifier une sortie

    delete($id): supprimer une sortie

    cancel($id): pour que l’admin puisse annuler une sortie (changement de statut)
     */
}
