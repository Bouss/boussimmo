<?php

namespace App\Form\Type;

use Google_Service_Gmail_Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterPropertyAdsType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newerThan', ChoiceType::class, [
                'label' => 'Annonces datant de moins de',
                'choices' => [
                    '24 heures' => 1,
                    '3 jours' => 3,
                    '5 jours' => 5,
                    '1 semaine' => 7,
                    '2 semaines' => 14,
                    '4 semaines' => 28,
                ]
            ])
            ->add('label', ChoiceType::class, [
                'label' => 'Label Gmail',
                'choices' => $this->formatLabelChoices($options['labels']),
                'required' => false,
                'empty_data' => null
            ])
            ->add('newBuild', CheckboxType::class, [
                'label' => 'Neuf',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Appliquer'
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'labels' => [],
        ]);

        $resolver->setAllowedTypes('labels', 'array');
    }

    /**
     * @param Google_Service_Gmail_Label[] $labels
     *
     * @return array
     */
    private function formatLabelChoices(array $labels): array
    {
        $choices = [];

        foreach ($labels as $label) {
            $choices[$label->getName()] = $label->getId();
        }

        return $choices;
    }
}
