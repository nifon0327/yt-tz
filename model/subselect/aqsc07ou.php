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
$OUSelect="";
$RowFromSTR=$OUFrom==""?"":" AND A.Id='$OUFrom'";
$OUResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.aqsc07_ou A WHERE A.Estate=1 $RowFromSTR ORDER BY A.Id",$link_id);
if($OURow = mysql_fetch_array($OUResult)){
	if($OUFrom!=""){//来自于记录行，输出类型名称
		$OUName=$OURow["Name"];
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$OUSelect="<select name='OUId' id='OUId' onchange='ResetPage(this.name)'><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$OUWidth= $OUWidth==""?"380px": $OUWidth;
				$OUSelect="<select name=value[] id='value[]' style='width:$OUWidth'><option value='' selected>全部</option>";
			break;
			default://来自于新增或更新页面
				$OUWidth= $OUWidth==""?"380px": $OUWidth;
				$OUSelect="<select name='OUId' id='OUId' style='width:$OUWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
			break;
			}	
		do{
			$OUName = $OURow["Name"];
			$theId=$OURow["Id"];
			if($OUId==$theId){
				$OUSelect.="<option value='$theId' selected>$OUName</option>";
				if($cSignTB!=""){
					$SearchRows.=" AND $cSignTB.OU='$theId'";
					}
				}
			else{
				$OUSelect.="<option value='$theId'>$OUName</option>";					
				}
			}while ($OURow = mysql_fetch_array($OUResult));
		$OUSelect.="</select>&nbsp;";
		echo $OUSelect;
		}
	}
?>