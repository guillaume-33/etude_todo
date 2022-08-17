<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\ProjetType;
use App\Form\TacheType;
use App\Repository\CategorieRepository;
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
    public function createTache(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository){
        $tache = new Tache();


        $form= $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $projet =new Projet();
            $projet->setTitre($form->get('projet')->getData());//recupere les donnée entrées sur le formulaire
            $projet->setCategorie($categorieRepository->find(1));
            $entityManager->persist($projet);
            $entityManager->flush();
            $tache->setProjet($projet);
            $entityManager->persist($tache);
            $entityManager->flush();

           $this->addFlash('success', 'tache créée');
        }
        return  $this->render('create_tache.html.twig',[
            'form'=>$form->createView(),
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


    /**
     * @Route("/user/update/tache", name="update_tache")
     */
    public function userUpdateTache(TacheRepository $tacheRepository , EntityManagerInterface $entityManager ,Request $request){
        $id=$request->query->get('id');

        $tache =$tacheRepository->find($id);

        $user=$this->getUser();// je verifie l'utisisateur en ligne

        if ($user===$tache->getDestinataire()){ //si utilisateur === destinataire, j'accede a la modification
            if($request->query->has('statut')){
                $statut = $request->query->get('statut');

                $tache->setStatut($statut);

                $entityManager->persist($tache);
                $entityManager->flush();
            }

            return $this->render('user_update_tache.html.twig', [
                'tache'=>$tache
            ]);
            $this->addFlash('success', 'tache mise a jour');
        } else{
            return $this->render('user_taches.html.twig');
        }

    }

    /**
     * @Route("/edit/tache" , name="edit_tache")
     */

    public function adminupdateListes(TacheRepository $tacheRepository, Request $request, EntityManagerInterface $entityManager){
        $id=$request->query->get('id');

        $tache = $tacheRepository->find($id);
        $form= $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        $user = $this->getUser();//je recupère l'utilisateur en ligne
        if($user=== $tache->getExpediteur()) { //verifie que l'utilisateur soit bien l'expediteur
            if ($form ->isSubmitted()) {

                $entityManager->persist($tache);
                $entityManager->flush();
            }
            $this->addFlash("success", "Liste mise a jour");

            return $this->render("edit_tache.html.twig", [
                'form' => $form->createView()
            ]);
        }else{
            return $this->render('user_taches.html.twig');
        }
    }


    /**
     * @Route("/delete/tache", name="delete_tache")
     */
    public function deleteTache(Request $request, TacheRepository $tacheRepository, EntityManagerInterface $entityManager){
       $id=$request->query->get('id');
       $tache=$tacheRepository->find($id);
        $entityManager->remove($tache);
        $entityManager->flush();
        $this->addFlash('success', 'Tache supprimée');
        return $this->redirectToRoute('user_taches');
    }


}