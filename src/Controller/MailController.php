<?php

namespace App\Controller;


use App\Repository\PersonRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailController extends AbstractController
{
    /**
     * @Route("/brides/guests/mail/wedding/{id}", name="index_mail", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function indexMail(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, $id)
    {
        
        $contactsGroup = $guestGroupRepository->findAllQueryBuilder($id);
        
        
        //mariés exclus de ces comptes
        $countTotalGuests = $personRepository->findTotalGuestsCountQueryBuilder($id);
        $countPresent = $personRepository->findAttendancePresentCountQueryBuilder($id);
        $countAbsent = $personRepository->findAttendanceAbsentCountQueryBuilder($id);
        //problème sur la requête, les autres fonctionnent bien avec le param id du wedding, mais celle-ci renvoie un count incorrect...
        $countWaiting = $personRepository->findAttendanceWaitingCountQueryBuilder($id);

        return $this->json(
            [
                'code' => 200,
                'message' => 'youpi',
                'errors' => [],
                'data' => [
                    'contactsGroup' => $contactsGroup,
                    'countTotalGuests' => $countTotalGuests,
                    'countPresent' => $countPresent,
                    'countAbsent' => $countAbsent,
                    'countWaiting' => $countWaiting
                ],
                //'token' => 'youpi',
                //'userid' => 'youpi',
            ]
        );
    }

}
