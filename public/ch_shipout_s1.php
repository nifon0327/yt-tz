<?php 
//电信-zxq 2012-08-01
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|40|序号|40|InvoiceNO|100|PO#|80|订单流水号|80|中文名|220|Product Code/Description|220|售价|60|订单数量|70|已提货数量|70";
$ColsNumber=14;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$nowWebPage="ch_shipout_s1";
include "../model/subprogram/s1_model_3.php";
$SearchRows=" AND M.CompanyId=$CompanyId";

/*echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;*/
include "../model/subprogram/s1_model_5.php";
$i=1;
$NewTotal=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT  S.Id,M.InvoiceNO,P.cName,P.eCode,Y.OrderPO,S.POrderId,S.Price,S.Qty,S.Mid
        FROM $DataIn.ch1_shipsheet S
		LEFT JOIN $DataIn.ch1_shipmain  M ON M.Id=S.Mid
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		WHERE 1 $SearchRows AND M.Id IN (SELECT ShipId FROM $DataIn.ch1_shipout)";
	//echo $mySql;	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;		
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];
		$POrderId=$myRow["POrderId"];
		$DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty,SUM(DeliveryQty*Price) AS DeliveryAmount  FROM $DataIn.ch1_deliverysheet WHERE POrderId='$POrderId'",$link_id);
		$DeliveryQty =mysql_result($DeliveryResult,0,"DeliveryQty");
		$DeliveryQty=$DeliveryQty==""?"0":$DeliveryQty;
		$checkidValue=$POrderId."^^".$Id."^^".$OrderPO."^^".$cName."^^".$eCode."^^".$Qty."^^".$DeliveryQty."^^".$Mid;
	 if($Qty!=$DeliveryQty){ 
	       $DeliveryQty=$DeliveryQty==0?0:"<div class='redB'>$DeliveryQty</div>"; 
		   $ValueArray=array(
			array(0=>$InvoiceNO,1=>"align='center'"),
			array(0=>$OrderPO,1=>"align='center'"),
			array(0=>$POrderId,1=>"align='center'"),
			array(0=>$cName,3=>"..."),
			array(0=>$eCode,3=>"..."),
			array(0=>$Price,1=>"align='center'"),
			array(0=>$Qty,		1=>"align='center'"),
			array(0=>$DeliveryQty,1=>"align='center'")
			);
		  $NewTotal++;
		  include "../model/subprogram/read_model_6.php";
		 }
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
//$RecordToTal= mysql_num_rows($myResult);
$RecordToTal= $NewTotal;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>