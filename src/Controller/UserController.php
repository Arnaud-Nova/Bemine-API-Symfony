<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Person;
use App\Entity\Wedding;
use App\Repository\UserRepository;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

            $data = 
            [
                'message' => 'Il y a déjà un compte associé à cet email. Merci de vous connecter ou de vous inscrire avec une autre adresse email.'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
            
        }
        
        //je récupère le reste de mes données du front
        // $urlAvatar = $contentDecode->urlAvatar;
        $firstname = $contentDecode->firstnameUser;
        $lastname = $contentDecode->nameUser;
        $spouseFirstname = $contentDecode->firstnamePartner;
        $spouseLastname = $contentDecode->namePartner;
        $weddingDate = $contentDecode->weddingDate;
        
        //Je crée une nouvelle instance de wedding car chaque nouveau user implique la création de son wedding.
        $wedding = new Wedding();
        $wedding->setDate(\DateTime::createFromFormat('Y-m-d', $weddingDate));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($wedding);

        //j'initialise les events type de mon mariage
        
        $event1 = new Event();
        $event1->setName('Cérémonie');
        $event1->setWedding($wedding);
        $event1->setActive(false);

        $event2 = new Event();
        $event2->setName('Vin d\'honneur');
        $event2->setWedding($wedding);
        $event2->setActive(false);

        $event3 = new Event();
        $event3->setName('Réception');
        $event3->setWedding($wedding);
        $event3->setActive(false);

        $event4 = new Event();
        $event4->setName('Brunch');
        $event4->setWedding($wedding);
        $event4->setActive(false);

        $entityManager->persist($event1);
        $entityManager->persist($event2);
        $entityManager->persist($event3);
        $entityManager->persist($event4);

        //je crée mon nouveau user 
        $user = new User();
        //petite interrogation sur la récupération du password
        // $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
        $encodedPassword = $passwordEncoder->encodePassword($user, $contentDecode->password);
        $user->setPassword($encodedPassword);
        $user->setEmail($email);
        // $user->setUrlAvatar($urlAvatar);
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

        
        $data = 
            [
                'message' => 'Votre compte a bien été crée.'
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;

    }

    /**
     * @Route("/brides/profil", name="profil", methods={"GET"})
     */
    public function profil(UserRepository $userRepository, PersonRepository $personRepository, WeddingRepository $weddingRepository, Request $request)
    {
        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();
        //récupération du user id grâce à AuthenticatedListener
        $userId = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getId();

        // je récupère mon user connecté grâce à l'id du user passée en url
        $thisUser = $userRepository->findUserProfilQueryBuilder($userId);

        
        if (!$thisUser){
            
            $data = 
            [
                'message' => 'Le user id n\'existe pas.'
            ]
            ;
            
            $response = new JsonResponse($data, 400);
        
            return $response;
        }
        
        $newlyweds = $personRepository->findByNewlyweds($userWedding);
        $thisWeddingArray = $weddingRepository->findThisWedding($userWedding);
    //     $renderThisWedding = [];

    //    $renderThisWedding['id'] = $thisWeddingArray[0]['id'];
    //    $renderThisWedding['date'] = $thisWeddingArray[0]['date']->format('Y-m-d');
        // dd($renderThisWedding);


        $data = 
            [
                'thisUser' => $thisUser,
                'newlyweds' => $newlyweds,
                'wedding' => $thisWeddingArray
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;
    }

    /**
     * @Route("/brides/profil/edit", name="editProfil", methods={"GET", "POST"})
     */
    public function editProfil(UserRepository $userRepository, PersonRepository $personRepository, WeddingRepository $weddingRepository, Request $request)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();
        //récupération du user id grâce à AuthenticatedListener
        $userId = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getId();

        // je récupère mon user connecté grâce à l'id du user passée en url
        $thisUser = $userRepository->findUserProfilQueryBuilder($userId);

        if (!$thisUser){
            
            $data = 
            [
                'message' => 'Le user id n\'existe pas.'
            ]
            ;
            
            $response = new JsonResponse($data, 400);
        
            return $response;
        }

        $entityManager = $this->getDoctrine()->getManager();
        
       
        foreach ($contentDecode->thisUser as $thisUser){
            if ($userId != $thisUser->userId){
                $message = 'Le user Id envoyé ne correspond pas au user id du user connecté';
                        
                $response = new JsonResponse($message, 400);
            
                return $response;
            } else {
                //ajouter la modif d'email
                $user = $userRepository->find($userId);
                $user->setEmail($thisUser->email);
                $entityManager->persist($user);
            }
        }
        

        foreach ($contentDecode->newlyweds as $newlywedDecode){
            $newlywed = $personRepository->find($newlywedDecode->id);
            
            if ($userWedding->getId() != $newlywed->getWedding()->getId()){
                $message = 'Les newlyweds ne font pas partie du mariage du user connecté';
                        
                $response = new JsonResponse($message, 400);
            
                return $response;
            } else {
                
                $newlywed->setFirstname($newlywedDecode->firstname);
                $newlywed->setLastname($newlywedDecode->lastname);
                $entityManager->persist($newlywed);
            }
        }

        //j'enregistre la nouvelle date en BDD
        $thisWedding = $weddingRepository->find($userWedding);

        foreach($contentDecode->wedding as $oneWedding){
            if ($thisWedding->getId() != $oneWedding->id){
                $message = 'L\'id du mariage envoyé ne correspond pas à l \'id du mariage du user connecté';
                        
                $response = new JsonResponse($message, 400);
            
                return $response;
            } else {
                $weddingDate = $oneWedding->date->date;
                $createDate = new DateTime($weddingDate);
                $finalDate = $createDate->format('Y-m-d');
                // dd($finalDate);
                $thisWedding->setDate(\DateTime::createFromFormat('Y-m-d', $finalDate));
                $entityManager->persist($thisWedding);
            }
                

        }   
       

        $entityManager->flush();

        // $newlyweds = $personRepository->findByNewlyweds($id);
        // $thisWeddingArray = $weddingRepository->findThisWedding($id);

        $data = 
            [
                // 'thisUser' => $thisUser,
                // 'newlyweds' => $newlyweds,
                // 'wedding' => $thisWeddingArray
                'message' => 'La modification a bien été prise en compte.'
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;
    }

    // /**
    //  * @Route("/brides/profil/edit/{userId}/{id}", name="editProfil", methods={"GET", "POST"})
    //  */
    // public function editProfil(UserRepository $userRepository, $userId, $id, PersonRepository $personRepository, WeddingRepository $weddingRepository, Request $request)
    // {

    //     //je récupère les données du front dans l'objet request.
    //     $content = $request->getContent();
    //     $contentDecode = json_decode($content);

    //     // je récupère mon user connecté grâce à l'id du user passée en url
    //     $thisUser = $userRepository->findUserProfilQueryBuilder($userId);

    //     if (!$thisUser){
            
    //         $data = 
    //         [
    //             'message' => 'Le user id n\'existe pas.'
    //         ]
    //         ;
            
    //         $response = new JsonResponse($data, 400);
        
    //         return $response;
    //     }

    //     $entityManager = $this->getDoctrine()->getManager();
        
        
    //     foreach ($contentDecode->thisUser as $thisUser){
    //         //ajouter la modif d'email
    //         $user = $userRepository->find($userId);
    //         $user->setEmail($thisUser->email);
    //         $entityManager->persist($user);
    //     }

    //     foreach ($contentDecode->newlyweds as $newlywedDecode){
    //         $newlywed = $personRepository->find($newlywedDecode->id);
    //         $newlywed->setFirstname($newlywedDecode->firstname);
    //         $newlywed->setLastname($newlywedDecode->lastname);
    //         $entityManager->persist($newlywed);
    //     }

    //     //j'enregistre la nouvelle date en BDD
    //     $thisWedding = $weddingRepository->find($id);

    //     foreach($contentDecode->wedding as $oneWedding){
    //         $weddingDate = $oneWedding->date->date;
    //         $createDate = new DateTime($weddingDate);
    //         $finalDate = $createDate->format('Y-m-d');
    //         // dd($finalDate);
    //         $thisWedding->setDate(\DateTime::createFromFormat('Y-m-d', $finalDate));
    //         $entityManager->persist($thisWedding);

    //     }   
       

    //     $entityManager->flush();

    //     // $newlyweds = $personRepository->findByNewlyweds($id);
    //     // $thisWeddingArray = $weddingRepository->findThisWedding($id);

    //     $data = 
    //         [
    //             // 'thisUser' => $thisUser,
    //             // 'newlyweds' => $newlyweds,
    //             // 'wedding' => $thisWeddingArray
    //             'message' => 'La modification a bien été prise en compte.'
    //         ]
    //     ;

    //     $response = new JsonResponse($data, 200);
       
    //     return $response;
    // }
}
