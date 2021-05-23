<?php 
//每日新单
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
$mySql="SELECT M.OrderDate,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,IFNULL(A.oTheCost,0) AS oTheCost   
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
    LEFT JOIN (
           SELECT M.OrderDate,SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*C.Rate) AS oTheCost
		        FROM $DataIn.yw1_ordersheet S
                LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN $DataIn.cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id
		        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 	
		        WHERE  $SearchRows GROUP BY  M.OrderDate) A ON A.OrderDate=M.OrderDate
	WHERE $SearchRows GROUP BY  M.OrderDate ORDER BY OrderDate DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;
while($myRow = mysql_fetch_array($Result)) {
	  $OrderDate=$myRow["OrderDate"];
	  $DateTitle=date("m-d",strtotime($OrderDate));
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
     
     if ($ReadPower==1){
		     $oTheCost=round($myRow["oTheCost"],2);
		     $profitRMB2PC=$Amount==0?0:round((($Amount-$oTheCost)/$Amount)*100);
		     if ($profitRMB2PC>10){
		        $profitColor="#009900";
		    }
		    else{
		      $profitColor=$profitRMB2PC>=3?"#FF6633":"#FF0000";
		    }
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
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$OrderDate"),
				                      "Title"=>array("Text"=>" $DateTitle","FontSize"=>"14","rIcon"=>"$wName"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"80, 2, 70, 30"),
				                      "Col2"=>array("Text"=>"$profitRMB2PC","Color"=>"$profitColor","Frame"=>"150, 2, 60, 30"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
				                   ); 
		$dataArray=array();
				                   
		if ($hiddenSign==0){
				$FromPage="Read";  $checkDate=$OrderDate;
				 include "order_today_ext_list.php";
		}                                	                   
	    $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","data"=>$dataArray); 
	    $hiddenSign=1;
}

$jsonArray=array("rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Chart","Args"=>"$today")),"data"=>$jsondata); 
?>