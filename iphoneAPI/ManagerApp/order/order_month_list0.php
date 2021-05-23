<?php 
//每日下单明细
 if (versionToNumber($AppVersion)>=287){
    $AmountMargin="-8,0,0,0";
 }
 else{
	 $AmountMargin="-15,0,0,0";
 }
 
$today=date("Y-m-d"); 
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
        WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$checkMonth' ",$link_id));
$AllOrderAmount=sprintf("%.0f",$orderResult["Amount"]); 

$mySql="SELECT M.CompanyId,C.Forshort,C.Estate,D.PreChar,D.Rate,SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty) AS Amount,
           SUM(S.Price*S.Qty*D.Rate) AS SortAmount 
			 FROM $DataIn.yw1_ordermain M
			 LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
             LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
             LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency  
		    WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$checkMonth' GROUP BY M.CompanyId ORDER BY SortAmount DESC";
 $myResult = mysql_query($mySql,$link_id);
 $dataArray=array();
 while($myRow = mysql_fetch_array($myResult)) 
{
       $CompanyId=$myRow["CompanyId"];
       $Forshort=$myRow["Forshort"];
       $sumQty = number_format($myRow["Qty"]);
       $sumAmount=number_format($myRow["Amount"]);
       $PreChar=$myRow["PreChar"];
       $Rate=$myRow["Rate"];
       
       $SortAmount=$myRow["SortAmount"];
       $Percent=$AllOrderAmount==0?0:($SortAmount/$AllOrderAmount)*100;
       $Percent=$Percent>=1?round($Percent)."%":""; 
       
       if ($ReadPower==1){
          $checkCost= mysql_fetch_array(mysql_query(" SELECT SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
		        FROM $DataIn.yw1_ordermain M
                LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN $DataIn.cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$checkMonth' AND M.CompanyId='$CompanyId'", $link_id)); 
		         
		     $oTheCost=$checkCost["oTheCost"]==""?0:round($checkCost["oTheCost"],2);

		    $profitRMB2PC=$SortAmount==0?0:round((($SortAmount-$oTheCost)/$SortAmount)*100);
		    
		     $ReadProfitColorSign=1; include "order_Profit.php"; $ReadProfitColorSign=0;
		     
		    $profitRMB2PC.="%";
	    }
	    else{
		    $profitRMB2PC="";$profitColor="";
	    }

       $ForshortColor=$myRow["Estate"]==0?"#FF0000":$TITLE_GRAYCOLOR;
       $totalArray=array(
                                      "onTap"=>array("Target"=>"List1","Args"=>"$checkMonth|$CompanyId"),
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"$ForshortColor"),
				                     // "Col1"=>array("Text"=>"$Percent","Margin"=>"-35,0,0,0"),
				                      "Col1"=>array("Text"=>"$sumQty","Margin"=>"6,0,0,0"),
				                      "Col2"=>array("Text"=>"$profitRMB2PC","Color"=>"$profitColor","Margin"=>"-20,0,0,0"),
				                      "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"$AmountMargin")
				                   );  
		$dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"1","data"=>$totalArray); 
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>