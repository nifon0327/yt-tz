<?php 
//已出信息明细
$today=date("Y-m-d");
$checkMonth=date("Y-m",strtotime($checkDate));
$dataArray=array();
 $shipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount    
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE M.Estate='0' and M.Date='$checkDate' ",$link_id));
$AllOrderAmount=sprintf("%.0f",$shipResult["Amount"]); 

 $shipSql="SELECT M.CompanyId,C.Forshort,D.Rate,D.PreChar,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS SortAmount,SUM(S.Price*S.Qty*M.Sign) AS Amount,SUM(S.Qty) AS Qty     
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE 1 and M.Estate='0' and  M.Date='$checkDate'  
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
		        
	           $TotalQty=number_format($TotalQty);
			   $TotalAmount=number_format($TotalAmount);
			   
			   //统计订单准时率
				$PuncSelectType=5;
			    include "submodel/order_punctuality.php";
				  
			  $totalArray=array(
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF"),
				                      "Col1"=>array("Text"=>"$Percent","Margin"=>"-35,0,0,0"),
				                      "Col2"=>array("Text"=>"$TotalQty","Margin"=>"-33,0,0,0","RLText"=>"$Punc_Percent","RLColor"=>"$Punc_Color"),
				                      "Col3"=>array("Text"=>"$PreChar$TotalAmount")
				                   );  
			$dataArray[]=array("Tag"=>"Total",
			                                 //"onTap"=>array("Value"=>"1","Target"=>"Curve","Title"=>"出货及时度分析图","Args"=>"$CompanyId|$checkMonth"),
			                                 "data"=>$totalArray); 
			
			  $chSql="SELECT M.Id,M.Date,M.InvoiceNO,M.Ship,M.ShipType,S.Type,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*M.Sign)  AS Amount,T.Type       
			                FROM $DataIn.ch1_shipmain M
			                LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
			                LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id  
					        WHERE M.Estate='0' and M.CompanyId='$CompanyId'  and M.Date='$checkDate' 
					        GROUP BY M.InvoiceNO ORDER BY M.Date DESC";	        
			$chResult=mysql_query($chSql, $link_id); 
			while($chRow = mysql_fetch_array($chResult)) {
			      $Id=$chRow["Id"];
				  $Date=date("m-d",strtotime($chRow["Date"]));
				  $InvoiceNO=$chRow["InvoiceNO"];
				  $Qty=$chRow["Qty"];
				  $Amount=round($chRow["Amount"],2);
				  $ShipType=$chRow["Ship"];
				  if ($chRow["ShipType"]=='credit' || $chRow["ShipType"]=='debit'){
			           $ShipType=$chRow["ShipType"]=='credit'?31:32;
			     }
			     $DeclareType=$chRow["Type"]==1?1:0;   
				  //是否已收款
				 $checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS Amount 
									FROM $DataIn.cw6_orderinsheet S 
									LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
									WHERE S.chId='$Id' ",$link_id));	
				  $PayAmount=round($checkAmount["Amount"],2);
				  $PayColor=$Amount-$PayAmount==0?"#00BB00":"";
				  
				  $Qty=number_format($Qty);
				  $Amount=number_format($Amount,2);
				  $bgColor=$today==$chRow["Date"]?"#C3FF64":"";
				  
				  //统计订单准时率
				  $PuncSelectType=3;
				  include "submodel/order_punctuality.php";
				  $tempArray=array(
							                       "RowSet"=>array("bgColor"=>"$bgColor"),
							                      "Title"=>array("Text"=>"$InvoiceNO","Ship"=>"$ShipType","Declare"=>"$DeclareType"),
							                      "Date"=>array("Text"=>"$Date","Color"=>"#54BCE5"),
							                      "Col1"=>array("Text"=>"$Qty","RLText"=>"$Punc_Percent","RLColor"=>"$Punc_Color"),
							                      "Col2"=>array("Text"=>"$PreChar$Amount","Color"=>"$PayColor")
							                   );  
				$dataArray[]=array("Tag"=>"chList","onTap"=>array("Value"=>"1","Target"=>"InvoiceNO","Args"=>"$InvoiceNO"),"data"=>$tempArray);              
			}
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>