<?php
namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/authors')]
class AuthorController extends AbstractController
{
    #[Route('/author/list', name: 'author_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $authors = $entityManager->getRepository(Author::class)->findAll();
        
        return $this->render('author/index.html.twig', [
            'authors' => $authors
        ]);
    }

    #[Route('/{id}', name: 'author_detail')]
    public function show(Author $author): Response
    {
        return $this->render('author/show.html.twig', [
            'author' => $author
        ]);
    }
}