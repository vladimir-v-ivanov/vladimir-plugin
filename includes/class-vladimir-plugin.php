<?php

/**
 * Test plugin
 */
class Vladimir_Plugin
{
    private $query;
    private $config;

    /**
     * Get the plugin's config
     *
     * @return array
     */
    public function get_config()
    {
        if(is_null($this->config)) {
            $this->config = require_once plugin_dir_path(VLADIMIR_PLUGIN_ROOT) . '/config.php';
        }

        return $this->config;
    }

    /**
     * Get current WP_Query object
     *
     * @return WP_Query wordpress query object
     */
    public function get_query()
    {
        return $this->query;
    }

    /**
     * Set current WP_Query object for future use in the plugin
     *
     * @param WP_Query wordpress query object
     */
    public function set_query($query)
    {
        $this->query = $query;
    }

    /**
     * Run the plugin routine
     */
    public function run()
    {
        add_action('template_include', [$this, 'set_template']);
        add_action('template_redirect', [$this, 'set_status']);
        add_action('parse_query', [$this, 'set_query']);
        add_filter('query_vars', [$this, 'set_query_vars']);
        add_filter('the_title', [$this, 'set_title']);
        add_filter('document_title_parts', [$this, 'set_document_title']);
    }

    /**
     * Set WordPress document title parts
     *
     * This function actually sets custom browser title for the user list page
     *
     * @param array title parts
     *
     * @return array modified title parts
     */
    function set_document_title($title)
    {
        if($this->is_user_list_request()) {
            $title = [
                'title' => get_bloginfo('name'),
                'tagline' => $this->get_config()['page_title']
            ];
        }

        return $title;
    }

    /**
     * Set title for page contents
     *
     * This function actually sets custom page title for the user list page
     *
     * @param string current title
     *
     * @return string modified title
     */
    public function set_title($title)
    {
        if($this->is_user_list_request()) {
            $title = $this->get_config()['page_title'];
        }

        return $title;
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

            wp_enqueue_script('vladimir_plugin_script', plugin_dir_url(VLADIMIR_PLUGIN_ROOT) . '/assets/js/script.js');
            wp_enqueue_style('vladimir_plugin_style', plugin_dir_url(VLADIMIR_PLUGIN_ROOT) . '/assets/css/style.css');
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
     * Get list of the users
     *
     * @return array Returns array of the existing users on success or empty array on fail
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
     * Get user details data in AJAX format
     *
     * @param int User ID
     *
     * @return array Returns array of user data on success or empty array on fail
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
     * Check if requested URL is for the user list page
     *
     * @return true
     */
    private function is_user_list_request()
    {
        return urldecode($this->get_query()->get('name')) == VALDIMIR_PLUGIN_LIST_URL;
    }

    /**
     * Check if requested URL is for the user details data
     *
     * @return true
     */
    private function is_user_details_request()
    {
        return urldecode($this->get_query()->get('name')) == VLADIMIR_PLUGIN_DETAILS_URL;
    }
}