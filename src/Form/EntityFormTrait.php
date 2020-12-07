<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Form\Type\ColorPickerType;
use App\Form\Type\DurationType;
use App\Form\Type\MetaFieldsCollectionType;
use App\Form\Type\YesNoType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

trait EntityFormTrait
{
    public function addCommonFields(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('color', ColorPickerType::class, [
                'required' => false,
            ])
        ;

        if ($options['include_budget']) {
            $builder
                ->add('budget', MoneyType::class, [
                    'empty_data' => '0.00',
                    'label' => 'label.budget',
                    'required' => false,
                    'currency' => $options['currency'],
                ])
                ->add('timeBudget', DurationType::class, [
                    'empty_data' => 0,
                    'label' => 'label.timeBudget',
                    'icon' => 'clock',
                    'required' => false,
                ])
            ;
        }

        $builder->add('metaFields', MetaFieldsCollectionType::class);

        $builder
            ->add('visible', YesNoType::class, [
                'label' => 'label.visible',
            ]);
    }

    public function addCreateMore(FormBuilderInterface $builder): void
    {
        $builder->add('create_more', CheckboxType::class, [
            'label' => 'label.create_more',
            'required' => false,
            'mapped' => false,
        ]);
    }
}
