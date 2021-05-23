<?php 
//电信-zxq 2012-08-01
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="采购单号|60|采购|50|供应商|80|选项|40|序号|40|需求流水号|90|配件名称|290|需求数|60|增购数|60|实购数|60";
$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch=$From!="slist"?"":$sSearch;
$sSearch.=$Jid==""?"":" and M.CompanyId='$Jid'";
$sSearch.=$Bid==""?"":" and M.BuyerId='$Bid'";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.PurchaseID,M.Date,M.Id,S.StuffId,S.StockId,S.AddQty,S.FactualQty,P.Forshort,B.Name,D.StuffCname,S.Price
	FROM $DataIn.cg1_stockmain M
	LEFT JOIN $DataIn.cg1_stocksheet S ON M.Id=S.Mid
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.staffmain B ON M.BuyerId=B.Number
	WHERE 1  AND S.Mid>0 $sSearch 
	ORDER BY M.Id";
//echo $mySql; 
//AND  S.StockId IN (SELECT StockId FROM cw1_fkoutsheet)
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$Mid=$mainRows["Id"];
		$Date=$mainRows["Date"];
		$PurchaseID=$mainRows["PurchaseID"];
		$Forshort=$mainRows["Forshort"];
		$Buyer=$mainRows["Name"];
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){			
			$StuffCname=$mainRows["StuffCname"];
			$StockId=$mainRows["StockId"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];			
			$CountQty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			

			if($tbDefalut==0 && $midDefault==""){//首行
				//输出并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				echo"<td class='A0111' width='$Field[$m]' align='center'>$PurchaseID</td>";
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$Buyer</td>";
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]'>$Forshort</td>";
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$unitWiath'>";
				$midDefault=$Mid;
				}
			$ValueSTR="$StockId^^$StuffId^^$StuffCname^^$FactualQty^^$AddQty^^$CountQty^^$Price^^$PurchaseID";
	
			$chooseStr="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$ValueSTR' disabled>";
			
			//输出明细
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=7;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-2;
				echo"<td align='center' width='$unitFirst'>$chooseStr</td>";
				$m=$m+2;
				echo"<td align='center' width='$Field[$m]'>$i</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StockId</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StuffCname</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$FactualQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$AddQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$CountQty</td>";					
				echo"</tr></table>";
				$i++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//输出并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				echo"<td class='A0111' width='$Field[$m]' align='center'>$PurchaseID</td>";
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$Buyer</td>";
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]'>$Forshort</td>";
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$unitWiath'>";
				$midDefault=$Mid;
				//输出明细
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-2;
				echo"<td align='center' width='$unitFirst'>$chooseStr</td>";
				$m=$m+2;
				echo"<td align='center' width='$Field[$m]'>$i</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StockId</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StuffCname</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$FactualQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$AddQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$CountQty</td>";						
				echo"</tr></table>";
				$i++;
				}
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>