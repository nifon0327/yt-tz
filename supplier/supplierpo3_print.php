<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.currencydata
$DataIn.trade_object
$DataIn.ck1_rksheet 
$DataIn.cw1_fkoutsheet
分开已更新
*/
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/style/orderprint.css'>";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=550;
ChangeWtitle("$SubCompany 采购单列表(结付分列)");
$Th_Col="下单日期|55|采购单号|45|行号|30|配件ID|35|配件名称|190|需求数|40|增购数|40|实购数|40|含税价|40|金额|50|收货数|40|欠数|40|货款|30|采购流水号|75";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤6：需处理数据记录处理
$i=1;
List_Title($Th_Col,"1",1);
if($Estate==1){
		$SearchRows.=" AND F.Estate IS NULL";
		}
	else{
		$SearchRows.=" AND F.Estate='$Estate'";
		}
$mySql="SELECT M.Date,M.PurchaseID,M.Remark,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,
A.StuffCname,A.Picture,A.Gfile,A.Gremark 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
WHERE 1  AND M.CompanyId='$myCompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')='$chooseDate' $SearchRows ORDER BY M.PurchaseID DESC";
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$PurchaseID=$mainRows["PurchaseID"];
		$Remark=$mainRows["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$mainRows[Remark]' width='16' height='16'>";
		//加密
		$MidJM=anmaIn($Mid,$SinkOrder,$motherSTR);
		$upMian="&nbsp;";
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);		
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$Locks=$mainRows["Locks"];
			$CompanyId=$mainRows["CompanyId"];
			$tdBGCOLOR=$mainRows["POrderId"]==""?"bgcolor='#FFCC99'":"";
			//收货情况				
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
			//结付情况/**/
			$LockRemark="";
			$checkPay=mysql_query("SELECT Estate,Month FROM $DataIn.cw1_fkoutsheet WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPayRow=mysql_fetch_array($checkPay)){
				$cwEstate=$checkPayRow["Estate"];
				$AskMonth=$checkPayRow["Month"];
				switch($cwEstate){
					case 0://已结付
						$cwEstate="<div class='greenB' title='已结付...货款月份:$AskMonth'>√</div>";
						$LockRemark="已结付，锁定操作";
					break;
					case 2:	//请款中
						$cwEstate="<div class='yellowB' title='请款中...货款月份:$AskMonth'>×.</div>";
						$LockRemark="已请款，锁定操作";
					break;
					case 3://请款通过
						$cwEstate="<div class='yellowB' title='等候结付...货款月份:$AskMonth'>√.</div>";
						$LockRemark="已请款通过，锁定操作";
					break;
					}
				}
			else{
				$cwEstate="<div class='redB'>×</div>";
				}
//尾数
			$Mantissa=$Qty-$rkQty;
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			if($Mantissa<=0){
				$BGcolor="class='greenB'";
				if($Mantissa<0){
					$BGcolor="class='redB'";
					$Mantissa="错误";
					}
				}
			else{
				if($Mantissa==$Qty){
					$BGcolor="class='redB'";
					}
				else{
					$BGcolor="class='yellowB'";
					}
				}			
			//////////////////////////////////////////////////
			///权限///////////////////////////////////////////
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Date</td>";//采购日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseID</td>";//采购单号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				echo"<td width='$unitWidth' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=5;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				$FWidth=$Field[$m]-1;
				echo"<td class='A0001' width='$FWidth' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";	//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";	//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";				//单价
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";		//欠数数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";//结付状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$StockId</td>";//需求流水号
				echo"</tr></table>";
				$i++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Date</td>";//采购日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseID</td>";//采购单号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td width='$unitWidth' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				$FWidth=$Field[$m]-1;
				echo"<td class='A0001' width='$FWidth' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";		//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";		//单价
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";	//欠数数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";		//结付状态
				$m=$m+2;

				echo"<td  class='A0001' width='$Field[$m]' align='center'>$StockId</td>";	//需求流水号
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
?>