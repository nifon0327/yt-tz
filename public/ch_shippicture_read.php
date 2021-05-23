<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 出货附加图片列表");
$funFrom="ch_shippicture";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|45|序号|45|出货日期|80|出货流水号|80|Invoice编号|120|附加文件说明|150|附加文件名称|150|更新日期|80|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3,4,7,8";
$Pagination=1;
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
$mySql="SELECT T.Id,T.Remark,T.Picture,T.Date,T.Locks,T.Operator,M.Number,M.InvoiceNO,M.Date AS ShipDate
FROM $DataIn.ch7_shippicture T
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=T.Mid
WHERE 1 $SearchRows
ORDER BY M.Date DESC,T.Picture DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Picture=$myRow["Picture"];
		$f1=anmaIn($Picture,$SinkOrder,$motherSTR);
		$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
		$Picture=$Picture==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$Picture</span>";

		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$Number=$myRow["Number"];
		include "../model/subprogram/staffname.php";
		$InvoiceNO=$myRow["InvoiceNO"];
		$ShipDate=$myRow["ShipDate"];		
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$ShipDate,	1=>"align='center'"),
			array(0=>$Number,	1=>"align='center'"),
			array(0=>$InvoiceNO),
			array(0=>$Remark),
			array(0=>$Picture,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
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
