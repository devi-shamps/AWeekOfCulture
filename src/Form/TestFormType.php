<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Saisissez votre nom',
                'required' => true,
                'attr' => [
                    'class' => 'une_classe_bootstrap',
                    'id' => 'un_id',
                ]
            ])
            ->add('choice', ChoiceType::class, [
                'label' => 'Aimez-vous les formulaires avec Symfony ?',
                'choices' => [
                    'Oui!' => true,
                    'Non!' => false,
                ]
            ])
            ->add('valide', SubmitType::class, [
                'label' => 'Je valide',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
