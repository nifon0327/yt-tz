<?php   
//电信-EWEN
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=600;
$sumCols="10,11,12,13";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 备料订单拆分审核");
$funFrom="yw_order_analyzes";

$Th_Col="选项|60|序号|30|客户名称|80|订单流水号|80|PO|80|产品名称|150|Product Code|120|单价|50|原单数量|60|拆分数量1|60|拆分数量2|60|拆分日期|80|拆分原因|260|操作人|60|状态|80|退回原因|100|";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;
$ActioToS="17,162";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
/*
if($From!="slist"){	
	$SearchRows="";
	$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN  $DataIn.yw10_ordersplit O ON O.POrderId=S.POrderId
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
	WHERE O.Estate=0 GROUP BY M.CompanyId order by M.CompanyId",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];$theForshort=$ClientRow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows="and M.CompanyId='$theCompanyId'";
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	}
*/
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.Forshort,O.Id,O.POrderId,O.Qty,O.Qty1,O.Qty2,O.Remark,O.Estate,O.Date,O.Operator,P.cName,P.eCode,S.OrderPO,S.Price,P.TestStandard,P.ProductId
	FROM $DataIn.yw10_ordersplit O
	LEFT JOIN  $DataIn.yw1_ordersheet S ON O.POrderId=S.POrderId
	LEFT JOIN  $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
	WHERE O.Estate=0 AND S.Estate>0 AND S.Estate<4 $SearchRows ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$POrderId=$myRow["POrderId"];
		$OrderPO=toSpace($myRow["OrderPO"]);
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];
		$Qty1=$myRow["Qty1"];
		$Qty2=$myRow["Qty2"];
		$Remark=$myRow["Remark"];
		//$Estate=$myRow["Estate"]==0?"<div class='redB'>×</div>":"<div class='greenB'>√</div>";
		switch($Estate)
		{
			case 0:
			{
				$Estate = "<div class='redB'>×</div>";
			}
			break;
			case 1:
			{
				$Estate = "<div class='greenB'>√</div>";
			}
			break;
			case 2:
			{
				$Estate = "<div class='blueB'>√</div>";
			}
			break;
		}
		
		$returnReasonSql = mysql_query("Select * From $DataPublic.returnreason Where tableId = '$Id' and targetTable = '$DataIn.yw10_ordersplit' order by DateTime Desc Limit 1");
		$returnReasonRows = mysql_fetch_assoc($returnReasonSql);
		$returnReason = ($returnReasonRows["Reason"]=="")?"&nbsp;":$returnReasonRows["Reason"];
		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Date=$myRow["Date"];
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$Forshort, 1=>"align='left'"),			  
			array(0=>$POrderId,1=>"align='center'"),
			array(0=>$OrderPO,1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$eCode,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
            array(0=>$Qty,1=>"align='right'"),
			array(0=>$Qty1,	1=>"align='right'"),
			array(0=>$Qty2,1=>"align='right'"),			
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$returnReason,1=>"align='center'")
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
