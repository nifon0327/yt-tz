<?php   
/*
已更新电信---yang 20120801
*/
$TypeId=8036;
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=600;
$funFrom="desk_slimshipped";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|55|序号|30|出货日期|80|PO号|80|中文名|250|Product Code|160|单位|35|售价|55|数量|50|金额|65|标准图|40|包装说明|90|出货方式|80|出货时间|80|内部单号|80";
$sumCols="8,9";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$SearchRows="";	
	$SearchRows=" and M.Estate='0' AND P.TypeId='$TypeId' ";	
	$date_Result = mysql_query("SELECT M.Date 
	FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理FILTER: revealTrans(transition=7,duration=0.5) blendTrans(duration=0.5);
$sumQty=0;
$sumAmount=0;
$sumTOrmb=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Date,
S.Id,S.Mid,S.POrderId,S.ProductId,S.Qty,S.Price,S.Type,S.YandN,
P.cName,P.eCode,P.TestStandard,U.Name AS Unit,YS.OrderPO,YS.PackRemark,YS.DeliveryDate,YS.ShipType
FROM $DataIn.ch1_shipsheet S
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
LEFT JOIN $DataIn.yw1_ordersheet YS ON YS.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
WHERE 1 AND S.Type='1' $SearchRows ORDER BY M.Date DESC,M.CompanyId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$Dir=anmaIn("../download/teststandard",$SinkOrder,$motherSTR);
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);			
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
			$td=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowN'>查看</span>";
			}
		else{
			if($TestStandard==2){
				$TestStandard="<div class='blueB' title='标准图审核中'>-</div>";
				}
			else{
				$TestStandard="&nbsp;";
				}
			}
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$sumAmount=$sumAmount+$Amount;
		$PackRemark=$myRow["PackRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$ShipType=$myRow["ShipType"];
		$Date=$myRow["Date"];
		//$Date=CountDays($Date,0);
		$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];		
		$sumQty=$sumQty+$Qty;
		////////////////////////////
		//订单状态色
		$checkColor=mysql_query("SELECT Id FROM $DataIn.cg1_stocksheet WHERE Mid='0' and (FactualQty>'0' OR AddQty>'0' ) and PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
			}
		else{//已全部下单，看领料数量
			$OrderSignColor="bgColor='#339900'";	//设默认绿色
			//领料数量不等时，黄色
			$checkLL=mysql_fetch_array(mysql_query("SELECT SUM(L.Qty) AS LQty FROM $DataIn.ck5_llsheet L WHERE L.StockId LIKE '$POrderId%'",$link_id));
			$LQty=$checkLL["LQty"];
			$checkCK=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS GQty FROM $DataIn.cg1_stocksheet G WHERE G.POrderId='$POrderId'",$link_id));
			$GQty=$checkCK["GQty"];	
			if($GQty!=$LQty){
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
		//动态读取
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏配件采购明细资料. $GQty!=$LQty ' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//0:内容	1：对齐方式		2:单元格属性		3：截取
			$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$OrderPO),
			array(0=>$cName, 		3=>"..."),
			array(0=>$eCode, 		3=>"..."),
			array(0=>$Unit, 		1=>"align='center'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$Qty, 			1=>"align='right'"),
			array(0=>$Amount, 		1=>"align='right'"),
			array(0=>$TestStandard,	1=>"align='center'"),
			array(0=>$PackRemark, 	3=>"..."),
			array(0=>$ShipType, 	3=>"..."),
			array(0=>$DeliveryDate, 1=>"align='center'",	3=>"..."),
			array(0=>$POrderId,		1=>"align='center'"),
				);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
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
ChangeWtitle($SubCompany." SLIM皮套已出明细列表");
include "../model/subprogram/read_model_menu.php";
?>