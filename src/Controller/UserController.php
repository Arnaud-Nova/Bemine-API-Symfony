<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Person;
use App\Entity\Wedding;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    /**
     * @Route("/signup", name="signup", methods={"POST"})
     */
    public function signup(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);
   
        //je récupère mes données du front
        $email = $contentDecode->email;
        
        //si l'email existe déjà en base, je renvoie un message
        $alreayUser = $userRepository->findByEmail($email);
        if ($alreayUser){
            $message = 'l\'email du user existe déjà';
            $response = new Response($message, 200);
            $response->headers->set('Content-Type', 'application/json');
           
            return $response;
            
        }
        
        //je récupère le reste de mes données du front
        $urlAvatar = $contentDecode->urlAvatar;
        $firstname = $contentDecode->firstname;
        $lastname = $contentDecode->lastname;
        $spouseFirstname = $contentDecode->spouseFirstname;
        $spouseLastname = $contentDecode->spouseLastname;
        $weddingDate = $contentDecode->date;
        
        //Je crée une nouvelle instance de wedding car chaque nouveau user implique la création de son wedding.
        $wedding = new Wedding();
        $wedding->setDate(\DateTime::createFromFormat('Y-m-d', $weddingDate));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($wedding);

        //je crée mes events types
        
        $event = new Event();
        $event->setName('Cérémonie');
        
        
        
        // $wedding->setDate(date($contentDecode->date));
        // dd($wedding);
        
        //je crée mon nouveau user 
        $user = new User();
        //petite interrogation sur la récupération du password
        // $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
        $encodedPassword = $passwordEncoder->encodePassword($user, $contentDecode->password);
        $user->setPassword($encodedPassword);
        $user->setEmail($email);
        $user->setUrlAvatar($urlAvatar);
        $user->setWedding($wedding);
        // $user->setRoles(['ROLE_USER']);
        
        //je crée ma nouvelle personne, car un user est aussi une personne.
        $person = new Person();
        $person->setNewlyweds(true);
        $person->setFirstname($firstname);
        $person->setLastname($lastname);
        $person->setMenu('ADULTE');
        $person->setWedding($wedding);
        $person->setAttendance(1);
        
        //je crée le deuxième marié
        $personSpouse = new Person();
        $personSpouse->setFirstname($spouseFirstname);
        $personSpouse->setLastname($spouseLastname);
        $personSpouse->setNewlyweds(true);
        $personSpouse->setMenu('ADULTE');
        $personSpouse->setWedding($wedding);
        $personSpouse->setAttendance(1);
        
        $entityManager->persist($user);
        $entityManager->persist($wedding);
        $entityManager->persist($person);
        $entityManager->persist($personSpouse);
        $entityManager->flush();
        
        //je set mon flash message avec symfo, voir si c'est fait avec react ou pas
        $this->addFlash(
            'success',
            'Votre compte a bien été crée, merci de vous connecter.'
        );

        $userId = $user->getId();
        
        $data = 
            [
                'userId' => $userId
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;

    }

    /**
     * @Route("/brides/profil/{userId}", name="profil", methods={"GET", "POST"})
     */
    public function profil(UserRepository $userRepository, $userId)
    {
        // je récupère mon user connecté grâce à l'id du user passée en url
        $thisUser = $userRepository->findUserProfilQueryBuilder($userId);

        if (!$thisUser){
            $message = 'Le user id n\'existe pas';
            $response = new Response($message, 404);
            $response->headers->set('Content-Type', 'application/json');
           
            return $response;
        }

        $data = 
            [
                'thisUser' => $thisUser
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;
    }
}
