<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class SortPropertyAdsType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'label' => 'Annonces datant de moins de',
                'choices' => [
                    'Date décroissante' => json_encode(['field' => 'publishedAt', 'order' => -1]),
                    'Date croissante' => json_encode(['field' => 'publishedAt', 'order' => 1]),
                    'Prix décroissant' => json_encode(['field' => 'price', 'order' => -1]),
                    'Prix croissant' => json_encode(['field' => 'price', 'order' => 1]),
                    'Surface décroissante' => json_encode(['field' => 'area', 'order' => -1]),
                    'Surface croissante' => json_encode(['field' => 'area', 'order' => 1])
                ]
            ])
        ;
    }
}
