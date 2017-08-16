<?php

/**
 * Liquid Outreach API Event Profile
 *
 * @since 0.24.0
 * @package Liquid_Outreach
 */
class Lo_Ccb_api_event_profile extends Lo_Ccb_api_main
{
    /**
     * Define the CCB API service we are using
     *
     * @var string $api_name    CCB Event Profile Service
     * @since 0.24.0
     */
    protected $api_name    = "event_profile";
    
    /**
     * Define the required CCB srv to execute request
     * on CCB API service event_profile
     *
     * @var string $api_req_str
     * @since 0.24.0
     */
    protected $api_req_str = "srv=event_profile";
    
    /**
     * The URL used to access the CCB API
     *
     * @var string  $api_url
     * @since 0.24.0
     */
    protected $api_url = "";
    
    /**
     * @var
     * @since 0.24.0
     */
    protected $api_fields;
    
    /**
     * Lo_Ccb_api_event_profile constructor.
     *
     * @param $plugin
     * @since 0.24.0
     */
    public function __construct($plugin)
    {
        parent::__construct($plugin);
    }
    
    /**
     * Create CCB API map
     * @param $data
     * @since 0.24.0
     */
    public function api_map($data = [])
    {
        $this->map_fields($data);
        $this->mod_req_str();
        $this->call_ccb_api();
        $this->process_api_response();
    }
    
    /**
     * Modify the CCB API request call based on required fields
     *
     * @since 0.24.0
     */
    public function mod_req_str() {
        $add_req_str = http_build_query($this->api_fields);
        $this->api_req_str .= '&' . $add_req_str;
    }
    
    /**
     * Handle Errors
     * @return WP_Error
     * @since 0.24.0
     */
    public function map_fields($data)
    {
        $this->api_fields = [
            'id' => $data['event_id'],
            'include_image_link' => !empty($_POST['per_page']) ? $_POST['include_image_link'] : true
        ];
    }
    
    /**
     * Execute call against CCB API
     *
     * @since 0.24.0
     */
    public function call_ccb_api()
    {
        parent::call_ccb_api();
    
        $this->api_url = $this->api_base . '?' . $this->api_req_str;
        $this->api_args = array_merge(
            $this->api_args,
            array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'body' => array(),
                'cookies' => array()
            ));

        $this->api_response = wp_remote_post($this->api_url, $this->api_args);
//        lo_debug('add', array($this->api_name . ' -> raw_api_response', $this->api_response, 0, 'lo-api-calls'));
    }

}