
<style type="text/css">
.sc {width:170px;list-style:none;}
.sc ul{ padding:0px; margin:0px;  } 
.sc li {list-style:none;width:100%;margin:0px 0px 0px 0px;}
.span1 {width:80px; text-align:left; display:inline-block;margin:0px 0px 0px 0px;}
.span2 {width:60px; color:#FF0000;text-align:right; display:inline-block;margin:0px 0px 0px 0px; }
</style><?php
//25	退税金额						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("
select M.Id,M.Taxdate,M.TaxNo,M.Taxamount,M.Taxgetdate,M.Attached,M.Estate,M.Remark,M.Operator,M.endTax,M.TaxIncome,M.Proof,B.Title
FROM $DataIn.cw14_mdtaxmain M  
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
WHERE M.Id='$Id_Remark' order by M.Taxdate DESC
",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='30' class='A1111'>序号</td>
<td width='60' class='A1101'>国税时间</td>
<td width='100' class='A1101'>免抵退税金额</td>
<td width='100' class='A1101'>免抵退税发票号</td>
<td width='100' class='A1101'>结付银行</td>
<td width='80' class='A1101'>收款日期</td>
<td width='40' class='A1101'>扫描<br>附件</td>
<td width='140' class='A1101'>供应商税款</td>
<td width='40' class='A1101'>报关费用</td>
<td width='80' class='A1101'>行政费用</td>
<td width='200' class='A1101'>备注</td>
<td width='80' class='A1101'>期末留抵税额</td>
<td width='40' class='A1101'>结付<br>凭证</td>
</tr>";
$i=1;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Id=$checkRow["Id"];
	   $TaxNo=$checkRow["TaxNo"];
	   $Taxdate=date("Y-m",strtotime($checkRow["Taxdate"]));
	   $endTax=$checkRow["endTax"];
	   $Taxamount=$checkRow["Taxamount"];
	   $BankName=$checkRow["Title"];
	   $Taxgetdate=$checkRow["Taxgetdate"];
	   if($Taxgetdate=="0000-00-00")$Taxgetdate="&nbsp;";
	   $Attached=$checkRow["Attached"];
	   $Proof=$checkRow["Proof"].".pdf";
	   $TaxIncome=$checkRow["TaxIncome"];
	   $Dir=anmaIn("download/cwmdtax/",$SinkOrder,$motherSTR);
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-<A onfocus=this.blur();  onclick='ActionToUpFile($Id)' style='CURSOR: pointer;color:#FF6633'><img src='../images/upFile.gif' style='background:#F00' alt='上传' width='9' height='9'></A>";
			}
		if($Proof!=""){
			$Proof=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$Dir\",\"$Proof\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="-<A onfocus=this.blur();  onclick='ActionToUpFile($Id)' style='CURSOR: pointer;color:#FF6633'><img src='../images/upFile.gif' style='background:#F00' alt='上传' width='9' height='9'></A>";
			}
	   	$Estate=$checkRow["Estate"];
		switch($checkRow["Estate"]){
			case 1:
				$Estate="<div class='redB' title='未请款,税款未收到...'>×</div>";
                break;
			case 2:
				$Estate="<div class='yellowB' title='请款中,税款未收到....'>×.</div>";
				break;
			case 3:
				$Estate="<div class='yellowB' title='请款通过,等候结付!税款未收到'>√.</div>";
				break;
			case 0:
				$Estate="<div class='greenB' title=',税款已收到,已结付,结付日期：$PayDate'>√</div>";
				if(!($Keys & mLOCK)) $LockRemark="记录已经结付,强制锁定!修改需取消结付.";
					break;
                }
		$Remark=$checkRow["Remark"];
		if($Remark=="")$Remark="&nbsp;";
		$Operator=$checkRow["Operator"];
		include "../model/subprogram/staffname.php";

		$Gysfee="&nbsp;";
		$SumGysfee=0;
		$gysResult = mysql_query("select G.Id,G.Forshort,G.Amount,G.Getdate from $DataIn.cw2_gyssksheet G order by G.Id",$link_id);
		if($gysRows = mysql_fetch_array($gysResult)){		   
			$Gysfee="";
		   	do{ 
				$Forshort =$gysRows["Forshort"];
			 	$Amount =$gysRows["Amount"];
			 	$Amount=sprintf("%.2f",$Amount);
			  	$Getdate =date("Y-m",strtotime($gysRows["Getdate"]));
		   		if ($Getdate=="2013-03")  $Getdate="2013-04";
		   		if($Taxdate==$Getdate){
		   			$SumGysfee=$SumGysfee+$Amount;
		       		if($Gysfee==""){
						$Gysfee="<span class='span1'>$Forshort</span><span class='span2'>$Amount</span>";
						}
					else{
						$Gysfee=$Gysfee." <span class='span1'>$Forshort</span><span class='span2'>$Amount</span>";
						}
					}
                }while($gysRows = mysql_fetch_array($gysResult));	
				$SumGysfee=sprintf("%.2f",$SumGysfee);
			}
			$Gysfee=$Gysfee==""?"&nbsp;":$Gysfee;
		 $Declarationfee="&nbsp;";
		 $SumDeclarationfee=0;
		 $decResult = mysql_query("SELECT T.InvoiceNumber,F.declarationCharge  from $DataIn.cw14_mdtaxsheet T,$DataIn.ch4_freight_declaration F,$DataIn.ch1_shipmain M 
		  where M.Id=F.chId and M.InvoiceNO=T.InvoiceNumber and T.TaxNo='$TaxNo'",$link_id);	 
		 if($decRows= mysql_fetch_array($decResult)){
		     $Declarationfee="";
			 do{
			     $InvoiceNumber=$decRows["InvoiceNumber"];
				 $declarationCharge=$decRows["declarationCharge"];
				 $declarationCharge=sprintf("%.2f",$declarationCharge);
				 $SumDeclarationfee=$SumDeclarationfee+$declarationCharge;
				 if($Declarationfee==""){
						        $Declarationfee="<span class='span1'>$InvoiceNumber</span><span class='span2'>$declarationCharge</span> ";
						      }
					      else{
						       $Declarationfee=$Declarationfee." <span class='span1'>$InvoiceNumber</span><span class='span2'>$declarationCharge</span> ";
					          }
				 
                }while($decRows= mysql_fetch_array($decResult));	
				$SumDeclarationfee=sprintf("%.2f",$SumDeclarationfee);
			}
			$Declarationfee=$Declarationfee==""?"&nbsp;":$Declarationfee;
			
		
		 $Otherfee="&nbsp;";
		 $SumOtherfee=0;
		 $otherResult =mysql_query("select O.otherfeeNumber,O.TaxNo,S.Date,S.Amount,S.TypeId,S.Content from $DataIn.cw14_mdtaxfee O,hzqksheet S  where S.Id=O.otherfeeNumber and O.TaxNo='$TaxNo' ",$link_id);
		 if($otherRows=mysql_fetch_array($otherResult)){
		    $Otherfee="";
			do{ 
			     $otherfeeNumber=$otherRows["otherfeeNumber"];
				 $otherDate=$otherRows["Date"];
			     $otherAmount=$otherRows["Amount"];
				 $Content=$otherRows["Content"];
				 $otherAmount=sprintf("%.2f",$otherAmount);
			     $SumOtherfee=$SumOtherfee+$otherAmount;
			    if($Otherfee==""){
						        $Otherfee="<span class='span1'>$otherDate</span><span class='span2'>$otherAmount</span> ";
						      }
					      else{
						       $Otherfee=$Otherfee." <span class='span1'>$otherDate</span><span class='span2'>$otherAmount</span> ";
					          }
				 
			   
			   }while($otherRows=mysql_fetch_array($otherResult));
			   $SumOtherfee=sprintf("%.2f",$SumOtherfee);
		  }
		  $Otherfee=$Otherfee==""?"&nbsp;":$Otherfee;
		  $showPurchaseorder="<img onClick='Showotherfee(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏费用明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;
			</div><br></td></tr></table>";
   if($checkRow["Estate"]==0){
		   	if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_mdtax_upmain\",$Id)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";	
			}
		else{
			$upMian="&nbsp;";
			}
     }
else 			$upMian="&nbsp;";

		//输出首行前段
		echo"<tr><td class='A0101' align='center' height='20'>$i</td>";	
		echo"<td class='A0101' align='center'>$Taxdate</td>";
		echo"<td class='A0101'>$Taxamount</td>";
		echo"<td class='A0101'>$TaxNo</td>";
		echo"<td class='A0101'>$BankName</td>";
		echo"<td class='A0101'  align='center'>$Taxgetdate</td>";
		echo"<td class='A0101' align='center'>$Attached</td>";
		echo"<td class='A0101' align='right'>$Gysfee</td>";
		echo"<td class='A0101' align='center'>$Declarationfee</td>";
		echo"<td  class='A0101' align='center'>$Otherfee</td>";
		echo"<td  class='A0101' align='center'>$Remark</td>";	
		
		echo"<td  class='A0101' align='center'>$endTax</td>";	
		echo"<td  class='A0101' align='center'>$Proof</td>";	
		echo"</tr>";
		$i++;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
?>