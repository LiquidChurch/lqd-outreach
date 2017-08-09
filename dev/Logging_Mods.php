<?php

if (class_exists('WP_Logging')) {

    class LO_Logging extends WP_Logging
    {

        public function __construct()
        {
            parent::__construct();
            add_filter('wp_log_types', array($this, 'pw_add_log_types'));
        }

        public function pw_add_log_types($types)
        {
            $types[] = 'lo-api-calls';
            return $types;
        }
    }

    global $LO_Logging;
    $LO_Logging = new LO_Logging();

    function lo_debug($call_back, $param_arr)
    {
        global $LO_Logging;
        call_user_func_array(array($LO_Logging, $call_back), $param_arr);
    }

    function lo_delete_log()
    {
        global $LO_Logging;
        add_filter('wp_logging_should_we_prune', function(){
            return true;
        });
        call_user_func_array(array($LO_Logging, 'prune_logs'), []);
    }

} else {

    function lo_debug($call_back, $param_arr)
    {
        return false;
    }

}