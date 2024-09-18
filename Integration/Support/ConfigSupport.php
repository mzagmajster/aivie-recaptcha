<?php

declare(strict_types=1);

namespace MauticPlugin\MauticRecaptchaBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;

class ConfigSupport extends RecaptchaIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;
}
