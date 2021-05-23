<?php 
/*
参数说明
2、$SelectFrom 这个选择框放在哪个页面 1:浏览页面；2新增页面，3更新页面，4查询页面，5:不含"全部"选项
 		2、3为必选，1、4可选
 3、$WorkAddTB 过滤条件的数据表别名
 4、$RowFrom 是否来自于记录行，是则加入条件 AND A.Id='$RowFrom';

$WorkAddFrom		工作地点Id
$SelectWidth 选择框宽度
  */
$AddFloorSelect="";
$RowFromSTR=$AddFloorSelect==""?"":" AND A.Id='$floorAdd'";
$floorAddResult = mysql_query("SELECT A.Id,A.Floor FROM $DataPublic.attendance_floor A WHERE A.Estate=1 $RowFromSTR ORDER BY A.Id",$link_id);

if($floorAddRow = mysql_fetch_array($floorAddResult)){
	if($AddFloorSelect!=""){//来自于记录行，输出类型名称
		$floorAddName=$floorAddRow["Floor"];
		$floorAdd="<spnn <strong>".$floorAddName."</strong></span>";
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$AddFloorSelect="<select name='floorAdd' id='floorAdd' onchange='ResetPage(this.name)' selected><option value=''>全部楼层</option>";
			break;
			case 4://来自于查询页面
				$SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
				$AddFloorSelect="<select name=value[] id='value[]' style='width:$SelectWidth'><option selected  value=''>全部楼层</option>";
			break;
			case 5://不含"全部"选项
			     $AddFloorSelect="<select name='floorAdd' id='floorAdd' onchange='ResetPage(this.name)'>";
			     if ($FloorAdd=="") $FloorAdd=$_SESSION["Floor"] ;
			     break;
            break;
			default://来自于新增或更新页面
				$SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
				$AddFloorSelect="<select name='floorAdd' id='floorAdd' style='width:$SelectWidth'><option value='0' selected>--请选择--</option>";
			break;
			}
		do{
			$theId=$floorAddRow["Id"];
			$theName=$floorAddRow["Floor"];
			if ($theId==$floorAdd){
				$AddFloorSelect.="<option value='$theId' selected>$theName</option>";
				if($SelectTB!=""){
					$SearchRows.=" AND $SelectTB.AttendanceFloor='$theId'";
					}
				}
			else{
				$AddFloorSelect.="<option value='$theId'>$theName</option>";
				}
			}while ($floorAddRow = mysql_fetch_array($floorAddResult));
		$AddFloorSelect.= "</select>&nbsp;";
		echo $AddFloorSelect;
		}
	}
?>