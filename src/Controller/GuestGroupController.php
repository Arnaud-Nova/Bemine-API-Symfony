<?php

namespace App\Controller;

use App\Repository\PersonRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GuestGroupController extends AbstractController
{
    /**
     * @Route("/brides/guests/group/{groupId}", name="show_group", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function showGroup(GuestGroupRepository $guestGroupRepository, $groupId)
    {
        $guestGroupArray = $guestGroupRepository->findByGuestGroupIdQueryBuilder($groupId);
        
        if (!$guestGroupArray){
            $data = 
            [
                'message' => 'Le guestGroupId n\'existe pas'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
        }

        
        $data = 
            [
                'guestGroup' => $guestGroupArray
            ]
        ;

        $response = new JsonResponse($data, 200);       
        return $response;
    }

    /**
     * @Route("/brides/group/edit", name="edit_group", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function editGroup(GuestGroupRepository $guestGroupRepository, Request $request)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        //je récupère le guestGroup 
        $guestGroup = $guestGroupRepository->find($contentDecode->groupId);   
        
        if (!$guestGroup){
            $data = 
            [
                'message' => 'Le guestGroupId n\'existe pas'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
        }

        
        $guestGroup->setEmail($contentDecode->email);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($guestGroup);
        $entityManager->flush();

        
        $data = 
            [
                'message' => 'La modification a bien été prise en compte.'
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;

    }

    /**
     * @Route("/brides/group/delete", name="delete_group", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function deleteGroup(GuestGroupRepository $guestGroupRepository, Request $request)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);
        
        //je récupère le guestGroup 
        $guestGroup = $guestGroupRepository->find($contentDecode->groupId);   
        
        if (!$guestGroup){
            $data = 
            [
                'message' => 'Le guestGroupId n\'existe pas'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
        }


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($guestGroup);
        $entityManager->flush();

        
        $data = 
            [
                'message' => 'La suppression a bien été prise en compte.'
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;

    }

    
}
