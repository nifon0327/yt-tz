<?php 
/*
//电信-EWEN 2012－11－24
 读取总务物品分类的主分类
适用于add,upodate,selecdt页面
 */
$mainTypeResult = mysql_query("SELECT * FROM $DataPublic.nonbom1_maintype ORDER BY Id",$link_id);
if($mainTypeRow = mysql_fetch_array($mainTypeResult)){
	$mainTypeWidth= $mainTypeWidth==""?"380px": $mainTypeWidth;
	if($SelectFrom==""){
    	echo"<select name='mainType' id='mainType' style='width:$mainTypeWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
		}
	else{//来自于查询页面
		echo"<select name=value[] id='value[]' style='width:$mainTypeWidth'><option selected  value=''>全部</option>";
		}
	do{
		$theId=$mainTypeRow["Id"];
        $theName=$mainTypeRow["Name"];
        if ($theId==$mainType){
        	echo "<option value='$theId' selected>$theName</option>";
            }
		else{
        	echo "<option value='$theId'>$theName</option>";
			}
        }while ($mainTypeRow = mysql_fetch_array($mainTypeResult));
    echo "</select>&nbsp;";
	}
?>