<?php 
//免抵退审核  LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
include "../../basic/downloadFileIP.php";
$cztest = ' AND M.Estate=2 ';
$czLimt = '';
/*
if ($test_cz) {
	$cztest = '';
	$czLimt = 'limit 0,3';
}
*/
$mySql="select M.Id,M.Taxdate,M.TaxNo,M.Taxamount,M.Taxgetdate,M.Attached,M.Estate,M.Remark,M.Operator,M.endTax,M.TaxIncome,M.Proof,B.Title
FROM $DataIn.cw14_mdtaxmain M  
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
WHERE 1 $cztest  order by Taxdate DESC $czLimt ";

 $Result=mysql_query($mySql,$link_id);
 $Dir=  "http://".$_SERVER ['HTTP_HOST']. "/download/cwmdtax/";
 while($myRow = mysql_fetch_array($Result)) 
 {

	
	 $Id=$myRow["Id"];
	   //echo $Id;
	   $TaxNo=$myRow["TaxNo"];
	   $Taxdate=date("Y-m",strtotime($myRow["Taxdate"]));
	   $endTax=$myRow["endTax"];
	   $endTax = number_format($endTax,2);
	   $Taxamount=$myRow["Taxamount"];
	   $Taxamount = number_format($Taxamount,2);
	   $BankName=$myRow["Title"];
	   $Taxgetdate=$myRow["Taxgetdate"];
	   
	   $Attached=$myRow["Attached"];
	   $Proof=$myRow["Proof"];
	   $TaxIncome=$myRow["TaxIncome"];
	    $TaxIncome = number_format($TaxIncome,2);
	$ImageList = array();
  if ($Attached && $Attached != "") {
  	$ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>$Dir.$Attached );
  
  }
	
	if ($Proof && $Proof != "" && file_exists($Dir.$Proof )) {
  	$ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>$Dir.$Proof );
  
  }
	



    $Date = $myRow["Taxdate"];
    //$Date=date("m-d H:i",strtotime($OPdatetime));
   $Date=GetDateTimeOutString($Date,'');
   

    $Remark=$myRow["Remark"];
 
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
	 
	 
	 		$SumGysfee=0;
			$dataL1 = array();
		$gysResult = mysql_query("select G.Id,G.Forshort,G.Amount,G.Getdate from $DataIn.cw2_gyssksheet G order by G.Id",$link_id);
		if($gysRows = mysql_fetch_array($gysResult)){		   
	          
		   do{ 
		      //$Id =$gysRows["Id"];
		      $Forshort =$gysRows["Forshort"];
			  $Amount =$gysRows["Amount"];
			  $Amount=sprintf("%.2f",$Amount);
			  $Getdate =date("Y-m",strtotime($gysRows["Getdate"]));
		    
		   if($Taxdate==$Getdate){
		   $SumGysfee+=$Amount;
		   
						       $dataL1[]=array("Cols"=>1,"Name"=>"$Forshort","Text"=>"¥".$Amount);
					          
						
					}
                }while($gysRows = mysql_fetch_array($gysResult));	
				$SumGysfee=sprintf("%.2f",$SumGysfee);
			}
			$Gysfee=$Gysfee==""?"&nbsp;":$Gysfee;
			
			
		
	 
	  $SumDeclarationfee=0;
	  $dataL2 = array();
		 $decResult = mysql_query("SELECT T.InvoiceNumber,F.declarationCharge  from $DataIn.cw14_mdtaxsheet T,$DataIn.ch4_freight_declaration F,$DataIn.ch1_shipmain M 
		  where M.Id=F.chId and M.InvoiceNO=T.InvoiceNumber and T.TaxNo='$TaxNo'",$link_id);
		 
		 if($decRows= mysql_fetch_array($decResult))
		 {
		    
			 do{
			     $InvoiceNumber=$decRows["InvoiceNumber"];
				 $declarationCharge=$decRows["declarationCharge"];
				 $declarationCharge=sprintf("%.2f",$declarationCharge);
				 $SumDeclarationfee+=$declarationCharge;
				  $dataL2[]=array("Cols"=>1,"Name"=>"$InvoiceNumber","Text"=>"¥".$declarationCharge);
				 
                }while($decRows= mysql_fetch_array($decResult));	
				$SumDeclarationfee=sprintf("%.2f",$SumDeclarationfee);
			}
			
			$dataL3 = array();
	  $SumOtherfee=0;
		 $otherResult =mysql_query("select S.Date,S.Amount from $DataIn.cw14_mdtaxfee O,hzqksheet S  where S.Id=O.otherfeeNumber and O.TaxNo='$TaxNo' ",$link_id);
		 if($otherRows=mysql_fetch_array($otherResult))
		 {
		   
			do{ 
			   
				 $otherDate=$otherRows["Date"];
			     $otherAmount=$otherRows["Amount"];
			
				 /*$TypeId=$otherRows["TypeId"];
				 $costSql="select Name from adminitype where TypeId=$TypeId";
				 $costResult=mysql_query($costSql,$link_id);
				 $costRow=mysql_fetch_array($costResult);
				 $otherType=$costRow["Name"];*/
				 $otherAmount=sprintf("%.2f",$otherAmount);
			     $SumOtherfee=$SumOtherfee+$otherAmount;
			   
			   $dataL3[]=array("Cols"=>1,"Name"=>"$otherDate","Text"=>"¥".$otherAmount);
			   }while($otherRows=mysql_fetch_array($otherResult));
			   $SumOtherfee=sprintf("%.2f",$SumOtherfee);
		  }
     
     $listArray=array();
     $listArray[]=array("subIndex"=>7,"leaf"=>0,"Title"=>array("Text"=>"供应商税款:"),"Col1"=>array("Text"=>"¥$SumGysfee"),"Tag"=>"List","onTap"=>array("Value"=>1,"hidden"=>"1"),"data"=>$dataL1,"bg"=>"1");
     $listArray[]=array("subIndex"=>7,"leaf"=>0,"Title"=>array("Text"=>"报关费用:"),"Col1"=>array("Text"=>"¥$SumDeclarationfee"),"Tag"=>"List","onTap"=>array("Value"=>1,"hidden"=>"1"),"bg"=>"1","data"=>$dataL2);
	 $listArray[]=array("subIndex"=>7,"leaf"=>0,"Title"=>array("Text"=>"行政费用:"),"Col1"=>array("Text"=>"¥$SumOtherfee"),"Tag"=>"List","onTap"=>array("Value"=>1,"hidden"=>"1"),"bg"=>"1","data"=>$dataL3);
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Taxdate"),
						 "Month"=>array("Text"=>"$TaxNo"),
	                     "Col1"=>array("Text"=>"¥$Taxamount","Color"=>"#000000"),
	                   
	                     "Col3"=>array("Text"=>"¥$endTax","Color"=>"#000000","Margin"=>"-20,0,20,0"),
	                     "Col4"=>array("Text"=>"¥$TaxIncome","Color"=>"#000000","Margin"=>"-30,0,30,0"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList,"dataSub"=>$listArray)
                     );
 }

?>