<?php 
/*
电信-EWEN
代码、数据库共享-EWEN
参数说明
1、$SelectFrom 这个选择框放在哪个页面 1:浏览页面；2新增页面，3更新页面，4查询页面
 		2、3为必选，1、4可选
2、$cSignTB 过滤条件的数据表别名	
3、$RowFrom 是否来自于记录行，是则加入条件 AND A.Id='$RowFrom';
*/
$ObjectSelect="";
$RowFromSTR=$ObjectFrom==""?"":" AND A.Id='$ObjectFrom'";
$ObjectResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.aqsc07_object A WHERE A.Estate=1 $RowFromSTR ORDER BY A.Id",$link_id);
if($ObjectRow = mysql_fetch_array($ObjectResult)){
	if($ObjectFrom!=""){//来自于记录行，输出类型名称
		$ObjectName=$ObjectRow["Name"];
		
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$ObjectSelect="<select name='ObjectId' id='ObjectId' onchange='ResetPage(this.name)'><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$ObjectWidth= $ObjectWidth==""?"380px": $ObjectWidth;
				$ObjectSelect="<select name=value[] id='value[]' style='width:$ObjectWidth'><option value='' selected>全部</option>";
			break;
			default://来自于新增或更新页面
				$ObjectWidth= $ObjectWidth==""?"380px": $ObjectWidth;
				$ObjectSelect="<select name='ObjectId' id='ObjectId' style='width:$ObjectWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
			break;
			}	
		do{
			$ObjectName = $ObjectRow["Name"];
			$theId=$ObjectRow["Id"];
			if($ObjectId==$theId){
				$ObjectSelect.="<option value='$theId' selected>$ObjectName</option>";
				if($cSignTB!=""){
					$SearchRows.=" AND $cSignTB.Object='$theId'";
					}
				}
			else{
				$ObjectSelect.="<option value='$theId'>$ObjectName</option>";					
				}
			}while ($ObjectRow = mysql_fetch_array($ObjectResult));
		$ObjectSelect.="</select>&nbsp;";
		echo $ObjectSelect;
		}
	}
?>