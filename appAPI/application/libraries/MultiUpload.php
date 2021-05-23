<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MultiUpload{  
 public function multi_upload($field,$config)
 {
                // Is $_FILES[$field] set? If not, no reason to continue.
                if ( ! isset($_FILES[$field]))
                {
                       // show_error('upload_no_file_selected');
                        return false;
                }
 
                // 临时文件上传数组
                $tmpfiles       = array();
                for ($i = 0, $len = count($_FILES[$field]['name']); $i < $len; $i ++)
                {
                        if ($_FILES[$field]['size'][$i])
                        {
                            $tmpfiles['upfiles' . $i] = array(
                                    'name'          => $_FILES[$field]['name'][$i],
                                    'type'          => $_FILES[$field]['type'][$i],
                                    'tmp_name'      => $_FILES[$field]['tmp_name'][$i],
                                    'error'         => $_FILES[$field]['error'][$i],
                                    'size'          => $_FILES[$field]['size'][$i],
                                    );
                        }
                }
                //覆盖 $_FILES 内容
                $_FILES = $tmpfiles;
                
                $errors = array();
                $files  = array();
                $index  = 0;
                
                 $CI =& get_instance();
                 $CI -> load -> library('upload', $config); //调用CI的upload类
            
                foreach ($_FILES  as $key=>$value)
                {
                       if ($index>0){
	                      $CI->upload->set_file_name_override($config['file_name']  . '-' . $index);   
                       } 
                        
                        if( ! $CI->upload->do_upload($key))
                        {
                                $errors[$index] = $CI->upload->display_errors('', '');
                                $CI->error_msg        = array();
                        }
                        else
                        {
                                $files[$index] = $CI->upload->data();
                        }
                        
                        
                        $index  ++;
                }

                // 返回数组
                return array(
                                'error' => $errors,
                                'files' => $files
                        );
        }
}
?>
