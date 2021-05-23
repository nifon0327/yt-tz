<?php 
//电信-zxq 2012-08-01
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="客户|60|订单PO|60|产品名称|150|选项|40|序号|40|需求流水号|90|配件ID|50|配件名称|210|订单数量|60|已领数量|60|未领数量|60|可领数量|60";

$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch=$From!="slist"?"":$sSearch;
$sSearch.=$Bid==""?"":" and M.CompanyId='$Bid'";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$EstateSTR="AND S.Estate>0";
if($Login_P_Number==10002){
	$EstateSTR="";
	}
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.OrderPO,S.POrderId,S.Qty,S.ProductId,P.cName,
	G.StuffId,G.StockId,G.OrderQty,D.StuffCname,C.Forshort 
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId	
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	WHERE 1  AND G.llSign>0 AND ((G.Mid>0 AND G.FactualQty>0) OR (G.Mid=0 AND G.FactualQty=0)) $EstateSTR $sSearch 
	ORDER BY S.Id,G.Id
	";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$Mid=$mainRows["Id"];
		$Forshort=$mainRows["Forshort"];
		$OrderPO=$mainRows["OrderPO"];
		$cName=$mainRows["cName"];
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){			
			$StockId=$mainRows["StockId"];
			$StuffCname=$mainRows["StuffCname"];			
			$OrderQty=$mainRows["OrderQty"];

			//领料数量
			$outTemp=mysql_query("SELECT SUM(Qty) AS a1 FROM $DataIn.ck5_llsheet WHERE StockId=$StockId",$link_id);; 
			$llQty=mysql_result($outTemp,0,"a1");
			if($llQty==""){
				$llQty=0;
				}
							
			//在库
			$stockSql=mysql_query("SELECT tStockQty FROM $DataIn.ck9_stocksheet WHERE 1 and StuffId=$StuffId order by Id DESC LIMIT 1",$link_id);
			if($stockRow=mysql_fetch_array($stockSql)){
				$tStockQty=$stockRow["tStockQty"];
				}
			else{
				$tStockQty=0;
				}
			$Unreceive=$OrderQty-$llQty;//未领料总数
			//可领料数
			$CanUseQty=$tStockQty>$Unreceive?$Unreceive:$tStockQty;
			$ValueSTR="$StockId^^$StuffId^^$StuffCname^^$OrderQty^^$Unreceive^^$CanUseQty";
			$Choose="&nbsp;";
			if($CanUseQty>0){
				$Choose="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$ValueSTR' disabled>";
				$CanUseQty="<div class='greenB'>$CanUseQty</div>";
				}
			
			if($tbDefalut==0 && $midDefault==""){//首行
				//输出并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				echo"<td class='A0111' width='$Field[$m]' align='center'>$Forshort</td>";	//客户
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$OrderPO</td>";		//PO
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]'>$cName</td>";					//产品名称
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$unitWiath'>";
				$midDefault=$Mid;
				}
			
			//输出明细
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=7;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-2;
				echo"<td align='center' width='$unitFirst'>$Choose</td>";
				$m=$m+2;
				echo"<td align='center' width='$Field[$m]'>$i</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StockId</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StuffId</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StuffCname</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$OrderQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$llQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$Unreceive</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$CanUseQty</td>";							
				echo"</tr></table>";
				$i++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//输出并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				echo"<td class='A0111' width='$Field[$m]' align='center'>$Forshort</td>";	//客户
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$OrderPO</td>";		//PO
				$unitWiath=$unitWiath-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]'>$cName</td>";					//产品名称
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
				echo"<td align='center' width='$unitFirst'>$Choose</td>";
				$m=$m+2;
				echo"<td align='center' width='$Field[$m]'>$i</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StockId</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StuffId</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]'>$StuffCname</td>";				
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$OrderQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$llQty</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$Unreceive</td>";
				$m=$m+2;
				echo"<td width='$Field[$m]' align='right'>$CanUseQty</td>";							
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
include "../model/subprogram/read_model_menu.php";
?>