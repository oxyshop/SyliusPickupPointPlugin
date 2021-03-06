<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\Form\Type;

use Setono\SyliusPickupPointPlugin\Provider\ProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PickupPointChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // @todo Check multiple option works as expected if it will be required
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['choice_name'] = $options['choice_name'];
        $view->vars['choice_value'] = $options['choice_value'];
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['pickup_point_type'] = $options['pickup_point_type'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'choice_name',
                'choice_value',
            ])
            ->setDefaults([
                'multiple' => false,
                'error_bubbling' => false,
                'placeholder' => '',
                'pickup_point_type' => ProviderInterface::PICKUP_POINT_TYPE_SELECTBOX,
            ])
            ->setAllowedTypes('choice_name', ['string'])
            ->setAllowedTypes('choice_value', ['string'])
            ->setAllowedTypes('multiple', ['bool'])
            ->setAllowedTypes('placeholder', ['string'])
            ->setAllowedTypes('pickup_point_type', ['string'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_pickup_point_choice';
    }
}
