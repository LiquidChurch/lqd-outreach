<?php

/**
 * Liquid Outreach API Group Profiles
 *
 * @since 0.3.5
 * @package Liquid_Outreach
 */
class Lo_Ccb_api_group_profiles extends Lo_Ccb_api_main
{
    /**
     * @var string
     * @since 0.3.5
     */
    protected $api_name    = "group_profiles";
    
    /**
     * @var string
     * @since 0.3.5
     */
    protected $api_req_str = "srv=group_profiles";
    
    /**
     * @var string
     * @since 0.3.5
     */
    protected $api_url = "";
    
    /**
     * @var
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
     *
     * @since 0.3.5
     */
    public function api_map($data = [])
    {
        $this->map_fields();
        $this->mod_req_str();
        $this->call_ccb_api();
        $this->process_api_response();
    }
    
    /**
     *
     * @since 0.3.5
     */
    public function mod_req_str() {
        $add_req_str = http_build_query($this->api_fields);
        $this->api_req_str .= '&' . $add_req_str;
    }
    
    /**
     * @return WP_Error
     * @since 0.3.5
     */
    public function map_fields()
    {
        $post_fields = [
            'modified_since' => !empty($_POST['modified_since']) ? date('Y-m-d', strtotime($_POST['modified_since'])) : ''
        ];

        $this->api_fields = [
            'page' => !empty($_POST['page']) ? $_POST['page'] : 1,
            'per_page' => !empty($_POST['per_page']) ? $_POST['per_page'] : 100
        ];
        
        if(!empty($post_fields['modified_since'])) {
            $this->api_fields['modified_since'] = $post_fields['modified_since'];
        }
	    if(!empty($_POST['include_participants'])) {
		    $this->api_fields['include_participants'] = !empty($_POST['include_participants']) ? true : false;
	    }
	    if(!empty($_POST['include_image_link'])) {
		    $this->api_fields['include_image_link'] = !empty($_POST['include_image_link']) ? true : false;
	    }
    }
    
    /**
     *
     * @since 0.3.5
     */
    public function call_ccb_api()
    {
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