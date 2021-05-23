<?php 
/*
电信-EWEN
代码、数据库共享-EWEN
参数说明
2、$SelectFrom 这个选择框放在哪个页面 1:浏览页面；2新增页面，3更新页面，4查询页面，5:不含"全部"选项
 		2、3为必选，1、4可选
 3、$WorkAddTB 过滤条件的数据表别名
 4、$RowFrom 是否来自于记录行，是则加入条件 AND A.Id='$RowFrom';

$WorkAddFrom		工作地点Id
 $SelectWidth 选择框宽度
  */
$AddWorkSelect="";
$RowFromSTR=$WorkAddFrom==""?"":" AND A.Id='$WorkAddFrom'";
$WorkAddResult = mysql_query("SELECT A.Id,A.Name,A.ColorValue FROM $DataPublic.staffworkadd A WHERE A.Estate=1 $RowFromSTR ORDER BY A.Id",$link_id);
if($WorkAddRow = mysql_fetch_array($WorkAddResult)){
	if($WorkAddFrom!=""){//来自于记录行，输出类型名称
		$ColorValue=$WorkAddRow["ColorValue"];
		$WorkAddName=$WorkAddRow["Name"];
		$WorkAdd="<spnn style='color:$ColorValue'><strong>".$WorkAddName."</strong></span>";
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$AddWorkSelect="<select name='WorkAdd' id='WorkAdd' onchange='ResetPage(this.name)' selected><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
				$AddWorkSelect="<select name=value[] id='value[]' style='width:$SelectWidth'><option selected  value=''>全部</option>";
			break;
			case 5://不含"全部"选项
			     $AddWorkSelect="<select name='WorkAdd' id='WorkAdd' onchange='ResetPage(this.name)'>";
			     if ($WorkAdd=="") $WorkAdd=$_SESSION["Login_WorkAdd"] ;
			     break;
            break;
			default://来自于新增或更新页面
				$SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
				$AddWorkSelect="<select name='WorkAdd' id='WorkAdd' style='width:$SelectWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
			break;
			}
		do{
			$theId=$WorkAddRow["Id"];
			$theName=$WorkAddRow["Name"];
			if ($theId==$WorkAdd){
				$AddWorkSelect.="<option value='$theId' selected>$theName</option>";
				if($SelectTB!=""){
					$SearchRows.=" AND $SelectTB.WorkAdd='$theId'";
					}
				}
			else{
				$AddWorkSelect.="<option value='$theId'>$theName</option>";
				}
			}while ($WorkAddRow = mysql_fetch_array($WorkAddResult));
		$AddWorkSelect.= "</select>";
		echo $AddWorkSelect;
		}
	}
?>