<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class SortPropertyAdsType extends AbstractType
{
    public const PUBLISHED_AT_ASC = 'published_at_asc';
    public const PUBLISHED_AT_DESC = 'published_at_desc';
    public const PRICE_ASC = 'price_asc';
    public const PRICE_DESC = 'price_desc';
    public const AREA_ASC = 'area_asc';
    public const AREA_DESC = 'area_desc';

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'label' => 'Annonces datant de moins de',
                'choices' => [
                    'Date décroissante' => self::PUBLISHED_AT_DESC,
                    'Date croissante' => self::PUBLISHED_AT_ASC,
                    'Prix décroissant' => self::PRICE_DESC,
                    'Prix croissant' => self::PRICE_ASC,
                    'Surface décroissante' => self::AREA_DESC,
                    'Surface croissante' => self::AREA_ASC
                ]
            ])
        ;
    }
}
