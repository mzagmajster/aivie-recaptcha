<?php

namespace MauticPlugin\MauticRecaptchaBundle\EventListener;

use Mautic\CoreBundle\Translation\Translator;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticRecaptchaBundle\Form\Type\RecaptchaType;
use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;
use MauticPlugin\MauticRecaptchaBundle\RecaptchaEvents;
use MauticPlugin\MauticRecaptchaBundle\Service\RecaptchaClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{
    public const MODEL_NAME_KEY_LEAD = 'lead.lead';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RecaptchaClient
     */
    protected $recaptchaClient;
    protected $siteKey;

    /**
     * @var bool
     */
    private $recaptchaIsConfigured = false;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        IntegrationsHelper $integrationsHelper,
        RecaptchaClient $recaptchaClient,
        LeadModel $leadModel,
        Translator $translator
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->recaptchaClient = $recaptchaClient;
        $integration           = $integrationsHelper->getIntegration(RecaptchaIntegration::NAME);

        if ($integration && $integration->getIntegrationConfiguration()->getIsPublished()) {
            $this->siteKey   = getenv('GC_RECAPTCHA_SITE_KEY');
            // $this->siteKey = 'test123';

            if ($this->siteKey) {
                $this->recaptchaIsConfigured = true;
            } else {
                error_log('Recaptcha is not configured properly - check your ENV variables');
            }
        }
        $this->leadModel  = $leadModel;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD         => ['onFormBuild', 0],
            RecaptchaEvents::ON_FORM_VALIDATE => ['onFormValidate', 0],
        ];
    }

    /**
     * @throws \Mautic\CoreBundle\Exception\BadConfigurationException
     */
    public function onFormBuild(FormBuilderEvent $event)
    {
        if (!$this->recaptchaIsConfigured) {
            return;
        }

        $event->addFormField('plugin.recaptcha', [
            'label'          => 'mautic.plugin.actions.recaptcha',
            'formType'       => RecaptchaType::class,
            'template'       => '@MauticRecaptcha/Integration/recaptcha.html.twig',
            'builderOptions' => [
                'addLeadFieldList' => false,
                'addIsRequired'    => false,
                'addDefaultValue'  => false,
                'addSaveResult'    => true,
            ],
            'siteKey'     => $this->siteKey,
            'tagAction'   => $this->recaptchaClient->getTagActionName(),
            'jsTokenUrl'  => 'https://tf3captcha.ddev.site/plugins/MauticRecaptchaBundle/Assets/js/get-token.js',
        ]);

        $event->addValidator('plugin.recaptcha.validator', [
            'eventName' => RecaptchaEvents::ON_FORM_VALIDATE,
            'fieldType' => 'plugin.recaptcha',
        ]);
    }

    public function onFormValidate(ValidationEvent $event)
    {
        if (!$this->recaptchaIsConfigured) {
            return;
        }

        if ($this->recaptchaClient->verify($event->getValue(), $event->getField())) {
            return;
        }

        $event->failedValidation(null === $this->translator ? 'reCAPTCHA was not successful.' : $this->translator->trans('mautic.integration.recaptcha.failure_message'));

        $this->eventDispatcher->addListener(LeadEvents::LEAD_POST_SAVE, function (LeadEvent $event) {
            if ($event->isNew()) {
                $this->leadModel->deleteEntity($event->getLead());
            }
        }, -255);
    }
}
