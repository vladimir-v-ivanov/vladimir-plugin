<?php

function add_action($action, $callable)
{
    $GLOBALS['function_called']['add_action'] = true;
    $GLOBALS['action'][$action] = $callable;
}

function has_action($action, $callable)
{
    $GLOBALS['function_called']['has_action'] = true;

    return isset($GLOBALS['action'][$action]) && $GLOBALS['action'][$action] === $callable;
}

function add_filter($action, $callable)
{
    $GLOBALS['function_called']['add_filter'] = true;
    $GLOBALS['filter'][$action] = $callable;
}

function has_filter($action, $callable)
{
    $GLOBALS['function_called']['has_filter'] = true;

    return isset($GLOBALS['filter'][$action]) && $GLOBALS['filter'][$action] === $callable;
}

function get_transient($key)
{
    $GLOBALS['function_called']['get_transient'] = true;

    return isset($GLOBALS['transient'][$key]) ? $GLOBALS['transient'][$key] : false;
}

function set_transient($key, $value)
{
    $GLOBALS['function_called']['set_transient'] = true;
    $GLOBALS['transient'][$key] = $value;
}

function wp_remote_get()
{
    $GLOBALS['function_called']['wp_remote_get'] = true;

    return [];
}

function wp_remote_retrieve_response_code($response)
{
    return $GLOBALS['wp_remote_response_code'];
}

function wp_remote_retrieve_body()
{
    return $GLOBALS['wp_remote_body'];
}

function wp_send_json( $array ) {
    echo json_encode( $array );
}