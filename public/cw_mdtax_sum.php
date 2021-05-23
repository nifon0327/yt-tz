<?php 
//电信-zxq 2012-08-01
/*$sumGysfee=0;
$sumBgfee=0;
$sumXzfee=0;
$sumTaxfee=0;
//============供应商税款累计和
$sumGysResult=mysql_query("select G.Amount,G.Getdate from $DataIn.cw2_gyssksheet G where G.Getdate!='0000-00-00' and DATE_FORMAT(G.Getdate,'%Y-%m')<='$Taxdate' ",$link_id);
if($sumGysRow=mysql_fetch_array($sumGysResult)){
   do{   
         $Amount =$sumGysRow["Amount"];
	     $Amount=sprintf("%.2f",$Amount);
		 $sumGysfee=$sumGysfee+$Amount;
		 }while($sumGysRow=mysql_fetch_array($sumGysResult));
	} 
	//echo $sumGysfee;
//============报关费用累计和
$sumBgResult=mysql_query("SELECT T.InvoiceNumber,F.declarationCharge  from $DataIn.cw14_mdtaxsheet T,$DataIn.ch12_declaration F,$DataIn.ch1_shipmain M 
		  where M.Id=F.chId and M.InvoiceNO=T.InvoiceNumber and DATE_FORMAT(T.Date,'%Y-%m')<='$Taxdate'",$link_id);
if($sumBgRow=mysql_fetch_array($sumBgResult)){
    do{
	   
	   $Bgfee=$sumBgRow["declarationCharge"];
	   $Bgfee=sprintf("%.2f",$Bgfee);
	   $sumBgfee=$sumBgfee+$Bgfee;
	   }while($sumBgRow=mysql_fetch_array($sumBgResult));
	 }
	 //echo $sumBgfee;
//=============行政费用累计和
$sumXzResult=mysql_query("select O.otherfeeNumber,O.TaxNo,S.Amount from $DataIn.cw14_mdtaxfee O,hzqksheet S  where S.Id=O.otherfeeNumber and DATE_FORMAT(O.Date,'%Y-%m')<='$Taxdate'",$link_id);
if($sumXzRow=mysql_fetch_array($sumXzResult)){
   do{
       $Xzfee=$sumXzRow["Amount"];
      $Xzfee=sprintf("%.2f",$Xzfee);
	  $sumXzfee=$sumXzfee+$Xzfee;

	  }while($sumXzRow=mysql_fetch_array($sumXzResult));
	}  
	 //echo $sumXzfee;
//==========免抵退税金额累计和
$sumTaxResult=mysql_query("select Taxamount from $DataIn.cw14_mdtaxmain  where DATE_FORMAT(Taxdate,'%Y-%m')<='$Taxdate'",$link_id);
if($sumTaxRow=mysql_fetch_array($sumTaxResult)){
   do{
      $Taxamount=$sumTaxRow["Taxamount"];
	  $Taxamount=sprintf("%.2f",$Taxamount); 
	  $sumTaxfee=$sumTaxfee+$Taxamount;
	  }while($sumTaxRow=mysql_fetch_array($sumTaxResult));
	}
	  //echo $sumTaxfee;
//===========免抵退税累计收益额

$summdtax=$sumTaxfee-$sumGysfee-$sumBgfee-$sumXzfee;
*/
$sumSql="SELECT SUM(TaxIncome) AS sumTax,SUM(Taxamount) AS SumTaxamount  From $DataIn.cw14_mdtaxmain WHERE DATE_FORMAT(Taxdate,'%Y-%m')<='$Taxdate'";
$sumResult=mysql_query($sumSql,$link_id);
if($sumRows=mysql_fetch_array($sumResult)){
   $sumTax=$sumRows["sumTax"];
   $SumTaxamount=$sumRows["SumTaxamount"];
   }
  $sumTax=sprintf("%.2f",$sumTax);
?>



