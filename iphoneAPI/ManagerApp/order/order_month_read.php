<?php 
//每月订单
$today=date("Y-m-d");$m=0;
 if (versionToNumber($AppVersion)>=287){
    $Col3Frame="205,2, 102, 30";
 }
 else{
   $Col3Frame="210, 2, 103, 30";
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
                        
$mySql="SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount    
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 GROUP BY  DATE_FORMAT(M.OrderDate,'%Y-%m')  ORDER BY Month DESC";
		
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;$sortAmount=array();
while($myRow = mysql_fetch_array($Result)) {
	  $Month=$myRow["Month"];
	  $checkMonth=$checkMonth==""?$Month:$checkMonth;
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
     if ($m<24) $sortAmount[]=$Amount; $m++;
     
     if ($ReadPower==1){
            $checkCost= mysql_fetch_array(mysql_query(" SELECT SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
		        FROM $DataIn.yw1_ordermain M
                LEFT JOIN $DataIn.yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN $DataIn.cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 and DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ", $link_id)); 
		         
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
     
     $NoChQty=$myRow["NoChQty"];
      $AddRows=array(); $height=35;
	  if ($NoChQty>0){
	      $NoChQty=number_format($NoChQty);
	      $NoChAmount=number_format($myRow["NoChAmount"]);
		  $AddRows[]=array("ColName"=>"Col1","Text"=>"$NoChQty","Color"=>"#FF0000","Margin"=>"0,0,0,30");
		  $AddRows[]=array("ColName"=>"Col3","Text"=>"¥$NoChAmount","Color"=>"#FF0000","Margin"=>"0,0,0,30");
		  $height=42;
	  }

     //$wName=date("D",strtotime($OrderDate));
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>"List0","Args"=>"$Month"),
				                      "Title"=>array("Text"=>"$Month"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"90, 2, 70, 30"),
				                      "Col2"=>array("Text"=>"$profitRMB2PC","Color"=>"$profitColor","Frame"=>"150, 2, 60, 30"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"$Col3Frame"),
				                      "AddRows"=>$AddRows
				                   ); 
		$dataArray=array();
				                   
		if ($hiddenSign==0){
				$FromPage="Read";  $checkMonth=$Month;
				 include "order_month_list0.php";
		}                                	                   
	    $jsondata[]=array("head"=>$headArray,"ModuleId"=>"$dModuleId","hidden"=>"$hiddenSign","data"=>$dataArray); 
	    $hiddenSign=1;
}

//排序
arsort($sortAmount,SORT_NUMERIC);
$j=0;
while(list($key,$val)= each($sortAmount)) 
{
    $j++;
    $tmpArray=$jsondata[$key];
    $headArray=$tmpArray["head"];
    $headArray["Rank"]=array("Icon"=>"1");
    $tmpArray["head"]=$headArray;
    $jsondata[$key]=$tmpArray;
    if ($j==6) break;
}

$curMonth=date("Y-m");
if (count($sortAmount)>12){
		asort($sortAmount,SORT_NUMERIC);
		$j=0;
		while(list($key,$val)= each($sortAmount)) 
		{
		    $j++;
		    $tmpArray=$jsondata[$key];
		    $headArray=$tmpArray["head"];
		    $TitleArray=$headArray["Title"];
		    if (trim($TitleArray["Text"])==$curMonth){
			    $j--;
		    }
		    else{
			    $headArray["Rank"]=array("Icon"=>"2");
			    $tmpArray["head"]=$headArray;
			    $jsondata[$key]=$tmpArray;
		    }
		    if ($j==6) break;
		}
}

$jsonArray=array("rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Chart","Args"=>"$checkMonth")),"data"=>$jsondata); 
?>