<?php

/**
 * Liquid Outreach API Attendance Profile
 *
 * @since 0.1.4
 * @package Liquid_Outreach
 */
class Lo_Ccb_api_attendance_profile extends Lo_Ccb_api_main
{
    /**
     * @var string
     * @since 0.1.4
     */
    protected $api_name    = "attendance_profile";
    
    /**
     * @var string
     * @since 0.1.4
     */
    protected $api_req_str = "srv=attendance_profile";
    
    /**
     * @var string
     * @since 0.1.4
     */
    protected $api_url = "";
    
    /**
     * @var
     * @since 0.1.4
     */
    protected $api_fields;
    
    /**
     * Lo_Ccb_api_event_profiles constructor.
     *
     * @param $plugin
     * @since 0.1.4
     */
    public function __construct($plugin)
    {
        parent::__construct($plugin);
    }
    
    /**
     *
     * @since 0.1.4
     */
    public function api_map($data = [])
    {
        $this->map_fields($data);
        $this->mod_req_str();
        $this->call_ccb_api();
        $this->process_api_response();
    }
    
    /**
     *
     * @since 0.1.4
     */
    public function mod_req_str() {
        $add_req_str = http_build_query($this->api_fields);
        $this->api_req_str .= '&' . $add_req_str;
    }
    
    /**
     * @return WP_Error
     * @since 0.1.4
     */
    public function map_fields($data)
    {
        $post_fields = [
            'id' => !empty($data['event_id']) ? $data['event_id'] : '',
            'occurrence' => !empty($data['occurrence']) ? $data['occurrence'] : date('Y-m-d', time())
        ];

        if(!empty($post_fields['id'])) {
            $this->api_fields['id'] = $post_fields['id'];
        }
        if(!empty($post_fields['occurrence'])) {
            $this->api_fields['occurrence'] = $post_fields['occurrence'];
        }
    }
    
    /**
     *
     * @since 0.1.4
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
//        lo_debug('add', array($this->api_name . ' -> raw_api_response', json_encode($this->api_response), 0, 'API'));
    }

}