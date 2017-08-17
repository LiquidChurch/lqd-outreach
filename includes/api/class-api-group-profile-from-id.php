<?php

/**
 * Liquid Outreach CCB API Group Profile from ID
 *
 * @since 0.3.5
 * @package Liquid_Outreach
 */
class Lo_Ccb_api_group_profile_from_id extends Lo_Ccb_api_main
{
    /**
     * Define the CCB API service we are using
     *
     * @var string  $api_name
     * @since 0.3.5
     */
    protected $api_name    = "group_profile_from_id";
    
    /**
     * Define the required CCB srv to execute request
     * on CCB API service group_profile_from_id
     *
     * @var string  $api_req_str
     * @since 0.3.5
     */
    protected $api_req_str = "srv=group_profile_from_id";
    
    /**
     * The URL used to access the CCB API
     *
     * @var string  $api_url
     * @since 0.3.5
     */
    protected $api_url = "";
    
    /**
     * @var $api_fields
     * @since 0.3.5
     */
    protected $api_fields;
    
    /**
     * Lo_Ccb_api_event_profiles constructor.
     *
     * @param $plugin
     * @since 0.3.5
     */
    public function __construct($plugin)
    {
        parent::__construct($plugin);
    }
    
    /**
     * Create CCB API map
     *
     * @param $data
     * @since 0.3.5
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
     * @since 0.3.5
     */
    public function mod_req_str() {
        $add_req_str = http_build_query($this->api_fields);
        $this->api_req_str .= '&' . $add_req_str;
    }
    
    /**
     * Create Fields Map
     *
     * @param   $api_data
     * @since 0.3.5
     */
    public function map_fields($api_data)
    {
        $post_fields = [
            'id' => !empty($_POST['group_id']) ? $_POST['group_id'] : (!empty($api_data['group_id']) ? $api_data['group_id'] : null)
        ];

        $this->api_fields = [
            'id' => $post_fields['id'],
            'include_image_link' => !empty($_POST['per_page']) ? $_POST['include_image_link'] : true
        ];

    }
    
    /**
     * Execute call against CCB API
     *
     * @since 0.3.5
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