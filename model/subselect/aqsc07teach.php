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
$TeachSelect="";
$RowFromSTR=$TeachFrom==""?"":" AND A.Id='$TeachFrom'";
$TeachResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.aqsc07_teach A WHERE A.Estate=1 $RowFromSTR ORDER BY A.Id",$link_id);
if($TeachRow = mysql_fetch_array($TeachResult)){
	if($TeachFrom!=""){//来自于记录行，输出类型名称
		$TeachName=$TeachRow["Name"];
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$TeachSelect="<select name='TeachId' id='TeachId' onchange='ResetPage(this.name)'><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$TeachWidth= $TeachWidth==""?"380px": $TeachWidth;
				$TeachSelect="<select name=value[] id='value[]' style='width:$TeachWidth'><option value='' selected>全部</option>";
			break;
			default://来自于新增或更新页面
				$TeachWidth= $TeachWidth==""?"380px": $TeachWidth;
				$TeachSelect="<select name='TeachId' id='TeachId' style='width:$TeachWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
			break;
			}	
		do{
			$TeachName = $TeachRow["Name"];
			$theId=$TeachRow["Id"];
			if($TeachId==$theId){
				$TeachSelect.="<option value='$theId' selected>$TeachName</option>";
				if($cSignTB!=""){
					$SearchRows.=" AND $cSignTB.Teach='$theId'";
					}
				}
			else{
				$TeachSelect.="<option value='$theId'>$TeachName</option>";					
				}
			}while ($TeachRow = mysql_fetch_array($TeachResult));
		$TeachSelect.="</select>&nbsp;";
		echo $TeachSelect;
		}
	}
?>