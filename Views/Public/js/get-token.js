/**
 * Communicate with Google reCAPTCHA Enterprise to get a token and add the token 
 * to the form field to be validated (assessed) in the backend after submitting.
 * Has to happen within 2 minutes after the token is generated.
 *
 * @param {Event} event Form submit event
 * @param {string} siteKey reCAPTCHA site key
 * @param {string} tagAction reCAPTCHA tag action
 * @link https://cloud.google.com/recaptcha/docs/create-assessment-website
 */
function getTokenAndValidateRecaptcha(event, siteKey, tagAction) {
    event.preventDefault();
    grecaptcha.enterprise.ready(async () => {
        const token = await grecaptcha.enterprise.execute(siteKey, { action: tagAction });
        const form = event.target;
        inputElement = form.querySelector('input.mautic-recaptcha-token');

        if (!inputElement) {
            throw new Error("No input element found with class 'mautic-recaptcha-token'");
        }
        inputElement.value = token;
        console.debug('Recaptcha: Token received and added to form field. Sending form now.');
        form.submit();
    });
}

/**
 * 
 * @param {string} formId Mautic form name (alias)
 * @param {string} siteKey reCAPTCHA site key
 * @param {string} tagAction reCAPTCHA tag action
 */
function validateFormByRecaptcha(formId, siteKey, tagAction) {

    const form = document.getElementById(formId);
    if (!form) {
        throw new Error(`Form with id '${formId}' not found`);
    }

    form.addEventListener('submit', function (event) {
        // add the getting a token to the end of the validation queue
        setTimeout(function () {
            if (!checkIfFormIsValid(form)) {
                console.debug('Recaptcha: Aborting, form not valid');
                return false;
            }
            getTokenAndValidateRecaptcha(event, siteKey, tagAction);
        }, 0);
    });
};

/**
 * Workaround as we don't use HTML 5 validation.
 * We check if any child has the 'mauticform-errormsg' class and is visible.
 * 
 * @param {HTMLFormElement} form 
 * @returns boolean
 */
function checkIfFormIsValid(form) {

    let hasErrorMsg = false;

    // Go through all child elements
    form.querySelectorAll('*').forEach(function (child) {
        if (child.classList.contains('mauticform-errormsg')) {
            if (child.style.display !== 'none') {
                hasErrorMsg = true;
            }
        }
    });

    return !hasErrorMsg;
}