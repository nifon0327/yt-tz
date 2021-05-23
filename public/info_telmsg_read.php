<?php 
//电信-ZX
/*
$DataPublic.info2_telmsg
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 来电留言记录列表");
$funFrom="info_telmsg";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|留言对象|60|内容|450|状态|60|登记时间|80|登记人|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,20";

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
$mySql="SELECT I.Id,I.Remark,I.Date,I.Estate,I.Operator,M.Name 
FROM $DataPublic.info2_telmsg I
LEFT JOIN $DataPublic.staffmain M ON M.Number=I.Number 
WHERE 1 $SearchRows
order by I.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Remark=$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='redB'>未处理</div>":"<div class='greenB'>已处理</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=1;
		$ValueArray=array(
			array(0=>$Name,
				1=>"align='center'"),
			array(0=>$Remark,
				1=>"align='center'"),
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
