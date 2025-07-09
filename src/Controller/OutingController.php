<?php


namespace App\Controller;

use App\Entity\Location;
use App\Entity\Outing;
use App\Form\LocationForm;
use App\Form\OutingTypeForm;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Service\OutingAuthorizationService;
use App\Service\OutingStatusUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/outing', name: "outing_")]
final class OutingController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException("User is not logged in");
        }

        $outing = new Outing();
        $outing->setOrganizer($user);

        if (!$user->getSite()) {
            throw new \LogicException("L'utilisateur n'a pas de site associé.");
        }

        $outing->setSite($outing->getOrganizer()->getSite());

        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                // Définir le statut selon le bouton cliqué
                if ($form->get('create')->isClicked()) {
                    $outing->setStatus($statusRepo->findOneBy(['label' => 'Créée']));
                } else if ($form->get('publish')->isClicked()) {
                    $outing->setStatus($statusRepo->findOneBy(['label' => 'Ouverte']));
                } else {
                    return $this->redirectToRoute('main_home');
                }

                $entityManager->persist($outing);
                $entityManager->flush();
                $this->addFlash('success', 'Sortie créée avec succès !');

                //redirige sur la page d'affichage de Sortie
                return $this->redirectToRoute('main_home');
                }
                catch (Exception $exception) {
                    $this->addFlash('warning', $exception->getMessage());
            }
        }

        return $this->render('outing/create.html.twig', [
            'form' => $form
        ]);
    }


    // Page d'affichage de Sortie (par id)

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'],  methods: ['GET'])]
    public function detail(int $id, OutingRepository $outingRepo): Response
    {
        $outing = $outingRepo->find($id);

        if (!$outing) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        return $this->render('outing/detail.html.twig', [
            'outing' => $outing,
            'now' => new \DateTime()
        ]);
    }


    // pour la modification de Sortie

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(Outing $outing, Request $request, StatusRepository $statusRepo, EntityManagerInterface $em): Response {
        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Cas : Bouton "Annuler la sortie" cliqué
            if ($form->has('cancel') && $form->get('cancel')->isClicked()) {
                if ($outing->getStartingDateTime() <= new \DateTime()) {
                    $this->addFlash('danger', "La sortie a déjà commencé ou est terminée, elle ne peut plus être annulée !");
                    return $this->redirectToRoute('_update', ['id' => $outing->getId()]);
                }

                // Sinon rediriger vers la page d’annulation dédiée
                //return $this->redirectToRoute('outing/cancel.html.twig', ['id' => $outing->getId()]);
                return $this->redirectToRoute('outing_cancel', ['id' => $outing->getId()]);
            }

            // Cas : Bouton "Publier"
            if ($form->has('publish') && $form->get('publish')->isClicked()) {
                $outing->setStatus($statusRepo->findOneBy(['label' => 'Ouverte']));
            } else {
                // Cas : Enregistrement simple
                $outing->setStatus($statusRepo->findOneBy(['label' => 'Créée']));
            }

            $em->flush();
            $this->addFlash('success', 'Sortie modifiée avec succès !');

            return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        }

        return $this->render('outing/update.html.twig', [
            'form' => $form,
            'outing' => $outing,
        ]);
    }

    /*
     * #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    // TO DO ACCES LIMITE
    //#[IsGranted('UPDATE', subject: 'outing')]
    public function update(
        Outing $outing,
        Request $request,
        StatusRepository       $statusRepo,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir le statut selon le bouton cliqué

            if ($form->get('publish')->isClicked()) {
                $outing->setStatus($statusRepo->findOneBy(['label' => 'Ouverte']));
            }
           if ($form->get('cancel')->isClicked()) {
               if ($outing->getStartingDateTime() > now\DateTime()) {
                   $outing->setStatus($statusRepo->findOneBy(['label' => 'Annulée']));
               }
               else {
                   $this->addFlash('danger', "La Sortie est en cours et ne peux pas être modifiée!");
               }
           }
           else {
                $outing->setStatus($statusRepo->findOneBy(['label' => 'Créée']));
            }
            $outing->setstartingDateTime(new \DateTime());
            $outing->setregistrationDeadline(new \DateTime());

            $em->flush();
            $this->addFlash('success', 'Sortie modifiée avec succès !');

            return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        }

        return $this->render('outing/update.html.twig', [
            'form' => $form,
            'outing' => $outing
        ]);
    }

*/
// Annulation de Sortie
    #[Route('/cancel/{id}', name: 'cancel', requirements: ['id' => '\d+'],  methods: ['GET', 'POST'] )]
    // #[IsGranted('CANCEL', subject: 'outing')]

    public function cancel(
        Outing $outing,
        Request $request,
        Security $security,
        EntityManagerInterface $em,
        StatusRepository $statusRepo
    ): Response {
        $user = $security->getUser();
        //POUR TESTER EN BAS JE COMMENTE CETTE CONDITION

   /*     if ($outing->getOrganizer() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas l'organisateur");
        }
    */
        if ($outing->getStartingDateTime() <= new \DateTime()) {
            throw $this->createAccessDeniedException("Sortie a déjà commencée");
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
            return $this->redirectToRoute('main_home');
        }

        return $this->render('outing/cancel.html.twig', [
            'form' => $form,
            'outing' => $outing,
        ]);
    }

    //Suppression de Sortie

   // Suppression de Sortie
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(Outing $outing, Request $request, EntityManagerInterface $entityManager): Response {
        $user = $this->getUser();
        // Vérification de l'organisateur
        if ($outing->getOrganizer() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas l'organisateur");
        }
        // Vérification que la sortie n'a pas commencé
        if ($outing->getStartingDateTime() <= new \DateTime()) {
            throw $this->createAccessDeniedException("Sortie déjà commencée");
        }
        // Création d'un formulaire de confirmation simple
//        $form = $this->createFormBuilder()
//            ->add('confirm', SubmitType::class, ['label' => 'Confirmer la suppression'])
//            ->getForm();
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
            // Supprimer la sortie
            $entityManager->remove($outing);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie supprimée avec succès');

            return $this->redirectToRoute('main_home');
//        }
//        return $this->render('outing/delete.html.twig', [
//            'form' => $form->createView(),
//            'outing' => $outing,
//        ]);

//        return $this->redirectToRoute('main_home');
    }

//      Inscription/Desinscription des participants

    #[Route('/outing/register/{id}', name: 'register', requirements: ['id' => '\d+'])]
    public function register(
        Outing $outing,
        EntityManagerInterface $em,
        OutingAuthorizationService $authorizationService,
        OutingStatusUpdater $statusUpdater,
    ): Response {
        $user = $this->getUser();
        $now = new \DateTime();

        // Update status
        $this->$statusUpdater->updateStatus($outing);

        // Register participant
        if (!$this->$authorizationService->canUserRegister($outing, $user)) {
            if ($authorizationService->isUserParticipant($outing, $user)) {
                $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie');
            } else if ($authorizationService->isUserOrganizer($outing, $user)) {
                $this->addFlash('error', 'L’organisateur ne peut pas s’inscrire à sa propre sortie');
            } else if ($authorizationService->isStatusOpened($outing)) {
                $this->addFlash('error', 'La sortie n’est pas ouverte aux inscriptions');
            } else if ($outing->getRegistrationDeadline() < $now) {
                $this->addFlash('error', 'La date limite d\'inscription est dépassée');
            } else if ($outing->getParticipants()->count() >= $outing->getMaxParticipants()) {
                $this->addFlash('error', 'Le nombre maximum de participants est atteint');
            }
        } else {
            $outing->addParticipant($user);
            $em->flush();
            $this->addFlash('success', 'Inscription réussie !');
        }

        return $this->redirectToRoute('main_home');
    }

    #[Route('/outing/unregister/{id}', name: 'unregister', requirements: ['id' => '\d+'])]
    public function unregister(
        Outing $outing,
        EntityManagerInterface $em,
        OutingAuthorizationService $authorizationService,
        OutingStatusUpdater $statusUpdater,
    ): Response {
        $user = $this->getUser();
        $now = new \DateTime();

       if (!$authorizationService->canUserUnregister($outing, $user)) {
           if ($outing->getStartingDateTime() < $now) {
               $this->addFlash('error', 'La sortie a déjà commencé');
           } elseif (!$outing->getParticipants()->contains($user)) {
               $this->addFlash('error', 'Vous n\'êtes pas inscrit à cette sortie');
           }
       } else {
            $outing->removeParticipant($user);
            $em->flush();
            // Update status
            $statusUpdater->updateStatus($outing);
            $this->addFlash('success', 'Désinscription effectuée');

       }

//        return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        return $this->redirectToRoute('main_home');
    }


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

