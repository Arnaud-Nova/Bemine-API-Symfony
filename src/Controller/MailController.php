<?php

namespace App\Controller;


use App\Repository\MailRepository;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailController extends AbstractController
{
    /**
     * @Route("/brides/guests/mail/wedding/{id}", name="index_mail", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function indexMail(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, $id, WeddingRepository $weddingRepository)
    {
        $wedding = $weddingRepository->find($id);
        
        if (!$wedding){
            $data = 
            [
                'message' => 'Le wedding id n\'existe pas.'
            ]
            ;
            
            $response = new JsonResponse($data, 400);
        
            return $response;
        }
        
        // $contactsGroup = $guestGroupRepository->findGroupsQueryBuilder($id);
        

        // dd($contactsGroup);

        //mariés exclus de ces comptes
        $countTotalGuests = $personRepository->findTotalGuestsCountQueryBuilder($id);
        $countPresent = $personRepository->findAttendancePresentCountQueryBuilder($id);
        $countAbsent = $personRepository->findAttendanceAbsentCountQueryBuilder($id);
        //problème sur la requête, les autres fonctionnent bien avec le param id du wedding, mais celle-ci renvoie un count incorrect...
        $countWaiting = $personRepository->findAttendanceWaitingCountQueryBuilder($id);

        $mails = $mailRepository->findAllQueryBuilder();
        
        $data = 
            [
                'mails' => $mails,
                'contactsGroup' => $contactsGroup,
                'countTotalGuests' => $countTotalGuests,
                'countPresent' => $countPresent,
                'countAbsent' => $countAbsent,
                'countWaiting' => $countWaiting
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;
    }

}
