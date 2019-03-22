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

class UserController extends AbstractController
{
    /**
     * @Route("/signup", name="signup", methods={"POST"})
     */
    public function signup(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);
   
        //je récupère mes données du front
        $email = $contentDecode->email;
        $urlAvatar = $contentDecode->urlAvatar;
        $firstname = $contentDecode->firstname;
        $lastname = $contentDecode->lastname;
        $spouseFirstname = $contentDecode->spouseFirstname;
        $spouseLastname = $contentDecode->spouseLastname;
        
        //Je crée une nouvelle instance de wedding car chaque nouveau user implique la création de son wedding.
        $wedding = new Wedding();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($wedding);
       
        // $wedding->setDate(date($contentDecode->date));
        // dd($wedding);
        
        //je crée mon nouveau user 
        $user = new User();
        //petite interrogation sur la récupération du password
        $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
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
        $person->setAttendance(true);

        //je crée le deuxième marié
        $personSpouse = new Person();
        $personSpouse->setFirstname($spouseFirstname);
        $personSpouse->setLastname($spouseLastname);
        $personSpouse->setNewlyweds(true);
        $personSpouse->setMenu('ADULTE');
        $personSpouse->setWedding($wedding);
        $personSpouse->setAttendance(true);

        $entityManager->persist($user);
        $entityManager->persist($wedding);
        $entityManager->persist($person);
        $entityManager->persist($personSpouse);
        $entityManager->flush();
        // dd($user);

        //je set mon flash message avec symfo, voir si c'est fait avec react ou pas
        $this->addFlash(
            'success',
            'Votre compte a bien été crée, merci de vous connecter.'
        );
                 
        return $this->json(
            [
                'code' => 200,
                'message' => 'youpi',
                'errors' => [],
                'data' => $user,
                //'token' => 'youpi',
                //'userid' => '',
            ]
        );
    }

    /**
     * @Route("/brides/profil/{userId}", name="profil", methods={"GET"})
     */
    public function profil(UserRepository $userRepository, $userId)
    {
        // je récupère mon user connecté grâce à l'id du user passée en url
        $thisUser = $userRepository->findUserProfilQueryBuilder($userId);

        if (!$thisUser){
            return $this->json(
                [
                    'code' => 404,
                    'message' => 'Le user id n\'existe pas',
                    'errors' => [],
                    'data' => [
                    ],
                    //'token' => 'youpi',
                    //'userid' => 'youpi',
                ]
            );
        }

        return $this->json(
            [
                'code' => 200,
                'message' => 'youpi',
                'errors' => [],
                'data' => $thisUser,
                //'token' => 'youpi',
                //'userid' => 'youpi',
            ]
        );
    }
}
