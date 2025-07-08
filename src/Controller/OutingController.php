<?php


namespace App\Controller;

use App\Entity\Outing;
use App\Form\FilterForm;
use App\Form\OutingTypeForm;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/outing', name: "outing_")]
final class OutingController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
        StatusRepository       $statusRepo
    ): Response
    {
        $outing = new Outing();
        $user = $this->getUser();

        $outing->setOrganizer($user);
        $outing->setStatus($statusRepo->findOneBy(['label' => 'created']));

        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                // Si le statut n'est pas rempli par le formulaire, on l'assigne ici
                if (!$outing->getStatus()) {
                    $outing->setStatus($statusRepo->findOneBy(['label' => 'created']));
                }

            $em->persist($outing);
            $em->flush();
            $this->addFlash('success', 'Sortie créée avec succès !');
            return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        }

        return $this->render('outing/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // pour la création d'une page affichage de sortie (par id)

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, OutingRepository $outingRepo): Response
    {
        $outing = $outingRepo->findOneBy($id);

        if (!$outing) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        return $this->render('outing/detail.html.twig', [
            'outing' => $outing,
            'now' => new \DateTime()
        ]);
    }


// pour création d'une page Liste de Sorties (lecture)
    #[Route('/list', name: 'list')]
    public function list(
        OutingRepository $outingRepo,
        Request $request
    ): Response {
        $user = $this->getUser();
        $siteFilter = $request->query->get('site');
        $now = new \DateTime();

        // Récupération avec filtres
        //pour tester pour instant
        $outings = $outingRepo->findAll();
        /* code pour remplacer test du haut
        $outings = $outingRepo->findAllWithFilters(
            $user->getId(),
            $siteFilter
        );
*/
        return $this->render('outing/list.html.twig', [
            'outings' => $outings,
            'now' => $now
        ]);
    }

    // pour la modification de Sortie

    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '\d+'])]
    #[IsGranted('EDIT', subject: 'outing')]
    public function edit(
        Outing $outing,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Sortie modifiée avec succès !');
            return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        }

        return $this->render('outing/edit.html.twig', [
            'form' => $form->createView(),
            'outing' => $outing
        ]);
    }

    //pour l'annulation

    #[Route('/cancel/{id}', name: 'cancel', requirements: ['id' => '\d+'])]
    #[IsGranted('CANCEL', subject: 'outing')]
    public function cancel(
        Outing $outing,
        Request $request,
        EntityManagerInterface $em,
        StatusRepository $statusRepo
    ): Response {
        $form = $this->createForm(CancelOutingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $outing->setCancellationReason($form->get('reason')->getData());
            $outing->setStatus($statusRepo->findOneBy(['name' => 'cancelled']));

            $em->flush();
            $this->addFlash('success', 'Sortie annulée avec succès');
            return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        }

        return $this->render('outing/cancel.html.twig', [
            'form' => $form->createView(),
            'outing' => $outing
        ]);
    }
    /*
    #[Route('/register/{id}', name: 'register', requirements: ['id' => '\d+'])]
    public function register(
        Outing $outing,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $now = new \DateTime();

        // Vérifications
        if ($outing->getRegistrationDeadline() < $now) {
            $this->addFlash('error', 'La date limite d\'inscription est dépassée');
        } elseif ($outing->getParticipants()->count() >= $outing->getMaxParticipants()) {
            $this->addFlash('error', 'Le nombre maximum de participants est atteint');
        } elseif ($outing->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie');
        } else {
            $outing->addParticipant($user);
            $em->flush();
            $this->addFlash('success', 'Inscription réussie !');
        }

        return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
    }

    #[Route('/unregister/{id}', name: 'unregister', requirements: ['id' => '\d+'])]
    public function unregister(
        Outing $outing,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $now = new \DateTime();

        if ($outing->getStartingDateTime() < $now) {
            $this->addFlash('error', 'La sortie a déjà commencé');
        } elseif (!$outing->getParticipants()->contains($user)) {
            $this->addFlash('error', 'Vous n\'êtes pas inscrit à cette sortie');
        } else {
            $outing->removeParticipant($user);
            $em->flush();
            $this->addFlash('success', 'Désinscription effectuée');
        }

        return $this->redirectToRoute('outing_show', ['id' => $outing->getId()]);
    }
*/

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

}

