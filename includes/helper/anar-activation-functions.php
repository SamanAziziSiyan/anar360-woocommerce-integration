<?php
function awca_check_activation_state()
{
    $activation_key = get_option('_awca_activation_key');
    return ($activation_key && awca_is_valid_activation_key()) ? true : false;
}

function awca_is_valid_activation_key()
{
    $tokenValidation = awca_get_data_from_api('https://api.anar360.com/api/360/auth/validate');

    if ($tokenValidation !== null) {
        if (isset($tokenValidation->success) && $tokenValidation->success === true) {
            return true;
        }
    }
    return false;
}

function awca_save_activation_key()
{
    try {
        if (!isset($_POST['activation_code']) || !is_string($_POST['activation_code'])) {
            return false;
        }
        $activation_code = sanitize_text_field(wp_unslash($_POST['activation_code']));
        if ($activation_code === '' || strlen($activation_code) > 4096) {
            return false;
        }
        $activation = update_option('_awca_activation_key', $activation_code, false);
        return $activation || get_option('_awca_activation_key') === $activation_code;
    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());

        if (function_exists('Sentry\\captureException')) {
            Sentry\captureException($e);
        }

        return false;
    }
}

function awca_get_activation_key()
{
    return (get_option('_awca_activation_key')) ? get_option('_awca_activation_key') : '';
}
