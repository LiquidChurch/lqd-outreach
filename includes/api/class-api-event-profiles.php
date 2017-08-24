<?php

/**
 * Liquid Outreach API Event Profiles
 *
 * @since   0.0.5
 * @package Liquid_Outreach
 */
class Lo_Ccb_api_event_profiles extends Lo_Ccb_api_main
{
    /**
     * Define the CCB API service we are using
     *
     * @var string
     * @since 0.0.5
     */
    protected $api_name = "event_profiles";

    /**
     * Define the required CCB srv to execute request
     * on CCB API service event_profiles
     *
     * @var string
     * @since 0.0.5
     */
    protected $api_req_str = "srv=event_profiles";

    /**
     * The URL used to access the CCB API
     *
     * @var string
     * @since 0.0.5
     */
    protected $api_url = "";

    /**
     * @var
     * @since 0.0.5
     */
    protected $api_fields;

    /**
     * Lo_Ccb_api_event_profiles constructor.
     *
     * @param $plugin
     *
     * @since 0.0.5
     */
    public function __construct($plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * Create CCB API map
     *
     * @param $data
     *
     * @since 0.0.5
     */
    public function api_map($data = [])
    {
        $this->map_fields();
        $this->mod_req_str();
        $this->call_ccb_api();
        $this->process_api_response();
    }

    /**
     * Modify the CCB API request call based on required fields
     *
     * @since 0.0.5
     */
    public function mod_req_str()
    {
        $add_req_str       = http_build_query($this->api_fields);
        $this->api_req_str .= '&' . $add_req_str;
    }

    /**
     * Handle Errors
     *
     * @return WP_Error
     * @since 0.0.5
     */
    public function map_fields()
    {
        $post_fields = [
            'modified_since' => ! empty($_POST['modified_since']) ? date('Y-m-d', strtotime($_POST['modified_since'])) : ''
        ];

        $this->api_fields = [
            'page'               => ! empty($_POST['page']) ? $_POST['page'] : 1,
            'per_page'           => ! empty($_POST['per_page']) ? $_POST['per_page'] : 25,
            'include_image_link' => ! empty($_POST['per_page']) ? $_POST['include_image_link'] : TRUE
        ];

        if ( ! empty($post_fields['modified_since']))
        {
            $this->api_fields['modified_since'] = $post_fields['modified_since'];
        }
    }

    /**
     * Execute call against CCB API
     *
     * @since 0.0.5
     */
    public function call_ccb_api()
    {
        parent::call_ccb_api();

        $this->api_url  = $this->api_base . '?' . $this->api_req_str;
        $this->api_args = array_merge(
            $this->api_args,
            array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => TRUE,
                'body'        => array(),
                'cookies'     => array()
            ));

        $this->api_response = wp_remote_post($this->api_url, $this->api_args);
//        lo_debug('add', array($this->api_name . ' -> raw_api_response', $this->api_response, 0, 'lo-api-calls'));
    }

}