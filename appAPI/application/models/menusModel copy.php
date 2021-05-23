<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MenusModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }


    function main_types() {

	    $sql = "
	    select Name,Id from ac_menustype T where T.TopSign=1 and T.Estate=1 order by T.Order;

	    ";
	    $query = $this->db->query($sql);
	    $types = array();

	    $allDict = array('n'=>'');
	    $selIndex = -1;
	    if ($query->num_rows() > 0) {
		    $rs = $query->result_array();
		    $iter = 0;
		    foreach($rs as $rows) {
			    $typeid = $rows['Id'];
			    if ($typeid == 1) {
				    $selIndex = $iter;
			    }

			    $baseColor =  $typeid=='99'?'#FF6B00':'#358fc1';
			    $titleObj = array(
			    'isAttribute'=>'1',
              	'attrDicts'=>array(
              	  array('Text'=>$rows['Name'],
	              		'Color'=>$baseColor,
              			'FontSize'=>$typeid=='99'?'15':'14',
              			'FontWeight'=>'regular',
              			'FontName'=>$typeid=='99'?'ShinGoMin-Shadow':'system')
			  			)
                );

				$selTitle = null;
				$subtext = '';
				if ($typeid == 99) {

					$selTitle = array(
				    'isAttribute'=>'1',
	              	'attrDicts'=>array(
	              	  array('Text'=>'NEW',
	              			'FontSize'=>'14',
	              			'FontName'=>'Avenir-Medium')
				  			)
	                );
					$this->load->model('NewArrivalModel');
					$subtext = $this->NewArrivalModel->getTodayNewArrivals();
				} else {
					switch ($typeid) {
						case 1: $subtext='★';
						break;
						case 2: $subtext='100%';
						break;
						case 7:
						case 3: $subtext='100%';
						break;
						case 6: $subtext='50%';
						break;

					}
				}
				$subObj = array('Text'=>''.$subtext, 'Color'=>$baseColor);



			    $onetype=array(
				    'title'=>$titleObj,
				    'sub'=>$subObj,

				    'load'=>$typeid=='99'?'newarrival':'main',
				    'loadid'=>''.$typeid
			    );

			    if ($typeid == 99) {
				    $onetype['seltitle'] = $selTitle;
				    $onetype['fixcolor'] = $baseColor;
			    }

			    $types[]=$onetype;

			    $iter ++;

		    }
	    }

	    if ($selIndex>=0) {
		    $allDict['selIndex'] = $selIndex;
	    }
	    $allDict['types'] = $types;
	    return $allDict;

    }

    function get_frommain($loadid=1) {
	     $this->load->model('LoginUser');


        $empData=array(
					           'id'       =>'-999',
					           'moduleid' =>'-999',
					           'name'     =>'',
					           'ServerId' =>"0",
					           'Estate'   =>'',
					           'row'      =>"0",
					           'col'      =>"4",
					           'abs'      =>"0",
					           'build'    =>'',
					           'icon_type'=>'',
					           'showid'   =>''
					      );

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



		$newSign = '';
		$newSign = " and M.TypeId in ($loadid, 4, 5)";


		$ordersign = " field(M.typeid,$loadid, 4,5) ";
		$sql = "
		SELECT M.ModuleId,M.oldModuleId,M.oldItemId,M.name,M.imgid,
	           M.Estate,M.row,M.col,M.icon_type,M.abs,M.typeid,M.callback,M.build,
	           S.ModuleId AS showid
		FROM ac_menus M 
		LEFT JOIN ac_menus_show S ON M.ModuleId=S.ModuleId
		-- LEFT JOIN ac_menustype T ON T.Id=M.typeid
		WHERE M.parent_id=0 and M.Estate=1 $newSign  order by $ordersign ";

		// echo("$sql");
	    $query = $this->db->query($sql);

	    $old_typeid=-1;
	    $old_row=-1;
	    $row_count=0;

	    $rowsArray=array();
	    $dataArray=array();
	    foreach($query->result_array() as $row){

		    if ($loadid == 2 && $row['typeid']==2) {


/*
			    $indata = $row;
			    unset($indata['showid']);
			    $indata['action'] = '';
			     $indata['typeid'] = '7';
			     $indata['order']='1';
			    $indata['imgid'] = $indata['imgid']==''?$indata['ModuleId'] :$indata['imgid'];
			    $indata['ModuleId'] = ($indata['ModuleId']+500);
			    $this->db->insert('ac_menus', $indata);

*/

		    }

		    if ($row['ModuleId'] == 129 || $row['ModuleId'] == 116|| $row['ModuleId'] == 138) {
			    continue;
		    }

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
                        'TypeId' =>$old_typeid == $loadid?'': "$old_typeid",

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
							  $rowRow = 4;
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
						  case 100:
					      $dataArray[]=$empData;
					      $dataArray[]=$empData;

							  $rowCol = 5;
							  $rowRow = 0;

						  break;
						  case 116:

							  $rowCol = 4;
							  $rowRow = 0;
							  $rowAbs = 0;
						  break;
						  case 143:

							  $rowCol = 3;
							  $rowRow = 4;
							  $rowAbs = 0;
						  break;
						  case 142:

							  $rowCol = 2;
							  $rowRow = 4;

						  break;
						  case 141:

							  $rowCol = 1;
							  $rowRow = 4;

						  break;
						case 108:
							$rowCol = 4;
							$rowAbs = 1;

						break;
					    case 154:
							$rowAbs = 0;
					    break;

					  }
				  }

		$infos = array('no'=>'');

 {

  switch ($rowModuleId) {

	  case 724:
	  case 224:
	  case 124:
	  if ($isPad == 0 || $rowModuleId!=124) {
		  $rowRow = 1;
		  $rowCol = 2;
		  $dataArray[]=array(
		           'id'       =>'-999',
		           'moduleid' =>'-999',
		           'name'     =>'',
		           'ServerId' =>"0",
		           'Estate'   =>'',
		           'row'      =>"1",
		           'col'      =>"1",
		           'abs'      =>"0",
		           'build'    =>'',
		           'icon_type'=>'',
		           'showid'   =>''
		      );

	  }

	  break;
	  case 128:
	  if ($isPad == 0) {
		  $rowRow = 1;
		  $rowCol = 3;

	  }

	  break;
	  case 143:{

			  $rowAbs = 0;


	  }
	  break;
	  case 108:
	  {

		  $rowRow = 4;

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
// 		  $row['showid']='';
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

	         $this->load->model('CkrksheetModel');
	         $SendFloor = '';
			 $warehouseId='all';

	        $records = $this->CkrksheetModel->get_stock_amount($warehouseId,$SendFloor);
	        $stockQty         = round($records['Qty']);
	        $stockAmount = round($records['Amount']);
	        $records = null;

	        $totalQty         = $stockQty;
			$totalAmount = $stockAmount;

	        $records = $this->CkrksheetModel->get_order_amount($warehouseId,$SendFloor);
	        $orderQty        = round($records['OrderQty']);        //订单需求数量
	        $orderAmount= round($records['Amount']); //订单需求金额
	        $M1Amount   = round($records['M1Amount']);//一个月内未有下单
	        $M3Amount   = round($records['M3Amount']);//三个月内未有下单

	        $M0Amount = $orderAmount-$M1Amount;
	        $M1Amount = $M1Amount - $M3Amount;

	        $M1Percent  = $orderAmount>0?round($M1Amount/$orderAmount*100):0;
	        $M3Percent  = $orderAmount>0?round($M3Amount/$orderAmount*100):0;
	        $M0Percent  = 100 - $M1Percent - $M3Percent;

		   $OrderPercent  = $orderAmount>0?round($orderAmount/$stockAmount*100):0;
		   $ClearPercent      = 100 - $OrderPercent;

	       $infos = array(
			        'val1'=>array(
				        array('value'=>"$M0Percent",'color'=>"#72b2d4"),
				        array('value'=>"$M1Percent",'color'=>"#dceaf4"),
				        array('value'=>"$M3Percent",'color'=>"#ff3a43")
			        ),
			        'val2'=>array(
				        array('value'=>"$OrderPercent",'color'=>"#46e346"),
				        array('value'=>"".$ClearPercent,'color'=>"clear")
			        ),
			        'title'=>array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$OrderPercent",
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



if ($row['ModuleId'] == 109 && $isPad==0) {
/*
	$dataArray[]=array(
		           'id'       =>'-999',
		           'moduleid' =>'-999',
		           'name'     =>'',
		           'ServerId' =>"0",
		           'Estate'   =>'',
		           'row'      =>"4",
		           'col'      =>"0",
		           'abs'      =>"0",
		           'build'    =>'',
		           'icon_type'=>'',
		           'showid'   =>''
		      );
*/
}

				  $dataArray[]=array(
		           'id'       =>$row['ModuleId'],
		           'imgid'=>$row['imgid'],
		           'moduleid' =>$row['ModuleId'],
		           'name'     =>$row['name'],
		           'ServerId' =>"0",
		           'Estate'   =>$checkSign==true?'1':'0',
		           'row'      =>"$rowRow",
		           'col'      =>"$rowCol",
		           'abs'      =>"$rowAbs",
		           'build'    =>$old_typeid==$loadid ? '1':$row['build'] ,
		           'icon_type'=>$ictype,
		           'value'    =>"$icon_value",
		           'bgSign'   =>"$bgSign",
		           'showid'   =>$row['showid'].'',
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
	                    'TypeId' =>$old_typeid == $loadid?'': "$old_typeid",

	                       'Rows'=>$row_count,
	                       'data'=>$dataArray,
	                    'bgImage'=>"$groupImage",
	                  'titleIcon'=>"$icon"
	                  );
	   }
       return $rowsArray;

    }

    function get_main_menus_slice($newSign = '') {
	     $this->load->model('LoginUser');
        $newslicedata = array();

        $empData=array(
					           'id'       =>'-999',
					           'moduleid' =>'-999',
					           'name'     =>'',
					           'ServerId' =>"0",
					           'Estate'   =>'',
					           'row'      =>"0",
					           'col'      =>"4",
					           'abs'      =>"0",
					           'build'    =>'',
					           'icon_type'=>'',
					           'showid'   =>''
					      );

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



// 	    $sql="SELECT ModuleId,oldModuleId,oldItemId,name,Estate,row,col,icon_type,abs,typeid,callback,build  FROM ac_menus WHERE parent_id=0 and Estate=1  order by typeid";//Estate=1

		if ($this->LoginNumber == 11965) {
			$newSign = '1';
		}
		if ($newSign = '') {
			$newSign = 'and M.Id<=69';
		} else {
			$newSign = '';
		}

		$sql = "
		SELECT M.ModuleId,M.oldModuleId,M.oldItemId,M.name,
	           M.Estate,M.row,M.col,M.icon_type,M.abs,M.typeid,M.callback,M.build,
	           S.ModuleId AS showid
		FROM ac_menus M 
		LEFT JOIN ac_menus_show S ON M.ModuleId=S.ModuleId
		WHERE M.parent_id=0 and M.Estate=1 $newSign  order by M.typeid";
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


		       $asectionDict=array(
                      'GroupName'=>$groupName,
                        'TypeId' =>"$old_typeid",
                        'hidden' =>"$hiddened",
                           'Rows'=>$row_count,
                           'data'=>$dataArray,
                        'bgImage'=>"$groupImage",
                      'titleIcon'=>"$icon"
                          );

                      if ($old_typeid==1) {
		                  $asectionDict['slices']='yes';
		                  $rowsArray[]=$asectionDict;



		                  $newsection = array('load'=>'newarrival');
		                  $newsection['data'] = array();

		                  // A-OTF-ShinGoMin-Shadow
		                  $newsection['fiximg'] = 'main_stretch_orange';
						  $newsection['srt']='-100';
		                  $newsection['title'] = array('isAttribute'=>'1',
		                  	'attrDicts'=>array(array('Text'=>'NEW','Color'=>'#FF6B00','FontSize'=>'14','FontName'=>'ShinGoMin-Shadow'))
		                  );
						  $newslicedata[]=$newsection;
	                  }

	                  if ($old_typeid <= 3) {
		                  $asectionDict['srt'] = $old_typeid==3 ? 0:$old_typeid;
		                  $groupName = str_replace('研砼', '', $groupName);
		                  $subtext = '';
		                  if ($old_typeid==1) {
			                  $groupName = '信息';
			                 $subtext = '★';
		                  } else {
			                  $subtext = $old_typeid==3?'100%':'50%';
		                  }
		                  // Avenir-Medium
		                  $asectionDict['title'] = array('isAttribute'=>'1',
		                  	'attrDicts'=>array(array('Text'=>$groupName,'Color'=>'#358fc1','FontSize'=>'14','FontName'=>$old_typeid==3?'ShinGoMin-Shadow': 'Avenir-Medium'))
		                  );


		                   $asectionDict['sub'] = array('Text'=>''.$subtext, 'Color'=>'#358fc1');
// 						  $newslicedata[]=$asectionDict;
						  $newslicedata[]=$asectionDict;
						  if ($old_typeid == 3) {
							   $subtext = '50%';
		                  $asectionDict['srt'] = 9;
		                  $asectionDict['title'] = array('isAttribute'=>'1',
		                  	'attrDicts'=>array(array('Text'=>'软件','Color'=>'#358fc1','FontSize'=>'14','FontName'=>'Avenir-Medium'))
		                  );


		                   $asectionDict['sub'] = array('Text'=>''.$subtext, 'Color'=>'#358fc1');
							  $newslicedata[]=$asectionDict;
						  }
	                  } else {

		                  $rowsArray[]=$asectionDict;
	                  }

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
							  $rowRow = 4;
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
						  case 100:



					      $dataArray[]=$empData;
					      $dataArray[]=$empData;

							  $rowCol = 5;
							  $rowRow = 0;

						  break;
						  case 116:

							  $rowCol = 4;
							  $rowRow = 0;
							  $rowAbs = 0;
						  break;
						  case 143:

							  $rowCol = 3;
							  $rowRow = 4;
							  $rowAbs = 0;
						  break;
						  case 142:

							  $rowCol = 2;
							  $rowRow = 4;

						  break;
						  case 141:

							  $rowCol = 1;
							  $rowRow = 4;

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

	  case 100:
	 case 120:

	  case 129:
// 	  	$row['showid']='';
	  break;
	  case 143:{
// 		  if ($isPad==0) {
			  $rowAbs = 0;
// 		  }

	  }
	  break;
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
// 		  $row['showid']='';
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


		  /*
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
	        */
	         $this->load->model('CkrksheetModel');
	         $SendFloor = '';
			 $warehouseId='all';

	        $records = $this->CkrksheetModel->get_stock_amount($warehouseId,$SendFloor);
	        $stockQty         = round($records['Qty']);
	        $stockAmount = round($records['Amount']);
	        $records = null;

	        $totalQty         = $stockQty;
			$totalAmount = $stockAmount;

	        $records = $this->CkrksheetModel->get_order_amount($warehouseId,$SendFloor);
	        $orderQty        = round($records['OrderQty']);        //订单需求数量
	        $orderAmount= round($records['Amount']); //订单需求金额
	        $M1Amount   = round($records['M1Amount']);//一个月内未有下单
	        $M3Amount   = round($records['M3Amount']);//三个月内未有下单

	        $M0Amount = $orderAmount-$M1Amount;
	        $M1Amount = $M1Amount - $M3Amount;

	        $M1Percent  = $orderAmount>0?round($M1Amount/$orderAmount*100):0;
	        $M3Percent  = $orderAmount>0?round($M3Amount/$orderAmount*100):0;
	        $M0Percent  = 100 - $M1Percent - $M3Percent;

		   $OrderPercent  = $orderAmount>0?round($orderAmount/$stockAmount*100):0;
		   $ClearPercent      = 100 - $OrderPercent;

	       $infos = array(
			        'val1'=>array(
				        array('value'=>"$M0Percent",'color'=>"#72b2d4"),
				        array('value'=>"$M1Percent",'color'=>"#dceaf4"),
				        array('value'=>"$M3Percent",'color'=>"#ff3a43")
			        ),
			        'val2'=>array(
				        array('value'=>"$OrderPercent",'color'=>"#46e346"),
				        array('value'=>"".$ClearPercent,'color'=>"clear")
			        ),
			        'title'=>array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$OrderPercent",
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



if ($row['ModuleId'] == 109 && $isPad==0) {
/*
	$dataArray[]=array(
		           'id'       =>'-999',
		           'moduleid' =>'-999',
		           'name'     =>'',
		           'ServerId' =>"0",
		           'Estate'   =>'',
		           'row'      =>"4",
		           'col'      =>"0",
		           'abs'      =>"0",
		           'build'    =>'',
		           'icon_type'=>'',
		           'showid'   =>''
		      );
*/
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
		           'showid'   =>$row['showid'].'',
		           'infos'    =>$infos
		      );


       }
       if (count($dataArray)>0){
           $groupName =$this->get_GroupName($old_typeid);
	       $groupImage=$this->get_GroupImage($old_typeid);
	       $icon      =$this->get_titleIcon($old_typeid);

	       $hiddened = ($old_typeid == 2 || $old_typeid==3) ? 1:0;



		  $asectionDict = array(
	                  'GroupName'=>$groupName,
	                    'TypeId' =>"$old_typeid",
	                    'hidden' =>"$hiddened",
	                       'Rows'=>$row_count,
	                       'data'=>$dataArray,
	                    'bgImage'=>"$groupImage",
	                  'titleIcon'=>"$icon"
	                  );
	                  if ($old_typeid==1) {
		                  $asectionDict['slices']='yes';
	                  }

	                  $rowsArray[]= $asectionDict;
	   }
	   $newData = array();
	   $newslicedatas = $newslicedata;

	   usort($newslicedata, function($a, $b) {
            $al = ($a['srt']);
            $bl = ($b['srt']);
            if ($al == $bl)
                return 0;
            return ($al < $bl) ? -1 : 1;
        });



// 		$newslicedata[]=$newslicedata[0];
/*
		$newslicedata[]=$newslicedata[1];
		$newslicedata[]=$newslicedata[2];
		$newslicedata[]=$newslicedata[3];
		$newslicedata[]=$newslicedata[4];

// 		$newslicedata[]=$newslicedata[0];
		$newslicedata[]=$newslicedata[1];
		$newslicedata[]=$newslicedata[2];
		$newslicedata[]=$newslicedata[3];
		$newslicedata[]=$newslicedata[4];
*/
	   /*

	   $newslicedata = array();
	   $dataa1 = $newslicedatas[0];
	    $dataa1['title'] = array('Text'=>'NEW', 'Color'=>'#FF0000');
$dataa1['fiximg'] = 'main_stretch_orange';

$newslicedata[]=$dataa1;

$dataa = $newslicedatas[1];
	      $dataa['title'] = array('Text'=>'TIME1', 'Color'=>'#358fc1');
	      $dataa['sub'] = array('Text'=>'100%', 'Color'=>'#358fc1');
$newslicedata[]=$dataa;

$dataa = $newslicedatas[0];
$dataa['title'] = array('Text'=>'TIME2', 'Color'=>'#358fc1');
	      $dataa['sub'] = array('Text'=>'100%', 'Color'=>'#358fc1');
$newslicedata[]=$dataa;

$dataa = $newslicedatas[2];
$dataa['title'] = array('Text'=>'TIME3', 'Color'=>'#358fc1');
	      $dataa['sub'] = array('Text'=>'100%', 'Color'=>'#358fc1');
$newslicedata[]=$dataa;
$dataa['title'] = array('Text'=>'TIME4', 'Color'=>'#358fc1');
	      $dataa['sub'] = array('Text'=>'100%', 'Color'=>'#358fc1');
$newslicedata[]=$dataa;
	   */
	   foreach ($rowsArray as $sectionDict) {
		   $aslice = element('slices', $sectionDict, '');
		   if ($aslice == 'yes') {
			   $sectionDict['types'] = $newslicedata;
			   $sectionDict['selIndex'] = '2';

			   $adatain = $sectionDict['data'];
			   $sectionDict['data'] = array();
			   $newData[]=$sectionDict;

			   $sectionDict['slices']='';
			   $sectionDict['data'] = $adatain;
			   $sectionDict['types'] = array();
		   }
		   $newData[]=$sectionDict;
	   }
       return $newData;

    }

    function get_main_menus_420($newSign = ''){
        $this->load->model('LoginUser');


        $empData=array(
					           'id'       =>'-999',
					           'moduleid' =>'-999',
					           'name'     =>'',
					           'ServerId' =>"0",
					           'Estate'   =>'',
					           'row'      =>"0",
					           'col'      =>"4",
					           'abs'      =>"0",
					           'build'    =>'',
					           'icon_type'=>'',
					           'showid'   =>''
					      );

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



// 	    $sql="SELECT ModuleId,oldModuleId,oldItemId,name,Estate,row,col,icon_type,abs,typeid,callback,build  FROM ac_menus WHERE parent_id=0 and Estate=1  order by typeid";//Estate=1

		if ($this->LoginNumber == 11965) {
			$newSign = '1';
		}
		if ($newSign = '') {
			$newSign = 'and M.Id<=69';
		} else {
			$newSign = 'and M.Id<=70';
		}

		$sql = "
		SELECT M.ModuleId,M.oldModuleId,M.oldItemId,M.name,
	           M.Estate,M.row,M.col,M.icon_type,M.abs,M.typeid,M.callback,M.build,
	           S.ModuleId AS showid
		FROM ac_menus M 
		LEFT JOIN ac_menus_show S ON M.ModuleId=S.ModuleId
		WHERE M.parent_id=0 and M.Estate=1 $newSign  order by M.typeid";
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
							  $rowRow = 4;
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
						  case 100:



					      $dataArray[]=$empData;
					      $dataArray[]=$empData;

							  $rowCol = 5;
							  $rowRow = 0;

						  break;
						  case 116:

							  $rowCol = 4;
							  $rowRow = 0;
							  $rowAbs = 0;
						  break;
						  case 143:

							  $rowCol = 3;
							  $rowRow = 4;
							  $rowAbs = 0;
						  break;
						  case 142:

							  $rowCol = 2;
							  $rowRow = 4;

						  break;
						  case 141:

							  $rowCol = 1;
							  $rowRow = 4;

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

	  case 100:
	 case 120:

	  case 129:
// 	  	$row['showid']='';
	  break;
	  case 143:{
// 		  if ($isPad==0) {
			  $rowAbs = 0;
// 		  }

	  }
	  break;
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
// 		  $row['showid']='';
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


		  /*
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
	        */
	         $this->load->model('CkrksheetModel');
	         $SendFloor = '';
			 $warehouseId='all';

	        $records = $this->CkrksheetModel->get_stock_amount($warehouseId,$SendFloor);
	        $stockQty         = round($records['Qty']);
	        $stockAmount = round($records['Amount']);
	        $records = null;

	        $totalQty         = $stockQty;
			$totalAmount = $stockAmount;

	        $records = $this->CkrksheetModel->get_order_amount($warehouseId,$SendFloor);
	        $orderQty        = round($records['OrderQty']);        //订单需求数量
	        $orderAmount= round($records['Amount']); //订单需求金额
	        $M1Amount   = round($records['M1Amount']);//一个月内未有下单
	        $M3Amount   = round($records['M3Amount']);//三个月内未有下单

	        $M0Amount = $orderAmount-$M1Amount;
	        $M1Amount = $M1Amount - $M3Amount;

	        $M1Percent  = $orderAmount>0?round($M1Amount/$orderAmount*100):0;
	        $M3Percent  = $orderAmount>0?round($M3Amount/$orderAmount*100):0;
	        $M0Percent  = 100 - $M1Percent - $M3Percent;

		   $OrderPercent  = $orderAmount>0?round($orderAmount/$stockAmount*100):0;
		   $ClearPercent      = 100 - $OrderPercent;

	       $infos = array(
			        'val1'=>array(
				        array('value'=>"$M0Percent",'color'=>"#72b2d4"),
				        array('value'=>"$M1Percent",'color'=>"#dceaf4"),
				        array('value'=>"$M3Percent",'color'=>"#ff3a43")
			        ),
			        'val2'=>array(
				        array('value'=>"$OrderPercent",'color'=>"#46e346"),
				        array('value'=>"".$ClearPercent,'color'=>"clear")
			        ),
			        'title'=>array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$OrderPercent",
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



if ($row['ModuleId'] == 109 && $isPad==0) {
/*
	$dataArray[]=array(
		           'id'       =>'-999',
		           'moduleid' =>'-999',
		           'name'     =>'',
		           'ServerId' =>"0",
		           'Estate'   =>'',
		           'row'      =>"4",
		           'col'      =>"0",
		           'abs'      =>"0",
		           'build'    =>'',
		           'icon_type'=>'',
		           'showid'   =>''
		      );
*/
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
		           'showid'   =>$row['showid'].'',
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
		   case 7:
		   case 3: return '研砼HK';
		   case 4: return '行政管理';
		   case 5: return '应用';
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