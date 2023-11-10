<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Posts;
use App\Entity\User;
use App\Form\PostsType;
use App\Repository\LikeRepository;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/posts')]
class PostsController extends AbstractController
{
    #[Route('/', name: 'app_posts_index', methods: ['GET'])]
    public function index(PostsRepository $postsRepository, Request $request): Response
    {

        $test = $request->getSession()->get('test', false);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('posts/index.html.twig', [
            'posts' => $postsRepository->findAll(),
            'test' => $test,
        ]);
    }

    #[Route('/new', name: 'app_posts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // 1 POSTS PAR SEMAINE TEST
        $user = $this->getUser();

        // Vérification si l'utilisateur a déjà créé un post cette semaine
        $now = new \DateTime('now');
        $startOfWeek = (clone $now)->modify('last monday');
        $endOfWeek = (clone $now)->modify('next sunday');

        $postRepository = $doctrine->getRepository(Posts::class);
        $postsThisWeek = $postRepository->findByUserAndWeek($user, $startOfWeek, $endOfWeek);
        $test = false;

        if (count($postsThisWeek) >= 1) {
            // L'utilisateur a déjà créé un post cette semaine, rediriger ou afficher un message d'erreur
            $test = true;
            $request->getSession()->set('test', $test);
            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }


        // FIN DU 1 POSTS PAR SEMAINE

        $post = new Posts();
        $post->setDateTimeModif(new \DateTime('now'));
        $post->setUser($this->getUser());

        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        $posts = $doctrine->getRepository(Posts::class)->findAll();

        return $this->render('posts/new.html.twig',  [
            'post' => $post,
            'posts' => $posts,
            'form' => $form,
            'user' => $user,
            'test' => $test,
        ]);
    }

    #[Route('/{id}', name: 'app_posts_show', methods: ['GET'])]
    public function show(Posts $post): Response
    {
        $user = $post->getUser();

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_posts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_posts_delete', methods: ['POST'])]
    public function delete(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/recherche', name: 'recherche_posts', methods: ['GET'])]
    public function search(Request $request, PostsRepository $postsRepository): Response
    {
        $term = $request->query->get('q');
        $posts = $postsRepository->searchByUser($term);

        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/like/{id}', name: 'like_post')]
    public function likePost(Posts $post, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        // Vérifiez si l'utilisateur a déjà aimé le post
        $liked = $post->getLikes()->exists(function($key, $like) use ($user) {
            return $like->getUser() === $user;
        });

        if ($liked) {
            // Retirer le like
            $like = $post->getLikes()->filter(function($like) use ($user) {
                return $like->getUser() === $user;
            })->first();

            $doctrine->getManager()->remove($like);
        } else {
            // Ajouter le like
            $like = new Like();
            $like->setUser($user);
            $like->setPost($post);

            $doctrine->getManager()->persist($like);

        }

        $doctrine->getManager()->flush();
        // Rediriger vers la page où se trouve le post
        return $this->redirectToRoute('app_posts_index');
    }
}
