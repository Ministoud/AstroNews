<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artName')
            ->add('artContent')
            ->add('artImage', null, array('data_class' => null, 'mapped' => false, 'attr' => [
                'placeholder' => 'Choisissez une image pour votre article (240x320 de préférence pour éviter les déformations)'
            ]))
            ->add('artSections', EntityType::class, [
                'class' => Section::class,
                'choice_label' => 'secName',
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
