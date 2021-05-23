<?php 
  //今日摘要
 $ReadAccessSign=3;
include "user_access.php";  //用户权限

$today=date("Y-m-d");

$today=date("Y-m-d");
$countArray=array();
$countSign=1;
$content = file_get_contents('todaywidget.data');
if ($content){
	$dataArray=json_decode($content,true);
	$oldDate=$dataArray['date'];
	if ($oldDate==$today && count($dataArray)>1){
		 $countArray=$dataArray;
		 $countSign=0;
	}
}
if ($countSign==1) $countArray['date']=$today;

$dataArray=array();
if (in_array("173",$itemArray)){     
	$checkSql="SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount 
        FROM $DataIn.yw1_ordersheet S 
        LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	    LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE  M.OrderDate=CURDATE()";
        $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
        $Amount=$checkRow["Amount"]==""?0:$checkRow["Amount"]; 
        
        $checkCost= mysql_fetch_array(mysql_query(" SELECT SUM(IFNULL(A.OrderQty*A.Price*C.Rate,0)) AS oTheCost
		        FROM $DataIn.yw1_ordermain M
                LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN $DataIn.cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		        WHERE  M.OrderDate=CURDATE() ", $link_id)); 
		   $oTheCost=$checkCost["oTheCost"]==""?0:round($checkCost["oTheCost"],2);

		     $profitRMB2PC=$Amount==0?0:round((($Amount-$oTheCost)/$Amount)*100);
		     
		     $ReadProfitColorSign=1; include "order/order_Profit.php"; $ReadProfitColorSign=0;
		     
		$Amount=$Amount>0?"¥" . number_format($Amount):0;
		
		$dataArray[]=array("Id"=>"3", "Name"=>"下单","Text"=>"$Amount","Percent"=>"$profitRMB2PC","Color"=>"$profitColor","Icon"=>"iprofit_r");  
}
 
if (in_array("104",$itemArray)){
      $checkSql = "SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount 
		                        FROM $DataIn.ch1_shipmain M 
		                         LEFT JOIN $DataIn.ch1_shipsheet H ON H.Mid=M.Id 
		                        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=H.POrderId 
		                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
		                        LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		                        WHERE  S.Estate='0' AND M.Date=CURDATE()";  
		$checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));                        
		 $Amount=$checkRow["Amount"]==""?0:"¥" . number_format($checkRow["Amount"]);   
		 
		 //统计订单准时率
		 $checkDate=date("Y-m-d");
		  $PuncSelectType=4;
		  include "submodel/order_punctuality.php";
	     $dataArray[]=array("Id"=>"4","Name"=>"出货","Text"=>"$Amount","Percent"=>"$Punc_Value","Color"=>"$Punc_Color","Icon"=>"ipunctuality_r"); 
 }
 
if (count($dataArray)>0){
	   $jsonArray[] = array("Id"=>"0","Tag"=>"Percent","data"=>$dataArray);
}

if (in_array("213",$itemArray)){     
	$checkSql="SELECT SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj  WHERE DATE_FORMAT(Date,'%Y-%m-%d')=CURDATE() AND TypeId='7100'";     
	$checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
	$Qty=$checkRow["Qty"]==""?0:$checkRow["Qty"]; 
	
	if ($countSign==1){
	        $yDate=date("Y-m-d",strtotime("-1 day"));
			$k=0;$n=0;$DateCheckRows="";
			do{
			   $eDate=date("Y-m-d",strtotime("$yDate  -$n   day"));
			   //判断当天是否有登记生产数量
			   $CheckScState=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj  WHERE DATE_FORMAT(Date,'%Y-%m-%d')='$eDate'",$link_id));
			    if ($CheckScState["Qty"]>0){
					   $k++;
				 }
				$n++;
			}while($k<5 && $n<31);
			$DateCheckRows=" AND DATE_FORMAT(S.Date,'%Y-%m-%d')>='$eDate' AND DATE_FORMAT(S.Date,'%Y-%m-%d')<='$yDate' ";
			 $scResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty FROM $DataIn.sc1_cjtj S WHERE 1 $DateCheckRows AND S.TypeId='7100'",$link_id));
			$avg_Qty=round($scResult["Qty"]/5);
			$countArray['PackAvgQty']=$avg_Qty;
	}
	else{
		$avg_Qty=$countArray['PackAvgQty'];
	}
	
	$Percent=$avg_Qty==0?100:round($Qty/$avg_Qty*100);
	$Qty=$Qty==0?0:number_format($Qty)."pcs";			
	$jsonArray[] = array("Id"=>"1","Tag"=>"Progress" ,"Name"=>"今日组装","Text"=>"$Qty","Percent"=>"$Percent");
	
	
	$checkSql="SELECT SUM(Qty) AS Qty FROM $DataSub.sc1_cjtj  WHERE Date=CURDATE() AND TypeId='7090'";   
	$checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
	$Qty=$checkRow["Qty"]==""?0:$checkRow["Qty"];   
	
	if ($countSign==1){
		$yDate=date("Y-m-d",strtotime("-1 day"));
		$k=0;$n=0;$DateCheckRows="";
		do{
		   $eDate=date("Y-m-d",strtotime("$yDate  -$n   day"));
		   //判断当天是否有登记生产数量
		   $CheckScState=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataSub.sc1_cjtj  WHERE Date='$eDate' AND TypeId='7090'",$link_id));
		    if ($CheckScState["Qty"]>0){
				   $k++;
			 }
			$n++;
		}while($k<5 && $n<31);
		$DateCheckRows=" AND  Date>='$eDate' AND  Date<='$yDate' ";
		 $scResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataSub.sc1_cjtj  WHERE 1 $DateCheckRows AND TypeId='7090' ",$link_id));
		$avg_Qty=round($scResult["Qty"]/5);
		$countArray['PtAvgQty']=$avg_Qty;
    }else{
	    $avg_Qty=$countArray['PtAvgQty'];
    }
	
	$Percent=$avg_Qty==0?100:round($Qty/$avg_Qty*100);
	$Qty=$Qty==0?0:number_format($Qty)."pcs";
	     
	$jsonArray[] = array("Id"=>"2","Tag"=>"Progress" , "Name"=>"皮套生产","Text"=>"$Qty","Percent"=>"$Percent");      
}

//$jsonArray[] = array("Id"=>"2","Tag"=>"" , "Name"=>"待审核项目:","Text"=>"20"); 

if ($countSign==1){
		$fp = fopen("todaywidget.data", "w");
		$txt=json_encode($countArray);
		fwrite($fp,$txt );
		fclose($fp); 
}
?>