<?
//非BOM货款 ewen 2013-11-25 ＯＫ
$MonthSTR=$Month==""?"":" AND A.Month='$Month'";
$PayMonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录";	$EstateSTR=" AND A.Estate='0'"; break;
	case "W":$DataTSTR="未结付记录";$EstateSTR=" AND A.Estate='3'"; break;
	case "A":$DataTSTR="全部记录";$EstateSTR=" AND (A.Estate='0' OR A.Estate='3')"; break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.mainType IN($Parameters)";

echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1180px;height:480px' align='left'>
	<tr bgcolor='#36C' align='center' style='Color:#FFF'>
		<td width='40' height='20'>序号</td>
		<td width='80'>下单日期</td>
		<td width='100'>供应商</td>
		<td width='60'>采购单号</td>
		<td width='65'>货款</td>
		<td width='60'>请款月份</td>
		<td width='65'>请款金额</td>
		<td width='65'>转RMB</td>
		<td >配件名称</td>
		<td width='50'>数量</td>
		<td width='55'>单价</td>
		<td width='65'>金额</td>
		<td width='50'>欠数</td>
		<td width='50'>申购人</td>
		<td width='70'>申购时间</td>
	</tr>
	<tr>
		<td colspan='15' height='450px'>
		<div style='width:1181;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
		<table cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1180px;height:450px' align='center'>
		";
		//读取记录：以入库的数量来计算金额
		$checkSql=mysql_query("SELECT A.Id,A.cgMId,A.Amount,(A.Amount*D.Rate)AS RmbAmount,A.Month,A.Estate,A.Remark,A.Locks,A.Date,A.Operator,DATE_FORMAT(G.Date,'%Y-%m-%d')  AS cgDate,G.PurchaseID,G.Remark AS mainRemark,G.BuyerId,
			C.Forshort,C.CompanyId,D.PreChar,
			G.taxAmount as S_taxAmount,G.shipAmount as S_shipAmount,
			F.Name
			FROM $DataIn.nonbom11_qksheet A
			LEFT JOIN $DataIn.nonbom6_cgmain G ON G.Id=A.cgMId
			LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
			LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
			LEFT JOIN $DataPublic.staffmain F ON F.Number=G.BuyerId
			WHERE 1 $Parameters $MonthSTR $EstateSTR ORDER BY A.Month,A.Date DESC
			",$link_id);	
		$i=1;
		$SumAmount=0;
		if($checkRow=mysql_fetch_array($checkSql)){
			$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
			$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
			$DirRK=anmaIn("download/nonbom_rk/",$SinkOrder,$motherSTR);
			do{			
				$Id=$checkRow["Id"];
				$cgMId=$checkRow["cgMId"];
				$Mid=$cgMId;
				$cgDate=$checkRow["cgDate"];
				$PurchaseID=$checkRow["PurchaseID"];
				$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
				$mainRemark=$checkRow["mainRemark"]==""?"&nbsp;":$checkRow["mainRemark"];
				$PurchaseID="<a href='../nonbom/nonbom6_view.php?f=$MidSTR' target='_blank' title='$mainRemark'>$PurchaseID</a>";
				$Name=$checkRow["Name"];
				$Forshort=$checkRow["Forshort"];
				$Remark=$checkRow["Remark"]==""?"&nbsp;":$checkRow["Remark"];
				$Month=$checkRow["Month"];
				//本次待结付数据
				$checkHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS cgAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$Mid' ",$link_id));		
				$cgAmount=sprintf("%.2f",$checkHk["cgAmount"]);   //采购总款
		
				$Amount=sprintf("%.2f",$checkRow["Amount"]);//本次货款
				$RmbAmount=sprintf("%.2f",$checkRow["RmbAmount"]);//本次RMB货款
				$Estate= "<div class='redB'>未审核</div>";
				$Date=$checkRow["Date"];
				$Operator=$checkRow["Operator"];
				$PreChar=$checkRow["PreChar"];
				$Sum_RmbAmount+=$RmbAmount;
				//0值不显示
				$Amount=zerotospace($Amount);
				$RmbAmount=zerotospace($RmbAmount);
				echo"
					<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
					<td width='38' height='20' align='center'>$i</td>
					<td width='80' align='center'>$cgDate</td>
					<td width='100'>$Forshort</td>
					<td width='60' align='center'>$PurchaseID</td>
					<td width='65' align='right'><span class='redB'>$cgAmount</span></td>
					<td width='60' align='center'>$Month</td>
					<td width='65' align='right'><span class='redB'>$Amount</span></td>		
					<td width='65' align='right'><span class='redB'>$RmbAmount</span></td>							<td colspan='7'>
					<table cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;'>
					";
					//读取明细
					$checkSubSql=mysql_query("SELECT A.Id,A.Mid,A.GoodsId,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,A.rkSign,A.Estate,A.Locks,A.Date,A.Remark,B.GoodsName,B.Attached,C.Name
						FROM $DataIn.nonbom6_cgsheet A
						LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId 
						LEFT JOIN $DataPublic.staffmain C ON C.Number=A.Operator   
						WHERE A.Mid='$cgMId'
						",$link_id);
					if($checkSubRow = mysql_fetch_array($checkSubSql)){
						$j=1;
						do{	
							//add by cabbage 20141201 app紀錄檔案的下載路徑
							$appAttachedPath = "";
															
							$sheetId=$checkSubRow["Id"];
							$GoodsId=$checkSubRow["GoodsId"];
							$GoodsName=$checkSubRow["GoodsName"];
							$Attached=$checkSubRow["Attached"];
							if($Attached==1 && $goodsId!='70442'){
								$Attached=$GoodsId.".jpg";
								//app用
								$appAttachedPath = "/download/nonbom/".$Attached;
								$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
								$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
								}
							//快递处理
							if($GoodsId=='70442'){
								$checkFromSql=mysql_query("SELECT A.fromMid,B.PurchaseID FROM $DataIn.nonbom6_cgsheet A LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.fromMid WHERE A.Id='$sheetId' AND B.Id!=A.Mid",$link_id);
								if($checkFromRow=mysql_fetch_array($checkFromSql)){
									$fromPurchaseID=$checkFromRow["PurchaseID"];	//源采购单号
									$fromMid=$checkFromRow["fromMid"];					//源采购单ID
									$fromMid=anmaIn($fromMid,$SinkOrder,$motherSTR);
									$GoodsName.="<a href='../public/nonbom6_view.php?f=$fromMid' target='_blank'>(采购单:$fromPurchaseID)</a>";
									//app用
									$appAttachedPath = "/public/nonbom6_view.php?f=$fromMid";
									}
								}
							$Qty=$checkSubRow["Qty"];
							$Price=$checkSubRow["Price"];
							$Amount=sprintf("%.2f",$checkSubRow["Amount"]);
							//入库数量
							$cgId=$checkSubRow["Id"];
							$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' AND cgId='$cgId'",$link_id);
							$rkQty=mysql_result($rkTemp,0,"Qty");
							$rkQty=$rkQty==""?0:del0($rkQty);
							$wsQty=$Qty-$rkQty;//欠数
							$wsQty=$wsQty>0?"<span class='redB'>".$wsQty."</span>":($wsQty==0?"&nbsp;":"<span class='yellowB'>?".$wsQty."</span>");
							$Name=$checkSubRow["Name"];
							$Date=$checkSubRow["Date"];
							$ClassStr=$j==1?"":"class='A1000'";
							echo "<tr>
							<td width='305' $ClassStr>$GoodsName</td>
							<td width='50' align='right' $ClassStr>$Qty</td>
							<td width='55' align='right' $ClassStr>$Price</td>
							<td width='65' align='right' $ClassStr>$Amount</td>
							<td width='40' align='right' $ClassStr>$wsQty</td>
							<td width='50' align='right' $ClassStr>$Name</td>
							<td width='70' align='right' $ClassStr>$Date</td></tr>";
							$j++;
							
							//add by cabbage 20141126 app採集單月紀錄
							$detailList[] = array(
								"Month" => $checkRow["Month"],
								"Forshort" => $checkRow["Forshort"],
								"GoodsName" => $checkSubRow["GoodsName"],
								"PurchaseID" => $checkRow["PurchaseID"],
								"Qty" => $checkSubRow["Qty"],
								"PreChar"=> $PreChar,
								"Amount" => $checkSubRow["Amount"],
								"wsQty" => $Qty-$rkQty,
								"Name" => $checkSubRow["Name"],
								"Remark"=> $checkSubRow["Remark"],
								"FilePath" => $appAttachedPath,
							);
							
							}while($checkSubRow = mysql_fetch_array($checkSubSql));
						}
					echo"</table>
					</td>
					</tr>";
				$i++;
				}while($checkRow=mysql_fetch_array($checkSql));
			}
		for($j=$i;$j<27;$j++){//补空行
			echo"
			<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td height='20'>$j</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan='7'>&nbsp;</td>
			</tr>";
			}
		$Sum_RmbAmount=number_format(sprintf("%.0f",$Sum_RmbAmount));
		echo"</table>
		</div>
		</td></tr>
		<tr bgcolor='#36C' align='center' style='Color:#FFF'><td height='20'>合计</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align='right'>$Sum_RmbAmount</td>
		<td  colspan='7'>&nbsp;</td></tr>
	</table>";
?>