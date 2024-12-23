<?php

namespace MauticPlugin\MauticRecaptchaBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RecaptchaType.
 */
class RecaptchaType extends AbstractType
{
    /**
     * Builds the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'minScore',
            NumberType::class,
            [
                'label'      => 'mautic.recaptcha.min.score',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.recaptcha.min.score.tooltip',
                ],
                'data'       => $options['minScore'] ?? 0.8,
            ]
        );

        $builder
            ->add('badgePosition',
                ChoiceType::class,
                [
                    'choices' => [
                        'mautic.recaptcha.badge.bright' => 'bottomright',
                        'mautic.recaptcha.badge.bleft'  => 'bottomleft',
                        'mautic.recaptcha.badge.inline' => 'inline',
                    ],
                    'label'    => 'mautic.recaptcha.badge.position',
                    'required' => true,
                    'data'     => $options['badgePosition'] ?? 'bottomright',
                ]);

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'apply_text'     => false,
                'save_text'      => 'mautic.core.form.submit',
                'cancel_onclick' => 'javascript:void(0);',
                'cancel_attr'    => [
                    'data-dismiss' => 'modal',
                ],
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * Configures the options for this type.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'minScore'      => 0.8,
            'badgePosition' => 'bottomright',
        ]);
    }

    /**
     * Returns the prefix for the form type.
     */
    public function getBlockPrefix(): string
    {
        return 'recaptcha';
    }
}
