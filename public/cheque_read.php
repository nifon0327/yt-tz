<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.adminitype
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 支票登记");
$funFrom="cheque";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|支票号|80|金额|60|供应商|100|备注|300|票据|40|状态|40|时间|80|操作|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

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
$mySql="SELECT * FROM $DataIn.cheque C WHERE 1 $SearchRows ORDER BY C.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$ChequeNum=$myRow["ChequeNum"];
		$Receiver=$myRow["Receiver"];		
		$Amount=$myRow["Amount"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/cheque/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="C".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$ChequeNum, 	1=>"align='center'"),
            array(0=>$Amount,	1=>"align='center'"),
		  	array(0=>$Receiver,		1=>"align='center'"),
			array(0=>$Remark,		1=>"align='center'"),
			array(0=>$Bill,		1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	 	1=>"align='center'"),
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