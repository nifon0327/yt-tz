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
$TypSelect="";
//$RowFromSTR=$FirstId==""?"":" AND B.FirstId='$FirstId'";
$RowFromSTR=$funFrom=="adminitype"?" AND A.ExpenseSign=1 ":"";

$TypeResult = mysql_query("SELECT  A.FirstId,A.ListName
FROM acfirsttype  A 
WHERE LENGTH(A.FirstId)>4 $RowFromSTR",$link_id);

if($TypeRow = mysql_fetch_array($TypeResult)){
	if($TypeFrom!=""){//来自于记录行，输出类型名称
		$FirstName=$TypeRow["ListName"];
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$TypSelect="<select name='FirstId' id='FirstId' onchange='ResetPage(this.name)'><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$TypeIdWidth= $TypeIdWidth==""?"380px": $TypeIdWidth;
				$TypSelect="<select name=value[] id='value[]' style='width:$TypeIdWidth'><option value='' selected>全部</option>";
			break;
			default://来自于新增或更新页面
				$TypeIdWidth= $TypeIdWidth==""?"380px": $TypeIdWidth;
				$TypSelect="<select name='FirstId' id='FirstId' style='width:$TypeIdWidth' dataType='Require' msg='未选择' ><option value='' selected>--请选择--</option>";
			break;
			}	
		do{
			$FirstName = $TypeRow["ListName"];
			$theId=$TypeRow["FirstId"];
			if($FirstId==$theId){
				$TypSelect.="<option value='$theId' style= 'font-weight: bold' selected>$FirstName</option>";
				if($cSignTB!=""){
					$SearchRows.=" AND $cSignTB.TypeId='$theId'";
					}
				}
			else{
				$TypSelect.="<option value='$theId' style= 'font-weight: bold'>$FirstName</option>";					
				}
			}while ($TypeRow = mysql_fetch_array($TypeResult));
		$TypSelect.="</select>&nbsp;";
		echo $TypSelect;
		}
	}
?>