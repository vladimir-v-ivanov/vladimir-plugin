<?php

/**
 *
 */
class Vladimir_Plugin
{
    private $query;

    /**
     * Get current WP_Query object
     *
     * @return WP_Query WP_Query object
     */
    public function get_query()
    {
        return $this->query;
    }

    /**
     * Set current WP_Query object for future use in the plugin
     *
     * @param WP_Query $query WP_Query object
     */
    public function set_query($query)
    {
        $this->query = $query;
    }

    /**
     *
     */
    public function run()
    {
        add_action('template_include', [$this, 'set_template']);
        add_action('template_redirect', [$this, 'set_status']);
        add_action('parse_query', [$this, 'set_query']);
        add_filter('query_vars', [$this, 'set_query_vars']);
    }

    /**
     *
     */
    public function set_query_vars($vars)
    {
        $vars[] = 'user_id';

        return $vars;
    }

    /**
     *
     */
    public function set_status()
    {
        if($this->is_user_list_request() || $this->is_user_details_request()) {
            status_header(200);
            nocache_headers();
        }
    }

    /**
     *
     */
    public function set_template($template)
    {
        if($this->is_user_list_request()) {
            $template = plugin_dir_path(VLADIMIR_PLUGIN_ROOT) . 'views/' . VLADIMIR_PLUGIN_LIST_TEMPLATE;
        }

        if($this->is_user_details_request()) {
            $user_id = (int) $this->get_query()->get('user_id');
            $details = [];

            if($user_id) {
                $details = $this->get_user_details($user_id);
            }

            echo json_encode([
                'result' => true,
                'details' => $details
            ]);

            $template = false;
        }

        return $template;
    }

    /**
     *
     */
    public function get_user_list()
    {
        $user_list = get_transient('vladimir_plugin_user_list');

        if($user_list == false) {
            $response = wp_remote_get(VLADIMIR_PLUGIN_API_URL . '/users');

            if(wp_remote_retrieve_response_code($response) != 200) {
                return [];
            }

            $user_list = json_decode(wp_remote_retrieve_body($response), true);

            if($user_list == false) {
                return [];
            }

            set_transient('vladimir_plugin_user_list', $user_list, MINUTE_IN_SECONDS * 5);
        }

        return $user_list;
    }

    /**
     *
     */
    public function get_user_details($user_id)
    {
        $transientKey = 'vladimir_plugin_user_details_' . $user_id;
        $details = get_transient($transientKey);

        if($details == false) {
            $response = wp_remote_get(VLADIMIR_PLUGIN_API_URL . '/users/' . $user_id);

            if(wp_remote_retrieve_response_code($response) != 200) {
                return [];
            }

            $details = json_decode(wp_remote_retrieve_body($response), true);

            if($details == false) {
                return [];
            }

            set_transient($transientKey, $details, MINUTE_IN_SECONDS * 5);
        }

        return $details;
    }

    /**
     *
     */
    private function is_user_list_request()
    {
        return urldecode($this->get_query()->get('name')) == VALDIMIR_PLUGIN_LIST_URL;
    }

    /**
     *
     */
    private function is_user_details_request()
    {
        return urldecode($this->get_query()->get('name')) == VLADIMIR_PLUGIN_DETAILS_URL;
    }
}