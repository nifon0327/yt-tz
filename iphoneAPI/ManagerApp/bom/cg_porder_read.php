<?php 
//已下单记录
$m=0;
$today=date("Y-m-d");
$endDate=date("Y-m-d",strtotime("$today -3 month"));                    
$mySql="SELECT M.Date,SUM(S.FactualQty+S.AddQty) AS Qty,SUM((S.FactualQty+S.AddQty)*S.Price*D.Rate) AS Amount,SUM(IF(S.POrderId>0,0, (S.FactualQty+S.AddQty)*S.Price*D.Rate)) AS outAmount  
	FROM $DataIn.cg1_stocksheet S   
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE  M.Date>='$endDate' GROUP BY  M.Date ORDER BY Date DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;
while($myRow = mysql_fetch_array($Result)) {
	  $Date=$myRow["Date"];
	  $DateSTR=date("m-d",strtotime($Date));  
	  $wName=date("D",strtotime($Date));
	  
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);

      
      $outAmount=round($myRow["outAmount"],2);
      $outPre_2=$Amount>0?round($outAmount/$Amount*100):0;
      $outPre_1=100-$outPre_2;
      $LegendArray=array("$outPre_1","$outPre_2");                         
      
	  $Qty=number_format($Qty);
	  $OverQty=number_format($OverQty);
	  $Amount=number_format($Amount);
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Date"),
				                      "Title"=>array("Text"=>" $DateSTR","RTIcon"=>"$wName"),
				                      "Col1"=>array("Text"=>"$Qty","Margin"=>"-20,0,20,0"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 2, 103, 30"),
				                      "Legend"=>$LegendArray
				                   ); 
				                   
		$dataArray=array();
				                   
		if ($hiddenSign==0){
				$FromPage="Read";  $CheckDate =$Date;
				 include "cg_porder_list.php";
		} 
		                         	                   
	    $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","data"=>$dataArray); 
	    $hiddenSign=1;
}

$jsonArray=array("data"=>$jsondata); 
?>