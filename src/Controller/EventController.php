<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/brides/events", name="events_by_wedding", methods={"GET"})
     */
    public function eventsByWedding(EventRepository $eventRepository, Request $request, UserRepository $userRepository)
    {
        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $eventsByWedding = $eventRepository->findEventsByWedding($userWedding);

        $data = 
        [
         'events' => $eventsByWedding,
        ]
    ;
    $response = new JsonResponse($data, 200);
   
    return $response;
    }
}
