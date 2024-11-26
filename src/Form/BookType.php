<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('publicationYear')
            ->add('isbn')
            ->add('authorsString', TextType::class, [
                'mapped' => false, // Ne pas mapper directement à l'entité
                'required' => false,
                'label' => 'IDs des auteurs (séparés par des virgules)'
            ])
            ->add('categoriesString', TextType::class, [
                'mapped' => false, // Ne pas mapper directement à l'entité
                'required' => false,
                'label' => 'IDs des catégories (séparés par des virgules)'
            ])
            ;
    }
/**
 * Configures the options for this form type.
 *
 * @param OptionsResolver $resolver The resolver for the options.
 */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
