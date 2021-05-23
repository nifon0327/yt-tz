<?php 
include "../model/modelhead.php";		
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc11";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 安全生产证书");
$Th_Col="选项|45|序号|45|证书类型|80|证书说明|300|证书|40|有效期至|80|状态|40|更新日期|80|操作员|60";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";

	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Caption,A.Attached,A.EndDate,A.Date,A.Estate,A.Locks,A.Operator,B.Name FROM $DataPublic.aqsc11 A LEFT JOIN $DataPublic.aqsc11_type B ON B.Id=A.TypeId WHERE 1 $SearchRows ORDER BY A.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Name=$myRow["Name"];
		$Caption=$myRow["Caption"];
		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/pdf.gif'/></a>";
			}
		else{
			$Attached="-";
			}
		$EndDate=$myRow["EndDate"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<sapn class='greenB'>可用</sapn>":"<sapn class='redB'>禁用</sapn>";;
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Name),
			array(0=>$Caption),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$EndDate,1=>"align='center'"),		
			array(0=>$Estate,1=>"align='center'"),	
			array(0=>$Date,1=>"align='center'"),	
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>