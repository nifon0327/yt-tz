<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StuffDevelopLogModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
   function get_picture_path(){
	   return  $this->config->item('download_path') . "/developlog/";
   }
   
    //来自develop controllers
   function save_item($params){
          $data = array(
               'mid'=> element('Id', $params, 0),
               'remark' => element('Remark', $params, 0),
               'date'=>$this->Date,
               'operator'=>$this->LoginNumber,
               'estate'=>'1',
               'creator'=>$this->LoginNumber,
               'created'=>$this->DateTime
            );
          
     
          $this->db->trans_begin();
          $query=$this->db->insert('stuffdevelop_log', $data); 
          $insert_id=$this->db->insert_id();
          //$this->output->enable_profiler(TRUE);
          if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
			else{
			    $this->db->trans_commit();
			}
			if ($insert_id>0){
					 // 上传文件配置放入config数组
			        $config['upload_path'] = '../download/developlog';
			        $config['allowed_types'] = 'gif|jpg|png';
			        $config['max_size'] = '102400';
			        $config['width'] =  120;
                    $config['height'] = 160;
			        $config['file_name'] = 'log_'. $insert_id;
			        $this->load->library('multiupload');
			        
			        $result=$this->multiupload->multi_upload('upfiles',$config);
			       
			        //取得上传文件名更新字段
		            $filenames=''; $images=array();
		            if ($result){
		               
			           foreach($result['files'] as $files){
				              $filenames.=$filenames==""?$files['file_name']:"|" . $files['file_name'];
				              $images[]=$files['full_path'];
			           }
			           
			           $this->load->library('graphics');
			           $this->graphics->create_thumb($images);
			           
			           if ($filenames!=""){
				                 $picture = array(
						                  'picture' =>$filenames,
						            );
						          $this->db->where('id',$insert_id );
						          $this->db->trans_begin();
						          $query=$this->db->update('stuffdevelop_log', $picture);
						          if ($this->db->trans_status() === FALSE){
									    $this->db->trans_rollback();
									}
									else{
									    $this->db->trans_commit();
									}
			           }
		          }
            }
		    return  $insert_id; 
    }
}
?>
