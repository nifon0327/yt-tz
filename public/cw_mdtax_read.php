<style type="text/css">
.sc {width:170px;list-style:none;}
.sc ul{ padding:0px; margin:0px;  } 
.sc li {list-style:none;width:100%;margin:0px 0px 0px 0px;}
.span1 {width:80px; text-align:left; display:inline-block;margin:0px 0px 0px 0px;}
.span2 {width:50px; color:#FF0000;text-align:right; display:inline-block;margin:0px 0px 0px 0px; }
</style>

<?php 
//电信-zxq 2012-08-01
//步骤1 $DataIn.'cw14_mdtax'
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=15;
$tableMenuS=500;
//$sumCols="5";
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 免抵退税收益明细表");
$funFrom="cw_mdtax";


$Th_Col="选项|60|序号|40|操作|40|所属公司|60|国税时间|60|免抵退税金额|80|免抵退税发票号|90|结付银行|100|收款日期|70|扫描附件|60|供应商税款|140|报关费用|140|行政费用|140|备注|250|期末留抵税额|80|状态|50|结付凭证|60|操作人|50";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	
$Page_Size = 100;
$ActioToS="1,2,3,4";				

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
$Estate=$Estate==""?3:$Estate;
switch($Estate){
     case "1":$ActioToS="1,2,3,4,14";break;
     case "2":$ActioToS="1,15";break;
     case "3":$ActioToS="1,18,15";break;
     case "0":$ActioToS="1,15";break;
}
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='1' $EstateSTR1>未请款</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>审核通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	$SearchRows.="  and M.Estate=$Estate";
	$monthResult = mysql_query("SELECT M.Taxdate  FROM $DataIn.cw14_mdtaxmain M WHERE 1 $SearchRows group by DATE_FORMAT(M.Taxdate,'%Y-%m') order by M.Taxdate DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
	echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Taxdate"]));
			if($FirstValue==""){$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Taxdate"]));
			if($chooseMonth==$dateValue){
				$theMonth=$dateValue;
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PeaDate=" and DATE_FORMAT(M.Taxdate,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		if($PeaDate==""){
			$PeaDate=" and DATE_FORMAT(M.Taxdate,'%Y-%m')='$FirstValue'";
			}
		echo"</select>&nbsp;";
		}
		$SearchRows.=$PeaDate;
	}
   echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="select M.Id,M.cSign,M.Taxdate,M.TaxNo,M.Taxamount,M.Taxgetdate,M.Attached,M.Estate,M.Remark,M.Operator,M.endTax,M.TaxIncome,M.Proof,B.Title
FROM $DataIn.cw14_mdtaxmain M  
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
WHERE 1 $SearchRows order by Taxdate DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{

	   $m=1;
	   $Id=$myRow["Id"];
	   //echo $Id;
	   $TaxNo=$myRow["TaxNo"];
	   $Taxdate=date("Y-m",strtotime($myRow["Taxdate"]));
	   $endTax=$myRow["endTax"];
	   $Taxamount=$myRow["Taxamount"];
	   $BankName=$myRow["Title"];
	   $Taxgetdate=$myRow["Taxgetdate"];
	   if($Taxgetdate=="0000-00-00")$Taxgetdate="&nbsp;";
	   $Attached=$myRow["Attached"];
	   $Proof=$myRow["Proof"];
	   $TaxIncome=$myRow["TaxIncome"];
	   $Dir=anmaIn("download/cwmdtax/",$SinkOrder,$motherSTR);
		if($Attached!=""){
		    $FileName="M".$TaxNo. ".jpg";
			$Attached=anmaIn($FileName,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-<A onfocus=this.blur();  onclick='ActionToUpFile($Id)' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' alt='上传' width='9' height='9'>
							</A>";
			}
		if($Proof!=""){
		    $pFileName="P".$TaxNo. ".jpg";
			$Proof=anmaIn($pFileName,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$Dir\",\"$Proof\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="-<A onfocus=this.blur();  onclick='ActionToUpFile($Id)' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' alt='上传' width='9' height='9'>
							</A>";
			}
	   $Estate=$myRow["Estate"];
                switch($myRow["Estate"])
                {
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
                        $Locks=0;
                        if(!($Keys & mLOCK)) $LockRemark="记录已经结付,强制锁定!修改需取消结付.";
                        break;
                }
		$Remark=$myRow["Remark"];
		if($Remark=="")$Remark="&nbsp;";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

        $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		
		$Gysfee="&nbsp;";
		$SumGysfee=0;
		$gysResult = mysql_query("select G.Id,G.Forshort,G.Amount,G.Getdate from $DataIn.cw2_gyssksheet G order by G.Id",$link_id);
		if($gysRows = mysql_fetch_array($gysResult)){		   
	          $Gysfee="";
		   do{ 
		      //$Id =$gysRows["Id"];
		      $Forshort =$gysRows["Forshort"];
			  $Amount =$gysRows["Amount"];
			  $Amount=sprintf("%.2f",$Amount);
			  $Getdate =date("Y-m",strtotime($gysRows["Getdate"]));
		   if ($Getdate=="2013-03")  $Getdate="2013-04";
		   if($Taxdate==$Getdate){
		   $SumGysfee=$SumGysfee+$Amount;
		       if($Gysfee==""){
						        $Gysfee="<span class='span1'>$Forshort</span><span class='span2'>$Amount</span> ";
						      }
					      else{
						       $Gysfee=$Gysfee." <span class='span1'>$Forshort</span><span class='span2'>$Amount</span> ";
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
		 
		 if($decRows= mysql_fetch_array($decResult))
		 {
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
		 if($otherRows=mysql_fetch_array($otherResult))
		 {
		    $Otherfee="";
			do{ 
			     $otherfeeNumber=$otherRows["otherfeeNumber"];
				 $otherDate=$otherRows["Date"];
			     $otherAmount=$otherRows["Amount"];
				 $Content=$otherRows["Content"];
				 /*$TypeId=$otherRows["TypeId"];
				 $costSql="select Name from adminitype where TypeId=$TypeId";
				 $costResult=mysql_query($costSql,$link_id);
				 $costRow=mysql_fetch_array($costResult);
				 $otherType=$costRow["Name"];*/
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
   if($myRow["Estate"]==0){
		   	if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_mdtax_upmain\",$Id)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";	
			}
		else{
			$upMian="&nbsp;";
			}
     }
else 			$upMian="&nbsp;";
		 $ValueArray=array(
			array(0=>$upMian,1=>"align='center'"),
			array(0=>$cSign,1=>"align='center'"),
			array(0=>$Taxdate,1=>"align='center'"),
			array(0=>$Taxamount,1=>"align='center'"),
			array(0=>$TaxNo,1=>"align='center'"),
			array(0=>$BankName,1=>"align='center'"),
			array(0=>$Taxgetdate,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Gysfee,1=>"align='left'"),
			array(0=>$Declarationfee,1=>"align='center'"),
			array(0=>$Otherfee,1=>"align='center'"),
			array(0=>$Remark,1=>"align='left'"),
			array(0=>$endTax,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Proof,1=>"align='center'"),
			array(0=>$Operator ,1=>"align='center'"),		
             );
		$SumMonth=$Taxamount-($SumGysfee+$SumDeclarationfee+$SumOtherfee); 
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
		
	$year=date("Y",strtotime($Taxdate));
	
	include "cw_mdtax_sum.php";
	if($TaxIncome!=$SumMonth){
	  $updateSql="update $DataIn.cw14_mdtaxmain SET TaxIncome='$SumMonth' where Id='$Id' ";
	  $IncomeResult=mysql_query($updateSql,$link_id);
	}
	}
else{
	noRowInfo($tableWidth,"");	
  	}
  	/*
  	//鼠宝专用
  	if ($_SESSION["Login_cSign"]==3){
	  		$SUMAmonut=number_format($SumTaxamount+2615436.70,2);
            $SUMSY=number_format($sumTax+703856.43,2);
            $tempTRStr=" <tr><td align='center' height='30' class='A0111'>3</td>
    <td class='A0101'>鼠宝包装系统转入(2010.12~2012.01)</td>
	<td class='A0101' align='right'><span class='redB' >703,856.43</span> </td>
	<td class='A0101' align='right'><span class='redB' >2,615,436.70</span></td>
	<td class='A0101' bgcolor=\"#CCCCCC\" >&nbsp;</td>
	</tr>
	<tr><td align='center' height='30' class='A0111'>4</td>
    <td class='A0101'>2-3项合计</td>
	<td class='A0101' align='right'><span class='redB' >$SUMSY</span> </td>
	<td class='A0101' align='right'><span class='redB' >$SUMAmonut</span></td>
	<td class='A0101' bgcolor=\"#CCCCCC\" >&nbsp;</td>
	</tr>
";
  	}
  	*/
  
echo"
	<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='ffffff'>
	<tr bgcolor=\"#CCCCCC\" align='center'>
    <td width='100' height='30' class='A0111'>序号</td>
    <td width='570' class='A0101'>项目</td>
	<td class='A0101' width='140'>免抵退税收益</td>
	<td class='A0101' width='140'>免抵退税额</td>
	<td class='A0101' width='632'>&nbsp;</td>
	</tr>
    <tr>
    <td align='center' height='30' class='A0111'>1</td>
    <td class='A0101'>$Taxdate 月</td>
	<td class='A0101' align='right'><span class='redB' >".number_format($SumMonth,2)."</span></td>
	<td class='A0101' align='right'><span class='redB' >".number_format($Taxamount,2)."</span></td>
	<td class='A0101' bgcolor=\"#CCCCCC\" >&nbsp;</td>
	</tr>
	<tr><td align='center' height='30' class='A0111'>2</td>
     <td class='A0101'>截止至".$Taxdate."月份总计</td>
	<td class='A0101' align='right'><span class='redB' >".number_format($sumTax,2)."</span> </td>
	<td class='A0101' align='right'><span class='redB' >".number_format($SumTaxamount,2)."</span></td>
	<td class='A0101' bgcolor=\"#CCCCCC\" >&nbsp;</td>
	</tr>
	 $tempTRStr
	</table>";

//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
function ActionToUpFile(upId){
	var funFrom=document.form1.funFrom.value;
        document.form1.action=funFrom+"_upfile.php?ActionId=84&Id="+upId;
        document.form1.target="_self";
        document.form1.submit();		
        document.form1.target="_self";
        document.form1.action="";
}
</script>
