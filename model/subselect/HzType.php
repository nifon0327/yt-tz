<?php 
/*
电信-EWEN
代码、数据库共享-EWEN
读取行政费用分类资料
参数说明
1、$SelectFrom 这个选择框放在哪个页面 1:浏览页面；2新增页面，3更新页面，4查询页面
 		2、3为必选，1、4可选
2、$cSignTB 过滤条件的数据表别名	
3、$TypeFrom 是否来自于记录行，是则加入条件 AND A.TypeId='$TypeFrom';
?未完成
*/
$theTypSelect="";
$RowFromSTR=$TypeFrom==""?"":" AND A.TypeId='$TypeFrom'";
$checkResult = mysql_query("SELECT A.TypeId,A.Name,A.Letter FROM $DataPublic.adminitype A WHERE A.Estate=1 $RowFromSTR ORDER BY A.Letter,A.Name,A.TypeId",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	if($TypeFrom!=""){//来自于记录行，输出类型名称
		$TypeName=$checkRow["Name"];
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$theTypSelect="<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$TypeIdWidth= $TypeIdWidth==""?"380px": $TypeIdWidth;
				$theTypSelect="<select name=value[] id='value[]' style='width:$TypeIdWidth'><option value='' selected>全部</option>";
			break;
			default://来自于新增或更新页面
				$TypeIdWidth= $TypeIdWidth==""?"380px": $TypeIdWidth;
				$theTypSelect="<select name='hzID' id='hzID' style='width:$TypeIdWidth'><option value='0' selected>--请选择--</option>";//$theTypSelect="<select name='TypeId' id='TypeId' style='width:$TypeIdWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
			break;
			}	
		do{
			$theId=$checkRow["TypeId"];
			$theLetter = $checkRow["Letter"];
			$theName = $checkRow["Name"];
			if($hzID==$theId){
				$theTypSelect.="<option value='$theId' selected>$theLetter $theName</option>";
				if($cSignTB!=""){
					$SearchRows.=" AND $cSignTB.TypeId='$theId'";
					}
				}
			else{
				$theTypSelect.="<option value='$theId'>$theLetter $theName</option>";					
				}
			}while ($checkRow = mysql_fetch_array($checkResult));
		$theTypSelect.="</select>&nbsp;";
		echo $theTypSelect;
		}
	}
?>