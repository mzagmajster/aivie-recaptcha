<?php

/*
 * @copyright   2018 Konstantin Scheumann. All rights reserved
 * @author      Konstantin Scheumann
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecaptchaBundle\Tests;

use Mautic\FormBundle\Entity\Field;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;
use MauticPlugin\MauticRecaptchaBundle\Service\RecaptchaClient;

class RecaptchaClientTest extends TestCase
{
    /**
     * @var MockObject|IntegrationsHelper
     */
    private $integrationsHelper;

    /**
     * @var MockObject|RecaptchaIntegration
     */
    private $integration;

    /**
     * @var Field
     */
    private $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->integrationsHelper = $this->createMock(IntegrationsHelper::class);
        $this->integration       = $this->createMock(RecaptchaIntegration::class);
        $this->field = new Field();
    }

    public function testVerifyWhenPluginIsNotInstalled()
    {
        $test = $this->createRecaptchaClient()->verify('', $this->field);
        $this->assertFalse($test);
    }

    /**
     * @return RecaptchaClient
     */
    private function createRecaptchaClient()
    {
        return new RecaptchaClient(
            $this->integrationsHelper
        );
    }
}
