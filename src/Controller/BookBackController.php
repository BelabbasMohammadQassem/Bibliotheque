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
            // Récupérer les chaînes d'auteurs et de catégories
            $authorsString = $form->get('authorsString')->getData();
            $categoriesString = $form->get('categoriesString')->getData();
            // Réinitialiser les auteurs et catégories existants
            foreach ($book->getAuthors() as $author) {
                $book->removeAuthor($author);
            }
            foreach ($book->getCategories() as $category) {
                $book->removeCategory($category);
            }
            // Traiter les chaînes d'auteurs et de catégories
            $this->processAuthorsAndCategories($book, $authorsString, $categoriesString, $entityManager);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été modifié avec succès.');
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
        // Traitement des auteurs
        // Vérifie si une chaîne d'auteurs est fournie
        if (!empty($authorsString)) {
            // Divise la chaîne en un tableau, supprime les espaces superflus et filtre les entrées vides
            $authorNames = array_filter(array_map('trim', explode(',', $authorsString)));
            
            // Parcourt chaque nom d'auteur
            foreach ($authorNames as $authorName) {
                // Recherche si l'auteur existe déjà en base de données
                $author = $em->getRepository(Author::class)->findOneBy(['name' => $authorName]);
                
                // Si l'auteur n'existe pas, en crée un nouveau
                if (!$author) {
                    $author = new Author();
                    $author->setName($authorName);
                    // Prépare la persistance du nouvel auteur en base de données
                    $em->persist($author);
                }
                
                // Vérifie si l'auteur n'est pas déjà associé au livre
                // Cela évite les doublons dans la relation
                if (!$book->getAuthors()->contains($author)) {
                    // Ajoute l'auteur au livre
                    $book->addAuthor($author);
                }
            }
        }
    
        // Traitement des catégories 
        // Logique similaire au traitement des auteurs
        if (!empty($categoriesString)) {
            // Divise la chaîne en un tableau, supprime les espaces superflus et filtre les entrées vides
            $categoryNames = array_filter(array_map('trim', explode(',', $categoriesString)));
            
            // Parcourt chaque nom de catégorie
            foreach ($categoryNames as $categoryName) {
                // Recherche si la catégorie existe déjà en base de données
                $category = $em->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
                
                // Si la catégorie n'existe pas, en crée une nouvelle
                if (!$category) {
                    $category = new Category();
                    $category->setName($categoryName);
                    // Prépare la persistance de la nouvelle catégorie en base de données
                    $em->persist($category);
                }
                
                // Vérifie si la catégorie n'est pas déjà associée au livre
                // Cela évite les doublons dans la relation
                if (!$book->getCategories()->contains($category)) {
                    // Ajoute la catégorie au livre
                    $book->addCategory($category);
                }
            }
        }
    }
}
