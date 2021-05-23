<?php 
/*
电信-EWEN
代码、数据库共享-EWEN
读取公司标识cSign
参数说明
1、$SharingShow 是否显示 “共享”,Y显示，其他值不显示
2、$SelectFrom 这个选择框放在哪个页面 1:浏览页面；2新增页面，3更新页面，4查询页面，5:不含"全部"选项
 		2、3为必选，1、4可选
 3、$cSignTB 过滤条件的数据表别名
 4、$RowFrom 是否来自于记录行，是则加入条件 AND A.Id='$RowFrom';
  */
$SharingShowSTR=$SharingShow=="Y"?"":" AND A.cSign!=0";
$cSignSelect="";
$cSignFromSTR=$cSignFrom==""?"":" AND A.cSign='$cSignFrom'";
if($EstateChoose=="9"){$EstateStr="";}//来自于固定资产
else {   $EstateStr=" AND A.Estate=1";}
//echo "cSignFromSTR:$cSignFromSTR";
$cSignResult = mysql_query("SELECT A.cSign,A.CShortName,A.Db,ColorValue FROM $DataPublic.companys_group  A WHERE  1 $EstateStr  $SharingShowSTR $cSignFromSTR ORDER BY A.Id",$link_id);
if($cSignRow = mysql_fetch_array($cSignResult)){
	if($cSignFrom!=""){//来自于记录行，输出类型名称
		$ColorValue=$cSignRow["ColorValue"];
		$CShortName=$cSignRow["CShortName"];
		$cSign="<spnn style='color:$ColorValue'><strong>".$CShortName."</strong></span>";
		}
	else{//输出选择框
		switch($SelectFrom){
			case 1://来自于浏览页面
				$cSignSelect="<select name='cSign' id='cSign' onchange='ResetPage(this.name)'><option value=''>全部</option>";
			break;
			case 4://来自于查询页面
				$cSignWidth= $cSignWidth==""?"380px": $cSignWidth;
				$cSignSelect="<select name=value[] id='value[]' style='width:$cSignWidth'><option selected  value=''>全部</option>";
			break;
			case 5://不含"全部"选项
			     $cSignSelect="<select name='cSign' id='cSign' onchange='ResetPage(this.name)'>";
			     if ($cSign=="") $cSign=$_SESSION["Login_cSign"] ;
			     break;
            break;
			default://来自于新增或更新页面
				$cSignWidth= $cSignWidth==""?"380px": $cSignWidth;
				$cSignSelect="<select name='cSign' id='cSign' style='width:$cSignWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
			break;
			}
		do{
			$theId=$cSignRow["cSign"];
			$theName=$cSignRow["CShortName"];
			$dbName=$cSignRow["Db"];
			if ($theId==$cSign){
				$DataIn=$dbName;
				$cSignSelect.="<option value='$theId' selected>$theName</option>";
				if($cSignTB!=""){
					$SearchRows.=" AND $cSignTB.cSign='$theId'";
					}
				}
			else{
				$cSignSelect.="<option value='$theId'>$theName</option>";
				}
			}while ($cSignRow = mysql_fetch_array($cSignResult));
		$cSignSelect.= "</select>&nbsp;";
		echo $cSignSelect;
		}
	}
?>