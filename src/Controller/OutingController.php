<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;





final class OutingController extends AbstractController
{
    #[Route('/outing/create', name: 'outing_create')]
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

        $form = $this->createForm(OutingType::class, $outing);
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


    /* TO DO :
     * list(): afficher toutes les sorties

    show($id): afficher une sortie en détail

    edit($id): modifier une sortie

    delete($id): supprimer une sortie

    cancel($id): pour que l’admin puisse annuler une sortie (changement de statut)
     */
}
