<?php

require_once __DIR__ . '/test-framework-in-a-tweet.php';
require_once __DIR__ . '/wp-function.php';
require_once __DIR__ . '/../includes/class-vladimir-plugin.php';

define('VLADIMIR_PLUGIN_API_URL', 'https://jsonplaceholder.typicode.com');
define('MINUTE_IN_SECONDS', 60);

$vladimir_plugin = new Vladimir_Plugin;

it('should add action for the "template_redirect" hook', function() use ($vladimir_plugin) {
    $vladimir_plugin->run();

    return has_action('template_redirect', [$vladimir_plugin, 'set_status']);
});

it('should add action for the "template_include" hook', function() use ($vladimir_plugin) {
    $vladimir_plugin->run();

    return has_action('template_include', [$vladimir_plugin, 'set_template']);
});

it('should add action for the "parse_query" hook', function() use ($vladimir_plugin) {
    $vladimir_plugin->run();

    return has_action('parse_query', [$vladimir_plugin, 'set_query']);
});

it('should add filter for the "query_vars" hook', function() use ($vladimir_plugin) {
    $vladimir_plugin->run();

    return has_filter('query_vars', [$vladimir_plugin, 'set_query_vars']);
});

it('should send remote request on method "get_user_list" if cache not exists', function() use ($vladimir_plugin) {
    $GLOBALS['wp_remote_response_code'] = 200;
    $GLOBALS['wp_remote_body'] = json_encode(['foo' => 'bar']);

    $vladimir_plugin->get_user_list();

    return isset($GLOBALS['function_called']['wp_remote_get']);
});

it('should not send remote request on method "get_user_list" if cache exists', function() use ($vladimir_plugin) {
    set_transient('vladimir_plugin_user_list', ['foo' => 'bar']);
    unset($GLOBALS['function_called']['wp_remote_get']);

    $vladimir_plugin->get_user_list();

    return !isset($GLOBALS['function_called']['wp_remote_get']);
});

it('should return an empty array on method "get_user_list" if remote request failed', function() use ($vladimir_plugin) {
    unset($GLOBALS['transient']['vladimir_plugin_user_list']);
    $GLOBALS['wp_remote_response_code'] = 500;

    $result = $vladimir_plugin->get_user_list();

    return is_array($result) && empty($result);
});

it('should return an empty array on method "get_user_list" if remote response contains not valid JSON in the body', function() use ($vladimir_plugin) {
    unset($GLOBALS['transient']['vladimir_plugin_user_list']);

    $GLOBALS['wp_remote_body'] = false;
    $GLOBALS['wp_remote_response_code'] = 200;

    $result = $vladimir_plugin->get_user_list();

    return is_array($result) && empty($result);
});

it('should write cache on method "get_user_list" on successfull request', function() use ($vladimir_plugin) {
    unset($GLOBALS['transient']['vladimir_plugin_user_list']);

    $responseBody = ['foo' => 'bar'];
    $GLOBALS['wp_remote_body'] = json_encode($responseBody);
    $GLOBALS['wp_remote_response_code'] = 200;

    $vladimir_plugin->get_user_list();

    return get_transient('vladimir_plugin_user_list') === $responseBody;
});

it('should return retrieved array on method "get_user_list" on success', function() use ($vladimir_plugin) {
    unset($GLOBALS['transient']['vladimir_plugin_user_list']);

    $responseBody = ['foo' => 'bar'];
    $GLOBALS['wp_remote_body'] = json_encode($responseBody);
    $GLOBALS['wp_remote_response_code'] = 200;

    return $vladimir_plugin->get_user_list() === $responseBody;
});

it('should not send remote request on method "get_user_details" if cache exists', function() use ($vladimir_plugin) {
    $user_id = 1;

    set_transient('vladimir_plugin_user_details_' . $user_id, ['foo' => 'bar']);
    unset($GLOBALS['function_called']['wp_remote_get']);

    $vladimir_plugin->get_user_details($user_id);

    return !isset($GLOBALS['function_called']['wp_remote_get']);
});

it('should send remote request on method "get_user_details" if cache not exists', function() use ($vladimir_plugin) {
    $user_id = 1;

    unset($GLOBALS['transient']['vladimir_plugin_user_details_' . $user_id]);

    $GLOBALS['wp_remote_response_code'] = 200;
    $GLOBALS['wp_remote_body'] = json_encode(['foo' => 'bar']);

    $vladimir_plugin->get_user_details($user_id);

    return isset($GLOBALS['function_called']['wp_remote_get']);
});

it('should return an empty array on method "get_user_details" if remote request failed', function() use ($vladimir_plugin) {
    $user_id = 1;

    unset($GLOBALS['transient']['vladimir_plugin_user_details_' . $user_id]);
    $GLOBALS['wp_remote_response_code'] = 500;

    $result = $vladimir_plugin->get_user_details($user_id);

    return is_array($result) && empty($result);
});

it('should return an empty array on method "get_user_details" if remote response contains not valid JSON in the body', function() use ($vladimir_plugin) {
    $user_id = 1;

    unset($GLOBALS['transient']['vladimir_plugin_user_details_' . $user_id]);

    $GLOBALS['wp_remote_body'] = false;
    $GLOBALS['wp_remote_response_code'] = 200;

    $result = $vladimir_plugin->get_user_details($user_id);

    return is_array($result) && empty($result);
});

it('should write cache on method "get_user_details" on successfull request', function() use ($vladimir_plugin) {
    $user_id = 1;

    unset($GLOBALS['transient']['vladimir_plugin_user_details_' . $user_id]);

    $responseBody = ['foo' => 'bar'];
    $GLOBALS['wp_remote_body'] = json_encode($responseBody);
    $GLOBALS['wp_remote_response_code'] = 200;

    $vladimir_plugin->get_user_details($user_id);

    return get_transient('vladimir_plugin_user_details_' . $user_id) === $responseBody;
});

it('should return retrieved array on method "get_user_list" on success', function() use ($vladimir_plugin) {
    $user_id = 1;

    unset($GLOBALS['transient']['vladimir_plugin_user_details_' . $user_id]);

    $responseBody = ['foo' => 'bar'];
    $GLOBALS['wp_remote_body'] = json_encode($responseBody);
    $GLOBALS['wp_remote_response_code'] = 200;

    return $vladimir_plugin->get_user_details($user_id) === $responseBody;
});

it('method "set_query_vars" should add "user_id" variable to the vars array', function() use ($vladimir_plugin) {
    $vars = $vladimir_plugin->set_query_vars([]);

    return in_array('user_id', $vars);
});