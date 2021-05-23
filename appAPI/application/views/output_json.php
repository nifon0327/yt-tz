<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header('Content-type:text/json');
	    
//输出json数据
$this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($jsondata));
?>