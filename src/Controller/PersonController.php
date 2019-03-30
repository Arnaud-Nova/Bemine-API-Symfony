<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\GuestGroup;
use App\Repository\EventRepository;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

/**
* @Route("/brides/person/", name="person_")
*/
class PersonController extends AbstractController
{
    /**
     * @Route("list/wedding/{id}", name="indexGuests", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function indexGuests(PersonRepository $personRepository, $id, WeddingRepository $weddingRepository)
    {
        
        $guests = $personRepository->findAllQueryBuilder($id);
         //mariés exclus de ces comptes
        $countTotalGuests = $personRepository->findTotalGuestsCountQueryBuilder($id);
        $countPresent = $personRepository->findAttendancePresentCountQueryBuilder($id);
        $countAbsent = $personRepository->findAttendanceAbsentCountQueryBuilder($id);
        //problème sur la requête, les autres fonctionnent bien avec le param id du wedding, mais celle-ci renvoie un count incorrect...
        $countWaiting = $personRepository->findAttendanceWaitingCountQueryBuilder($id);
        // dd($guests);

        if (!$weddingRepository->find($id)){
            $message = 'Le wedding id n\'existe pas';

            $response = new Response($message, 404);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

         if (!$guests){
            $message = 'Vous n\'avez pas encore d\'invités ajoutés à votre mariage';

            $response = new Response($message, 404);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        
        $data = 
            [
                'guests' => $guests,
                'countTotalGuests' => $countTotalGuests,
                'countPresent' => $countPresent,
                'countAbsent' => $countAbsent,
                'countWaiting' => $countWaiting
            ]
        ;
        $response = new JsonResponse($data, 200);
       
        return $response;
    }

    /**
     * @Route("new", name="new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, GuestGroupRepository $guestGroupRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $guestGroup = $guestGroupRepository->findOneBy(['id' => $contentDecode->id]); //modification groupId en id
        $wedding = $guestGroup->getWedding();

        $person = new Person();
        $person->setLastname($contentDecode->lastname);
        $person->setFirstname($contentDecode->firstname);
        $person->setGuestGroup($guestGroup);
        $person->setWedding($wedding);

        $em->persist($person);
        $em->flush();

        $data = 'Enregistrement OK';
        $response = new JsonResponse($data, 201);

        return $response;
    }
    
    /**
     * @Route("delete", name="delete", methods={"DELETE"})
     */
    public function deletePerson(Request $request, PersonRepository $personRepository, EntityManagerInterface $em)
    {
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $person = $personRepository->findOneBy(['id' => $contentDecode->id]); //modification de personId en id à la demande d'olivier le 30/03 9h20

        if (!$person) {
            $data = 'Cette personne n\existe pas';

            $response = new JsonResponse($data, 400);

            return $response;
        }

        $em->remove($person);
        $em->flush();

        $data = 'Suppression OK';
        $response = new JsonResponse($data, 200);

        return $response;
    }

    /**
     * @Route("edit", name="edit", methods={"POST"})
     */
    public function editPerson(Request $request, PersonRepository $personRepository, EntityManagerInterface $em)
    {
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $person = $personRepository->findOneBy(['id' => $contentDecode->id]); //modification de personId en id à la demande d'olivier le 30/03 9h20

        if (!$person) {
            $data = 'Cette personne n\existe pas';

            $response = new JsonResponse($data, 400);

            return $response;
        }

        $person->setLastname($contentDecode->lastname);
        $person->setFirstname($contentDecode->firstname);
        $person->setAttendance($contentDecode->attendance);

        $em->flush();

        $data = 'Update OK';
        $response = new JsonResponse($data, 200);

        return $response;
    }

}
