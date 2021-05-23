<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StudysheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

	public function delete_item($params) {
		$del_id   = element('del_id', $params, -1);
		$attached   = element('attached', $params, '');
		
		
		if ($del_id <= 0) {
			return -1;	
		} else {
		
		
				$this->load->model('oprationlogModel');
		$LogItem = '公司文件';
		$LogFunction = '删除纪录';
		$Log = '文件Id为:'.$del_id.'的纪录';
			$this->db->where('id', $del_id);
			$this->db->trans_begin();
			$query = $this->db->delete('zw2_hzdoc'); 
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
		
		$this->db->where('id', $edit_id);
          
          $this->db->trans_begin();
          $query=$this->db->update('zw2_hzdoc', $data);
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
		$this->db->trans_begin();
		$query     = $this->db->insert('zw2_hzdoc', $data); 
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
		$PstmtArray = array();
		if ($TypeId != -1) {
			 $SearchRows .= ' and D.TypeId=? ';
			 $PstmtArray[]=$TypeId;
		}
		$reader = $this->LoginNumber;
        $sql   = "SELECT D.Id,D.Name title,D.TypeId,D.Icon,D.File,date_format(D.Date,'%m.%d %Y') uptitle ,ifnull(R.Readed,0) Readed,D.GoodsId,D.creator 
        
               FROM studysheet D 
               left join studyreaded R on R.StudyId=D.Id and R.Reader=$reader
               WHERE D.Estate>=1  $SearchRows ORDER BY Readed ,D.created desc";
        $query = $this->db->query($sql, $PstmtArray);
        return $query;

    }
	
	
	function get_download_path(){
	   return  $this->config->item('download_path') . "/";
    }
	

   function get_jpg_path(){
	   return  $this->get_download_path() . "nobom_intro/";
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