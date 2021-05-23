<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$ColsNumber=14;
$tableMenuS=600;
ChangeWtitle("$SubCompany 未提货订单列表");
$funFrom="ch_shippinglist";
$From=$From==""?"add":$From;
$sumCols="7,8,9";			//求和列,需处理
$Th_Col="选项|40|序号|40|InvoiceNO|80|PO#|80|中文名|180|productcode|120|订单日期|80|订单数量|80|提货数量|80|剩余数量|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
include "../model/subprogram/read_model_3.php";
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
	SELECT O.OrderNumber,O.CompanyId,O.OrderDate,Y.Id,Y.OrderPO,Y.POrderId,Y.ProductId,Y.Qty,
	Y.PackRemark,P.cName,P.eCode,P.TestStandard,M.InvoiceNO
	FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
	LEFT JOIN $DataIn.yw1_ordermain O ON O.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	WHERE 1 AND M.Id='$Ids'";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;	
		$Id=$myRow["Id"];	
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$Description=$myRow["Description"];
		$Type=$myRow["Type"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty FROM $DataIn.ch1_deliverysheet WHERE POrderId ='$POrderId '",$link_id);
		$DeliveryQty=mysql_result($DeliveryResult,0,"DeliveryQty");
		$DeliveryQty=$DeliveryQty==""?0:$DeliveryQty;
		$unDeQty=$Qty-$DeliveryQty;
		
		if($unDeQty!=0){
		$DivNum="a".$i;
		$TempId=$POrderId;
			$HideTableHTML="<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
					<td class='A0111'  colspan=\"7\">
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr></table>";
		   $DeliveryQty="<div class='greenB'>$DeliveryQty</div>";
		   $unDeQty="<div class='yellowB'>$unDeQty</div>";
		   $ValueArray=array(
		      array(0=>$InvoiceNO,1=>"align='center'"),
			  array(0=>$OrderPO,1=>"align='center'"),
			 // array(0=>$POrderId,1=>"align='center'"),
			 // array(0=>$ProductId,1=>"align='center'"),
			  array(0=>$TestStandard),
			  array(0=>$eCode),
			  array(0=>$OrderDate,1=>"align='center'"),
			  array(0=>$Qty,1=>"align='center'"),
			  array(0=>$DeliveryQty,1=>"align='center' onClick='SandH(\"$DivNum\",$i,\"$TempId\",\"ch_shipoutclient_unqty\",0);' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' alt='显示或隐藏下级资料. ' style='CURSOR: pointer;'"),
			  array(0=>$unDeQty,1=>"align='center'")
			  );
		   include "../model/subprogram/read_model_6.php";
		   echo $HideTableHTML;
		  }
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
<script>
function SandH(divNum,RowId,TempId,ToPage,Action){
  //alert(TempId);
	var e=eval("HideTable_"+divNum+RowId);
	if(Action==0)e.style.display=(e.style.display=="none")?"":"none";
	else e.style.display=="";
	if (e.style.display==""){
		
		if(TempId!=""){			
			var url="../admin/"+ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId+"&Action="+Action;
		　	var show=eval("HideDiv_"+divNum+RowId);
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