<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book/back')]
final class BookBackController extends AbstractController
{
    #[Route(name: 'app_book_back_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book_back/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_book_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les chaînes d'auteurs et de catégories
            $authorsString = $form->get('authorsString')->getData();
            $categoriesString = $form->get('categoriesString')->getData();

            // Traiter les chaînes d'auteurs et de catégories
            $this->processAuthorsAndCategories($book, $authorsString, $categoriesString, $entityManager);

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été ajouté avec succès.');

            return $this->redirectToRoute('app_book_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_back/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
            'authors' => $entityManager->getRepository(Author::class)->findAll(),
            'categories' => $entityManager->getRepository(Category::class)->findAll(),

        ]);
    }

    #[Route('/{id}', name: 'app_book_back_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book_back/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_back/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_back_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_back_index', [], Response::HTTP_SEE_OTHER);
    }

    private function processAuthorsAndCategories(Book $book, ?string $authorsString, ?string $categoriesString, EntityManagerInterface $em): void
    {
        // Traiter les auteurs
        if (!empty($authorsString)) {
            $authorNames = array_filter(array_map('trim', explode(',', $authorsString)));
            foreach ($authorNames as $authorName) {
                $author = $em->getRepository(Author::class)->findOneBy(['name' => $authorName]);
                if (!$author) {
                    $author = new Author();
                    $author->setName($authorName);
                    $em->persist($author);
                }
                
                // Vérifier si l'auteur n'est pas déjà lié au livre
                if (!$book->getAuthors()->contains($author)) {
                    $book->addAuthor($author);
                }
            }
        }

        // Traiter les catégories
        if (!empty($categoriesString)) {
            $categoryNames = array_filter(array_map('trim', explode(',', $categoriesString)));
            foreach ($categoryNames as $categoryName) {
                $category = $em->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
                if (!$category) {
                    $category = new Category();
                    $category->setName($categoryName);
                    $em->persist($category);
                }
                
                // Vérifier si la catégorie n'est pas déjà liée au livre
                if (!$book->getCategories()->contains($category)) {
                    $book->addCategory($category);
                }
            }
        }
    }
}
