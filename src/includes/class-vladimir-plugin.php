<?php

/**
 * Test plugin
 */
class Vladimir_Plugin
{
    private $query;
    private $config;
    private $userDetailsActionName = 'vladimir_plugin_user_details';
    private $pageTitle = 'User List';

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
    public function set_query( $query )
    {
        $this->query = $query;
    }

    /**
     * Run the plugin routine
     */
    public function run()
    {
        add_action( 'template_include', [ $this, 'set_template' ] );
        add_action( 'template_redirect', [ $this, 'set_status' ] );
        add_action( 'parse_query', [ $this, 'set_query' ] );
        add_filter( 'the_title', [ $this, 'set_title' ] );
        add_filter( 'document_title_parts', [ $this, 'set_document_title' ] );
        add_filter( 'body_class', [ $this, 'set_body_classes' ] );
        add_action( 'wp_ajax_nopriv_' . $this->userDetailsActionName, [ $this, 'get_user_details' ] );
        add_action( 'wp_ajax_' . $this->userDetailsActionName, [ $this, 'get_user_details' ] );
    }

    public function get_user_details()
    {
        $user_id = (int) $_GET['user_id'];
        $details = [];

        try {
            if( !$user_id ) {
                throw new Exception('Invalid request');
            }

            $transientKey = 'vladimir_plugin_user_details_' . $user_id;
            $details = get_transient( $transientKey );

            if( !$details ) {
                $response = wp_remote_get( VLADIMIR_PLUGIN_API_URL . '/users/' . $user_id );

                if( wp_remote_retrieve_response_code($response) != 200 ) {
                    throw new Exception('Unexpected error');
                }

                $details = json_decode( wp_remote_retrieve_body( $response ), true );

                if( $details == false ) {
                    throw new Exception('Unexpected error');
                }

                set_transient( $transientKey, $details, MINUTE_IN_SECONDS * 5 );
            }
        } catch(Exception $e) {
            wp_send_json([
                'result' => false,
                'error' => $e->getMessage()
            ]);
        }

        wp_send_json([
            'result' => true,
            'details' => $this->esc_html($details)
        ]);
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
    function set_document_title( $title )
    {
        if( $this->is_user_list_request() ) {
            $title = [
                'title' => get_bloginfo( 'name' ),
                'tagline' => __( $this->pageTitle, 'vladimir-plugin' )
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
    public function set_title( $title )
    {
        if( $this->is_user_list_request() ) {
            $title = __( $this->pageTitle, 'vladimir-plugin' );
        }

        return $title;
    }

    /**
     * Set status 200
     */
    public function set_status()
    {
        if( $this->is_user_list_request() ) {
            status_header( 200 );
            nocache_headers();
        }
    }

    /**
     * Delete error404 class from body
     */
    public function set_body_classes($classes)
    {
        if( $this->is_user_list_request() ) {
            if ( ( $key = array_search( 'error404', $classes ) ) !== false ) {
                unset( $messages[ $key ] );
            }
        }
    }

    /**
     *
     */
    public function set_template( $template )
    {
        if( $this->is_user_list_request() ) {
            $template = plugin_dir_path( VLADIMIR_PLUGIN_ROOT ) . 'views/' . VLADIMIR_PLUGIN_LIST_TEMPLATE;

            wp_enqueue_script(
                'vladimir_plugin_script',
                plugin_dir_url( VLADIMIR_PLUGIN_ROOT ) . '/assets/js/script.js'
            );
            wp_localize_script(
                'vladimir_plugin_script',
                'vladimirPluginData',
                [
                    'url' => admin_url( 'admin-ajax.php' ),
                    'action' => $this->userDetailsActionName,
                    'errorText' => __('Unexpected error occured', 'vladimir-plugin')
                ]
            );
            wp_enqueue_style(
                'vladimir_plugin_style',
                plugin_dir_url( VLADIMIR_PLUGIN_ROOT ) . '/assets/css/style.css'
            );
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
        $user_list = get_transient( 'vladimir_plugin_user_list' );

        if( $user_list == false ) {
            $response = wp_remote_get( VLADIMIR_PLUGIN_API_URL . '/users' );

            if( wp_remote_retrieve_response_code( $response ) != 200 ) {
                return [];
            }

            $user_list = json_decode( wp_remote_retrieve_body( $response ), true );

            if( $user_list == false ) {
                return [];
            }

            set_transient( 'vladimir_plugin_user_list', $user_list, MINUTE_IN_SECONDS * 5 );
        }

        return $user_list;
    }

    /**
     * Check if requested URL is for the user list page
     *
     * @return true
     */
    private function is_user_list_request()
    {
        return urldecode( $this->get_query()->get('name') ) == VALDIMIR_PLUGIN_LIST_URL
            || urldecode( $this->get_query()->query['p'] ) == VALDIMIR_PLUGIN_LIST_URL;
    }

    /**
     * Escape HTML in array recursive
     *
     * @param mixed $value Value to be escaped
     *
     * @return mixed Escaped value
     */
    private function esc_html($value) {
        if(is_iterable($value)) {
            foreach($value as $k => $v) {
                $value[$k] = $this->esc_html($v);
            }
        } else {
            $value = htmlspecialchars($value);
        }

        return $value;
    }
}