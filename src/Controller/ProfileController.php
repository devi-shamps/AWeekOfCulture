<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class ProfileController extends AbstractController
{
    #[Route('/profile/{pseudo}', name: 'app_profile')]
    public function index(string $pseudo, ManagerRegistry $doctrine, PostsRepository $postsRepository): Response
    {

        $user = $doctrine->getRepository(User::class)->findOneBy(['pseudo' => $pseudo]);

        $posts = $user->getPosts();

        $nbPosts = count($posts);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'posts' => $posts,
            'nbPosts' => $nbPosts,
        ]);
    }
}
