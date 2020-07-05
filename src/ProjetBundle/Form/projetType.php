<?php

namespace ProjetBundle\Form;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Client;
use AppBundle\Entity\Jardin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class projetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre')

            ->add('dateprojet', BirthdayType::class, [
            'placeholder' => [
                'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
            ]
        ])
            ->add('description')->add('budget')
            ->add('client', EntityType::class,[
                'class' => Client::class,
                'choice_label' => 'nom',

                'expanded' => false,
                'multiple' => false,

            ])
            ->add('Categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libelle'
            ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Projet'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_projet';
    }


}
