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
use App\Repository\UserRepository;
use App\Repository\ReceptionTableRepository;

/**
* @Route("/brides/person/", name="person_")
*/
class PersonController extends AbstractController
{
    /**
     * @Route("list", name="indexGuests", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function indexGuests(PersonRepository $personRepository, WeddingRepository $weddingRepository, UserRepository $userRepo, Request $request)
    {

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepo->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();
        $weddingId = $userWedding->getId();
        
        $guests = $personRepository->findAllQueryBuilder($weddingId);
         //mariés exclus de ces comptes
        $countTotalGuests = $personRepository->findTotalGuestsCountQueryBuilder($weddingId);
        $countPresent = $personRepository->findAttendancePresentCountQueryBuilder($weddingId);
        $countAbsent = $personRepository->findAttendanceAbsentCountQueryBuilder($weddingId);
        $countWaiting = $personRepository->findAttendanceWaitingCountQueryBuilder($weddingId);

        if (!$weddingRepository->find($weddingId)){
            $message = 'Le wedding id n\'existe pas';

            $response = new Response($message, 404);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

         if (!$guests){

            $data = 
            [
                'guests' => 'Vous n\'avez pas encore d\'invités ajoutés à votre mariage',
                'countTotalGuests' => $countTotalGuests,
                'countPresent' => $countPresent,
                'countAbsent' => $countAbsent,
                'countWaiting' => $countWaiting
            ];

        } else {
            $data = 
            [
                'guests' => $guests,
                'countTotalGuests' => $countTotalGuests,
                'countPresent' => $countPresent,
                'countAbsent' => $countAbsent,
                'countWaiting' => $countWaiting
            ];
        }
        
        $response = new JsonResponse($data, 200);
       
        return $response;
    }

    /**
     * @Route("new", name="new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, GuestGroupRepository $guestGroupRepository, UserRepository $userRepo, ReceptionTableRepository $receptionTableRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $userWedding = $userRepo->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        //récupération de la table d'invités initialisée au signup
        $nameTable = 'Liste des invités';
        $tableGuestsId = $receptionTableRepository->findTableGuestsId($userWedding, $nameTable);
        $table = $receptionTableRepository->find($tableGuestsId[0]['id']);

        $guestGroup = $guestGroupRepository->findOneBy(['id' => $contentDecode->id]); //modification groupId en id
        if (!$guestGroup) {
            $message = 'L\'id ' . $contentDecode->id . ' ne correspond pas à un groupe';
            $code = 404;
        } else {
            $wedding = $guestGroup->getWedding();
            
            if ($userWedding != $wedding) {
                $message = 'Le groupe ne correspond pas à ce mariage';
                $code = 400;
            } else {
                $person = new Person();
                $person->setLastname($contentDecode->lastname);
                $person->setFirstname($contentDecode->firstname);
                $person->setGuestGroup($guestGroup);
                $person->setWedding($wedding);
                $person->setReceptionTable($table);

                $em->persist($person);
                $em->flush();

                $message = 'Enregistrement OK';
                $code = 201;
            }
        }
        

        
        $response = new JsonResponse($message, $code);

        return $response;
    }
    
    /**
     * @Route("delete", name="delete", methods={"DELETE"})
     */
    public function deletePerson(Request $request, PersonRepository $personRepository, EntityManagerInterface $em, UserRepository $userRepo)
    {
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $userWedding = $userRepo->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $person = $personRepository->findOneBy(['id' => $contentDecode->id]);

        if (!$person) {
            $message = 'Cette personne n\'existe pas';
            $code = 400;
        } elseif ($person->getWedding() != $userWedding) {
            $message = 'Cette personne ne correspond pas à ce mariage';
            $code = 400;
        } elseif ($person->getContactGuestGroup()) {
            $message = 'La personne, dont l\'id est ' . $person->getId() . ', est le contact principal d\'un groupe et ne peux être supprimée par cette route';
            $code = 400;
        } else {

            $em->remove($person);
            $em->flush();
    
            $message = 'Suppression OK';
            $code = 200;
        }

        $response = new JsonResponse($message, $code);

        return $response;
    }

    /**
     * @Route("edit", name="edit", methods={"POST"})
     */
    public function editPerson(Request $request, PersonRepository $personRepository, EntityManagerInterface $em, UserRepository $userRepo)
    {
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $userWedding = $userRepo->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $person = $personRepository->findOneBy(['id' => $contentDecode->id]);

        if (!$person) {
            $message = 'Cette personne n\'existe pas';
            $code = 400;
        } elseif ($person->getWedding() != $userWedding) {
            $message = 'Cette personne ne correspond pas à ce mariage';
        } else { // only edit modifications asked

            if (isset($contentDecode->lastname)) {
                $person->setLastname($contentDecode->lastname);
            }

            if (isset($contentDecode->firstname)) {
                $person->setFirstname($contentDecode->firstname);
            }

            if (isset($contentDecode->attendance)) {
                $person->setAttendance($contentDecode->attendance);
            }

            if (isset($contentDecode->menu)) {
                $person->setMenu($contentDecode->menu);
            }

            if (isset($contentDecode->allergies)){
                $person->setAllergies($contentDecode->allergies);
            }

            if (isset($contentDecode->halal)) {
                $person->setHalal($contentDecode->halal);
            }

            if (isset($contentDecode->noAlcohol)) {
                $person->setNoAlcohol($contentDecode->noAlcohol);
            }

            if (isset($contentDecode->vegetarian)) {
                $person->setVegetarian($contentDecode->vegetarian);
            }

            if (isset($contentDecode->vegan)) {
                $person->setVegan($contentDecode->vegan);
            }

            if (isset($contentDecode->casher)) {
                $person->setCasher($contentDecode->casher);
            }

            if (isset($contentDecode->commentAllergies)) {
                $person->setCommentAllergies($contentDecode->commentAllergies);
            }

            if (isset($contentDecode->seatNumber)) {
                $person->setSeatNumber($contentDecode->seatNumber);
            }

            if (isset($contentDecode->receptionTable)) {

                $receptionTable = $tRepo->findOneBy(['id' => $contentDecode->receptionTable]);
                $person->setReceptionTable($receptionTable);
            }
            
            $em->flush();
    
            $message = 'Update OK';
            $code = 200;
        }

        $response = new JsonResponse($message, $code);

        return $response;
    }

    /**
     * @Route("plan/list", name="plan_list", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function planList(PersonRepository $personRepository, WeddingRepository $weddingRepository, UserRepository $userRepo, Request $request)
    {
        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepo->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();
        $weddingId = $userWedding->getId();
        
        $guests = $personRepository->findPlanList($weddingId);

        if (!$weddingRepository->find($weddingId)){
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
        
        $data = $guests;

        $response = new JsonResponse($data, 200);
       
        return $response;
    }

}
