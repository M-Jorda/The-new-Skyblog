<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post', name: 'post_')]
class PostController extends AbstractController {
    #[Route('s', name: 'list')]
    public function postList(PostRepository $postRepo) {
        $posts = $postRepo->getPosts();
        return $this->render('post/listPost.html.twig', [
            "posts" => $posts
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(int $id, PostRepository $postRepo) {
        $post = $postRepo->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Nothing here');
        }

        return $this->render('post/detailPost.html.twig', [
            "post" => $post,
        ]);
    }


    #[Route('/create', name: 'create')]
    public function createPost(Request $request, EntityManagerInterface $em) {
        $post = new Post();
        $post->setUser($this->getUser());
        $post->setCreatedDate(new \DateTime());
        $postForm = $this->createForm(PostType::class, $post);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_detail', ['id' => $post->getId()]);
        }

        return $this->render('post/create.html.twig', [
            'postForm' => $postForm->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(
        int $id,
        PostRepository $postRepo,
        Request $request,
        EntityManagerInterface $em
    ) {
        $post = $postRepo->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Nothing here');
        }

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('post_detail', ['id'=> $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form
        ]);
    }
}