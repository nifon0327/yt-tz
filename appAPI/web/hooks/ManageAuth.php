<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** * * 后台权限拦截钩子  */
class ManageAuth 
{
		 private $CI; 
		 
		 public function __construct() { 
		        $this->CI = &get_instance();    
		}           
		 /**    权限认证   **/    
		 public function auth() {
		        /*
		        $AppBundleId=$this->CI->input->post('BundleId');
		        
		        if (strpos($AppBundleId, 'ClientAppFor')!==false || strpos($AppBundleId, 'NewProducts')!==false) return;
		        
		        $controller_name=strtolower($this->CI->uri->rsegment(1));
		        if ($controller_name!='login' && $controller_name!='todayWidget' && $controller_name!='checkVersion')
		        {
			        $login_key=$this->CI->input->post('KEY');
			        $login_number=$this->CI->input->post('LoginNumber');
			        $this->CI->load->helper('security');
			        $publickey=$this->CI->config->item('public_key');
			        
			        $new_key=do_hash( $publickey . $login_number . date('Y') . date('W' ), 'md5');
			        if ($login_key!=$new_key){
			            show_error('Key Error!');
				        exit;
			        }
		        }
		       */
		}
}