<?php 
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 车间生产历史记录");
$funFrom="sc_scHistory";
$nowWebPage=$funFrom."_read";
$sumCols="7,8,9";
$Th_Col="选项|45|序号|45|日期|70|订单PO|100|订单流水号|100|产品名称|300|加工类型|80|加工总数|80|已经登记|80|本次登记|80|备注|50|登记人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	//日期
	$chooseDay=$chooseDay==""?date("Y-m-d"):$chooseDay;
	echo"<input name='chooseDay' type='text' id='chooseDay' size='12' maxlength='10' value='$chooseDay' onchange='document.form1.submit()'  onFocus='WdatePicker()'/>&nbsp;";
	$SearchRows="AND DATE_FORMAT(D.Date,'%Y-%m-%d')='$chooseDay'";
	//生产分类

	$typeResult = mysql_query("SELECT T.TypeId,T.TypeName 
    FROM $DataIn.sc1_cjtj D
    LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId  WHERE 1  $SearchRows GROUP BY T.TypeId",$link_id);
	if ($typeRow = mysql_fetch_array($typeResult)){
		echo"<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";
		do{
			$typeValue=$typeRow["TypeId"];
			$TypeName=$typeRow["TypeName"];
			$TypeId=$TypeId==""?$typeValue:$TypeId;
			if($TypeId==$typeValue){
				echo"<option value='$typeValue' selected>$TypeName</option>";
				$SearchRows.="AND D.TypeId='$typeValue'";
				}
			else{
				echo"<option value='$typeValue'>$TypeName</option>";
				}
			}while($typeRow = mysql_fetch_array($typeResult));
		echo"</select>&nbsp;";
		}	
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

$weekDay=date("w",strtotime($chooseDay));	
$DateType=($weekDay==6 || $weekDay==0)?"X":"G";
$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$chooseDay'",$link_id);
if($holidayRow = mysql_fetch_array($holidayResult)){
	$jbTimes=$holidayRow["jbTimes"];
	switch($holidayRow["Type"]){
	case 0:		$DateType="W";		break;
	case 1:		$DateType="Y";		break;
	case 2:		$DateType="F";		break;
		}
	}

$rqddResult = mysql_query("SELECT Id,XDate FROM $DataIn.kqrqdd WHERE (GDate='$chooseDay' OR XDate='$chooseDay') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){
			$weekDayTempX=date("w",strtotime($rqddRow["XDate"]));		//调动的休息日
			$weekDayTempG=date("w",strtotime($rqddRow["GDate"]));	//调动的工作日
			$DateType=$DateType=="X"?"G":"X";
		 }

$overTimehourSql = mysql_query("Select * From $DataIn.kqovertime Where otDate = '$chooseDay'");
$overTimehourResult = mysql_fetch_assoc($overTimehourSql);
$overTimeHours = 0;
switch($DateType)
{
	case "G":
	{
		$overTimeHours = $overTimehourResult["workday"];
	}
	break;
	case "X":
	{
		$overTimeHours = $overTimehourResult["weekday"];
	}
	case "F":
	{
		$overTimeHours = $overTimehourResult["weekday"];
	}
	break;
}
		
if($overTimeHours == 0 && ($DateType == "X" || $DateType == "F"))
{
	noRowInfo($tableWidth);
}
else
{

if($weekDay == 1)
{
	$saturday = date("Y-m-d",strtotime("$chooseDay - 2 days"));
	$sunday = date("Y-m-d",strtotime("$chooseDay - 1 days"));
	$weekendSql = mysql_query("Select * From $DataIn.kqovertime Where otDate = '$saturday'");
	$weekResult = mysql_fetch_assoc($weekendSql);
	if($weekResult["weekday"] > 0)
	{
		$SearchRows = "AND DATE_FORMAT(D.Date,'%Y-%m-%d') in ($sunday, $chooseDay)";
	}
	else
	{
		$SearchRows = "AND DATE_FORMAT(D.Date,'%Y-%m-%d') in ($sunday, $chooseDay, $saturday)";
	}
	
}

$mySql="
SELECT S.OrderPO,S.Estate,S.Qty AS OrderQty,
P.cName,P.TestStandard,
D.Id,D.POrderId,SUM(D.Qty) as Qty,D.Remark,D.Leader,D.TypeId,D.Date,
M.Name,T.TypeName
FROM $DataIn.sc1_cjtj D
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.staffmain M ON M.Number=D.Leader
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
WHERE 1 $SearchRows  Group by D.POrderId ORDER BY D.Date DESC";
$SumQty=0;
$LastQty=0;
$ThisQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Date = substr($Date, 0, 10);
		$OrderPO=$myRow["OrderPO"];
		$cName=$myRow["cName"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$TypeName=substr($myRow["TypeName"],0,6);
		$POrderId=$myRow["POrderId"];
		$Qty=$myRow["Qty"];
		$OrderQty=$myRow["OrderQty"];
		$TypeId=$myRow["TypeId"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Estate=$myRow["Estate"];
		$LockRemark="";
		$OrderSignColor="bgColor='#FFCC00'";
		if($Estate==0 || $Estate==4){
			$LockRemark="已出货或已生成出货单";
			$OrderSignColor="bgColor='#339900'";
			}
		$Operator=$myRow["Leader"];
		include "../model/subprogram/staffname.php";
		//本类登记总数
		$Locks=$myRow["Locks"];
		$cjtjOverSql=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS cjtjOverQty FROM $DataIn.sc1_cjtj WHERE 1 AND TypeId='$TypeId' AND POrderId='$POrderId'",$link_id));
		$cjtjOverQty=$cjtjOverSql["cjtjOverQty"];
        $LastQty=$LastQty+$cjtjOverQty;
        $SumQty=$SumQty+$OrderQty;
        $ThisQty=$ThisQty+$Qty;
		if($cjtjOverQty==$OrderQty){
			$OrderQtyStr="<div class='greenB'>$OrderQty</div>";
			}
		else{
			$LockRemark="";//有错就解除锁定
			$Locks=1;
			if($cjtjOverQty<$OrderQty){
				if($Estate==0){
					$OrderQtyStr="<div class='redB'>◆$OrderQty</div>";
					}
				else{
					$OrderQtyStr="<div class='yellowB'>$OrderQty</div>";
					}
				}
			else{
				$OrderQtyStr="<div class='redB'>◆$OrderQty</div>";
				$UpdateStr=1;
				}
			}
		
		$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$OrderPO),
			array(0=>$POrderId,			1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$TypeName,	1=>"align='center'"),
			array(0=>$OrderQtyStr,	1=>"align='center'"),		//加工数量
			array(0=>$cjtjOverQty,	1=>"align='center'"),	//已经登记
			array(0=>$Qty,			1=>"align='center'"),	//本次登记
			array(0=>$Remark,		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
        $m=1;
        $ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
                array(0=>"&nbsp;"	),
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
}
//步骤7：
echo '</div>';
//$myResult = mysql_query($mySql,$link_id);
//$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
