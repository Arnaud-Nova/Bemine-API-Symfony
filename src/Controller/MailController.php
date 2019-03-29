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
        // dd('ici');
        $destinataires = []; // pour créer une liste de destinataires
        $destinataires[] = 'calmelsarnaud@gmail.com';
        $name = 'toto'; // création de variables pour personnaliser la vue
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('oweddingteam@gmail.com')
        ->setTo($destinataires)
        ->setSubject('Bemine sera toujours là pour vous !! :)')
        ->setBody(
            $this->renderView(
                'mail/invitation.html.twig', // vue twig correspondant à l'email
                ['name' => $name] // passage des variables
            ),
            'text/html' //format du mail
        )
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'emails/registration.txt.twig',
                ['name' => $name]
            ),
            'text/plain'
        )
        */
        ;
        $mailer->send($message);
        $response = new JsonResponse($name, 200);
       
        return $response;
    }

}
