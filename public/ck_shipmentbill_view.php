<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 待出订单列表");
$funFrom="ch_shippinglist";
$From=$From==""?"add":$From;
$sumCols="8,9";			//求和列,需处理
$Th_Col="选项|40|序号|40|PO#|80|订单流水号|80|产品Id|50|中文名|220|Product Code/Description|220|售价|60|订单数量|60|金额|60|订单日期|70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

$ActioToS="";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//订单出货状态：1、未出，2、待出，4、生成出货单，0、已出
$CheckResult = mysql_query("
	SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	WHERE 1 AND S.Estate='2' GROUP BY M.CompanyId 
	",$link_id);
if($CheckRow = mysql_fetch_array($CheckResult)) {
	echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"ck_shipmentbill_view\")'>";
	do{			
		$thisCompanyId=$CheckRow["CompanyId"];
		$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
		$Forshort=$CheckRow["Forshort"];
		if($CompanyId==$thisCompanyId){
			echo"<option value='$thisCompanyId' selected>$Forshort</option>";
			$SearchRows.=" AND M.CompanyId='$thisCompanyId' ";
			}
		else{
			echo"<option value='$thisCompanyId'>$Forshort</option>";					
			}
		}while($CheckRow = mysql_fetch_array($CheckResult));
	echo"</select>&nbsp;";
	}
$otherAction="待出订单列表";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
	SELECT M.OrderNumber,M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.TestStandard
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	WHERE 1 AND S.Estate='2' $SearchRows";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;		
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"]; 
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$Description=$myRow["Description"];
		$checkidValue=$Id;
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Locks=0;
		$ValueArray=array(
			array(0=>$OrderPO,
					 1=>"align='center'"),
			array(0=>$POrderId,
					 1=>"align='center'"),
			array(0=>$ProductId,
					 1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$eCode,
					 3=>"..."),
			array(0=>$Price,
					 1=>"align='center'"),
			array(0=>$Qty,					
					 1=>"align='center'"),
			array(0=>$Amount,
					 1=>"align='center'"),
			array(0=>$OrderDate,
					 1=>"align='center'")
			);
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
