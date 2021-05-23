<?php 
//已出信息明细
 if (versionToNumber($AppVersion)>=287){
    $AmountMargin="-8,0,0,0";
 }
 else{
	 $AmountMargin="-15,0,0,0";
 }
 
$dataArray=array();
$CheckMonth=date("Y-m",strtotime("$today -1 year"));  
 $shipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount    
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE M.Estate='0' and M.CompanyId='$checkCompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')>='$CheckMonth' ",$link_id));
$AllOrderAmount=sprintf("%.0f",$shipResult["Amount"]); 

 $shipSql="SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,D.Rate,D.PreChar,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS SortAmount,SUM(S.Price*S.Qty*M.Sign) AS Amount,SUM(S.Qty) AS Qty     
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE 1 and M.Estate='0' and M.CompanyId='$checkCompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')>='$CheckMonth' 
        GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month DESC";
    $shipResult = mysql_query($shipSql,$link_id);
   $CompanyId=$checkCompanyId;
     while($shipRow = mysql_fetch_array($shipResult)) {
                $Month=$shipRow["Month"];
                $TotalQty=$shipRow["Qty"];
                $TotalAmount=$shipRow["Amount"];
                $Rate=$shipRow["Rate"];
                $PreChar=$shipRow["PreChar"];
                
                $Percent=($TotalAmount*$Rate/$AllOrderAmount)*100;
		        $Percent=$Percent>=1?round($Percent)."%":""; 
		        
		          //已收款
			 $checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(S.Amount*M.Sign,0)) AS Amount 
			         FROM $DataIn.ch1_shipmain M
					 LEFT JOIN  $DataIn.cw6_orderinsheet S ON S.chId=M.Id 
					 LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
		             LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
					WHERE  M.Estate='0'  AND M.cwSign IN (0,2)  AND M.CompanyId='$checkCompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' ",$link_id));	
			  $PayAmount=$checkAmount["Amount"];
			  $NoPayAmount=$TotalAmount-$PayAmount;
			  
			  $AddRows=array(); 
			  if ($NoPayAmount>0){
			      $NoPayAmount=number_format($NoPayAmount);
				  $AddRows[]=array("ColName"=>"Col3","Text"=>"$PreChar$NoPayAmount","Color"=>"#FF0000");
			  }

	           $TotalQty=number_format($TotalQty);
			   $TotalAmount=number_format($TotalAmount);
			   
			   //统计订单准时率
				$PuncSelectType=2;
				$checkMonth=$Month;
			    include "submodel/order_punctuality.php";
			    $AboveArray=array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon");
				
			  $titleColor=date("Y")==date("Y",strtotime($Month . "-01"))?"$TITLE_GRAYCOLOR":"#CCCCCC";  
			  $totalArray=array(
			                          "onTap"=>array("Target"=>"List1","Args"=>"$checkMonth|$CompanyId"),
				                      "Title"=>array("Text"=>"$Month","Color"=>"$titleColor"),
				                      "Col1"=>array("Text"=>"$Percent","Margin"=>"-35,0,0,0","FontSize"=>"11"),
				                      "Col2"=>array("Text"=>"$TotalQty","Margin"=>"-33,0,0,0","AboveText"=>$AboveArray),
				                      "Col3"=>array("Text"=>"$PreChar$TotalAmount","FontSize"=>"13","Margin"=>"$AmountMargin"),
				                       "AddRows"=>$AddRows   
				                   );  
			$dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"1","data"=>$totalArray); 
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>