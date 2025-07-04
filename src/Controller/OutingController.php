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
use Symfony\Bundle\SecurityBundle\Security;
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
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException("User is not logged in");
        }

        $outing = new Outing();
        $outing->setOrganizer($user);
        //$outing->setStatus($statusRepo->findOneBy(['label' => 'created']));

        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                // Si le statut n'est pas rempli par le formulaire, on l'assigne ici
                /*if (!$outing->getStatus()) {
                    $outing->setStatus($statusRepo->findOneBy(['label' => 'created']));
                }
                */

                // Définir le statut selon le bouton cliqué
                /*
                if ($form->get('publish')->isClicked()) {
                    $outing->setStatus($statusRepo->findOneBy(['label' => 'Ongoing']));
                } else {
                    $outing->setStatus($statusRepo->findOneBy(['label' => 'Created']));
                }
                //    $outing->setstartingDateTime(new \DateTime());
                  //  $outing->registrationDeadline(new \DateTime());
*/
                $outing->setStatus($statusRepo->findOneBy(['label' => 'Created']));

                $em->persist($outing);
                $em->flush();
                $this->addFlash('success', 'Sortie créée avec succès !');
                return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);

                }
                catch (Exception $exception) {
                $this->addFlash('warning', $exception->getMessage());
            }}

            return $this->render('outing/create.html.twig', [
                'form' => $form
                //'form' => $form->createView(),

            ]);
        }


//    #[Route('/list', name: 'list')]
//    public function list(
//        OutingRepository $outingRepository,
//        Request $request,
//    ) : Response {
//
//        $filterForm = $this->createForm(FilterForm::class);
//
//        $filterForm->handleRequest($request);
//
//        $outings = [];
//
//        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
//            $filters = $filterForm->getData();
//
//            $outings = $outingRepository->findByFilter($filters);
//        }
//
//        return $this->render("outing/list.html.twig", [
//            'filterForm' => $filterForm->createView(),
//            'outings' => $outings
//        ]);
//    }

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

    #[Route('/edit/{id}', name: 'outing_edit', requirements: ['id' => '\d+'])]
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
        Security $security,
        EntityManagerInterface $em,
        StatusRepository $statusRepo
    ): Response {
        $user = $security->getUser();

        if ($outing->getOrganizer() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas l'organisateur");
        }

        if ($outing->getStartingDateTime() <= new \DateTime()) {
            throw $this->createAccessDeniedException("Sortie déjà commencée");
        }

        $form = $this->createFormBuilder()
            ->add('cancelReason', TextareaType::class, ['label' => 'Motif d’annulation'])
            ->add('submit', SubmitType::class, ['label' => 'Annuler la sortie'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $outing->setCancelReason($data['cancelReason']);
            $outing->setStatus($statusRepo->findOneBy(['label' => 'Annulée']));

            $em->flush();
            $this->addFlash('info', 'Sortie annulée avec succès');
            return $this->redirectToRoute('outing_list');
        }

        return $this->render('outing/cancel.html.twig', [
            'form' => $form->createView(),
            'outing' => $outing,
        ]);
    }
    /*
    #[Route('/outing/register/{id}', name: 'outing_register', requirements: ['id' => '\d+'])]
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

    #[Route('/outing/unregister/{id}', name: 'outing_unregister', requirements: ['id' => '\d+'])]
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

}

