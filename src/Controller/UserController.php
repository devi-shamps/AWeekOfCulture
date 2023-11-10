<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function profile($username, ManagerRegistry $doctrine): Response
    {
        // Fetch user data based on the $username parameter
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }
}
