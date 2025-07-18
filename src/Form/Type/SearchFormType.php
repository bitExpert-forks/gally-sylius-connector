<?php

declare(strict_types=1);

namespace Gally\SyliusPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'gally_sylius.form.header.query.placeholder',
                    'autocomplete' => 'off',
                    'aria-label' => 'form.header.query.label',
                    'aria-describedby' => 'collapsedSearchResults' // This should be the ID of the button below
                ]
            ])
        ;
    }
}
