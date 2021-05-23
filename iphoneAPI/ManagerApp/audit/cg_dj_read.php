<?php 
//预付订金审核
 $mySql="SELECT S.Id,S.TypeId,S.CompanyId,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,S.OPdatetime,M.Id AS Mid,
		P.Forshort,C.PreChar 
 	FROM $DataIn.cw2_fkdjsheet S 
 	LEFT JOIN $DataIn.cg1_stockmain M ON M.PurchaseID=S.PurchaseID  
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE  S.Estate=2  order by S.Date DESC";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']. "/model/subprogram/purchaseorder_view.php?";
 while($myRow = mysql_fetch_array($Result)) 
 {
      $Id=$myRow["Id"];
     $Date=$myRow["Date"];
     $Forshort=$myRow["Forshort"];
     $Remark=$myRow["Remark"];
     $PreChar=$myRow["PreChar"];
     $Amount=$myRow["Amount"];
     $TypeId=$myRow["TypeId"];
     $Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
     $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
    $OPdatetime=$myRow["OPdatetime"];
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     
     $Mid=$myRow["Mid"];
     $ListSign=$Mid>0?1:0;
     $ImageFile=$Mid>0?$Dir . "Id=".$Mid."&FromPage=iPhone":"";
     //$Date=date("m-d H:i",strtotime($OPdatetime));
     $Date=GetDateTimeOutString($OPdatetime,'');
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Forshort"),
	                     "Col1"=>array("Text"=>"$Type"),
	                     "Col3"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$ListSign","Type"=>"WEB","ImageFile"=>"$ImageFile")
                     );
 }

?>