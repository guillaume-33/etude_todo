<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\ProjetType;
use App\Form\TacheType;
use App\Repository\CategorieRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
            $projet =new Projet(); //permet de rajouter un champ de l'entité Projet a la tache
            $projet->setTitre($form->get('projet')->getData());//recupere les données entrées dans le champ formulaire pour le projet
            $projet->setCategorie($categorieRepository->find(1));// pour l'instant,automatiquement selectionné sur travail
            $entityManager->persist($projet);
            $entityManager->flush();
            $tache->setProjet($projet);
            $tache->setExpediteur($this->getUser());//permet de définir l'utilisateur comme etant l'expediteur
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
    public function readTache(TacheRepository $tacheRepository){
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
    //methode permettant de mettre à jour les tache utilisateur
    //récupère l'id de la tache et l'utilisateur en ligne
    public function userUpdateTache(TacheRepository $tacheRepository , EntityManagerInterface $entityManager ,Request $request){
        $id=$request->query->get('id');
        $tache =$tacheRepository->find($id);

        // verifie l'utisisateur en ligne
        $user=$this->getUser();

        //si utilisateur est le destinataire de la tâche, il accède à la page de modification
        if ($user===$tache->getDestinataire()){
            if($request->query->has('statut')){
                $statut = $request->query->get('statut');
                $tache->setStatut($statut);
                $entityManager->persist($tache);
                $entityManager->flush();
                $this->addFlash('success', 'tache mise a jour');
            }
            return $this->render('user_update_tache.html.twig', [
                'tache'=>$tache
            ]);
        } else{
            //sinon il est redirigé
            return $this->render('accueil.html.twig');
        }
    }

    /**
     * @Route("/edit/tache" , name="edit_tache")
     */

    public function adminEditTache(TacheRepository $tacheRepository, Request $request, EntityManagerInterface $entityManager){
        $id=$request->query->get('id');

        $tache = $tacheRepository->find($id);
        $form= $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        //recupère l'utilisateur en ligne
        $user = $this->getUser();

        //verifie que l'utilisateur soit bien l'expediteur
        if($user=== $tache->getExpediteur()) {
            if ($form ->isSubmitted() && $form->isValid()) {
                $entityManager->persist($tache);
                $entityManager->flush();
                $this->addFlash("success", "Liste mise a jour");
            }
            return $this->render("edit_tache.html.twig", [
                'form' => $form->createView()
            ]);
        }else{
            //sinon, il est redirigé vers la page d'accueil
            return $this->render('accueil.html.twig');
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

    /**
     * @Route ("admin/read/tache" , name="admin_read_tache")
     * @IsGranted("ROLE_ADMIN", message="Accès non autorisé.")
     */

    public function adminReadTaches(TacheRepository $tacheRepository, EntityManagerInterface $entityManager){

            $taches = $tacheRepository->findAll();
            return $this->render('admin_read_taches.html.twig', [
                'taches' => $taches,
            ]);
        }


    /**
     * @Route ("/admin/tache", name="admin_delete")
     */
    public function adminDeleteTache(Request $request , TacheRepository $tacheRepository ,EntityManagerInterface $entityManager){
        $id=$request->query->get('id');
        $tache=$tacheRepository->fin($id);
        $entityManager->remove($tache);
        $entityManager->flush();
        $this->addFlash('success', 'Tache supprimée');
        return $this->redirectToRoute();
    }
}