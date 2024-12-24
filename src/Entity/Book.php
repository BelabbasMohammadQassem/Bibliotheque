<?php
namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $publicationYear = null;

    #[ORM\Column(length: 20)]
    private ?string $isbn = null;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'book_author')]
    private Collection $authors;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'book_category')]
    private Collection $categories;

    // Ajout des propriétés transientes
    private ?string $authorsString = null;
    private ?string $categoriesString = null;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    // Getters et setters pour les propriétés transientes
    public function getAuthorsString(): ?string
    {
        // Si aucune valeur n'est définie, générer à partir des auteurs existants
        if (null === $this->authorsString && !$this->authors->isEmpty()) {
            $this->authorsString = implode(', ', $this->authors->map(fn(Author $author) => $author->getName())->toArray());
        }
        return $this->authorsString;
    }

    public function setAuthorsString(?string $authorsString): self
    {
        $this->authorsString = $authorsString;
        return $this;
    }

    public function getCategoriesString(): ?string
    {
        // Si aucune valeur n'est définie, générer à partir des catégories existantes
        if (null === $this->categoriesString && !$this->categories->isEmpty()) {
            $this->categoriesString = implode(', ', $this->categories->map(fn(Category $category) => $category->getName())->toArray());
        }
        return $this->categoriesString;
    }

    public function setCategoriesString(?string $categoriesString): self
    {
        $this->categoriesString = $categoriesString;
        return $this;
    }

    // Toutes les autres méthodes restent identiques à votre version originale
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getPublicationYear(): ?int
    {
        return $this->publicationYear;
    }

    public function setPublicationYear(int $publicationYear): self
    {
        $this->publicationYear = $publicationYear;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
            $author->addBook($this); // Ajout de la relation bidirectionnelle
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->removeElement($author)) {
            $author->removeBook($this); // Suppression de la relation bidirectionnelle
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addBook($this); // Ajout de la relation bidirectionnelle
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeBook($this); // Suppression de la relation bidirectionnelle
        }

        return $this;
    }
}