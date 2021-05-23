<?php 
//每日下单明细
 if (versionToNumber($AppVersion)>=287){
    $AmountMargin="-8,0,0,0";
 }
 else{
	 $AmountMargin="-15,0,0,0";
 }
 
 //权限
   $ReadPower=0;
  if ($LoginNumber!=""){
			    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
			    if($TRow = mysql_fetch_array($TResult)){
			       $ReadPower=1;
			    }
			    else{
			       $ReadPower=0;
			    }
}                   
 $orderResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount    
        FROM $DataIn.yw1_ordersheet S
    	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE M.OrderDate='$checkDate' ",$link_id));
$AllOrderAmount=sprintf("%.0f",$orderResult["Amount"]); 

$mySql="SELECT M.CompanyId,C.Forshort,D.PreChar,D.Rate,SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty) AS Amount,
           SUM(S.Price*S.Qty*D.Rate) AS SortAmount,SUM(IF(F.Percent<3,1,0)) AS LowProfit,C.Estate  
			 FROM $DataIn.yw1_ordermain M
			 LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
			 LEFT JOIN $DataIn.yw1_orderprofit  F ON F.POrderId=S.POrderId  
             LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
             LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency  
		    WHERE M.OrderDate='$checkDate' GROUP BY M.CompanyId ORDER BY SortAmount DESC";
 $myResult = mysql_query($mySql,$link_id);
 $dataArray=array();$iNumber=8;
 while($myRow = mysql_fetch_array($myResult)) 
{
       $CompanyId=$myRow["CompanyId"];
       $Forshort=$myRow["Forshort"];
       
       $sumQty = number_format($myRow["Qty"]);
       $sumAmount=number_format($myRow["Amount"]);
       $PreChar=$myRow["PreChar"];
       $Rate=$myRow["Rate"];
       $LowProfit=$myRow["LowProfit"];
       
       $SortAmount=$myRow["SortAmount"];
       $Percent=$AllOrderAmount==0?0:($SortAmount/$AllOrderAmount)*100;
       $Percent=$Percent>=1?round($Percent)."%":""; 
       
       if ($ReadPower==1){
           $checkCost= mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.OrderQty*IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,A.Price)*IFNULL(C.Rate,1),0)) AS oTheCost 
		        FROM $DataIn.yw1_ordermain M
                LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN $DataIn.cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 	
		        WHERE  M.OrderDate='$checkDate' AND M.CompanyId='$CompanyId' AND A.Level=1 ", $link_id)); 
		       
		     $oTheCost=$checkCost["oTheCost"]==""?0:round($checkCost["oTheCost"],2);
		     $profitRMB2PC=$SortAmount==0?-100:round((($SortAmount-$oTheCost)/$SortAmount)*100);
		     $profitRMB2PC=$profitRMB2PC==-0?0:$profitRMB2PC;
		     $ReadProfitColorSign=1;  include "order_Profit.php"; $ReadProfitColorSign=0;
		     
		    $profitRMB2PC.="%";
	    }
	    else{
		    $profitRMB2PC="";$profitColor="";
	    }
       $ForshortColor=$myRow["Estate"]==0?"#FF0000":$TITLE_GRAYCOLOR;
       
       $iNumber--;
       $totalArray=array(
                                      "onTap"=>array("Target"=>"List1","Args"=>"$checkDate|$CompanyId"),
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"$ForshortColor"),
				                     // "Col1"=>array("Text"=>"$Percent","Margin"=>"-35,0,0,0"),
				                      "Col1"=>array("Text"=>"$sumQty","Margin"=>"-4,0,0,0"),
				                      "Col2"=>array("Text"=>"$profitRMB2PC","Color"=>"$profitColor","Margin"=>"-20,0,0,0"),
				                      "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"$AmountMargin"),
				                      "iNumber"=>"$LowProfit"
				                   );  
		$dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"1","data"=>$totalArray); 
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>