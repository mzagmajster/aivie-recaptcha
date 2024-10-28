<?php

namespace MauticPlugin\MauticRecaptchaBundle\Tests;

use Mautic\FormBundle\Entity\Field;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticRecaptchaBundle\EventListener\FormSubscriber;
use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;
use MauticPlugin\MauticRecaptchaBundle\Service\RecaptchaClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class IntegrationTest extends TestCase
{
    protected RecaptchaIntegration $integration;

    protected IntegrationsHelper $integrationsHelper;

    protected EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->integration = $this->getMockBuilder(RecaptchaIntegration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventDispatcher
            ->method('addListener')
            ->willReturn(true);

        $this->integrationsHelper = $this->getMockBuilder(IntegrationsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->integrationsHelper
            ->method('getIntegration')
            ->willReturn($this->integration);
    }

    public function testOnFormValidate()
    {
        /** @var LeadModel $leadModel */
        $leadModel = $this->getMockBuilder(LeadModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var MockObject|ValidationEvent $validationEvent */
        $validationEvent = $this->getMockBuilder(ValidationEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translator = $this->createMock(TranslatorInterface::class);

        $validationEvent
            ->method('getValue')
            ->willReturn('any-value-should-work');
        $validationEvent
            ->expects($this->never())
            ->method('failedValidation');
        $validationEvent
            ->method('getValue')
            ->willReturn('test');
        $validationEvent
            ->method('getField')
            ->willReturn(new Field());

        $formSubscriber = new FormSubscriber(
            $this->eventDispatcher,
            $this->integrationsHelper,
            new RecaptchaClient($this->integrationsHelper),
            $leadModel,
            $translator
        );
        $formSubscriber->onFormValidate($validationEvent);
    }
}
