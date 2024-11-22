<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $books = $entityManager->getRepository(Book::class)->findAll();
        
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'books' => $books
        ]);
    }

}
