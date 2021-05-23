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
$RowFromSTR=$TypeFrom==""?"":" AND A.Id='$TypeFrom'";
$TypeResult = mysql_query("SELECT A.Id,A.Name, A.Color FROM $DataPublic.tasktype A WHERE A.Estate=1 $RowFromSTR ORDER BY A.Id",$link_id);
if($TypeRow = mysql_fetch_array($TypeResult)){
	if($TypeFrom!=""){//来自于记录行，输出类型名称
		$TypeColor = $TypeRow["Color"];
		$TypeName=$TypeRow["Name"];
		$TypeName="<spnn style='color:$TypeColor'>".$TypeName."</span>";
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$TypSelect="<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$TypeIdWidth= $TypeIdWidth==""?"380px": $TypeIdWidth;
				$TypSelect="<select name=value[] id='value[]' style='width:$TypeIdWidth'><option value='' selected>全部</option>";
			break;
			default://来自于新增或更新页面
				$TypeIdWidth= $TypeIdWidth==""?"380px": $TypeIdWidth;
				$TypSelect="<select name='TypeId' id='TypeId' style='width:$TypeIdWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
			break;
			}	
		do{
			$TypeName = $TypeRow["Name"];
			$TypeColor = $TypeRow["Color"];
			$theId=$TypeRow["Id"];
			if($TypeId==$theId){
				$TypSelect.="<option value='$theId' style= 'color: $TypeColor;font-weight: bold' selected>$TypeName</option>";
				if($cSignTB!=""){
					$SearchRows.=" AND $cSignTB.TypeId='$theId'";
					}
				}
			else{
				$TypSelect.="<option value='$theId' style= 'color: $TypeColor;font-weight: bold'>$TypeName</option>";					
				}
			}while ($TypeRow = mysql_fetch_array($TypeResult));
		$TypSelect.="</select>&nbsp;";
		echo $TypSelect;
		}
	}
?>