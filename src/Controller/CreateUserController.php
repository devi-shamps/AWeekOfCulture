<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateUserController extends AbstractController
{
    #[Route('/create/user', name: 'app_create_user')]
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        //Creation du Login de l'utilisateur
        $theUser = new User();
        $theUser->setEmail('dimitri.dechamp@lycee-faure.fr');
        $theUser->setName("Dimitri");
        $theUser->setRoles(['ROLE_ADMIN']);
        $plaintextPassword = '123';

        //Création du mot de passe Hasher
        $hashedPassword = $passwordHasher->hashPassword(
            $theUser,
            $plaintextPassword
        );

        $theUser->setPassword($hashedPassword);

        $doctrine->getManager()->persist($theUser);
        $doctrine->getManager()->flush();
        return $this->render('create_user/index.html.twig', [
            'controller_name' => 'CreateUserController',
        ]);
    }
}
