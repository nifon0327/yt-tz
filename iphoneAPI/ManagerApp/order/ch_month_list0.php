<?php 
//已出信息明细
$AmountMargin="-8,0,0,0";
 
$today=date("Y-m-d");
$dataArray=array();
 $shipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount    
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE M.Estate='0' and DATE_FORMAT(M.Date,'%Y-%m')='$checkMonth' ",$link_id));
$AllOrderAmount=sprintf("%.0f",$shipResult["Amount"]); 

 $shipSql="SELECT M.CompanyId,C.Forshort,C.Estate,D.Rate,D.PreChar,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS SortAmount,SUM(S.Price*S.Qty*M.Sign) AS Amount,SUM(S.Qty) AS Qty     
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE 1 and M.Estate='0' and DATE_FORMAT(M.Date,'%Y-%m')='$checkMonth' 
        GROUP BY M.CompanyId ORDER BY SortAmount DESC";
    $shipResult = mysql_query($shipSql,$link_id);
     while($shipRow = mysql_fetch_array($shipResult)) {
                $CompanyId=$shipRow["CompanyId"];
                $Forshort=$shipRow["Forshort"];
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
					WHERE  M.Estate='0'  AND M.CompanyId='$CompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')='$checkMonth' ",$link_id));	
			  $PayAmount=$checkAmount["Amount"]==""?0:round($checkAmount["Amount"],2);
			  $NoPayAmount=$TotalAmount-$PayAmount;
	         
	          $AddRows=array(); 
			  if ($NoPayAmount>0){
			      $NoPayAmount=number_format($NoPayAmount);
				  $AddRows[]=array("ColName"=>"Col3","Text"=>"$PreChar$NoPayAmount","Color"=>"#FF0000");
			  }


	           $TotalQty=number_format($TotalQty);
			   $TotalAmount=number_format($TotalAmount);
			   
			   $ForshortColor=$shipRow["Estate"]==0?"#FF0000":$TITLE_GRAYCOLOR;
			   
			   //统计订单准时率
				$PuncSelectType=2;
			    include "submodel/order_punctuality.php";
			     $AddRows[]=array("ColName"=>"Col2","Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon");
				//$AboveArray=array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon");
			  $totalArray=array(
			                          "onTap"=>array("Target"=>"List1","Args"=>"$checkMonth|$CompanyId"),
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"$ForshortColor"),
				                      "Col1"=>array("Text"=>"$Percent","Margin"=>"-35,0,0,0","FontSize"=>"11","RIcon"=>"order_rmb"),
				                      "Col2"=>array("Text"=>"$TotalQty","Margin"=>"-33,0,0,0"),//, "AboveText"=>$AboveArray
				                      "Col3"=>array("Text"=>"$PreChar$TotalAmount","Margin"=>"$AmountMargin"),
				                      "AddRows"=>$AddRows                      
				                   );  
			$dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"1","data"=>$totalArray); 
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>