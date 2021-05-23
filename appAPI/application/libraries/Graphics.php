<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Graphics{  
		 public function create_thumb($images,$width = 160,$height = 240)
		 {
		        $config['image_library'] = 'gd2';
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['width'] =    $width;
				$config['height'] =  $height;
				
				$CI =& get_instance();
		     	$CI->load->library('image_lib'); 
		     	
		        foreach($images as $image){
		             $config['source_image'] = $image;
		             $CI->image_lib->initialize($config); 
		             $CI->image_lib->resize();
		        }
				
				return  $CI->image_lib->display_errors();
		 }
 }
?>
