<?php
$Th_Col="选项|30|配件|40|ID|30|客户|80|期限|60|工单流水号|80|PO|100|中文名|150|Product Code|150|生管备注|165|Qty|50|操作人|80|时间|100|现生产车间|80|变更后车间|80";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
if (strlen($tempcName)>0){
	$SearchRows.=" AND P.cName LIKE '%$tempcName%' ";
	$searchList1="<span class='ButtonH_25'  id='cancelQuery' value='取消' onclick='ResetPage(1,1)'>取消</span>";
    }
else{
	$searchList1="<input type='text' name='tempcName' id='tempcName' value='' width='20'/> &nbsp;<span class='ButtonH_25'  id='okQuery' value='查询' onclick='ResetPage(1,1)'>查询</span>";
 }
	echo "<td colspan='6' align='right' class=''>$searchList1<input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";


//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$j=2;$i=1;

$mySql="SELECT DISTINCT O.Forshort,M.CompanyId,M.OrderDate,
    S.POrderId,S.OrderPO,S.Price,S.sgRemark,S.DeliveryDate,S.ShipType,
    SC.Id,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId,SC.Remark,
    P.ProductId,P.cName,P.eCode,P.TestStandard,P.pRemark,
    U.Name AS Unit,PI.Leadtime,PI.Leadweek,W.Name AS WorkShopName,SF.Name,ck.created 
	FROM  $DataIn.yw1_scsheet SC 
	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=S.POrderId
	LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
	    LEFT JOIN $DataIn.ck5_llsheet ck ON ck.sPOrderId = SC.sPOrderId
    LEFT JOIN  $DataIn.staffmain SF ON SF.Number = ck.Operator
	WHERE 1  $SearchRows ORDER BY M.OrderDate";

//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$Forshort=$myRow['Forshort'];
		$POrderId=$myRow["POrderId"];
		$sPOrderId=$myRow["sPOrderId"];
		$ProductId=$myRow["ProductId"];
		$OrderPO=toSpace($myRow["OrderPO"]);
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		$Estate = $myRow["Estate"];
		include "../admin/Productimage/getProductImage.php";
		$WorkShopName=$myRow["WorkShopName"];

		$Qty=$myRow["Qty"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$OrderDate=$myRow["OrderDate"];
        $Leadtime=$myRow["Leadtime"];
        $Leadweek=$myRow["Leadweek"];
	    include "../model/subprogram/PI_Leadweek.php";
		$sumQty=$sumQty+$Qty;

        $Name = $myRow['Name'];
        $created = $myRow['created'];

		$CheckValue="<input type='checkbox' disabled />";
		if($SubAction==31  ){
              $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
              //$UpdateClick="onclick='ChangeScDempartment(\"$sPOrderId\")'";
              //$UpdateClick="onclick=\"openWinDialog(this,'$updateWebPage?Id=$Id&sPOrderId=$sPOrderId&fromWorkShopPage=1',405,300,'left')\" ";
              $UpdateClick="onclick=\"openWinDialog(this,'$updateWebPage?Id=$Id&POrderId=$POrderId&fromWorkShopPage=1',405,300,'left')\" ";

              //$CheckValue="<input type='checkbox' name='checkId$i' id='checkId$i' value='$Id|$sPOrderId' />";
              $CheckValue="<input type='checkbox' name='checkId$i' id='checkId$i' value='$Id|$POrderId' />";
          }
		//动态读取配件资料
		$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
		echo"<tr><td class='A0111' align='center' height='25' valign='middle' >$CheckValue</td>";
		echo"<td class='A0101' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId,$sPOrderId);' >$showPurchaseorder</td>";
		echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
		echo"<td class='A0101' align='center' >$Forshort</td>";
		echo"<td class='A0101' align='center' >$Leadweek</td>";
		echo"<td class='A0101' align='center'>$sPOrderId</td>";
		echo"<td class='A0101' align='center' >$OrderPO</td>";
		echo"<td class='A0101'>$TestStandard</td>";
		echo"<td class='A0101'>$eCode</td>";
		echo"<td class='A0101' onclick='InputRemark($j,$Id)'>$Remark</td>";
		echo"<td class='A0101' align='right'>$Qty</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101' align='center'>$created</td>";
		echo"<td class='A0101' align='center'>$WorkShopName</td>";
		echo"<td class='A0101' align='center' id='ShowChangeWorkShop$Id' $UpdateClick>$UpdateIMG</td>";
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