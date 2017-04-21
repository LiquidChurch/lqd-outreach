<?php

if (class_exists('WP_Logging')) {

    class Lo_Logging extends WP_Logging
    {

        public function __construct()
        {
            parent::__construct();
            add_filter('wp_log_types', array($this, 'lo_add_log_types'));
        }

        public function pw_add_log_types($types)
        {
            $types[] = 'API';
            return $types;
        }
    }

    global $Lo_Logging;
    $Lo_Logging = new Lo_Logging();

    function lo_debug($call_back, $param_arr)
    {
        global $Lo_Logging;
        call_user_func_array(array($Lo_Logging, $call_back), $param_arr);
    }

} else {

    function lo_debug($call_back, $param_arr)
    {
        return false;
    }

}

