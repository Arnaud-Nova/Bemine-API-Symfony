<?php

namespace App\Controller;


use App\Repository\MailRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/brides/mail/", name="mail_")
*/
class MailController extends AbstractController
{

    /**
     * @Route("send", name="send", methods={"POST"})
     */
    public function sendEmail(\Swift_Mailer $mailer)
    {
        $destinataires = []; // pour créer une liste de destinataires
        $destinataires[] = 'email@email';
        $name = ''; 
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('oweddingteam@gmail.com')
        ->setTo($destinataires)
        ->setSubject('Bemine sera toujours là pour vous !! :)')
        ->setBody(
            $this->renderView(
                'mail/invitation.html.twig', [
                    'name' => $name
                    ]),
            'text/html'
        );

        $mailer->send($message);
        $response = new JsonResponse($name, 200);
       
        return $response;
    }

    /**
     * @Route("show", name="show", methods={"POST"})
     */
    public function showEmail()
    {
        $name = '';

        return $this->render(
            'mail/invitation.html.twig',
            ['name' => $name]
        );

    }

}
