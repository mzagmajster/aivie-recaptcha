<?php


return [
    'name'        => 'reCAPTCHA',
    'description' => 'Enables reCAPTCHA integration.',
    'version'     => '1.0.1',
    'author'      => 'Adrian Schimpf',
    'routes'      => [],
    'menu'        => [],
    'services'    => [
        'events' => [
            'mautic.recaptcha.event_listener.form_subscriber' => [
                'class'     => MauticPlugin\MauticRecaptchaBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.integrations.helper',
                    'mautic.recaptcha.service.recaptcha_client',
                    'mautic.lead.model.lead',
                    'translator',
                ],
            ],
        ],
        'models' => [],
        'others' => [
            'mautic.recaptcha.service.recaptcha_client' => [
                'class'     => MauticPlugin\MauticRecaptchaBundle\Service\RecaptchaClient::class,
                'arguments' => [
                    'mautic.integrations.helper',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.recaptcha' => [
                'class'     => MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
                'tags'      => [
                    'mautic.basic_integration',
                ],
            ],
            // Provides the form types to use for the configuration UI
            'mautic.integration.recaptcha.configuration' => [
                'class'     => MauticPlugin\MauticRecaptchaBundle\Integration\Support\ConfigSupport::class,
                'arguments' => [],
                'tags'      => [
                    'mautic.config_integration',
                ],
            ],
        ],
    ],
    'parameters' => [],
];
