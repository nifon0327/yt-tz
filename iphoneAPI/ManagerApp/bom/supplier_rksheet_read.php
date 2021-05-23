<?php 
//入库记录
$m=0;
$CheckMonth=date("Y") . "-01";                    
$mySql="SELECT M.CompanyId,C.Forshort,SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty,SUM(S.Qty*G.Price*D.Rate) AS Amount    
	FROM $DataIn.ck1_rksheet S
	LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid 
	LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId=S.StockId  
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE  DATE_FORMAT(M.rkDate,'%Y-%m')>='$CheckMonth' GROUP BY  M.CompanyId ORDER BY Amount DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;
while($myRow = mysql_fetch_array($Result)) {
      $CompanyId=$myRow["CompanyId"];
	  $Forshort=$myRow["Forshort"];
	  $Qty=$myRow["Qty"];
	  $OverQty=$myRow["OverQty"];
	  $Amount=round($myRow["Amount"],2);

      $Percent_Color="#0050FF";
      $OverPercent=$Qty>0? round(($Qty-$OverQty)/$Qty*100) . "%":"";
      $Percent_Color=$OverPercent<80?"#66B3FF":$Percent_Color;
      
	  $Qty=number_format($Qty);
	  $OverQty=number_format($OverQty);
	  $Amount=number_format($Amount);
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$CompanyId"),
				                      "Title"=>array("Text"=>" $Forshort","FontSize"=>"14"),//,"Color"=>"#0066FF"
				                      "Col1"=>array("Text"=>"$Qty","Margin"=>"-32,0,20,0"),
				                      "Col2"=>array("Text"=>"$OverPercent","Color"=>"$Percent_Color","Margin"=>"-15,0,0,0","FontSize"=>"11"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
				                   ); 
				                   
		$dataArray=array();
				                   
		if ($hiddenSign==0){
				$FromPage="Read";  $CheckMonth =$Month;
				 include "supplier_rksheet_list.php";
		} 
		                         	                   
	    $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","data"=>$dataArray); 
	    $hiddenSign=1;
}

$jsonArray=array("data"=>$jsondata); 
?>