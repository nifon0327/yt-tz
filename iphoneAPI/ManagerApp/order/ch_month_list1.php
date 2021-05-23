<?php 
//已出信息明细
$today=date("Y-m-d");
$dataArray=array();
$chSql="SELECT M.Id,M.Date,M.InvoiceNO,M.Ship,M.ShipType,S.Type,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*M.Sign)  AS Amount,T.Type       
		                FROM $DataIn.ch1_shipmain M
		                LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
		                LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id  
				        WHERE M.Estate='0' and M.CompanyId='$checkCompanyId'  and DATE_FORMAT(M.Date,'%Y-%m')='$checkMonth' 
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

								WHERE S.chId='$Id' ",$link_id));	
			  $PayAmount=round($checkAmount["Amount"],2);
			  $PayColor=$Amount-$PayAmount==0?"#00BB00":"";
			  
			  $Qty=number_format($Qty);
			  $Amount=number_format($Amount,2);
			  $bgColor=$today==$chRow["Date"]?"#C3FF64":"";
			  
			  //统计订单准时率
			  $PuncSelectType=3;
			  include "submodel/order_punctuality.php";
			  $AboveArray=array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon");
			  
			  $tempArray=array(
						                       "RowSet"=>array("bgColor"=>"$bgColor"),
						                      "Title"=>array("Text"=>"$InvoiceNO","Ship"=>"$ShipType","Declare"=>"$DeclareType"),
						                      "Date"=>array("Text"=>"$Date","Color"=>"#54BCE5"),
						                      "Col1"=>array("Text"=>"$Qty", "AboveText"=>$AboveArray),//"RLText"=>"$Punc_Percent","RLColor"=>"$Punc_Color"
						                      "Col2"=>array("Text"=>"$PreChar$Amount","Color"=>"$PayColor")
						                   );  
			$dataArray[]=array("Tag"=>"chList","onTap"=>array("Value"=>"1","Target"=>"InvoiceNO","Args"=>"$InvoiceNO"),"data"=>$tempArray);     
		         
		}
			
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>