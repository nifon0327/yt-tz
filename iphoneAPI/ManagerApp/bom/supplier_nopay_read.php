<?php 
//供应商按月显示未付
 $SearchRows=$CompanyId==""?"":" AND S.CompanyId='$CompanyId' ";
 
$curDate=date("Y-m-d");
$LastMonth1=date("Y-m",strtotime("$curDate  -1   month"));
$LastMonth2=date("Y-m",strtotime("$curDate  -2   month"));


$myResult=mysql_query("SELECT S.Month,P.Forshort,P.GysPayMode,P.Prepayment,SUM(S.Amount*C.Rate) AS Amount,
            SUM(CASE WHEN P.GysPayMode=0 AND S.Month<'$LastMonth1' THEN S.Amount
                     WHEN P.GysPayMode=1 THEN S.Amount 
                     WHEN P.GysPayMode=2 AND S.Month<'$LastMonth2' THEN S.Amount
                     ELSE 0 END)*C.Rate AS OverAmount 
			FROM $DataIn.cw1_fkoutsheet S
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
			WHERE S.Estate=3 AND S.Amount>0  $SearchRows 
			GROUP BY S.Month ORDER BY Month DESC",$link_id);
				 if($myRow = mysql_fetch_array($myResult)){
		              do{
		                     $Month=$myRow["Month"];
		                     $OverAmount=$myRow["OverAmount"]>0?"¥" . number_format($myRow["OverAmount"]):"";
		                     $Amount=number_format($myRow["Amount"]);
		                     $Forshort=$myRow["Forshort"];
		                     
		                    $tempArray=array(
					                       "Id"=>"$Month",
					                       "RowSet"=>array("Accessory"=>"1"),//,"bgColor"=>"#EEEEEE"
					                       "Title"=>array("Text"=>"$Month","FontSize"=>"13","Color"=>"#0066FF"),
					                       "Col2"=>array("Text"=>"$OverAmount","Margin"=>"-48,0,0,0","Color"=>"#FF0000","FontSize"=>"13"),
					                       "Col3"=>array("Text"=>"¥$Amount","Margin"=>"-10,0,0,0","FontSize"=>"13")
					                     );
					        $onTapArray=array("Title"=>"$Forshort/$Month","Tag"=>"stuff","Args"=>"$CompanyId|$Month");
			                 $dataArray[]=array("Tag"=>"Total","onTap"=>$onTapArray,"data"=>$tempArray);
		               }while($myRow = mysql_fetch_array($myResult));
	                   $jsonArray=$dataArray;
	             }
?>
