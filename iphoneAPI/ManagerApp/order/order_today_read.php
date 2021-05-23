<?php 
//每日新单
 if (versionToNumber($AppVersion)>=287){
    $Col3Frame="205,2, 102, 30";
 }
 else{
   $Col3Frame="210, 2, 103, 30";
 }
 
$today=date("Y-m-d");$m=0;
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

$SearchRows=" TIMESTAMPDIFF( Month, M.OrderDate , CURDATE())<=2 "; 

$SearchRows.=$MC_FactoryCheckSign==1?' AND WEEKDAY(M.OrderDate)<5 ':'';
                          
$mySql="SELECT M.OrderDate,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,SUM(IF(F.Percent<3,1,0)) AS LowProfit  
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	LEFT JOIN $DataIn.yw1_orderprofit  F ON F.POrderId=S.POrderId  
	WHERE $SearchRows GROUP BY  M.OrderDate ORDER BY OrderDate DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;
while($myRow = mysql_fetch_array($Result)) {
	  $OrderDate=$myRow["OrderDate"];
	  $DateTitle=date("m-d",strtotime($OrderDate));
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
      $LowProfit=$myRow["LowProfit"];
     
     if ($ReadPower==1){
              $checkCost= mysql_fetch_array(mysql_query(" SELECT SUM(IFNULL(A.OrderQty*IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)) ,D.costPrice,A.Price)*IFNULL(C.Rate,1),0)) AS oTheCost
		        FROM $DataIn.yw1_ordermain M
                LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN $DataIn.cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		        WHERE  M.OrderDate='$OrderDate' AND A.Level=1 ", $link_id)); 
		      $oTheCost=$checkCost["oTheCost"]==""?0:round($checkCost["oTheCost"],2);
		      $profitRMB2PC=$Amount==0?0:round((($Amount-$oTheCost)/$Amount)*100);
		     
		     $ReadProfitColorSign=1; include "order_Profit.php"; $ReadProfitColorSign=0;
		      
		     $profitRMB2PC.="%";
    }
    else{
	        $profitRMB2PC="";$profitColor="";
    }

	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
     
     $wName=date("D",strtotime($OrderDate));
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>"List0","Args"=>"$OrderDate"),
				                      "Title"=>array("Text"=>" $DateTitle","rIcon"=>"$wName"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"80, 2, 70, 30"),
				                      "Col2"=>array("Text"=>"$profitRMB2PC","Color"=>"$profitColor","Frame"=>"150, 2, 60, 30"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"$Col3Frame"),
				                      "iNumber"=>"$LowProfit"
				                   ); 
		$dataArray=array();
				                   
		if ($hiddenSign==0){
				$FromPage="Read";  $checkDate=$OrderDate;
				 include "order_today_list0.php";
		}                                	                   
	    $jsondata[]=array("head"=>$headArray,"ModuleId"=>"109","hidden"=>"$hiddenSign","data"=>$dataArray); 
	    $hiddenSign=1;
}

$today=date("Y-m-d");
$jsonArray=array("rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Chart","Args"=>"$today")),"data"=>$jsondata); 
?>