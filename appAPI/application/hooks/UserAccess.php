<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** * * 用户访问记录  */
class UserAccess 
{
		 private $CI; 
		 
		 public function __construct() { 
		        $this->CI = &get_instance();    
		}           
		 /**    保存用户访问记录   **/    
		 public function savelogs() 
		 {
		        $loginNumber  = $this->CI->input->post('LoginNumber');
		        
		        $segment =$this->CI->uri->segment(1);
                $uriPath =$this->CI->uri->uri_string();
                
                
                
                if ($loginNumber>0 && $segment!='Badge' && $uriPath!='main/static_operation'){
	                
	                
	                
                   $appBundleId = $this->CI->input->post('BundleId');
                   $appBundleId = $appBundleId==''?$this->get_user_agent():$appBundleId;
                   $appBundleId = $appBundleId==''?'':$appBundleId;
                   
                   $appVersion = $this->CI->input->post('AppVersion');
                   $Device     = $this->CI->input->post('DeviceModel');
                   
                   $parameter = '';
                   $parameters = $this->CI->input->post();
                   foreach($parameters as $akey => $avalue)    
					{        
						
						
						switch  ($akey) {
							case 'AppVersion':
							case 'BundleId':
							case 'DeviceModel':
							case 'ISPAD':
							case 'KEY':
							case 'LoginNumber':
							case 'UserId':
							case 'cSign':
							break;
						
							default: {
								if ($parameter == '') {
									$parameter .= "$akey=>$avalue";
								} else {
									$parameter .= "||$akey=>$avalue";
								}
							}
							break;
						}  
					} 
                   
                   $Device = $Device==''?'':$Device;
                   //$session=$this->CI->load->library('session');
                   //$ip_Address=$this->CI->session->userdata('ip_address');
			       $ip_Address=$this->CI->input->ip_address();
			       $inRecode = array(
	                      'BundleId'=>"$appBundleId",
	                        'Device'=>"$Device",
	                      'Version' =>"$appVersion",
	                       'Segment'=>"$segment",
	                           'IP' =>"$ip_Address",
	                           'Uri'=>"$uriPath",
	                       'creator'=>"$loginNumber"
				          );     
				          
				          if ($parameter != '') {
					          $inRecode['Parameter'] = $parameter;
				          }
			       $this->CI->db->insert('app_userlog',$inRecode); 
			       
                }  
			   
		}
		
		function get_user_agent(){
		    
		    $this->CI->load->library('user_agent');
			if ($this->CI->agent->is_browser())
				{
				    $agent = $this->CI->agent->browser() . ' ' . $this->CI->agent->version();
				}
				elseif ($this->CI->agent->is_robot())
				{
				    $agent = $this->CI->agent->robot();
				}
				elseif ($this->CI->agent->is_mobile())
				{
				    $agent = $this->CI->agent->mobile();
				}
				else
				{
				    $agent = 'Unidentified';
				}
		   return $agent;
		}
}