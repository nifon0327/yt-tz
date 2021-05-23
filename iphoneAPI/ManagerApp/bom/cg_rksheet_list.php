<?php 
//每月入库记录
$mySql="SELECT M.CompanyId,C.Forshort,SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty,SUM(S.Qty*G.Price*D.Rate) AS Amount    
	FROM $DataIn.ck1_rksheet S
	LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid 
	LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId=S.StockId  
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE  DATE_FORMAT(M.rkDate,'%Y-%m')='$CheckMonth' GROUP BY  M.CompanyId ORDER BY Amount DESC";
 $myResult = mysql_query($mySql,$link_id);
 $dataArray=array();
 while($myRow = mysql_fetch_array($myResult)) 
{
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
       
       $totalArray=array(
                                      "RowSet"=>array("Accessory"=>"1"),
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF","FontSize"=>"13"),
				                      "Col1"=>array("Text"=>"$Qty","Margin"=>"16,0,0,0","FontSize"=>"13","Color"=>"#999999"),
				                      "Col2"=>array("Text"=>"$OverPercent","Color"=>"$Percent_Color","Margin"=>"-15,0,0,0","FontSize"=>"11"),
				                      "Col3"=>array("Text"=>"¥$Amount","FontSize"=>"13")
				                   );  
		$onTapArray=array("Title"=>"$Forshort/$CheckMonth","ModuleId"=>"List","Target"=>"stuff","Args"=>"$CheckMonth|$CompanyId");		                   
		$dataArray[]=array("Tag"=>"Total","onTap"=>$onTapArray,"data"=>$totalArray); 
       
}

if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>