<?php
namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'category_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/{id}', name: 'category_detail')]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }
}