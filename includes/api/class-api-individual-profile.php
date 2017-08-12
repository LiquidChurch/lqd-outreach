<?php

/**
 * Liquid Outreach API Individual Profile
 *
 * @since 0.1.3
 * @package Liquid_Outreach
 */
class Lo_Ccb_api_individual_profile extends Lo_Ccb_api_main
{
    /**
     * @var string
     * @since 0.1.3
     */
    protected $api_name    = "individual_profile_from_id";
    
    /**
     * @var string
     * @since 0.1.3
     */
    protected $api_req_str = "srv=individual_profile_from_id";
    
    /**
     * @var string
     * @since 0.1.3
     */
    protected $api_url = "";
    
    /**
     * @var
     * @since 0.1.3
     */
    protected $api_fields;
    
    /**
     * Lo_Ccb_api_event_profiles constructor.
     *
     * @param $plugin
     * @since 0.1.3
     */
    public function __construct($plugin)
    {
        parent::__construct($plugin);
    }
    
    /**
     *
     * @since 0.1.3
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
     * @since 0.1.3
     */
    public function mod_req_str() {
        $add_req_str = http_build_query($this->api_fields);
        $this->api_req_str .= '&' . $add_req_str;
    }
    
    /**
     * @return WP_Error
     * @since 0.1.3
     */
    public function map_fields($data)
    {
        $post_fields = [
            'individual_id' => !empty($data['organizer_id']) ? $data['organizer_id'] : ''
        ];

        if(!empty($post_fields['individual_id'])) {
            $this->api_fields['individual_id'] = $post_fields['individual_id'];
        }
    }
    
    /**
     *
     * @since 0.1.3
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