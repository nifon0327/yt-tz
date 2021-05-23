<?php
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";

//$path = $_SERVER["DOCUMENT_ROOT"];
//include_once("$path/model/subprogram/outputValueFunction.php");
//include_once($path.'/factoryCheck/checkSkip.php');

$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;
$tableMenuS=600;
ChangeWtitle("$SubCompany 加工工序登记记录");
$funFrom="sc_gxtj";
$nowWebPage=$funFrom."_read";
$sumCols="7,8,9";
$Th_Col="选项|45|序号|45|日期|70|工单流水号|90|生产单位|80|加工配件名称|300|工序类型|100|加工总数|80|已经登记|80|本次登记|80|备注|50|登记人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	//日期
	$chooseDay=$chooseDay==""?date("Y-m-d"):$chooseDay;
	echo"<input name='chooseDay' type='text' id='chooseDay' size='12' maxlength='10' value='$chooseDay'  onfocus='WdatePicker({onpicked:function(dp){ResetPage(this.name);}})' />&nbsp;";
	$SearchRows="AND D.Date='$chooseDay'";

	echo"<select name='WorkshopId' id='WorkshopId' onchange='ResetPage(this.name)'>";
	$pTPID = mysql_query("select W.Id,W.Name 
	from  $DataIn.sc1_gxtj D 
	LEFT JOIN yw1_scsheet S ON S.sPOrderId=D.sPOrderId
	LEFT JOIN  workshopdata W ON W.Id=S.WorkShopId
	WHERE 1 $SearchRows GROUP BY W.Id");
	if ($pTypeId=="") {
		echo"<option value='-1' selected>生产单位</option>";
		$pTypeId = -1;
	} else {
		echo"<option value='-1' >全部</option>";
	}

while ($pTyNames = mysql_fetch_array($pTPID)) {
	$ptypeValue=$pTyNames["Id"];
	$pTypeName=$pTyNames["Name"];
	if($WorkshopId==$ptypeValue){
			echo"<option value='$ptypeValue' selected>$pTypeName</option>";
			if ($pTypeId != -1) {
			 $SearchRows.="AND S.WorkshopId='$ptypeValue'";
	 }
	}
	else{
		echo"<option value='$ptypeValue'>$pTypeName</option>";

	}
	//$SearchRows="AND P.TypeId='$chooseDay'";
}
echo"</select>&nbsp;";
}
echo"<input name='FromDesktj' type='hidden' id='FromDesktj' value='ResetPage(this.name)'/>";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

$mySql="
SELECT D.Id,D.POrderId,D.sPOrderId,D.Qty,D.Remark,D.Leader,D.ProcessId,D.Date,D.Locks,
T.ProcessName,OD.StuffId,OD.StuffCname,OD.Picture,W.Name AS WorkShopName,S.Estate,S.StockId,S.Qty AS OrderQty   
FROM sc1_gxtj D
LEFT JOIN yw1_scsheet S ON S.sPOrderId=D.sPOrderId
LEFT JOIN  workshopdata W ON W.Id=S.WorkShopId 
LEFT JOIN cg1_stocksheet G ON G.StockId=S.mStockId 
LEFT JOIN stuffdata OD ON OD.StuffId=G.StuffId  
LEFT JOIN staffmain M ON M.Number=D.Leader
LEFT JOIN process_data T ON T.ProcessId=D.ProcessId 
WHERE 1 $SearchRows ORDER BY D.Date DESC";
//echo $mySql;
$SumQty=0;
$LastQty=0;
$ThisQty=0;
$sumPriceValue = 0;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$StockId=$myRow["StockId"];
		$ProcessId=$myRow["ProcessId"];
        $WorkShopName = $myRow["WorkShopName"];

        $StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];

		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片

		$ProcessName=$myRow["ProcessName"];
		$sPOrderId=$myRow["sPOrderId"];
		$Qty=$myRow["Qty"];
		$OrderQty=$myRow["OrderQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Estate=$myRow["Estate"];
		$LockRemark="";
		$OrderSignColor="bgColor='#FFCC00'";
		if($Estate==0){
			$LockRemark="已完成生产";
			$OrderSignColor="bgColor='#339900'";
			}
		$Operator=$myRow["Leader"];
		include "../model/subprogram/staffname.php";

		 //工序登记数
	   $BassLossSql=mysql_fetch_array(mysql_query("SELECT D.BassLoss 
	                   FROM $DataIn.cg1_processsheet  S 
	                    LEFT JOIN $DataIn.process_data  D ON D.ProcessId=S.ProcessId 
				       WHERE S.StockId='$StockId'  AND S.ProcessId='$ProcessId' ",$link_id));
		$BassLoss=$BassLossSql["BassLoss"]==""?0:$BassLossSql["BassLoss"];
		$GxOrderQty=ceil($OrderQty+$OrderQty*$BassLoss);

		//本类登记总数
		$Locks=$myRow["Locks"];
		$pTypeName = $myRow["tyName"];
		$cjtjOverSql=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS cjtjOverQty FROM $DataIn.sc1_gxtj WHERE 1 AND ProcessId='$ProcessId' AND sPOrderId='$sPOrderId'",$link_id));
		$cjtjOverQty=$cjtjOverSql["cjtjOverQty"];
        $LastQty=$LastQty+$cjtjOverQty;
        $SumQty=$SumQty+$GxOrderQty;
        $ThisQty=$ThisQty+$Qty;

		if($cjtjOverQty>=$GxOrderQty){
			$GxOrderQty="<div class='greenB'>$GxOrderQty</div>";
			}
		else{
		   $LockRemark="";$Locks=1;
			$GxOrderQty="<div class='yellowB'>$GxOrderQty</div>";
		}


		$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$sPOrderId,	1=>"align='center'"),
			array(0=>$WorkShopName,	1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$ProcessName,	1=>"align='center'"),
			array(0=>$GxOrderQty,	1=>"align='center'"),		//加工数量
			array(0=>$cjtjOverQty,	1=>"align='center'"),	//已经登记
			array(0=>$Qty,			1=>"align='center'"),	//本次登记
			array(0=>$Remark,		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
        $m=1;
        $SumQty  = number_format($SumQty);
        $LastQty = number_format($LastQty);
        $ThisQty = number_format($ThisQty);

        $ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
                array(0=>"&nbsp;"),
				 array(0=>"&nbsp;",	1=>"align='right'"	),
                array(0=>"$SumQty",	1=>"align='right'"),
                array(0=>"$LastQty",	1=>"align='right'"),
                array(0=>"$ThisQty",	1=>"align='right'"),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	)
              );
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
//$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
