<?php

namespace App\Controller;

use App\Form\SearchForm;
use App\Repository\OutingRepository;
use App\Service\OutingAuthorizationService;
use App\Service\OutingStatusUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'main_')]
final class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(
        OutingRepository $outingRepository,
        Request $request,
        OutingStatusUpdater $outingStatusUpdater,
        OutingAuthorizationService $outingAuthorizationService,
        EntityManagerInterface $entityManager,
    ) : Response {

        $searchForm = $this->createForm(SearchForm::class);
        $searchForm->handleRequest($request);

        $outings = [];
        $user = $this->getUser();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            $filters = $searchForm->getData();
            $outings = $outingRepository->findByFilter($filters, $user);

            //dd(count($outings), $outings);
        } else {

            // Par défaut, affichage de tout sauf événements CREATED dont pas organisateur
            $outings = $outingRepository->findAll();
        }

        // Write into database only if there's at least one status changed
        $hasChanges = false;

        foreach ($outings as $outing) {
            $statusChanged = $outingStatusUpdater->updateStatus($outing);
            $archived = $outingStatusUpdater->archiveOuting($outing);
            if ($statusChanged || $archived) {
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $entityManager->flush();
        }


        return $this->render('home/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'outings' => $outings,
            'user' => $user,
            'outingAuth' => $outingAuthorizationService,
        ]);
    }
}
