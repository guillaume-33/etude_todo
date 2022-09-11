<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;

use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Return_;
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

}