<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Team;
use App\Form\Type\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamEditForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Team|null $team */
        $team = $options['data'] ?? null;

        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                // documentation is for NelmioApiDocBundle
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Name of the team',
                ],
            ])
            ->add('teamlead', UserType::class, [
                'label' => 'label.teamlead',
                'multiple' => false,
                'expanded' => false,
                // documentation is for NelmioApiDocBundle
                'documentation' => [
                    'type' => 'integer',
                    'description' => 'User ID for the teamlead',
                ],
            ])
            ->add('users', UserType::class, [
                'multiple' => true,
                'expanded' => $options['expand_users'],
                'by_reference' => false,
                'documentation' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer', 'description' => 'User IDs'],
                    'title' => 'Team member',
                    'description' => 'Array of team member IDs',
                ],
                // make sure that disabled users show up in the result list
                'include_users' => (null !== $team && $team->getUsers()->count() > 0 ? $team->getUsers()->toArray() : [])
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'expand_users' => true,
            'csrf_token_id' => 'admin_team_edit',
            'attr' => [
                'data-form-event' => 'kimai.teamUpdate'
            ],
        ]);
    }
}
