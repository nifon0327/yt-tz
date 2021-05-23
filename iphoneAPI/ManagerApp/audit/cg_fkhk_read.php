<?php 
//货款返利审核
$mySql="SELECT S.Id,S.CompanyId,S.Amount,S.Remark,S.Rate,S.Date,S.Estate,S.Locks,S.Operator,P.Forshort,C.PreChar,S.OPdatetime 
 	FROM $DataIn.cw2_hksheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE 1 and S.Estate=2  order by S.Date DESC";
	
 $Result=mysql_query($mySql,$link_id);
 //$Dir= "http://".$_SERVER ['HTTP_HOST']. "/download/cgkkbill/";
 while($myRow = mysql_fetch_array($Result)) 
 {
      $Id=$myRow["Id"];
     $Date=$myRow["Date"];
     $CompanyId=$myRow["CompanyId"];
     $Forshort=$myRow["Forshort"];
     $Remark=$myRow["Remark"];
     $Amount=$myRow["Amount"];
     $PreChar=$myRow["PreChar"];
     $Rate=$myRow["Rate"]*100;
     $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
     
     $TempM=substr($Date, 0,7);
     $CheckhkResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS Amount 
		FROM $DataIn.cw1_fkoutsheet S 
        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
		WHERE S.Month='$TempM' AND  S.CompanyId=$CompanyId",$link_id));
        $thisAmount=number_format($CheckhkResult["Amount"],0);
        
     
    $OPdatetime=$myRow["OPdatetime"];
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     $Date=GetDateTimeOutString($OPdatetime,'');
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"0","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Col1"=>array("Text"=>"$Forshort"),
	                     "Col2"=>array("Text"=>"$PreChar$thisAmount"),
	                     "Col3"=>array("Text"=>"$Rate%"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount","IconType"=>"24",'fit'=>'1'),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator") 
                     );
 }

?>