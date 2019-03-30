<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/brides/events/wedding/{id}", name="events_by_wedding", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function eventsByWedding(EventRepository $eventRepository, $id)
    {

        $eventsByWedding = $eventRepository->findEventsByWedding($id);

        $data = 
        [
         'events' => $eventsByWedding,
        ]
    ;
    $response = new JsonResponse($data, 200);
   
    return $response;
    }
}
