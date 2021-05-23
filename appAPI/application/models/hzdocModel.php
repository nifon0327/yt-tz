<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  HzdocModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

	public function delete_item($params) {
		$del_id   = element('del_id', $params, -1);
		$attached   = element('attached', $params, '');
		$TypeId   = element('TypeId', $params, '');
		$table = $TypeId == '33' ? 'yw7_clientproxy':'zw2_hzdoc';
			
		
		if ($del_id <= 0) {
			return -1;	
		} else {
		
		
				$this->load->model('oprationlogModel');
		$LogItem = '公司文件';
		$LogFunction = '删除纪录';
		$Log = '文件Id为:'.$del_id.'的纪录';
			$this->db->where('id', $del_id);
			$this->db->trans_begin();
			
			
			$query = $this->db->delete(''.$table); 
			$OP = 'N';
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $Log .= '删除失败';
			}
			else{
			    $this->db->trans_commit();
			    
			    $Log .= '删除成功';
			    $OP = 'Y';
				if ($attached != '') {
					$baseJpg = '../download/hzdocjpg/';
					$basePdf = '../download/hzdoc/';
				$hasJpg = strstr($attached, '.jpg'); 
						if ($hasJpg == ".jpg") {
							$jpgPath = $baseJpg.$attached;
							@unlink($jpgPath);
							$thumbPath = $baseJpg.$del_id.'_thumb.jpg';
							@unlink($thumbPath);
						} else {
							$pdfPath = $basePdf.$attached;
							@unlink($pdfPath);
							$thumbPath = $baseJpg.$del_id.'_thumb.jpg';
							@unlink($thumbPath);
							$jpgPath = $baseJpg.$del_id.'.jpg';
							@unlink($jpgPath);
							$jpgPath = $baseJpg.$del_id.'-0.jpg';
							@unlink($jpgPath);
							$thumbPath0 = $baseJpg.$del_id.'-0_thumb.jpg';
							@unlink($thumbPath0);
						}
				}
				
				
			}
			
								   $this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log,'OperationResult'=>$OP));
			return $query;
		}
		
	}

	public function update_item($params) {
		$edit_id   = element('edit_id', $params, -1);
		$alter_img = element('alter_img', $params, -1);
		
		
		
			
			
		if ($edit_id <= 0) {
			return false;
		}
		$data = array(
               'Caption'  => element('Caption', $params, ''),
               'TypeId'   => element('TypeId',  $params, 0),
			    'Date'     => element('Date',    $params, $this->Date),
               'EndDate'  => element('EndDate', $params, NULL),
               'Operator' => $this->LoginNumber,
		);
		$TypeId   = element('TypeId', $params, '');
		$table = $TypeId == '33' ? 'yw7_clientproxy':'zw2_hzdoc';
			
		if ($TypeId == 33) {
			$data = array(
               'Caption'  => element('Caption', $params, ''),
			    'Date'     => element('Date',    $params, $this->Date),
               'TimeLimit'  => element('EndDate', $params, NULL),
               'Operator' => $this->LoginNumber,
		);
			
		}
		
		$this->db->where('id', $edit_id);
          
          $this->db->trans_begin();
          $query=$this->db->update(''.$table, $data);
          if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
			else{
			    $this->db->trans_commit();
				
				if ($alter_img == "1") {
				    $config['upload_path']   = '../download/hzdocjpg';
			         // 上传文件配置放入config数组
			        $config['allowed_types'] = 'gif|jpg|png|pdf';
			        $config['max_size'] = '102400';
			        $config['file_name'] = $edit_id;
					 $config['overwrite'] = true;
					 
			        $this->load->library('multiupload');
			        
			        $result=$this->multiupload->multi_upload('upfiles',$config);
			       
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
						                  'Attached' =>$filenames,
						            );
						          $this->db->where('id',$edit_id );
						          $this->db->trans_begin();
						          $query=$this->db->update('zw2_hzdoc', $picture);
						          if ($this->db->trans_status() === FALSE){
									    $this->db->trans_rollback();
									}
									else{
									    $this->db->trans_commit();
									}
			           }
		          }
				  
				}
				
			}
           return  $query; 
	}

	public function save_item($params) {
		
		$data = array(
               'Caption'  => element('Caption', $params, ''),
               'TypeId'   => element('TypeId',  $params, 0),
			    'Date'     => element('Date',    $params, $this->Date),
               'EndDate'  => element('EndDate', $params, NULL),
               'Operator' => $this->LoginNumber,
               'Estate'   => '1',
			    'Attached' => ''
		);
		
		$TypeId   = element('TypeId', $params, '');
		$table = $TypeId == '33' ? 'yw7_clientproxy':'zw2_hzdoc';
			
		if ($TypeId == 33) {
			$data = array(
               'Caption'  => element('Caption', $params, ''),
			    'Date'     => element('Date',    $params, $this->Date),
               'TimeLimit'  => element('EndDate', $params, NULL),
               'Operator' => $this->LoginNumber,
		);
			
		}
		
		
		$this->db->trans_begin();
		$query     = $this->db->insert(''.$table, $data); 
		$insert_id = $this->db->insert_id();
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
		} else {
			    $this->db->trans_commit();
		}
		if ($insert_id > 0){
			  		 $config['upload_path']   = '../download/hzdocjpg';
			         // 上传文件配置放入config数组
			        $config['allowed_types'] = 'gif|jpg|png|pdf';
			        $config['max_size'] = '102400';
					 $config['overwrite'] = true;
					 
			        $config['file_name'] = $insert_id;
			        $this->load->library('multiupload');
			        
			        $result=$this->multiupload->multi_upload('upfiles',$config);
			       
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
						                  'Attached' =>$filenames,
						            );
						          $this->db->where('id',$insert_id );
						          $this->db->trans_begin();
						          $query=$this->db->update('zw2_hzdoc', $picture);
						          if ($this->db->trans_status() === FALSE){
									    $this->db->trans_rollback();
									}
									else{
									    $this->db->trans_commit();
									}
			           }
		          }
				  
				  return $insert_id;
		} else {
			return -1;	
		}
		
	}

    public function get_item($params=array()){

		$SearchRows='';
		$TypeId = element('TypeId',$params,-1);

		if ($TypeId != -1) {
			 $SearchRows .= " and D.TypeId='$TypeId' ";

		}
		
        $sql   = 'SELECT D.Id,D.Caption,D.TypeId,D.Attached,D.Date,D.EndDate,D.Locks,D.Operator ,D.SortId,D.cSign
               FROM zw2_hzdoc D WHERE 1 '. $SearchRows. ' ORDER BY D.SortId ASC';
               
         if ($TypeId == 33) {
	         
	         
	         
	        $sql = "SELECT D.Id,D.Caption,D.TimeLimit as EndDate,D.Attached,D.Estate,D.Date,D.Locks,D.Operator,C.Forshort 
FROM yw7_clientproxy D
LEFT JOIN trade_object C ON C.CompanyId=D.CompanyId 
WHERE 1   ORDER BY D.Date DESC";
         }
        $query = $this->db->query($sql);
        return $query;

    }
	
	
	function get_download_path(){
	   return  $this->config->item('download_path') . "/";
    }
	
	
   function get_pdf_path($TypeId=''){
	   if ($TypeId == 33) {
		   return $this->get_download_path() . 'clientproxy/';
	   }
	   return  $this->get_download_path() . "hzdoc/";
   }
   
   function get_jpg_path(){
	   return  $this->get_download_path() . "hzdocjpg/";
   }
   
	public function get_jpg($Id){
		
	
		
          $photo_path = 'hzdocjpg/' . $Id . '.jpg';
          if (file_exists($this->config->item('document_root') . 'download/'.$photo_path)){
			  $photo_path = 'hzdocjpg/' . $Id . '_thumb.jpg';
			  if (file_exists($this->config->item('document_root') . 'download/'.$photo_path)) {
				 return $photo_path;  
			  } 
$config['source_image'] =$this->config->item('document_root') . 'download/'.'hzdocjpg/' . $Id . '.jpg';;
$config['create_thumb'] = TRUE;
$config['maintain_ratio'] = TRUE;
$config['width'] = 120;
$config['height'] = 120;

$this->load->library('image_lib', $config); 
if ( ! $this->image_lib->resize())
{
    $photo_path.= '--error--'.$this->image_lib->display_errors();
}
$this->image_lib->clear();
			  
	           return    $photo_path; 
          } else {
			  $photo_path = 'hzdocjpg/' . $Id . '-0.jpg';
			  
			  if (file_exists($this->config->item('document_root') . 'download/'.$photo_path)) {
				  
				  $photo_path = 'hzdocjpg/' . $Id . '-0_thumb.jpg';
			  if (file_exists($this->config->item('document_root') . 'download/'.$photo_path)) {
				 return $photo_path;  
			  } 
$config['source_image'] = $this->config->item('document_root'). 'download/' .'hzdocjpg/' . $Id . '-0.jpg';
$config['create_thumb'] = TRUE;
$config['maintain_ratio'] = TRUE;
$config['width'] = 120;
$config['height'] = 120;

$this->load->library('image_lib', $config); 

if ( ! $this->image_lib->resize())
{
    $photo_path.= '--error--'.$this->image_lib->display_errors();
}
$this->image_lib->clear();
				  return   $photo_path; 
			  }
          }
          return "";
    }

	 
}