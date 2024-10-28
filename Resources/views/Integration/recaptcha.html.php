<?php
$defaultInputClass = 'input, mautic-recaptcha-token';
$containerType     = 'div-wrapper';
include __DIR__.'/../../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$locale      = substr($app->getRequest()->getLocale(), 0, 2);
$js          = $view['assets']->getUrl('plugins/MauticRecaptchaBundle/Views/Public/js/get-token.js', null, null, true);
$siteKey     = $field['customParameters']['siteKey'];
$tagAction   = $field['customParameters']['tagAction'];
$action      = $app->getRequest()->get('objectAction');
$settings    = $field['properties'];
$formName    = str_replace('_', '', $formName);

$formButtons = (!empty($inForm)) ? $view->render(
    '@MauticForm:Builder:actions.html.php',
    [
        'deleted'        => false,
        'id'             => $id,
        'formId'         => $formId,
        'formName'       => $formName,
        'disallowDelete' => false,
    ]
) : '';

$label = (!$field['showLabel'])
    ? ''
    : <<<HTML
<label $labelAttr>{$view->escape($field['label'])}</label>
HTML;

$html = <<<HTML
    <script src="https://www.google.com/recaptcha/enterprise.js?render={$siteKey}&hl={$locale}&badge=bottomright" async></script>
    <script src="{$js}"></script>
    <script type="text/javascript">
        window.addEventListener('load', function() {
            validateFormByRecaptcha('mauticform_{$formName}', '{$siteKey}', '{$tagAction}');
        });
    </script>
	<div $containerAttr>
        {$label}
HTML;

$html .= <<<HTML
        <input $inputAttr type="hidden">
        <span class="mauticform-errormsg" style="display: none;"></span>
    </div>
HTML;

echo $html;
?>

