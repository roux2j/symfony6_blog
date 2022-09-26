<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                'placeholder' => 'Choisissez une catégorie',
            
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('tags', EntityType::class, [
                // looks for choices from this entity
                'class' => Tag::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'name',
            
                // used to render a select box, check boxes or radios
                'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('imageFile', VichImageType::class, array(
                'required' => false,
                'allow_delete' => false,
                'delete_label' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                // 'imagine_pattern' => '...',
                'asset_helper' => true,
            ))
            ->add('content', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
