<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription" , name="inscription")
     */
public function inscription(Request $request , EntityManagerInterface $entityManager , UserPasswordHasherInterface $userPasswordHasher){
    $user = new User();
    $user->setRoles(['ROLE_USER']);

    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $userPassword=$form->get('password')->getData();
            $cryptPassword=$userPasswordHasher->hashPassword($user,$userPassword);

            $user->setPassword($cryptPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Bienvenue sur notre site');
            return $this->redirectToRoute('home');

        }
        return $this->render("inscription.html.twig", [
                        "form"=> $form->createView()
        ]);
}

/**
 * @Route ("/add_admin" , name="add_admin")
 * @IsGranted("ROLE_ADMIN", message="Accès non autorisé.")
 */
//méthode permettant de créer un user avec le role admin
public function AddAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher){
    $user=new User();
    $user->setRoles(['ROLE_ADMIN']);

    $form= $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()){
        $userpassword= $form->get('password')->getData();
        $cryptedPassword=$userPasswordHasher->hashPassword($user, $userpassword);

        $user->setPassword($cryptedPassword);

        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Administrateur créé');
    }
    Return $this->render("Admin/add_admin.html.twig",[
        "form"=>$form->createView()
    ]);
}

/**
 * @Route ("/list_admin", name="list_admin")
 * @IsGranted("ROLE_ADMIN", message="Accès non autorisé.")
 */
//methode pour trouver tous les utilisateurs de la BDD
// à partir de ces user, dans le twig, on ne sélectionne que les admins à afficher
public function listAdmin(UserRepository $userRepository){

   $admins = $userRepository->findAll();
   return $this->render('Admin/list_admin.html.twig', [
       "admins"=>$admins
  ]);
}


    /**
     * @Route("/update_admin/{id}" , name="update_admin")
     * @IsGranted("ROLE_ADMIN", message="Accès non autorisé.")
     */
    //methode pour retirer le statut admin d'un utilisateur admin
public function changeStatus($id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request){

    $user = $userRepository->find($id);
    $user->setRoles(['ROLE_USER']);
    $entityManager->persist($user);
    $entityManager->flush();

    $this->addFlash('success', 'Statut administrateur retiré');
    return $this->redirectToRoute('list_admin');
}

    /**
     * @Route ("/list_user", name="list_user")
     * @IsGranted("ROLE_ADMIN", message="Accès non autorisé.")
     */
//methode pour trouver tous les utilisateurs de la BDD
// à partir de ces user, dans le twig, on ne sélectionne que les users à afficher
    public function listUser(UserRepository $userRepository){

        $users = $userRepository->findAll();
        return $this->render('Admin/list_user.html.twig', [
            "users"=>$users
        ]);
    }
    /**
     * @Route("/update_user/{id}" , name="update_user")
     * @IsGranted("ROLE_ADMIN", message="Accès non autorisé.")
     */
    //methode pour accorder le statut admin d'un utilisateur
    public function changeStatusUser($id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request){
//        $id=$request->query->get('id');

        $user = $userRepository->find($id);
        $user->setRoles(['ROLE_ADMIN']);
        $entityManager->persist($user);
        $entityManager->flush();

       $this->addFlash('success', 'Statut administrateur ajouté');
       return $this->redirectToRoute('list_admin');
    }

}