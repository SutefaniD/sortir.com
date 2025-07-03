<?php


namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingTypeForm;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OutingController extends AbstractController
{
    //création d'une page 'création de sortie
    #[Route('/outing/create', name: 'outing_create')]
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
            $em->persist($outing);
            $em->flush();

            $this->addFlash('success', 'Sortie créée avec succès !');
            return $this->redirectToRoute('outing_show', ['id' => $outing->getId()]);
        }

        return $this->render('outing/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // pour la création d'une page affichage de sortie (par id)

    #[Route('/outing/{id}', name: 'outing_detail', requirements: ['id' => '\d+'])]
    public function show(int $id, OutingRepository $outingRepo): Response
    {
        $outing = $outingRepo->findFullOuting($id);

        if (!$outing) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        return $this->render('outing/show.html.twig', [
            'outing' => $outing,
            'now' => new \DateTime()
        ]);
    }


// pour création d'une page Liste de Sorties (lecture)
    #[Route('/outing/list', name: 'outing_list')]
    public function list(
        OutingRepository $outingRepo,
        Request $request
    ): Response {
        $user = $this->getUser();
        $siteFilter = $request->query->get('site');
        $now = new \DateTime();

        // Récupération avec filtres
        $outings = $outingRepo->findAllWithFilters(
            $user->getId(),
            $siteFilter
        );

        return $this->render('outing/list.html.twig', [
            'outings' => $outings,
            'now' => $now
        ]);
    }

    // pour la modification de Sortie

    #[Route('/outing/edit/{id}', name: 'outing_edit', requirements: ['id' => '\d+'])]
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


}


/*

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingTypeForm;
use App\Repository\OutingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/outing')]
final class OutingController extends AbstractController
{
    #[Route('/create', name: 'outing_create')]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
        ParticipantRepository  $participantRepo,
        Security $security
    ): Response
    {
        $user = $security->getUser();

        // Vérifie que l'utilisateur est connecté et a le rôle adéquat
        if (!$this->isGranted('ROLE_ORGANIZER') && !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_USER')) //pour travailler - ACCES!!!!!!!!!!!
            ///////////////////////////////////////A CHANGER ///////////////////////////
        {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $outing = new Outing();
        $outing->setOrganizer($user); // Assigne automatiquement l’organisateur

        $form = $this->createForm(OutingTypeForm::class, $outing);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // Organisateur = utilisateur courant
            $outing->setOrganizer($this->getUser());

            // Récupération du repo Status
            $statusRepo = $em->getRepository(Status::class);

            if ($form->get('save')->isClicked()) {
                $outing->setStatus($statusRepo->findOneBy(['label' => StatusName::CREATED]));
                $em->persist($outing);
                $em->flush();

                return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
            }

            if ($form->get('publish')->isClicked()) {
                $outing->setStatus($statusRepo->findOneBy(['label' => StatusName::ONGOING]));
                $em->persist($outing);
                $em->flush();

                return $this->redirectToRoute('/list.html.twig');
            }

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
*/
