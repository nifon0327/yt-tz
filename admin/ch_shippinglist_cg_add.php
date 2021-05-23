<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=600;
ChangeWtitle("$SubCompany CG待出订单列表");
$funFrom="ch_shippinglist_cg";
$From=$From==""?"add":$From;
$sumCols="10,11";			//求和列,需处理
$Th_Col="选项|60|序号|40|PO#|80|ItemNo|80|PO No|80|订单流水号|80|产品Id|50|中文名|220|Product Code/Description|220|售价|60|订单数量|60|金额|60|订单日期|70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

$ActioToS="3,97";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项 200907300601
//客户
$SearchRows=" and S.Estate='2' AND S.scFrom=0 ";//CG订单
//$SearchRows=" and S.Estate='2' AND S.scFrom=0";
include "subprogram/ch_amountshow.php";  //add by zx 20101116 统计相应的金额！ 国内报关的金额，MC 为Cel, DP为MCA  //输出  $MaxStr
$clientResult = mysql_query("
	SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	WHERE 1 $SearchRows  AND M.CompanyId in (1049,1003)
	GROUP BY M.CompanyId",$link_id);
if($clientRow = mysql_fetch_array($clientResult)) {
	echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"ch_shippinglist_cg_add\")'>";
	do{			
		$thisCompanyId=$clientRow["CompanyId"];
		$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
		$Forshort=$clientRow["Forshort"];
		if($CompanyId==$thisCompanyId){
			echo"<option value='$thisCompanyId' selected>$Forshort</option>";
			$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
			}
		else{
			echo"<option value='$thisCompanyId'>$Forshort</option>";					
			}
		}while ($clientRow = mysql_fetch_array($clientResult));
	echo"</select>&nbsp;";
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr  $MaxStr ";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
	SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,S.Id,S.OrderPO,S.POrderId,
	S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.TestStandard,O.PONo,O.ItemNo
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.cg_order O ON O.POrderId=S.POrderId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId WHERE 1 $SearchRows";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;		
		$LockRemark="";
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];	
		$Id=$myRow["Id"];
		$ItemNo=$myRow["ItemNo"]==""?"&nbsp;":$myRow["ItemNo"];
		$PONo=$myRow["PONo"]==""?"&nbsp;":$myRow["PONo"];
		$POrderId=$myRow["POrderId"];
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=1 ORDER BY Id LIMIT 1",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			$ColbgColor="bgcolor='#0066FF'";
			}
		else{
			$ColbgColor="";
			}
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"]; 
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$Description=$myRow["Description"];
		$Type=$myRow["Type"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		
		$OrderPO=$Type==2?"随货项目":$OrderPO;
		$checkidValue=$Id."^^".$Type;
		$Locks=1;
		if($Type==1){//如果是订单：检查生产数量与需求数量是否一致，如果不一致，不允许选择
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
			WHERE C.POrderId='$POrderId' AND T.Estate=1",$link_id));
			$scQty=$CheckscQty["scQty"];
			if($gxQty!=$scQty){//生产完毕
				$LockRemark="生产登记异常！";
				$Locks=0;//不能操作
				}
			//检查领料记录 备料总数与领料总数比较
			$CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType<2",$link_id));
			$blQty=$CheckblQty["blQty"];
			$CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
				FROM $DataIn.cg1_stocksheet G 										
				LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				WHERE G.POrderId='$POrderId' AND K.Estate=0",$link_id));
			$llQty=$CheckllQty["llQty"];
			if($blQty!=$llQty){//领料完毕
				$LockRemark.="领料异常！";
				$Locks=0;//不能操作
				}
			}
		
		$showPurchaseorder="<img onClick='CH_SC_SandH(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i);' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$OrderPO,1=>"align='center'"),
			array(0=>$ItemNo,1=>"align='center'"),
			array(0=>$PONo,1=>"align='center'"),
			array(0=>$POrderId,1=>"align='center'"),
			array(0=>$ProductId,1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$eCode.$gxQty."/".$scQty,3=>"..."),
			array(0=>$Price,1=>"align='center'"),
			array(0=>$Qty,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$OrderDate,1=>"align='center'")
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
include "../model/subprogram/read_model_menu.php";
?>