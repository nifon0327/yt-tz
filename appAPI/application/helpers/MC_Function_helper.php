<?php
/**
 * CodeIgniter
 * 自定义函数
 */
defined('BASEPATH') OR exit('No direct script access allowed');

//功能：去除空或null子符串
if ( ! function_exists('out_format'))
{
	function out_format($str,$replace='')
	{
	    if (isset($str)){
		       if (is_numeric($str) && $replace!=''){
			       return $str==0?$replace:$str; 
		       }
		       else{
			       return ($str=='' || strlen($str)==0)?$replace:$str; 
			   }
	    }
	    else{
		   return ''; 
	    }
	}
}

