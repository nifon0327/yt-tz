<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class NewArrivalModel  
* 新品类  sql: ac.new_arrivaldata 
* 
*/ 
class  NewArrivalModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //获取新品的数量
	function getNewArrivalTotals(){
		$sql = 'SELECT IFNULL(COUNT(*),0) AS Counts  FROM new_arrivaldata  WHERE Estate>0';
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
	}
	
	function getTodayNewArrivals(){
		$sql = 'SELECT IFNULL(COUNT(*),0) AS Counts  FROM new_arrivaldata  WHERE Estate>0 AND DATE_FORMAT(created,\'%Y-%m-%d\')=CURDATE()';
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
	}


	public function type_sum() {
		$numNow =  "count(*)";
		$sql = 
	"
select * from 
	(
		select count(*) Num,
		100 TypeId,'New' TypeName, 
		count(*)  NumNow,
		0 sort 
		from new_arrivaldata A
		where A.Estate>=1
		and date_format(A.created,'%Y-%m-%d')=current_date
	union all
		select count(*) Num,
		A.TypeId,
		ifnull(T.TypeName,'未分类') TypeName,
		sum(if(date_format(A.created,'%Y-%m-%d')=current_date,1,0)) NumNow,
		1 sort
		from new_arrivaldata A
		left join stufftype T on A.TypeId=T.TypeId 
		where A.Estate>=1 
		group by A.TypeId	 
	order by sort, NumNow desc,Num desc 
    ) S 
where Num>0;
	";
		$query = $this->db->query($sql);
		return $query;
	}
 

/** 
* get_item  
* 获取新品列表 
* 
* @access public 
* @param array()  未使用的参数
* @return pdo rs obj 
*/  
    public function get_item($params=array()) {
		
		$typeid = element('typeid',$params,-1);
		$typeidSer = $typeid!= -1 ? " and A.TypeId=$typeid " : "";
		$dateSearch = "";
		if ($typeid!=-1) {
			if ($typeid==100) {
				$dateSearch = "and date_format(A.created,'%Y-%m-%d')=current_date";
				$typeidSer = "";

			} else {
				//$dateSearch = "and date_format(A.created,'%Y-%m-%d')!=current_date";
			}
		}
		
		$LoginNumber = element('LoginNumber',$params,-1);
		$limitstr = '';
		if ($LoginNumber == 11965) {
			$limitstr = 'limit 80';
		}
		
		$this->load->model('LoginUser');
		$uType=$this->LoginUser->get_user_type($this->UserId);
		$uType=$uType==''?0:$uType;
		
		$factoryCheck=$this->config->item('factory_check');
		
		$sql = "select 'new_a' tag,IF ($factoryCheck=1,'',DATE_FORMAT(A.created,'%Y-%m-%d')) Date,A.Name,IF($uType=1,concat('¥',ROUND(ifnull(A.Price,0)*ifnull(D.Rate,1),2)),'') as Price,
		if(A.Material='',' ',A.Material) Material,A.MOQ,ifnull(A.Images,'') as Images,ifnull(C.Forshort,A.CompanyName) as CompanyName,S.Name as Operator,
		ifnull(A.CompanyId,-1) CompanyId,A.Id,sum(if(F.Id is null,0,1)) as forward,ifnull(D.Rate,1) Rate ,A.TypeId,ifnull(A.Description,'') Description,A.created 
		from new_arrivaldata A 
		left join trade_object C on C.CompanyId=A.CompanyId 
		left join currencydata D on C.Currency=D.Id 
		left join staffmain S on S.Number=A.creator 
		left join new_forward F on F.NewId=A.Id 
		where A.Estate >=1 $typeidSer $dateSearch  group by A.Id order by  A.Id  desc $limitstr;";
	
		//    BundleId = ClientAppFor4Smarts;
		//    bundleId = clientApp
		
		$BundleId = element('BundleId',$params,'');
		if ($BundleId == "ClientAppFor4Smarts" || $BundleId == "ClientAppForInnov8"  || $BundleId == "NewProducts") {
			$Liker = $this->LoginNumber;
			$pricesql = "''";
			$mailers = "sunnychen@ashcloud.com|||candyzhang@ashcloud.com";
			$bgDate = "2015-10-23";
			if ($BundleId == "ClientAppForInnov8") {
				$mailers = "sunnysun@ashcloud.com|||candyzhang@ashcloud.com";
				$bgDate = "2015-10-29";
				$pricesql = "''";
			} else if ($BundleId == "NewProducts") {
				$bgDate = "2016-02-15";
				$pricesql = "concat('€',ROUND(ifnull(A.Price,0)*ifnull(D.Rate,1)*1.1/6.9,2))";
				$mailers = "candyzhang@ashcloud.com";
				if ($Liker == 50056) {
					$pricesql = "''";
				}
			}
			
			$sql = "
			select  'new_a' tag,
				DATE_FORMAT(A.Date,'%b %d, %Y') DateStr,
				DATE_FORMAT(A.created,'%Y-%m-%d') Date,A.MOQ,A.Description,A.Created ,
				ifnull(A.Images,'')  Images,ifnull(L.Liked,'0') Liked,
				if(DATE_FORMAT(A.created,'%Y-%m-%d')<'$bgDate',1,ifnull(R.Readed,'0'))  Readed,
				'$mailers' mailer,
				A.Id ,
				$pricesql as Price,
				if(date_format(A.created,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d'),1,0) isToday 
				from new_arrivaldata A 
					left join trade_object C on C.CompanyId=A.CompanyId 
		left join currencydata D on C.Currency=D.Id 
				left join new_liked L on L.NewId=A.Id and L.Liker=$Liker
				left join new_readed R on R.NewId=A.Id and R.Reader=$Liker
				where A.Description is not null and trim( A.Description )<>''
				group by A.Id
				order by A.created desc $limitstr;";


		}
        $query = $this->db->query($sql);
        return $query;
    }
	 
/** 
* get_dfile_path  
* 获取新品文档下载路径
* 
* @access public 
* @param  none
* @return string 
*/  
    public function get_dfile_path(){
	   return  $this->config->item('download_path') . "/newarrival/";
   }
	
/** 
* save_item  
* 插入一条新品纪录
* 
* @access public 
* @param  params array 一条纪录所需数据 和 图片文件（多张）
* @return int 返回生产的主键(失败返回-1) auto_generated_keyid
*/  
	public function save_item($params) {
		
		$iscompanyid = element('iscompanyid', $params, '0');
		$companyField = $iscompanyid == '1' ? 'CompanyId' : 'CompanyName';
		$uniqueName = element('name',  $params, '');
		
		/*
			$data = array('DateTime'=>element('DateTime',$params,this->DateTime),
					  'Operator'=>element('Operator',$params,this->LoginNumber),
					  'Item'=>element('LogItem',$params,''),
					  'Funtion'=>element('LogFunction',$params,''),
					  'Log'=>element('Log',$params,''),
					  'OperationResult'=>element('OperationResult',$params,'N'),
					  );
		
		*/
		$this->load->model('oprationlogModel');
		$LogItem = 'APP新品';
		$LogFunction = '新增纪录';
		$Log = '';
		$data = array(
               $companyField  => element('company', $params, ''),
               'Material'   => element('material',  $params, ''),
			   'Price'   => element('price',  $params, '0'),
			   'MOQ'   => element('moq',  $params, 0),
			   'Name'   => $uniqueName,
			   'Date'     => $this->Date,
			   'created'  => $this->DateTime,
			   'creator'=> $this->LoginNumber,
               'Operator' => $this->LoginNumber,
               'Estate'   => '1',
               'TypeId'=>element('type',  $params, '0'),
               'Description'=>element('desc',  $params, ''),
		);
		$uniqueQuery=$this->db->query('select Name from new_arrivaldata where Name=?',$uniqueName);
		if ($uniqueQuery->num_rows() >0) {
			$Log .= '新品名称重复，新品添加失败！';
			$this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log));
			return  -1;
		}
		
		
		$this->db->trans_begin();
		$query     = $this->db->insert('new_arrivaldata', $data); 
		$insert_id = $this->db->insert_id();
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $Log .= '新品添加失败！SQL ERROR!';
			    $this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log));
		} else {
			    $this->db->trans_commit();
			    $Log .= '新品添加成功！';
		}
		if ($insert_id > 0){
			  		 // 上传文件配置放入config数组
			        $config['upload_path'] = '../download/newarrival';
			        $config['allowed_types'] = 'gif|jpg|png';
			        $config['max_size'] = '1024000';
			         $config['max_width']  = '1024000';
  $config['max_height']  = '10240000';
			        $config['file_name'] = ''. $insert_id.'';
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
						                  'Images' =>$filenames,
						            );
						          $this->db->where('Id',$insert_id );
						          $this->db->trans_begin();
						          $query=$this->db->update('new_arrivaldata', $picture);
						          if ($this->db->trans_status() === FALSE){
						          	$Log .= '新品图片上传失败！';
									    $this->db->trans_rollback();
									}
									else{
									$Log .= '新品图片上传成功！';
									    $this->db->trans_commit();
									}
									
								   $this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log,'OperationResult'=>'Y'));
			           }
		          }
				  include "d:/website/mc/iphoneAPI/subpush/newarrival_push.php";
				  return $insert_id;
		} else {
			return -1;	
		}
		
	}
	 
	 
/** 
* edit_item  
* 更新一条新品纪录
* 
* @access public 
* @param  params array 一条纪录所需数据 和 图片文件（多张）
* @return int 0:失败 1:成功
*/  
public function edit_item($params) {
		$success = 0;
		$editId =  element('editid', $params, -1);
		$iscompanyid = element('iscompanyid', $params, '0');
		$companyField = $iscompanyid == '1' ? 'CompanyId' : 'CompanyName';
		$companyFieldAnti = $iscompanyid == '1' ? 'CompanyName' : 'CompanyId';
		$data = array(
               $companyField  => element('company', $params, ''),
			   $companyFieldAnti => NULL,
               'Material'   => element('material',  $params, ''),
			   'Price'   => element('price',  $params, '0'),
			   'MOQ'   => element('moq',  $params, 0),
			   'Name'   => element('name',  $params, ''),
			   'Date'     => $this->Date,
			   'modified'  => $this->DateTime,
			   'modifier'=> $this->LoginNumber,
               'Operator' => $this->LoginNumber,
               'TypeId'=>element('type',  $params, '0'),
               'Description'=>element('desc',  $params, ''),
		);
		
		
		
		$this->db->where('Id',$editId);
		$this->db->trans_begin();
		$query=$this->db->update('new_arrivaldata', $data);
						         
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
		} else {
			$success = 1;
			    $this->db->trans_commit();
		}
		$imageaction = element('imageaction',$params,-1);
			$basePath = '../download/newarrival';
		switch($imageaction) {
				
				case '1': {
					$mTimes = 0;
					$this->db->select('Images,mTimes');
						$query = $this->db->get_where('new_arrivaldata',array('Id'=>$editId),1);
						$checkRow = $query->row_array();
						$oldImages = $checkRow['Images'];
						$mTimes = $checkRow['mTimes'];
						$mTimes ++;
						$oldImagesList = explode('|',$oldImages);
						$deleted = 0;
						foreach ($oldImagesList as $oldSingleImage) {
							{
								$oldNameAndType = explode('.',$oldSingleImage);
								$oldThumb = $oldNameAndType[0].'_thumb.'.$oldNameAndType[1];
								$deletePath = $basePath.'/'.$oldSingleImage;
								$deletePathThumb = $basePath.'/'.$oldThumb;
								$tempOk = @unlink($deletePath);
								$deleted += $tempOk ? 1 : 0;
								unlink($deletePathThumb);
							}
						}
						
						// 上传文件配置放入config数组
			        $config['upload_path'] = '../download/newarrival';
			        $config['allowed_types'] = 'gif|jpg|png';
			      $config['max_size'] = '1024000';
			         $config['max_width']  = '1024000';
  $config['max_height']  = '10240000';
			        $config['file_name'] = ''. $editId.'-'."$mTimes".'';
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
						                  'Images' =>$filenames,
						                  'mTimes'  =>$mTimes
						            );
						          $this->db->where('Id',$editId );
						          $this->db->trans_begin();
						          $query=$this->db->update('new_arrivaldata', $picture);
						          if ($this->db->trans_status() === FALSE){
									    $this->db->trans_rollback();
										$success = 0;
									}
									else{
										$success = 1;
									    $this->db->trans_commit();
									}
			           }
		          }
						
				}	break;
				
				case '2': {
					
					$newImages = element('newimages',$params,'');
					if ($newImages != '') {
						$newImagesList = explode('|',$newImages);
						$this->db->select('Images');
						$query = $this->db->get_where('new_arrivaldata',array('Id'=>$editId),1);
						$checkRow = $query->row_array();
						$oldImages = $checkRow['Images'];
						$oldImagesList = explode('|',$oldImages);
						$deleted = 0;
						foreach ($oldImagesList as $oldSingleImage) {
							if (!in_array($oldSingleImage,$newImagesList)) {
								
								$oldNameAndType = explode('.',$oldSingleImage);
								$oldThumb = $oldNameAndType[0].'_thumb.'.$oldNameAndType[1];
								$deletePath = $basePath.'/'.$oldSingleImage;
								$deletePathThumb = $basePath.'/'.$oldThumb;
								$tempOk = @unlink($deletePath);
								$deleted += $tempOk ? 1 : 0;
								unlink($deletePathThumb);
							}
						}
						if ($deleted > 0) {
							  $picture = array(
						                  'Images' =>$newImages,
						            );
						          $this->db->where('Id',$editId);
						          $this->db->trans_begin();
						          $query=$this->db->update('new_arrivaldata', $picture);
						          if ($this->db->trans_status() === FALSE){
									    $this->db->trans_rollback();
										$success = 0;
									}
									else{
										$success = 1;
									    $this->db->trans_commit();
									}
						}
					}
					
				}	break;
				
				default :
				break;
				
				
			}
			
			
			return $success;
		
		
	}

}