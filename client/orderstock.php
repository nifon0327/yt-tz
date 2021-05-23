<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$tableMenuS=750;
ChangeWtitle("$SubCompany Stock Delivery Record");
$funFrom="orderstock";
$nowWebPage=$funFrom;
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$Th_Col="&nbsp;|60|NO.|40|DeliveryDate|100|InvoiceNO|180|InvoiceFile|100|InvoiceMark|100|InvoiceQty|100|DeliveredQty|100|BalanceQty|100";
$Th_Col="Choose|50|NO|60|ID|80|Product Code|280|Qty|70|DeliveredQty|70|BalanceQty|70";
$sumCols="6,7";			//求和列,需处理
$ColsNumber=16;	
//步骤3：

if ($myCompanyId==1081 || $myCompanyId==1002 || $myCompanyId==1080 || $myCompanyId==1065 ) {
	$CompanySTR=" and SM.CompanyId in ('1081','1002','1080','1065')";
}
else {
	$CompanySTR=" and SM.CompanyId='$myCompanyId' ";
}

include "../model/subprogram/read_model_3.php";

//已提和未提分类
if($deliveryState == '1'){
  $availableSelect = '';
  $deliveredSelect = 'selected';
  $stateSearch = ' and A.ShipQty=A.DeliveryQty';
}else{
  $availableSelect = 'selected';
  $deliveredSelect = '';
  $stateSearch = ' and A.ShipQty>A.DeliveryQty';
}

echo "<select name='deliveryState' id='deliveryState' onchange='ResetPage(this.name)'>";
echo "<option value='0' $availableSelect>Available Stock</option>";
echo "<option value='1' $deliveredSelect>Stock Delivered</option>";
echo "</select> &nbsp;";

echo $CencalSstr;
$searchtable="productdata|P|eCode|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";
include "../admin/subprogram/read_model_5.php";
$subTableWidth=$tableWidth-30;
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM (
SELECT  P.Price, P.cName,P.eCode,P.TestStandard,P.ProductId,C.Forshort,SUM(S.Qty) AS ShipQty,IFNULL(SUM(D.DeliveryQty),0) AS DeliveryQty
FROM  $DataIn.ch1_shipsheet S 
LEFT JOIN ( 
           SELECT POrderId,SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet  WHERE 1 GROUP BY  POrderId
        ) D ON D.POrderId=S.POrderId
LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=S.Mid
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=SM.CompanyId 
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=SM.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId  
WHERE 1 AND O.Id IS NOT NULL  AND  P.ProductId!='' $CompanySTR   $SearchRows GROUP BY P.ProductId )  A  WHERE  1 $stateSearch";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$SumQty=0;$SumQty1=0;$SumQty2=0;
	do{
         $m=1;
		$ProductId=$myRow["ProductId"];
		$Forshort=$myRow["Forshort"];
		$Price=$myRow["Price"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
       $eCode=$myRow["eCode"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$eCode="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633;'>$eCode</span>";
			}
  		$eCode=$eCode==""?"&nbsp;":$eCode;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $ShipQty=$myRow["ShipQty"];
        $Amount=sprintf("%.2f",$Price*$ShipQty);
		 $DeliveryQty=$myRow["DeliveryQty"];
         $SumQty1+=$DeliveryQty;
 
		 $unDeQty=$ShipQty-$DeliveryQty;
         $SumQty2+=$unDeQty;
        $SumQty=$SumQty1+$SumQty2;
            $unDeQty=$unDeQty>0?"<span class='redB'>$unDeQty</span>":"&nbsp;";
         $DeliveryQty=$DeliveryQty>0?"<a href='ch_shipoutclient_show.php?ProductId=$ProductId' target='_blank' style='color: #009900;font-weight: bold;'>$DeliveryQty</a>":"&nbsp;";
		 $OrderSignColor=""; 
		 $showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$ProductId\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		     $ValueArray=array(
			   array(0=>$ProductId,	1=>"align='center'"),
			   array(0=>$eCode),
			   array(0=>$ShipQty,	1=>"align='right'"),
			   array(0=>$DeliveryQty,	1=>"align='right'"),
			   array(0=>$unDeQty=="&nbsp;"?'0':$unDeQty,	1=>"align='right'"),
			 //  array(0=>$unAmount,	1=>"align='right'"),
			  );
		  
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	        $m=1;
			$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$SumQty,		1=>"align='right'"),
				array(0=>$SumQty1,		1=>"align='right'"),
				array(0=>$SumQty2,		1=>"align='right'")
				);
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";		
	}
else{
	noRowInfo($tableWidth);
  	}

//步骤7：
echo '</div>';//
List_Title($Th_Col,"0",0);
?>
<script language="javascript">

function sOrhOrder(e,f,Order_Rows,ProductId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(ProductId!=""){			
			var url="ch_shipoutclient_ajax.php?ProductId="+ProductId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}

</script>