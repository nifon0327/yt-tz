<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MenusShowModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
     function get_video_path()
   {
	   return  $this->config->item('download_path') . "/video/";
   }

        //保存上传文件


	function save_upvideo($saveName,$upfile)
	{
		// 上传文件配置放入config数组
	    $config['upload_path']   = '../download/video';
	    $config['allowed_types'] = 'mov|mpeg|mp4|avi';
	    $config['max_size']      = '1024000000';
	    $config['max_width']     = '';
	    $config['max_height']    = '';
        $config['file_name']     = $saveName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        //多次加载需重新初始化数据

		if (!$this->upload->do_upload($upfile)){

			//echo('Error in Uploading video '.$this->upload->display_errors().'<br />');
	        return 0;
	    }
	    else{

	        return 1;
	    }
    }

    function get_item($ModuleId) {


	    $checkHas = " select Id,FilePath from ac_menus_show where moduleid=$ModuleId ";
		$queryHas = $this->db->query($checkHas);
		if ($queryHas->num_rows() > 0) {
				$rowHas = $queryHas->row_array();
				return $rowHas;
		}
		return array();
    }

    function save_item($params) {



	    $ModuleId  = element('moduleid',$params,'-1');

	    $FilePath = $ModuleId.'_'.date('YmdHi');
	    $status = $this->save_upvideo($FilePath, 'videodata');
	    if ($status == 1) {
		    $Date  = element('data',$params, $this->Date);
		    $checkHas = " select Id,FilePath from ac_menus_show where moduleid=$ModuleId ";
			$queryHas = $this->db->query($checkHas);

			$data = array(
			    'ModuleId'=>$ModuleId,
			    'FilePath'=>$FilePath.'.mov'
		    );
			if ($queryHas->num_rows() > 0) {
				$rowHas = $queryHas->row_array();
				$updid = $rowHas["Id"];
				$data['modified']=$this->DateTime;
				$data['modifier']=$this->LoginNumber;
				$this->db->where('Id',$updid);
				$query=$this->db->update('ac_menus_show', $data);
				//100_201604300754
					unlink('../download/video/'.$rowHas['FilePath']);
			} else {
				$data['created']=$this->DateTime;
				$data['creator']=$this->LoginNumber;
				$query=$this->db->insert('ac_menus_show', $data);
			}

		    return $this->db->affected_rows();
	    }

        return -1;
    }


    function get_show_menus(){
        $this->load->model('LoginUser');

        $this->load->library('dateHandler');
		$this->load->model('WorkShopdataModel');
		$this->load->model('staffMainModel');
		$this->load->model('ScCjtjModel');


		$hourArr = $this->datehandler->get_worktimes();
		$hoursNow = $hourArr[1];

		$laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');


         $params = $this->input->post();
         $isPad = element("ISPAD",$params,0);

$basicPath = $this->get_video_path();

	    $sql="
	    	SELECT M.ModuleId,M.oldModuleId,M.oldItemId,M.name,M.Estate,M.row,M.col,M.icon_type,M.abs,M.typeid,M.callback,M.build ,M.imgid,
S.FilePath 
FROM ac_menus M
LEFT JOIN ac_menus_show S ON S.ModuleId=M.ModuleId
WHERE M.parent_id=0 and M.Estate=1  order by typeid;
	    ";//Estate=1
	    $query = $this->db->query($sql);

	    $old_typeid=-1;
	    $old_row=-1;
	    $row_count=0;

	    $rowsArray=array();
	    $dataArray=array();
	    foreach($query->result_array() as $row){
	       $old_typeid=$old_typeid==-1?$row['typeid']:$old_typeid;
	       $old_row=$old_row==-1?$row['row']:$old_row;

	       $typeid=$row['typeid'];
	       if ($old_typeid!=$typeid){
	         if (count($dataArray)>0){
	           $groupName =$this->get_GroupName($old_typeid);
	           $groupImage=$this->get_GroupImage($old_typeid);
	           $icon      =$this->get_titleIcon($old_typeid);
	           $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;
		       $rowsArray[]=array(
                      'GroupName'=>$groupName,
                        'TypeId' =>"$old_typeid",
                        'hidden' =>"$hiddened",
                           'Rows'=>$row_count,
                           'data'=>$dataArray,
                        'bgImage'=>"$groupImage",
                      'titleIcon'=>"$icon"
                          );
		     }
		       $dataArray=array();
		       $old_row=-1;
	           $row_count=0;
	           $old_typeid=$row['typeid'];
	       }

	       if ($old_row!=$row['row']){
		       $old_row=$row['row'];
		       $row_count++;
	       }

	       $checkSign = true;
	       if ($row['oldModuleId']!="" || $row['oldItemId']!=""){

	          $checkSign1=false; $checkSign2=false;
	          if ($row['oldModuleId']!=""){
	             $checkSign1=$this->LoginUser->check_authority_modules($row['oldModuleId']);
	          }

	          if ($row['oldItemId']!=""){
		         $checkSign2=$this->LoginUser->check_authority_Items($row['oldItemId']);
	          }
	          $checkSign=($checkSign1 || $checkSign2)?true:false;
	       }

	       if ($row['ModuleId']>=200 && $checkSign==false){
			      continue;
		   }

	       $icon_value='';

	       if ($checkSign==true){
	          if ($row['callback']!="") {
	             $callbacks=explode('/', $row['callback']);
	             if (count($callbacks)==1){
			          $icon_value=$this->$callbacks[0]();
			          if  ($row['ModuleId']==100 || $row['imgid']==100){
				          $icon_value=substr($icon_value, 4,2);
			          }
		         }
		         else{
			         $this->load->model($callbacks[0]);
		             $icon_value=$this->$callbacks[0]->$callbacks[1]();
		         }
	         }
	      }
	          $bgSign=($row['ModuleId']>=140 && $row['ModuleId']<144)?1:0;

				  $rowModuleId = $row['ModuleId'];
				  $rowCol = $row['col'];
				  $rowRow = $row['row'];
				  $ictype = $row['icon_type'];
				  $rowAbs = $row['abs'];
				  $FilePath = $row['FilePath'];
				  $hasVedio = $row['FilePath']!=''?1:0;

				  if ($isPad==1) {
// 					  for ipad layout
					  switch($rowModuleId) {
						  case 150:
						  case 147:
							  $rowCol = 4;
							  $rowRow = 0;
						  break;
						  case 104:
							  $rowCol = 5;
							  $rowRow = 0;
						  break;
						  case 132:
							  $rowCol = 4;
							  $rowRow = 1;
						  break;
						  case 148:
							  $rowCol = 5;
							  $rowRow = 1;
						  break;

						  case 149:
							  $rowCol = 5;
							  $rowRow = 0;
							  $rowAbs = 1;
						  break;

						  case 138:
							  $rowCol = 5;
							  $rowRow --;
							  $rowAbs = 1;
						  break;
						  case 124:
							  $rowCol = 2;
							  $rowRow = 1;
						  break;
						  case 128:
							  $rowCol = 3;
							  $rowRow = 1;
						  break;
						  case 116:

							  $rowCol = 3;
							  $rowRow = 0;
						  break;
						case 108:

						break;
					    case 133:

					    break;

					  }
				  }

		$infos = array('no'=>'');

 {

  switch ($rowModuleId) {
	  case 108:
	  {
		  $ictype = 7;
		$rows=$this->WorkShopdataModel->get_records(101);
		$groups  =$rows['GroupId'];
		$ActionId=$rows['ActionId'];

		$groupnums=$groups==''?0:$this->staffMainModel->date_checkInNums_ingroup($groups,$this->Date);

		$newDay = $hoursNow *$groupnums*$laborCost;
		$day_output   =$this->ScCjtjModel->get_workshop_day_output(101);
		$percen ='';
		if ($newDay > 0) {
			if ($day_output > $newDay)
				$percen ='+'.sprintf('%02d',round(($day_output-$newDay)/$newDay*100)).'%';
			else
				$percen ='-'.sprintf('%02d',round(($newDay-$day_output)/$newDay*100)).'%';
		} else {
			$percen = $day_output > 0 ? '+100%':' 00%';
		}
		$icon_value = $percen;
	  }
	  break;
	  case 142:
	  {
		  $ictype = 8;

		    $wsids = array(102,103,104,105,106);


		  $infos = array('k5'=>$hasVedio==0?'#c5c5c5':'#358fc1');
		  for ($i=0;$i<5;$i++) {
	$wsid = $wsids[$i];
	$rows =$this->WorkShopdataModel->get_records($wsid);
	$groups  =$rows['GroupId'];
	$ActionId=$rows['ActionId'];

	$groupnums=$groups==''?0:$this->staffMainModel->get_checkInNums_ingroup($groups);

	$newDay = $hoursNow *$groupnums*$laborCost;
	$day_output   =$this->ScCjtjModel->get_workshop_day_output($wsid);

	$infos["k$i"] = $hasVedio==0?'#c5c5c5':'#358fc1';
	if ($day_output > 0) {
		$infos["k$i"] = $newDay > $day_output ?($hasVedio==0?'#e5e5e5': '#ff3a43'):($hasVedio==0?'#c5c5c5':'#358fc1');
	}


		  }

	  }
	  break;
	  case 133:
	  {
		  $ictype = 9;
		  $this->load->model('Ck9stocksheetModel');
		  $sendFloor = 'all';
		    $aquery = $this->Ck9stocksheetModel->get_all_qty($sendFloor);
	        $rowone = $aquery->first_row('array');
	        $allckQty = $rowone['Amount'];
	        $aquery = $this->Ck9stocksheetModel->get_all_hasorder_floor($sendFloor);
	        $rowHasOrder = $aquery->first_row('array');
	        $hasOrderQty = $rowHasOrder['OrderAmount'];
	        $aquery = $this->Ck9stocksheetModel->get_over3m_notout($sendFloor);
	        $rowChart = $aquery->first_row('array');
	        $redQty = $rowChart['YearAmount'];
	        $aquery = $this->Ck9stocksheetModel->get_in1m_notout($sendFloor);
	        $rowChart = $aquery->first_row('array');
	        $blueQty = $rowChart['YearAmount'];
	        $leftQty = $allckQty - $blueQty - $redQty;

	        $percent = $allckQty >0 ? round($hasOrderQty/$allckQty*100) :0 ;





	        $infos = array(
		        'val1'=>array(
			        array('value'=>"$blueQty",'color'=>  $hasVedio ==0?'#c5c5c5':"#72b2d4"),
			        array('value'=>"$leftQty",'color'=>$hasVedio ==0?'#f5f5f5':"#dceaf4"),
			        array('value'=>"$redQty",'color'=>$hasVedio ==0?'#c5c5c5':"#ff3a43")
		        ),
		        'val2'=>array(
			        array('value'=>"$hasOrderQty",'color'=>$hasVedio ==0?'#e5e5e5':"#46e346"),
			        array('value'=>"".($allckQty-$hasOrderQty),'color'=>"clear")
		        ),
		        'title'=>array(
			        'isAttribute'=>'1',
			   		'attrDicts'=>array(
				   		array('Text'    =>"$percent",
				   			  'FontSize'=>'12',
				   			  'Color'   =>$hasVedio ==0?'#e5e5e5':"#04ce00"),
				   		array('Text'    =>'%',
				   			  'FontSize'=>'6',
				   			  'Color'   =>$hasVedio ==0?'#e5e5e5':"#04ce00")

				   	)
		        )
	        );


	  }
	  break;
  }

}


				  if ($hasVedio > 0) {
					  $FilePath = $FilePath;
				  }

				  $dataArray[]=array(
		           'id'       =>$row['ModuleId'],
		           'moduleid' =>$row['ModuleId'],
		           'imgid' =>$row['imgid'],
		           'name'     =>$row['name'],
		           'ServerId' =>"0",
		           'Estate'   =>$checkSign==true?'1':'0',
		           'row'      =>"$rowRow",
		           'col'      =>"$rowCol",
		           'abs'      =>"$rowAbs",
		           'build'    =>$hasVedio,
		           'canbuild'=>$row['build'],
		           'file'	 =>$hasVedio>0?$basicPath.$FilePath:'',
		           'filename'=>$FilePath,
		           'icon_type'=>$ictype,
		           'value'    =>"$icon_value",
		           'bgSign'   =>"$bgSign",
		           'infos'    =>$infos
		      );


       }
       if (count($dataArray)>0){
           $groupName =$this->get_GroupName($old_typeid);
	       $groupImage=$this->get_GroupImage($old_typeid);
	       $icon      =$this->get_titleIcon($old_typeid);

	       $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;


		   $rowsArray[]=array(
	                  'GroupName'=>$groupName,
	                    'TypeId' =>"$old_typeid",
	                    'hidden' =>"$hiddened",
	                       'Rows'=>$row_count,
	                       'data'=>$dataArray,
	                    'bgImage'=>"$groupImage",
	                  'titleIcon'=>"$icon"
	                  );
	   }
       return $rowsArray;
   }


      function get_main_menus_415(){
        $this->load->model('LoginUser');

        $this->load->library('dateHandler');
		$this->load->model('WorkShopdataModel');
		$this->load->model('staffMainModel');
		$this->load->model('ScCjtjModel');


		$hourArr = $this->datehandler->get_worktimes();
		$hoursNow = $hourArr[1];

		$laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');


         $params = $this->input->post();
         $isPad = element("ISPAD",$params,0);

         $versionCheck = 'and Id<=67';
//          $versionNum = $this->versionToNumber($this->AppVersion);
/*
         if ($versionNum >= 420) {
	         $versionCheck = '';
         }
*/

	    $sql="SELECT ModuleId,oldModuleId,oldItemId,name,Estate,row,col,icon_type,abs,typeid,callback,build  FROM ac_menus WHERE parent_id=0 and Estate=1  $versionCheck order by typeid";//Estate=1
	    $query = $this->db->query($sql);

	    $old_typeid=-1;
	    $old_row=-1;
	    $row_count=0;

	    $rowsArray=array();
	    $dataArray=array();
	    foreach($query->result_array() as $row){
	       $old_typeid=$old_typeid==-1?$row['typeid']:$old_typeid;
	       $old_row=$old_row==-1?$row['row']:$old_row;

	       $typeid=$row['typeid'];
	       if ($old_typeid!=$typeid){
	         if (count($dataArray)>0){
	           $groupName =$this->get_GroupName($old_typeid);
	           $groupImage=$this->get_GroupImage($old_typeid);
	           $icon      =$this->get_titleIcon($old_typeid);
	           $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;
		       $rowsArray[]=array(
                      'GroupName'=>$groupName,
                        'TypeId' =>"$old_typeid",
                        'hidden' =>"$hiddened",
                           'Rows'=>$row_count,
                           'data'=>$dataArray,
                        'bgImage'=>"$groupImage",
                      'titleIcon'=>"$icon"
                          );
		     }
		       $dataArray=array();
		       $old_row=-1;
	           $row_count=0;
	           $old_typeid=$row['typeid'];
	       }

	       if ($old_row!=$row['row']){
		       $old_row=$row['row'];
		       $row_count++;
	       }

	       $checkSign = true;
	       if ($row['oldModuleId']!="" || $row['oldItemId']!=""){

	          $checkSign1=false; $checkSign2=false;
	          if ($row['oldModuleId']!=""){
	             $checkSign1=$this->LoginUser->check_authority_modules($row['oldModuleId']);
	          }

	          if ($row['oldItemId']!=""){
		         $checkSign2=$this->LoginUser->check_authority_Items($row['oldItemId']);
	          }
	          $checkSign=($checkSign1 || $checkSign2)?true:false;
	       }

	       if ($row['ModuleId']>=200 && $checkSign==false){
			      continue;
		   }

	       $icon_value='';

	       if ($checkSign==true){
	          if ($row['callback']!="") {
	             $callbacks=explode('/', $row['callback']);
	             if (count($callbacks)==1){
			          $icon_value=$this->$callbacks[0]();
			          if  ($row['ModuleId']==100){
				          $icon_value=substr($icon_value, 4,2);
			          }
		         }
		         else{
			         $this->load->model($callbacks[0]);
		             $icon_value=$this->$callbacks[0]->$callbacks[1]();
		         }
	         }
	      }
	          $bgSign=($row['ModuleId']>=140 && $row['ModuleId']<144)?1:0;

				  $rowModuleId = $row['ModuleId'];
				  $rowCol = $row['col'];
				  $rowRow = $row['row'];
				  $ictype = $row['icon_type'];
				  $rowAbs = $row['abs'];
				  if ($isPad==1) {
// 					  for ipad layout
					  switch($rowModuleId) {
						  case 150:
						  	  $rowCol = 1;
							  $rowRow = 0;
						  break;
						  case 147:
							  $rowCol = 4;
							  $rowRow = 0;
						  break;
						  case 104:
							  $rowCol = 5;
							  $rowRow = 0;
						  break;
						  case 132:
							  $rowCol = 4;
							  $rowRow = 1;
						  break;
						  case 148:
							  $rowCol = 5;
							  $rowRow = 1;
						  break;

						  case 149:
							  $rowCol = 5;
							  $rowRow = 0;
							  $rowAbs = 1;
						  break;

						  case 138:
							  $rowCol = 5;
							  $rowRow --;
							  $rowAbs = 1;
						  break;
						  case 124:
							  $rowCol = 2;
							  $rowRow = 1;
						  break;
						  case 128:
							  $rowCol = 3;
							  $rowRow = 1;
						  break;
						  case 116:

							  $rowCol = 3;
							  $rowRow = 0;
						  break;
						case 108:

						break;
					    case 133:

					    break;

					  }
				  }

		$infos = array('no'=>'');

 {

  switch ($rowModuleId) {
	  case 108:
	  {
		  $ictype = 7;
		$rows=$this->WorkShopdataModel->get_records(101);
		$groups  =$rows['GroupId'];
		$ActionId=$rows['ActionId'];

		$groupnums=$groups==''?0:$this->staffMainModel->date_checkInNums_ingroup($groups,$this->Date);

		$newDay = $hoursNow *$groupnums*$laborCost;
		$day_output   =$this->ScCjtjModel->get_workshop_day_output(101);
		$percen ='';
		if ($newDay > 0) {
			if ($day_output > $newDay)
				$percen ='+'.sprintf('%02d',round(($day_output-$newDay)/$newDay*100)).'%';
			else
				$percen ='-'.sprintf('%02d',round(($newDay-$day_output)/$newDay*100)).'%';
		} else {
			$percen = $day_output > 0 ? '+100%':' 00%';
		}
		$icon_value = $percen;
	  }
	  break;
	  case 142:
	  {
		  $ictype = 8;

		    $wsids = array(102,103,104,105,106);
		  $infos = array('k5'=>'#358fc1');
		  for ($i=0;$i<5;$i++) {
	$wsid = $wsids[$i];
	$rows =$this->WorkShopdataModel->get_records($wsid);
	$groups  =$rows['GroupId'];
	$ActionId=$rows['ActionId'];

	$groupnums=$groups==''?0:$this->staffMainModel->get_checkInNums_ingroup($groups);

	$newDay = $hoursNow *$groupnums*$laborCost;
	$day_output   =$this->ScCjtjModel->get_workshop_day_output($wsid);

	$infos["k$i"] = '#358fc1';
	if ($day_output > 0) {
		$infos["k$i"] = $newDay > $day_output ? '#ff3a43':'#358fc1';
	}


		  }

	  }
	  break;
	  case 133:
	  {
		  $ictype = 9;
		  $this->load->model('Ck9stocksheetModel');
		  $sendFloor = 'all';
		    $aquery = $this->Ck9stocksheetModel->get_all_qty($sendFloor);
	        $rowone = $aquery->first_row('array');
	        $allckQty = $rowone['Amount'];
	        $aquery = $this->Ck9stocksheetModel->get_all_hasorder_floor($sendFloor);
	        $rowHasOrder = $aquery->first_row('array');
	        $hasOrderQty = $rowHasOrder['OrderAmount'];
	        $aquery = $this->Ck9stocksheetModel->get_over3m_notout($sendFloor);
	        $rowChart = $aquery->first_row('array');
	        $redQty = $rowChart['YearAmount'];
	        $aquery = $this->Ck9stocksheetModel->get_in1m_notout($sendFloor);
	        $rowChart = $aquery->first_row('array');
	        $blueQty = $rowChart['YearAmount'];
	        $leftQty = $allckQty - $blueQty - $redQty;

	        $percent = $allckQty >0 ? round($hasOrderQty/$allckQty*100) :0 ;
	        $infos = array(
		        'val1'=>array(
			        array('value'=>"$blueQty",'color'=>"#72b2d4"),
			        array('value'=>"$leftQty",'color'=>"#dceaf4"),
			        array('value'=>"$redQty",'color'=>"#ff3a43")
		        ),
		        'val2'=>array(
			        array('value'=>"$hasOrderQty",'color'=>"#46e346"),
			        array('value'=>"".($allckQty-$hasOrderQty),'color'=>"clear")
		        ),
		        'title'=>array(
			        'isAttribute'=>'1',
			   		'attrDicts'=>array(
				   		array('Text'    =>"$percent",
				   			  'FontSize'=>'12',
				   			  'Color'   =>"#04ce00"),
				   		array('Text'    =>'%',
				   			  'FontSize'=>'6',
				   			  'Color'   =>"#04ce00")

				   	)
		        )
	        );


	  }
	  break;
  }

}


				  $dataArray[]=array(
		           'id'       =>$row['ModuleId'],
		           'moduleid' =>$row['ModuleId'],
		           'name'     =>$row['name'],
		           'ServerId' =>"0",
		           'Estate'   =>$checkSign==true?'1':'0',
		           'row'      =>"$rowRow",
		           'col'      =>"$rowCol",
		           'abs'      =>"$rowAbs",
		           'build'    =>$row['build'],
		           'icon_type'=>$ictype,
		           'value'    =>"$icon_value",
		           'bgSign'   =>"$bgSign",
		           'infos'    =>$infos
		      );


       }
       if (count($dataArray)>0){
           $groupName =$this->get_GroupName($old_typeid);
	       $groupImage=$this->get_GroupImage($old_typeid);
	       $icon      =$this->get_titleIcon($old_typeid);

	       $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;

/*
	       if ($old_typeid == 5 && $this->LoginNumber==11965) {
		       $row_count ++;
		       $dataArray[]=array(
		           'id'       =>'150',
		           'moduleid' =>'150',
		           'name'     =>'计算器',
		           'ServerId' =>"0",
		           'Estate'   =>'1',
		           'row'      =>"1",
		           'col'      =>"1",
		           'abs'      =>"0",
		           'build'    =>'1',
		           'icon_type'=>''
		      );
	       }
*/

		   $rowsArray[]=array(
	                  'GroupName'=>$groupName,
	                    'TypeId' =>"$old_typeid",
	                    'hidden' =>"$hiddened",
	                       'Rows'=>$row_count,
	                       'data'=>$dataArray,
	                    'bgImage'=>"$groupImage",
	                  'titleIcon'=>"$icon"
	                  );
	   }
       return $rowsArray;
   }

     function get_main_menus_409(){
        $this->load->model('LoginUser');

         $params = $this->input->post();
         $isPad = element("ISPAD",$params,0);

	    $sql="SELECT ModuleId,oldModuleId,oldItemId,name,Estate,row,col,icon_type,abs,typeid,callback,build  FROM ac_menus WHERE parent_id=0 and Estate=1   order by typeid";//Estate=1
	    $query = $this->db->query($sql);

	    $old_typeid=-1;
	    $old_row=-1;
	    $row_count=0;

	    $rowsArray=array();
	    $dataArray=array();
	    foreach($query->result_array() as $row){
	       $old_typeid=$old_typeid==-1?$row['typeid']:$old_typeid;
	       $old_row=$old_row==-1?$row['row']:$old_row;

	       $typeid=$row['typeid'];
	       if ($old_typeid!=$typeid){
	         if (count($dataArray)>0){
	           $groupName =$this->get_GroupName($old_typeid);
	           $groupImage=$this->get_GroupImage($old_typeid);
	           $icon      =$this->get_titleIcon($old_typeid);
	           $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;
		       $rowsArray[]=array(
                      'GroupName'=>$groupName,
                        'TypeId' =>"$old_typeid",
                        'hidden' =>"$hiddened",
                           'Rows'=>$row_count,
                           'data'=>$dataArray,
                        'bgImage'=>"$groupImage",
                      'titleIcon'=>"$icon"
                          );
		     }
		       $dataArray=array();
		       $old_row=-1;
	           $row_count=0;
	           $old_typeid=$row['typeid'];
	       }

	       if ($old_row!=$row['row']){
		       $old_row=$row['row'];
		       $row_count++;
	       }

	       $checkSign = true;
	       if ($row['oldModuleId']!="" || $row['oldItemId']!=""){

	          $checkSign1=false; $checkSign2=false;
	          if ($row['oldModuleId']!=""){
	             $checkSign1=$this->LoginUser->check_authority_modules($row['oldModuleId']);
	          }

	          if ($row['oldItemId']!=""){
		         $checkSign2=$this->LoginUser->check_authority_Items($row['oldItemId']);
	          }
	          $checkSign=($checkSign1 || $checkSign2)?true:false;
	       }

	       if ($row['ModuleId']>=200 && $checkSign==false){
			      continue;
		   }

	       $icon_value='';

	       if ($checkSign==true){
	          if ($row['callback']!="") {
	             $callbacks=explode('/', $row['callback']);
	             if (count($callbacks)==1){
			          $icon_value=$this->$callbacks[0]();
			          if  ($row['ModuleId']==100){
				          $icon_value=substr($icon_value, 4,2);
			          }
		         }
		         else{
			         $this->load->model($callbacks[0]);
		             $icon_value=$this->$callbacks[0]->$callbacks[1]();
		         }
	         }
	      }
	          $bgSign=($row['ModuleId']>=140 && $row['ModuleId']<144)?1:0;

				  $rowModuleId = $row['ModuleId'];
				  $rowCol = $row['col'];
				  $rowRow = $row['row'];
				  $ictype = $row['icon_type'];
				  $rowAbs = $row['abs'];
				  if ($isPad==1) {
// 					  for ipad layout
					  switch($rowModuleId) {
						  case 147:
							  $rowCol = 4;
							  $rowRow = 0;
						  break;
						  case 104:
							  $rowCol = 5;
							  $rowRow = 0;
						  break;
						  case 132:
							  $rowCol = 4;
							  $rowRow = 1;
						  break;
						  case 148:
							  $rowCol = 5;
							  $rowRow = 1;
						  break;

						  case 149:
							  $rowCol = 5;
							  $rowRow = 0;
							  $rowAbs = 1;
						  break;

						  case 138:
							  $rowCol = 5;
							  $rowRow --;
							  $rowAbs = 1;
						  break;
						  case 124:
							  $rowCol = 2;
							  $rowRow = 1;
						  break;
						  case 128:
							  $rowCol = 3;
							  $rowRow = 1;
						  break;
						  case 116:

							  $rowCol = 3;
							  $rowRow = 0;
						  break;
case 108:

break;

					  }
				  }


				  $dataArray[]=array(
		           'id'       =>$row['ModuleId'],
		           'moduleid' =>$row['ModuleId'],
		           'name'     =>$row['name'],
		           'ServerId' =>"0",
		           'Estate'   =>$checkSign==true?'1':'0',
		           'row'      =>"$rowRow",
		           'col'      =>"$rowCol",
		           'abs'      =>"$rowAbs",
		           'build'    =>$row['build'],
		           'icon_type'=>$ictype,
		           'value'    =>"$icon_value",
		           'bgSign'   =>"$bgSign"
		      );


       }
       if (count($dataArray)>0){
           $groupName =$this->get_GroupName($old_typeid);
	       $groupImage=$this->get_GroupImage($old_typeid);
	       $icon      =$this->get_titleIcon($old_typeid);

	       $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;

		   $rowsArray[]=array(
	                  'GroupName'=>$groupName,
	                    'TypeId' =>"$old_typeid",
	                    'hidden' =>"$hiddened",
	                       'Rows'=>$row_count,
	                       'data'=>$dataArray,
	                    'bgImage'=>"$groupImage",
	                  'titleIcon'=>"$icon"
	                  );
	   }
       return $rowsArray;
   }


    function get_main_menus(){
        $this->load->model('LoginUser');

         $params = $this->input->post();
         $isPad = element("ISPAD",$params,0);

	    $sql="SELECT ModuleId,oldModuleId,oldItemId,name,Estate,row,col,icon_type,abs,typeid,callback,build  FROM ac_menus WHERE parent_id=0 and Estate=1 and Id<=57  order by typeid";//Estate=1
	    $query = $this->db->query($sql);

	    $old_typeid=-1;
	    $old_row=-1;
	    $row_count=0;

	    $rowsArray=array();
	    $dataArray=array();
	    foreach($query->result_array() as $row){
	       $old_typeid=$old_typeid==-1?$row['typeid']:$old_typeid;
	       $old_row=$old_row==-1?$row['row']:$old_row;

	       $typeid=$row['typeid'];
	       if ($old_typeid!=$typeid){
	         if (count($dataArray)>0){
	           $groupName =$this->get_GroupName($old_typeid);
	           $groupImage=$this->get_GroupImage($old_typeid);
	           $icon      =$this->get_titleIcon($old_typeid);
	           $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;
		       $rowsArray[]=array(
                      'GroupName'=>$groupName,
                        'TypeId' =>"$old_typeid",
                        'hidden' =>"$hiddened",
                           'Rows'=>$row_count,
                           'data'=>$dataArray,
                        'bgImage'=>"$groupImage",
                      'titleIcon'=>"$icon"
                          );
		     }
		       $dataArray=array();
		       $old_row=-1;
	           $row_count=0;
	           $old_typeid=$row['typeid'];
	       }

	       if ($old_row!=$row['row']){
		       $old_row=$row['row'];
		       $row_count++;
	       }

	       $checkSign = true;
	       if ($row['oldModuleId']!="" || $row['oldItemId']!=""){

	          $checkSign1=false; $checkSign2=false;
	          if ($row['oldModuleId']!=""){
	             $checkSign1=$this->LoginUser->check_authority_modules($row['oldModuleId']);
	          }

	          if ($row['oldItemId']!=""){
		         $checkSign2=$this->LoginUser->check_authority_Items($row['oldItemId']);
	          }
	          $checkSign=($checkSign1 || $checkSign2)?true:false;
	       }

	       if ($row['ModuleId']>=200 && $checkSign==false){
			      continue;
		   }

	       $icon_value='';

	       if ($checkSign==true){
	          if ($row['callback']!="") {
	             $callbacks=explode('/', $row['callback']);
	             if (count($callbacks)==1){
			          $icon_value=$this->$callbacks[0]();
			          if  ($row['ModuleId']==100){
				          $icon_value=substr($icon_value, 4,2);
			          }
		         }
		         else{
			         $this->load->model($callbacks[0]);
		             $icon_value=$this->$callbacks[0]->$callbacks[1]();
		         }
	         }
	      }
	          $bgSign=($row['ModuleId']>=140 && $row['ModuleId']<144)?1:0;
			  if ($old_typeid==4 && $row['ModuleId']>=145 && $this->LoginNumber!=11965) {

			  } else {
				  $rowModuleId = $row['ModuleId'];
				  $rowCol = $row['col'];
				  $rowRow = $row['row'];
				  if ($isPad==1) {
					  switch($rowModuleId) {
						  case 145:
							  $rowCol = 4;
							  $rowRow = 0;
						  break;
						  case 146:
							  $rowCol = 5;
							  $rowRow = 0;
						  break;
						  case 147:
							  $rowCol = 4;
							  $rowRow = 1;
						  break;
						  case 148:
							  $rowCol = 5;
							  $rowRow = 1;
						  break;
						  case 149:
							  $rowCol = 5;
							  $rowRow = 0;
						  break;

					  }
				  }

				  $dataArray[]=array(
		           'id'       =>$row['ModuleId'],
		           'moduleid' =>$row['ModuleId'],
		           'name'     =>$row['name'],
		           'ServerId' =>"0",
		           'Estate'   =>$checkSign==true?'1':'0',
		           'row'      =>"$rowRow",
		           'col'      =>"$rowCol",
		           'abs'      =>$row['abs'],
		           'build'    =>$row['build'],
		           'icon_type'=>$row['icon_type'],
		           'value'    =>"$icon_value",
		           'bgSign'   =>"$bgSign"
		      );
			  }

       }
       if (count($dataArray)>0){
           $groupName =$this->get_GroupName($old_typeid);
	       $groupImage=$this->get_GroupImage($old_typeid);
	       $icon      =$this->get_titleIcon($old_typeid);

	       $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;

		   $rowsArray[]=array(
	                  'GroupName'=>$groupName,
	                    'TypeId' =>"$old_typeid",
	                    'hidden' =>"$hiddened",
	                       'Rows'=>$row_count,
	                       'data'=>$dataArray,
	                    'bgImage'=>"$groupImage",
	                  'titleIcon'=>"$icon"
	                  );
	   }
       return $rowsArray;
   }

   function get_main_menus_old(){
        $this->load->model('LoginUser');

	    $sql="SELECT ModuleId,oldModuleId,oldItemId,name,Estate,row,col,icon_type,abs,typeid,callback,build FROM ac_menus WHERE parent_id=0 and Estate=1   order by typeid";//Estate=1
	    $query = $this->db->query($sql);

	    $old_typeid=-1;
	    $old_row=-1;
	    $row_count=0;

	    $rowsArray=array();
	    $dataArray=array();
	    foreach($query->result_array() as $row){
	       $old_typeid=$old_typeid==-1?$row['typeid']:$old_typeid;
	       $old_row=$old_row==-1?$row['row']:$old_row;

	       $typeid=$row['typeid'];
	       if ($old_typeid!=$typeid){
	         if (count($dataArray)>0){
	           $groupName =$this->get_GroupName($old_typeid);
	           $groupImage=$this->get_GroupImage($old_typeid);
	           $icon      =$this->get_titleIcon($old_typeid);
		       $rowsArray[]=array(
                      'GroupName'=>$groupName,
                        'TypeId' =>"$old_typeid",
                           'Rows'=>$row_count,
                           'data'=>$dataArray,
                        'bgImage'=>"$groupImage",
                      'titleIcon'=>"$icon"
                          );
		     }
		       $dataArray=array();
		       $old_row=-1;
	           $row_count=0;
	           $old_typeid=$row['typeid'];
	       }

	       if ($old_row!=$row['row']){
		       $old_row=$row['row'];
		       $row_count++;
	       }

	       $checkSign = true;
	       if ($row['oldModuleId']!="" || $row['oldItemId']!=""){

	          $checkSign1=false; $checkSign2=false;
	          if ($row['oldModuleId']!=""){
	             $checkSign1=$this->LoginUser->check_authority_modules($row['oldModuleId']);
	          }

	          if ($row['oldItemId']!=""){
		         $checkSign2=$this->LoginUser->check_authority_Items($row['oldItemId']);
	          }
	          $checkSign=($checkSign1 || $checkSign2)?true:false;
	       }

	       $icon_value='';
	       if ($checkSign==true){
	          if ($row['callback']!="") {
	             $callbacks=explode('/', $row['callback']);
	             if (count($callbacks)==1){
			          $icon_value=$this->$callbacks[0]();
			          if  ($row['ModuleId']==100){
				          $icon_value=substr($icon_value, 4,2);
			          }
		         }
		         else{
			         $this->load->model($callbacks[0]);
		             $icon_value=$this->$callbacks[0]->$callbacks[1]();
		         }
	         }
	      //}
		     /*
		      if ($row['ModuleId']>=200 && $checkSign==false){
			      continue;
		      }
		      */

	          $bgSign=($row['ModuleId']>=140 && $row['ModuleId']<144)?1:0;

		      $dataArray[]=array(
		           'id'       =>$row['ModuleId'],
		           'moduleid' =>$row['ModuleId'],
		           'name'     =>$row['name'],
		           'ServerId' =>"0",
		           'Estate'   =>$checkSign==true?'1':'0',
		           'row'      =>$row['row'],
		           'col'      =>$row['col'],
		           'abs'      =>$row['abs'],
		           'build'    =>$row['build'],
		           'icon_type'=>$row['icon_type'],
		           'value'    =>"$icon_value",
		           'bgSign'   =>"$bgSign"
		      );
	      }
       }
       if (count($dataArray)>0){
           $groupName =$this->get_GroupName($old_typeid);
	       $groupImage=$this->get_GroupImage($old_typeid);
	       $icon      =$this->get_titleIcon($old_typeid);

	       $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;


		   $rowsArray[]=array(
	                  'GroupName'=>$groupName,
	                    'TypeId' =>"$old_typeid",
	                    'hidden' =>"$hiddened",
	                       'Rows'=>$row_count,
	                       'data'=>$dataArray,
	                    'bgImage'=>"$groupImage",
	                  'titleIcon'=>"$icon"
	                  );
	   }
       return $rowsArray;
   }

   function get_GroupName($typeid){
	  switch($typeid){
		   case 2: return '研砼贸易';
		   case 3: return '研砼HK';
		   case 4: return '行政管理';
		   case 5: return '应用';
		   case 6: return '软件';
		   default: return '';
	  }
   }

   function get_GroupImage($typeid){
	  switch($typeid){
		   case 2: return 'trade_bg';
		   case 3: return 'hk_bg';
		   default: return '';
	  }
   }

   function get_titleIcon($typeid){
	  switch($typeid){
		   case 3: return 'hk';
		   default: return '';
	  }
   }


   //获取未出数量
	function get_UnShipAmount(){
		$sql="SELECT SUM((S.Qty-S.shipQty)*S.Price*D.Rate) AS Amount 
								FROM(SELECT S.Id,S.POrderId,S.OrderNumber,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
					               FROM yw1_ordersheet S 
					               LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
					               WHERE S.Estate>0 GROUP BY S.POrderId
						        )S  
								LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
								LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
								LEFT JOIN currencydata D ON D.Id=C.Currency";
		$query=$this->db->query($sql);

		$row = $query->first_row();
		$Amount=$row->Amount;
		$Amount=$Amount==""?0:round($Amount/1000000,0);
	    $Amount.="M";
        return $Amount;
	}

	//全部未生产生产单数量
   function get_UnscSheetQty()
   {
		$sql = "SELECT SUM(A.Qty-A.ScQty) AS Qty 
				FROM (
				  SELECT S.Qty,SUM(IFNULL(T.Qty,0)) AS ScQty 
					  FROM   yw1_scsheet S 
					  LEFT JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
					  LEFT JOIN workorderaction W ON W.ActionId=S.ActionId 
				  WHERE S.ScFrom>0 AND S.Estate>0 AND W.SemiSign=1 GROUP BY S.sPOrderId 
				)A ";
		$query=$this->db->query($sql);

		$row = $query->first_row();
		$Qty=$row->Qty;
		$Qty=$Qty==""?0:round($Qty/10000,0);
	    $Qty.="W";
	    return $Qty;
	}

	//全部未生产生产单数量
    function get_UnscSheetCost()
    {
		$sql = "SELECT SUM((A.Qty-A.ScQty)*A.Price) AS Cost 
				FROM (
				  SELECT S.sPOrderId,S.Qty,SUM(IFNULL(T.Qty,0)) AS ScQty,G.Price
					  FROM   yw1_scsheet S 
					  LEFT JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
					  LEFT JOIN workorderaction W ON W.ActionId=S.ActionId 
					  LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
				  WHERE S.ScFrom>0 AND S.Estate>0 AND W.SemiSign=1 GROUP BY S.sPOrderId 
				)A ";
		$query=$this->db->query($sql);

		$row = $query->first_row();
		$Cost=$row->Cost;
		$Cost=$Cost==""?0:round($Cost/10000,0);
	    $Cost.="W";
	    return $Cost;
	 }


}