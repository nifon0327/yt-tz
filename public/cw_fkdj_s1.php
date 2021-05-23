<?php 
$SearchSTR=0;
//if ($CompanyId==""){return;}
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|40|采购时间|70|采购单号|60|备注|30|配件ID|40|配件名称|200|图档|30|需求数|45|增购数|45|实购数|45|单价|45|金额|60|金额(RMB)|60|交货日期|80|采购流水号|100";

$ColsNumber=100;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
//$Parameter.=",Bid,$Bid,Jid,$Jid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页

$Page_Size = 200;							//每页默认记录数量,13
//出货月份
	echo "<input name='CompanyId' type='hidden' id='CompanyId' value='$CompanyId' >";
		$SearchRows.=" and M.CompanyId='$CompanyId'";
		//月份   
		$date_Result = mysql_query("SELECT M.Date 
		FROM $DataIn.cg1_stockmain M
		WHERE 1 $SearchRows group by DATE_FORMAT(M.Date,'%Y-%m') order by M.Id DESC",$link_id);
		if ($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"cw_fkdj_s1\")'>";
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
		else{
			//无月份记录
			$SearchRows.=" and M.Date=''";
			}
echo"<select name='Pagination' id='Pagination' onchange='RefreshPage(\"cw_fkdj_s1\")'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Date,M.PurchaseID,M.Remark,S.Mid,M.CompanyId
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
WHERE 1 $SearchRows AND S.Mid>0 GROUP BY S.Mid  ORDER BY M.PurchaseID DESC";
$Keys=31;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$PurchaseID=$mainRows["PurchaseID"];
        $CompanyId=$mainRows["CompanyId"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$Remark=$mainRows["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='16' height='16'>";
		$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
			//供应商结付货币的汇率
	     $Rate=1;
		  $currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C
			                              LEFT JOIN $DataIn.trade_object P  ON P.Currency=C.Id 
			                              WHERE P.CompanyId='$CompanyId' ORDER BY C.Id LIMIT 1",$link_id);
			  if($RowTemp = mysql_fetch_array($currency_Temp)){
				   $Rate=$RowTemp["Rate"];//汇率
			   	}
        $checkSql1=mysql_fetch_array(mysql_query("SELECT SUM(S.Price*(S.FactualQty+S.AddQty)) AS Amount
		FROM $DataIn.cg1_stocksheet S  WHERE Mid='$Mid'",$link_id));
        $rmbAmount=$checkSql1["Amount"]*$Rate;
		$checkidValue=$Mid."^^".$PurchaseID."^^".$rmbAmount;
	    $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr bgcolor='$theDefaultColor'
			onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
			onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
		echo"<td class='A0111' width='$Field[1]' align='center'>$Choose</td>";
		echo"<td class='A0101' width='$Field[3]' align='center'>$j</td>";//$OrderSignColor为订单状态标记色
		echo"<td  class='A0101' width='$Field[5]'>$Date</td>";
		echo"<td  class='A0101' width='$Field[7]'>$PurchaseIDStr</td>";
		echo"<td  class='A0101' width='$Field[9]'>$Remark</td>";
		echo"<td  class='A0101'>";
	//明细记录
		$checkSql=mysql_query("SELECT S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
		S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,PI.Leadtime,A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.TypeId
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		WHERE S.Mid=$Mid",$link_id);
		if($checkRow = mysql_fetch_array($checkSql)){
			echo"<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
			do{
                $StuffId=$checkRow["StuffId"];
	        	$StuffCname=$checkRow["StuffCname"];
                $OrderQty=$checkRow["OrderQty"];
                $FactualQty=$checkRow["FactualQty"];
                $AddQty=$checkRow["AddQty"];
                $Qty=$FactualQty+$AddQty;
                $Price=$checkRow["Price"];
                $Amount=sprintf("%.2f",$Qty*$Price);		
			   $rmbAmount=sprintf("%.2f",$Amount*$Rate);
		       $Leadtime=$checkRow["Leadtime"];
			    $StockId=$checkRow["StockId"];
				echo"<tr><td  class='A0001' width='$Field[11]' align='right'>$StuffId</td>";
				echo"<td  class='A0001' width='$Field[13]'>$StuffCname</td>";
				echo"<td  class='A0001' width='$Field[15]' align='right'></td>";
				echo"<td  class='A0001' width='$Field[17]' align='center'>$FactualQty</td>";
				echo"<td  class='A0001' width='$Field[19]' align='right'>$AddQty</td>";
				echo"<td  class='A0001' width='$Field[21]' align='right'>$Qty</td>";
				echo"<td  class='A0001' width='$Field[23]' align='center'>$Price</td>";
				echo"<td  class='A0001' width='$Field[25]' align='center'>$Amount</td>";
				echo"<td  class='A0001' width='$Field[27]' align='center'>$rmbAmount</td>";
				echo"<td  class='A0001' width='$Field[29]' align='center'>$Leadtime</td>";
				echo"<td width='$Field[31]' align='center'>$StockId</td></tr>";
				}while($checkRow = mysql_fetch_array($checkSql));
			echo"</table>";
			}
		echo"</td></tr></table>";
		$i++;$j++;	
		}while ($mainRows = mysql_fetch_array($myResult));
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