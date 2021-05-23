<?php
$ClientList="";
$nowInfo="当前:成品配件转订单";
if($SearchOrder!=""){
	$SearchRows=" AND (P.cName LIKE '%$SearchOrder%' OR P.eCode LIKE '%$SearchOrder%')";
	$ClientList="来自于查询页面&nbsp;&nbsp;<input class='ButtonH_25' type='button'  onclick='document.form1.submit()' value='返  回'/>";
}
else{
$ClientList="<input name='SearchOrder' id='SearchOrder' type='text' size='20'><input class='ButtonH_25' type='button'  id='Querybtn' name='Querybtn' onclick='SearchScOrder()' value='查 询'>";
}
$Th_Col="+|40|ID|30|客户|80|交期|60|PO|80|产品ID|60|中文名|320|Product Code|100|Qty|60|单位|40|出货方式|60|可转数量|60|已转数量|60|转成品仓|80";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
//步骤5：

	echo"<table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>
	<tr>
	<td colspan='".($Cols-8)."' height='40px' class=''>&nbsp; </td><td colspan='8' align='center' class=''>$ClientList</td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$j=2;
$i=1;
$today=date("Y-m-d");
$MaxWeek  = date("Y")."99";
$mySql="SELECT 
S.Id,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.ShipType,S.scFrom,S.Estate,S.Locks,
P.cName,P.eCode,P.TestStandard,P.CompanyId,
U.Name AS Unit,M.OrderDate,S.dcRemark,S.sgRemark,S.PackRemark,
PI.Leadtime,IFNULL(IFNULL(PI.Leadweek,PL.Leadweek),$MaxWeek) AS Leadweek,T.Forshort 
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId = S.POrderId
LEFT JOIN ( SELECT Count(*) AS StuffCount,POrderId  FROM  $DataIn.cg1_stocksheet WHERE Level =1 AND blSign =1
GROUP BY POrderId ) G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.productunit U ON U.Id=P.Unit
WHERE 1   AND S.Estate=1   AND G.StuffCount=1  GROUP BY S.POrderId  ORDER BY Leadweek";
$SumQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		//加密参数
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		$Forshort=$myRow["Forshort"];
        $PackRemark=$myRow["PackRemark"]==""?"&nbsp;":$myRow["PackRemark"];
	    $dcRemark=$myRow["dcRemark"]==""?"&nbsp;":"<span class='redB'>(".$myRow["dcRemark"].")</span>";
        $Qty=$myRow["Qty"];
		$POrderId=$myRow["POrderId"];
        $OrderDate=$myRow["OrderDate"];
        $Leadtime=$myRow["Leadtime"];
        $Leadweek=$myRow["Leadweek"];
	    include "../model/subprogram/PI_Leadweek.php";
        $SumQty=$SumQty+$Qty;
		include "../admin/Productimage/getPOrderImage.php";
		$Unit=$myRow["Unit"];
		$ShipType=$myRow["ShipType"];
		 //出货方式
	   if (strlen(trim($ShipType))>0){
	        $CheckShipType=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType'  LIMIT 1",$link_id));
	        $ShipName=$CheckShipType["Name"];
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;' title='$ShipName'/>";
	    }
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];
		$sgRemark=$myRow["sgRemark"]==""?$dcRemark:$myRow["sgRemark"]."<br>".$dcRemark;

	    $czSign = 0 ;
	    $blcount = 0 ;
	    $tempK  = 0 ;
	    $CheckStockResult = mysql_query("SELECT G.OrderQty,K.tStockQty,G.StockId 
	    FROM $DataIn.cg1_stocksheet G 
	    LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = G.StuffId 
	    WHERE G.POrderId = '$POrderId' AND G.Level = 1  AND G.blSign=1",$link_id);
	    if($CheckStockRow = mysql_fetch_array($CheckStockResult)){

	        $thisOrderQty = $CheckStockRow["OrderQty"];
	        $tStockQty = $CheckStockRow["tStockQty"];
		    $llStockId = $CheckStockRow["StockId"];
		    $checkblQtyResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS blQty 
            FROM $DataIn.ck5_llsheet WHERE  StockId='$llStockId'",$link_id));
            $thisblQty=$checkblQtyResult["blQty"];

	    }

	    if($tStockQty>($thisOrderQty-$thisblQty)){
		    $canQty = $thisOrderQty-$thisblQty;
	    }else{
		    $canQty = $tStockQty;
	    }

	    if($canQty>0){
		    $czSign = 1;
		    $theDefaultColor = "#D3E9D3";
	    }

	    $CheckrkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS rkQty 
		FROM $DataIn.yw1_orderrk R 
		WHERE R.POrderId='$POrderId' AND R.ProductId = '$ProductId' ",$link_id));
		$rkQty=$CheckrkQty["rkQty"];



		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
         // if($SubAction==31 && $czSign==1){
            $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
            $UpdateClick="onclick='RegisterEstate($POrderId,$ProductId,this,$canQty)'";
         //   }
		//动态读取配件资料
		$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";

		echo"<tr bgcolor='$theDefaultColor'>
		<td height='25' class='A0111' align='center' id='theCel$i' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId);' >$showPurchaseorder</td>";
		echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
		echo"<td class='A0101' align='center' >$Forshort</td>";
		echo"<td class='A0101' align='center' >$Leadweek</td>";
		echo"<td class='A0101'>$OrderPO</td>";
		echo"<td class='A0101' align='center'>$ProductId</td>";
		echo"<td class='A0101'>$TestStandard</td>";
		echo"<td class='A0101'>$eCode</td>";
		echo"<td class='A0101' align='right'>$Qty</td>";
		echo"<td class='A0101' align='center'>$Unit</td>";
		echo"<td class='A0101' align='center'>$ShipType</td>";
		echo "<td class='A0101' align='right'>$canQty</td>";
		echo "<td class='A0101' align='right'>$rkQty</td>";
        echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
		echo"</tr>";
		echo $ListRow;
		$j=$j+2;$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
</html>
<script>
var Img2="<img src='../images/register.png' width='30' height='30'>";

function RegisterEstate(POrderId,ProductId,e,canRkQty){

    if(confirm("成品配件直接转成品仓出货?,可转数量为:"+canRkQty)){
	    var url="item5_15_ajax.php?POrderId="+POrderId+"&ProductId="+ProductId+"&canRkQty="+canRkQty+"&ActionId=1";
		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		    ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				 if(ajax.responseText=="Y"){//更新成功
	                  alert("请把货物拉去成品仓！");
	 		           e.innerHTML="&nbsp;";
	      		       e.style.backgroundColor="#339900";
	      		       e.onclick="";
	   			 }
	            else{
	                  alert("请检查库存是否足够,或者料是否备齐,请重新确认！");
	                }
			   }
		  }
	　	ajax.send(null);
    }

}
</script>