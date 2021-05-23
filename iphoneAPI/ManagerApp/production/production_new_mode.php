<?php 
//生产管理
$rowHeight=45;
switch ($info[0]) {
				case "241": {
					  //待分配
                   $onHome = 1; // include "../../desk/subtask/subtask-212.php";
					include "order_dfp_read.php";
					$Hid = 0;
					if (count($dfpList)<1) {
						$dfpList[]=array("Tag"=>"data","data"=>array("RowSet"=>array("bgColor"=>"F6F6F6"),
						"Title"=>array("Text"=>"暂无数据...","Color"=>"#858C95","FontSize"=>"14","Frame"=>"40,12,100,20"),
						),"CellID"=>"nodata","Swap"=>array("Right"=>"358FC1-分配"));
						$Hid=1;
					}
					$dfpList[count($dfpList)-1]["isLast"]="1";
					  $dataArray = array();
                    $dataArray[]=array(
			            "View"=>"List","Tag"=>"top","gpList"=>$listGroup,"load"=>"0",
			             "Id"=>"241","Timg"=>"i_dfp","List"=>$dfpList,"sbID"=>"dfp",
			             "onTap"=>array("Title"=>"待分配","Value"=>"1","value"=>"1","Tag"=>"Production2","Args"=>"","hidden"=>"0"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  分  配","Align"=>"L","Color"=>"#858C95"),
			              "Col_B"=>array("Title"=>"$OverTotalQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$totalQty"."pcs","Align"=>"R","RLColor"=>"$TITLE_GRAYCOLOR")
			          );   
					  
					  $dataArray = array_merge($dataArray,$dfpList); 
				} break;
				case "242": {
					
                    //待备料
                    $dblList;$onHome = 1;
                 include "order_dbl_read.php";
				 if (count($dblList)<1) {
						$dblList[]=array("Tag"=>"data","data"=>array("RowSet"=>array("bgColor"=>"F6F6F6"),
						"Title"=>array("Text"=>"暂无数据...","Color"=>"#858C95","FontSize"=>"14","Frame"=>"40,12,100,20"),
						),"CellID"=>"nodata");
					}
					$dblList[count($dblList)-1]["isLast"]="1";
					$dataArray = array();
                    $dataArray[]=array(
			            "View"=>"List","List"=>$dblList,
			             "Id"=>"242","Timg"=>"i_dbl","Tag"=>"top","sbID"=>"dat1","load"=>"0",
			             "onTap"=>array("Title"=>"待备料","Value"=>"1","value"=>"1","Tag"=>"Production2","Args"=>"","hidden"=>"0"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  备  料","Align"=>"L","Color"=>"#858C95"),
			             "Col_B"=>array("Title"=>"$OverTotalQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$totalQty"."pcs","Align"=>"R","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            
    
                   $dataArray = array_merge($dataArray,$dblList); 
					  
					
				} break;
				case "WLRK": {
					   $drkList;
					   $noJson = true;
                 include "order_rk_read.php";
				 if (count($drkList)<1) {
						$drkList[]=array("Tag"=>"data","data"=>array("RowSet"=>array("bgColor"=>"F6F6F6"),
						"Title"=>array("Text"=>"暂无数据...","Color"=>"#858C95","FontSize"=>"14","Frame"=>"40,12,100,20"),
						),"CellID"=>"nodata");
					}
					$drkList[count($drkList)-1]["isLast"]="1";
					$dataArray = array();
					 $dataArray[]=array(
			            "View"=>"List","List"=>$drkList,"Tag"=>"top",
			             "Id"=>"WLRK","Timg"=>"i_drk","sbID"=>"dat11","load"=>"0",
			             "onTap"=>array("Title"=>"待入库","Value"=>"1","value"=>"1","Tag"=>"Production2","Args"=>"","hidden"=>"0"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  入  库","Align"=>"L","Color"=>"#858C95"),
			             "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>$totalQty>0?"$totalQty"."pcs":"0pcs","Align"=>"R")
			          );     
				  $dataArray = array_merge($dataArray,$drkList); 
				} break;
				default : break;
}

$jsonArray = $dataArray;
 
  //   $jsonArray[]=array( "GroupName"=>"","Data"=>$dataArray); 
?>