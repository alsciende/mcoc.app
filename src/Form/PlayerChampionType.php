<?php

namespace App\Form;

use App\Form\Model\PlayerChampion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerChampionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('playerId', HiddenType::class)
            ->add('championId', HiddenType::class)
            ->add('name', TextType::class, [
                'disabled' => true
            ])
            ->add('type', TextType::class, [
                'disabled' => true
            ])
            ->add('checked', CheckboxType::class, [
                'required' => false,
            ])
            ->add('rank', IntegerType::class, [
                'required' => false,
            ])
            ->add('signature', IntegerType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlayerChampion::class,
        ]);
    }
}
