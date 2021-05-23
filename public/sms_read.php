<?php 
//$DataIn.smsdata / $DataPublic.staffmain 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 短消息列表");
$funFrom="sms";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|短消息内容|520|状态|60|日期|120|发信人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,4,20";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Note,S.Estate,S.Date,S.Operator FROM $DataPublic.smsdata S WHERE 1 AND S.Number='$Login_P_Number' $SearchRows ORDER BY S.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];		
		$Note=$myRow["Note"];
		$Estate=$myRow["Estate"]==1?"<div class='redB'>未处理</div>":"<div class='greenB'>已处理</div>";		
		$Date=$myRow["Date"];		
		$Operator=$myRow["Operator"];
		$P_Result = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Operator Limit 1",$link_id);
		if($P_Row = mysql_fetch_array($P_Result)){
			$Operator=$P_Row["Name"];
			}
		else{
			$Operator="系统";
			}
		$Locks=1;
		$ValueArray=array(
			array(0=>$Note,					
					 3=>"..."),
			array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
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
