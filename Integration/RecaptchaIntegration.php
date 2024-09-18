<?php

/*
 * @copyright   2018 Konstantin Scheumann. All rights reserved
 * @author      Konstantin Scheumann
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecaptchaBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

/**
 * Class RecaptchaIntegration.
 */
class RecaptchaIntegration  extends BasicIntegration implements BasicInterface
{
    const INTEGRATION_NAME = 'Recaptcha';

    public function getName(): string
    {
        return self::INTEGRATION_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/MauticRecaptchaBundle/Assets/img/recaptcha.png';
    }

    public function getDisplayName(): string
    {
        return 'reCAPTCHA';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [];
    }
}
