<?php

add_action('wp_ajax_wealf_ajax', 'wealf_ajax', 10);
add_action('wp_ajax_nopriv_wealf_ajax', 'wealf_ajax', 10);

function wealf_ajax()
{
    // Check ajax referrer
    check_ajax_referer('wealf_ajax_nonce', 'security');

    // Allow actions
    $allow_actions = [
        'get_select_options',
    ];

    // Get action name from ajax request
    $action_name = sanitize_text_field(wealf_array_element('action_name', $_POST));

    // Get data from ajax request
    $data = wealf_array_element('data', $_POST);

    // If action name is empty or not existing in allow actions list return error.
    if (!$action_name || !in_array($action_name, $allow_actions)) {
        wp_send_json_error([
            'message' => esc_html__('Action not allowed.', 'janus'),
        ]);
    }

    // Call ajax function by action name.
    $action_name = "wealf_ajax_$action_name";
    $action_name($data);

    die();
}

function wealf_ajax_get_select_options($payload = [])
{
    $callback = wealf_array_element('callback', $payload);

    if (!$callback) {
        wp_send_json_error([
            'message' => esc_html__('Callback not found.', 'janus'),
        ]);
    }

    if (!function_exists($callback)) {
        wp_send_json_error([
            'message' => esc_html__('Callback not found.', 'janus'),
        ]);
    }

    $options = $callback();

    wp_send_json_success([
        'options' => $options,
    ]);
}
