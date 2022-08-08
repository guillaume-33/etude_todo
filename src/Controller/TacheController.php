<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TacheController extends AbstractController
{
    /**
     * @Route("/create/tache", name="create_tache")
     */
    public function createTache(Request $request, EntityManagerInterface $entityManager){
        $tache = new Tache();

        $form= $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($tache);
            $entityManager->flush();

           $this->addFlash('success', 'tache créée');
        }
        return  $this->render('create_tache.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/user/taches" , name="user_taches")
     */
public function readTache(TacheRepository $tacheRepository, Request $request){
    $user=$this->getUser();
    $taches=$tacheRepository->findBy(['destinataire'=>$user]);
    $tacheSends=$tacheRepository->findBy(['expediteur'=>$user]);

    return $this->render('user_taches.html.twig',[
        'taches'=>$taches,
        'tachesends'=>$tacheSends
    ]);
}
}