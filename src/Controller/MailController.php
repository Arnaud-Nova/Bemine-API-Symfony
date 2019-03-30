<?php

namespace App\Controller;


use App\Repository\MailRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PersonRepository;

/**
* @Route("/brides/mail/", name="mail_")
*/
class MailController extends AbstractController
{

    /**
     * @Route("send", name="send", methods={"POST"})
     */
    public function sendEmail(GuestGroupRepository $ggRepo, Request $request, \Swift_Mailer $mailer, PersonRepository $pRepo)
    {
        $data = json_decode($request->getContent());

        $guestGroupsId = $data->list_mailing;
        $errorsMessage = [];

        
        foreach ($guestGroupsId as $id) {
            $guestGroup = $ggRepo->findOneBy(['id' => $id]);
            if (!$guestGroup) {
                $errorsMessage[] = 'Un email n\'a pas été expédié car il n\'y a pas de groupe avec l\'id ' . $id;
            } else {
            
            // $recipient = $guestGroup->getEmail(); // A commenter pour fonctionnement faker
            $recipient = 'calmelsarnaud@gmail.com'; // A décommenter pour fonctionnement faker
            $wedding = $guestGroup->getWedding();
            $newlyweds = $pRepo->findBy([
                'wedding' => $wedding,
                'newlyweds' =>true
            ]);

            $message = (new \Swift_Message('Invitation Email'))
            ->setFrom('oweddingteam@gmail.com')
            ->setTo($recipient)
            ->setSubject('Invitation au mariage de ' . $newlyweds[0]->getFirstname() . ' et ' . $newlyweds[1]->getFirstname())
            ->setBody(
                $this->renderView(
                    'mail/invitation.html.twig', [
                        'newlywed1' => $newlyweds[0],
                        'newlywed2' => $newlyweds[1],
                        'wedding' => $wedding,
                        'guestGroup' => $guestGroup,
                        ]),
                'text/html'
            );

            $mailer->send($message);
            }
        }


        if (!empty($errorsMessage)) {
            $httpCode = 400;
        } else {
            $httpCode = 200;
            $errorsMessage[] = 'Emails envoyés';
        }

        $response = new JsonResponse($errorsMessage, $httpCode);
       
        return $response;
    }

    /**
     * @Route("show", name="show", methods={"GET"})
     */
    public function showEmail(GuestGroupRepository $ggRepo, Request $request, \Swift_Mailer $mailer, PersonRepository $pRepo)
    {

        $data = json_decode($request->getContent());
        dump($request);
        dd($request->attributes->get('emailUser'));

        $guestGroupsId = $data->list_mailing;

        $i = 0;
        foreach ($guestGroupsId as $id) {
            $guestGroup = $ggRepo->findOneBy(['id' => $id]);
            if (!$guestGroup) {
                $errorsMessage[] = 'Pas de groupe correspondants à cet id : ' . $id;

            } elseif ($i < 1) {

            $recipient = $guestGroup->getEmail();
            $wedding = $guestGroup->getWedding();
            $newlyweds = $pRepo->findBy([
                'wedding' => $wedding,
                'newlyweds' =>true
            ]);
            $i++;
            } 
        }

        if (!empty($errorsMessage)) {
            $response = new JsonResponse($errorsMessage, 400);
       
            return $response;

        }

        return $this->render(
            'mail/invitation.html.twig', [
                'newlywed1' => $newlyweds[0],
                'newlywed2' => $newlyweds[1],
                'wedding' => $wedding,
                'guestGroup' => $guestGroup,
                ]
            );
    }

}