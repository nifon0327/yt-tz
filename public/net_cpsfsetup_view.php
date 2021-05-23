<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_softwarelist
$DataPublic.net_cpsfdata
$DataPublic.net_cpcheckdiary
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 已装软件列表");
$funFrom="net_cpsfsetup";
$nowWebPage=$funFrom."_view";
$Th_Col="选项|50|序号|40|已装软件/驱动名称|350|类型|100|许可状态|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
$CheckRow=mysql_fetch_array(mysql_query("SELECT CpName,Remark FROM $DataPublic.net_cpdata WHERE Id='$hdId'",$link_id));
$CpName=$CheckRow["CpName"];
$Remark=$CheckRow["Remark"];
//步骤5：
echo "电脑名称:".$CpName;
include "../model/subprogram/read_model_5.php";
echo "<input name='hdId' type='hidden' id='hdId' value='$hdId'>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Name,S.Sign,S.Estate,S.Locks,T.Name AS Type
 FROM $DataPublic.net_cpsfdata F,
 $DataPublic.net_softwarelist S,
 $DataPublic.net_softwaretype T
 WHERE 1 AND F.hdId='$hdId' 
 AND F.Sid=S.Id
 AND T.Id=S.Type 
 ORDER BY S.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Name=$myRow["Name"];
		$Type=$myRow["Type"];
		$Sign=$myRow["Sign"]==1?"<span class='greenB'>√</span>":"<span class='yellowB'>○</span>";
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Name,),
			array(0=>$Type,		1=>"align='center'"),
			array(0=>$Sign,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		//备注
	if($Remark!=""){
		echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
		echo"<tr $Fun_bgcolor><td class='A0111' height='25'>初始维护备注：</td></tr>";
		echo"<tr><td class='A0111'>$Remark &nbsp;</td></tr></table>";
		}
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
