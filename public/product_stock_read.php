<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 成品库存分析");
$funFrom="product_stock";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|40|序号|40|数据分析|60|产品Id|60|产品名称|320|Product Code|160|售价|60|币别|50|单位|50|订单总数|70|入库总数|70|出货总数|70|在库|70|库存金额(RMB)|80|更新|80";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1";

//步骤3：
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	$SearchRows="";
	
	$ClientResult = mysql_query("SELECT P.CompanyId,O.Forshort
	FROM $DataIn.productdata P
    LEFT JOIN $DataIn.trade_object O ON O.CompanyId = P.CompanyId
    LEFT JOIN (SELECT ProductId,IFNULL(SUM(Qty),0) AS orderQty FROM $DataIn.yw1_ordersheet GROUP BY ProductId ) S ON S.ProductId = P.ProductId
	WHERE 1 $SearchRows AND P.Estate>0 AND O.Estate>0 AND S.orderQty>0 GROUP BY P.CompanyId",$link_id);

	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	    echo "<option value='' selected>全部</option>";
	    do{
			$thisCompanyId=$ClientRow["CompanyId"];
			$thisForshort=$ClientRow["Forshort"];
			if ($thisCompanyId==$CompanyId){
				echo "<option value='$thisCompanyId'  selected>$thisForshort</option>";
				$SearchRows.= " AND P.CompanyId=".$thisCompanyId;
				}
			else{
				echo "<option value='$thisCompanyId' >$thisForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		  echo"</select>&nbsp;";
	  }
	  
	$result = mysql_query("SELECT P.TypeId,T.TypeName,T.Letter,C.Color ,T.NameRule	
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE T.Estate=1 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType DESC,T.Letter",$link_id);

	if ($myrow = mysql_fetch_array($result)){
		echo "<select name='ProductType' id='ProductType' onchange='ResetPage(this.name)'>";
	    echo "<option value='' selected>全部</option>";
	    do{
			$TypeId=$myrow["TypeId"];
			$Letter=$myrow["Letter"];
			$TypeName=$myrow["TypeName"];
			if ($ProductType==$TypeId){
				echo "<option value='$TypeId'  selected>$Letter-$TypeName</option>";
				$SearchRows.= " AND P.TypeId=".$ProductType;
				}
			else{
				echo "<option value='$TypeId' >$Letter-$TypeName</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		  echo"</select>&nbsp;";
	  }
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr&nbsp;&nbsp;&nbsp;&nbsp;<a href='product_stock_error.php' target='_blank'>数据有误产品</a>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理 AND K.oStockQty>0
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT P.ProductId,P.cName,P.eCode,P.TestStandard,P.Price,P.Unit,T.TypeId,T.TypeName,
K.tStockQty,U.Name AS UnitName,S.orderQty,C.Rate,C.Name AS CurrencyName,C.Symbol
FROM $DataIn.productdata P
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.productstock K ON K.ProductId=P.ProductId 
LEFT JOIN $DataIn.productunit  U ON U.Id = P.Unit
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = P.CompanyId
LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
LEFT JOIN (SELECT ProductId,IFNULL(SUM(Qty),0) AS orderQty FROM $DataIn.yw1_ordersheet GROUP BY ProductId ) S ON S.ProductId = P.ProductId 
WHERE 1 AND P.Estate>0  AND S.orderQty>0 $SearchRows";
//echo $mySql;
$SumotStockQty=0;
$SumKcAmount=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
      
	do{
		$m=1;
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
        $eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
        $Price=$myRow["Price"];
        $Rate=$myRow["Rate"];
        $TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
		$TypeName=$myRow["TypeName"];
		$tStockQty=$myRow["tStockQty"];
		$UnitName=$myRow["UnitName"];
		$CurrencyName=$myRow["CurrencyName"];
		$Symbol=$myRow["Symbol"];
		$myOpration="<a href='product_stock_result.php?ProductId=$ProductId&cName=$cName' target='_blank'>分析</a>";
          
		$kc_Amount=sprintf("%.2f", $tStockQty*$Price*$Rate);
		$SumKcAmount+=$kc_Amount;
		$SumtStockQty=$SumtStockQty+$tStockQty;
		//订单总数
		$orderQty=$myRow["orderQty"];
		/*$CheckOrderResult=mysql_query("SELECT SUM(Qty) AS orderQty FROM $DataIn.yw1_ordersheet WHERE ProductId='$ProductId' ",$link_id);
		if($CheckGRow=mysql_fetch_array($CheckOrderResult)){
			$orderQty=$CheckGRow["orderQty"]==""?0:$CheckGRow["orderQty"];
		}*/

		//入库总数
		$CheckrkResult=mysql_query("SELECT SUM(Qty) AS rkQty FROM $DataIn.yw1_orderrk WHERE ProductId='$ProductId'",$link_id);
		if($CheckrkRow=mysql_fetch_array($CheckrkResult)){
			$rkQty=$CheckrkRow["rkQty"]==""?0:$CheckrkRow["rkQty"];
		}


		//出货总数
		$CheckShipResult=mysql_query("SELECT SUM(Qty) AS shipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		if($CheckShipRow=mysql_fetch_array($CheckShipResult)){
			$shipQty=$CheckShipRow["shipQty"]==""?0:$CheckShipRow["shipQty"];
		}

        $UpdateStockClick = "&nbsp;";
        $lastQty = $rkQty - $shipQty;
		if($lastQty!=$tStockQty){
			$UpdateStockClick = "<input type='button' name='Submit' value='更正库存' onClick='javascript:ErrorCorrection($ProductId,$tStockQty,$lastQty);'>";
		 }
		 
		if($shipQty==0){
			$shipQty = "&nbsp;";
		}else{
			if($shipQty==$rkQty)$shipQty = "<span class='greenB'>$shipQty</span>";
			else if ($shipQty>$rkQty)$shipQty = "<span class='redB'>$shipQty</span>";
			else  $shipQty = "<span class='yellowB'>$shipQty</span>";
		}
		
		if($rkQty==0){
			$rkQty = "&nbsp;";
		}else{
			if($rkQty==$orderQty)$rkQty = "<span class='greenB'>$rkQty</span>";
			else if ($rkQty>$orderQty)$rkQty = "<span class='redB'>$rkQty</span>";
			else $rkQty = "<span class='yellowB'>$rkQty</span>";
		}
		
		$tStockQty = $tStockQty ==0 ?"&nbsp;":$tStockQty;
		$kc_Amount = $kc_Amount ==0 ?"&nbsp;":$kc_Amount;
		
		
		$ValueArray=array(
			array(0=>$myOpration, 1=>"align='center'"),
			array(0=>$ProductId,1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$eCode),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$UnitName,1=>"align='center'"),			
			array(0=>$orderQty,1=>"align='right'"),
			array(0=>$rkQty,1=>"align='right'"),
			array(0=>$shipQty,1=>"align='right'"),
			array(0=>$tStockQty,1=>"align='right'"),
			array(0=>$kc_Amount,1=>"align='right'"),
			array(0=>$UpdateStockClick,1=>"align='right'"),
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
       
	$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$SumtStockQty,1=>"align='right'"),
				array(0=>$SumKcAmount,1=>"align='right'"),
				array(0=>"&nbsp;"	)
				);
				
			$ShowtotalRemark="合计";
			$isTotal=1;$m=1;
			include "../model/subprogram/read_model_total.php";
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

<script>
function ErrorCorrection(ProductId,tStockQty,lastQty){  
		myurl="product_stock_updated.php?ProductId="+ProductId+"&tStockQty="+tStockQty+"&lastQty="+lastQty; 
		var ajax=InitAjax(); 
	        ajax.open("GET",myurl,true);
	        ajax.onreadystatechange =function(){
		    if(ajax.readyState==4 && ajax.status ==200){
				alert("产品的库存数据已更正！");
				document.form1.submit();
	         }
		 }
	    ajax.send(null); 
	}
</script>