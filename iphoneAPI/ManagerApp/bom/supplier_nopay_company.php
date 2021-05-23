<?php 
//入库记录
// echo("test");
/*
	
	<?php
//未付货款
$SearchRows=$SearchBuyerId==""?"":" AND S.BuyerId='$SearchBuyerId' ";
if ($SegmentIndex==2){
                $curDate=date("Y-m-d");
                $LastMonth1=date("Y-m",strtotime("$curDate  -1   month"));
                $LastMonth2=date("Y-m",strtotime("$curDate  -2   month"));
                
		        $myResult=mysql_query("SELECT S.CompanyId,P.Forshort,P.GysPayMode,P.Prepayment,SUM(S.Amount*C.Rate) AS Amount,
		                    SUM(CASE WHEN P.GysPayMode=0 AND S.Month<'$LastMonth1' THEN S.Amount
                                     WHEN P.GysPayMode=1 THEN S.Amount 
                                     WHEN P.GysPayMode=2 AND S.Month<'$LastMonth2' THEN S.Amount
                                     ELSE 0 END)*C.Rate AS OverAmount 
							FROM $DataIn.cw1_fkoutsheet S
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
							LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
							WHERE S.Estate=3 AND S.Amount>0 
							GROUP BY S.CompanyId ORDER BY Amount DESC",$link_id);

				 if($myRow = mysql_fetch_array($myResult)){
				       $CompanyAmount=0; $OverAmount=0;  $TotalAmount=0;  $TotalOverAmount=0; $hidden=1;
		              do{
		                     $CompanyId=$myRow["CompanyId"];
		                     $Forshort=$myRow["Forshort"];
		         
		                     $CompanyAmount=$myRow["Amount"];
		                     $CompanyCount=$myRow["Counts"];
							                      
		                     $OverCount=$myRow["OverCounts"];
		                     $OverAmount=$myRow["OverAmount"];
		                     
				               $TotalAmount+=$CompanyAmount;
				               $TotalOverAmount+=$OverAmount;
	
					            $OverPre_2=$CompanyAmount>0?round($OverAmount/$CompanyAmount*100):0;
                                $OverPre_1=100-$OverPre_2;
                               $LegendArray=array("$OverPre_1","$OverPre_2");
					           
					           $GysPayMode=$myRow["GysPayMode"];
					           if ($GysPayMode==1){
						           $GysPayMode=$myRow["Prepayment"]==1?8:$GysPayMode;
					           }
					           
					           $RTIcon="payout_" . $GysPayMode;
					           $OverAmount=$OverAmount>0?"¥" . number_format($OverAmount):"";
					           $CompanyAmount=number_format($CompanyAmount);
						       $tempArray=array(
								                      "Id"=>"$CompanyId",
								                      //"RowSet"=>array("bgColor"=>"#EEEEEE"),
								                      "onTap"=>array("Target"=>"Week","Args"=>"$CompanyId"),
								                      "Title"=>array("Text"=>" $Forshort","FontSize"=>"14","RTIcon"=>"$RTIcon"),//"Color"=>"#0066FF",
								                       "Col1"=>array("Text"=>"$OverAmount","FontSize"=>"13","Color"=>"#FF0000","Margin"=>"-30,0,30,0"),
								                       "Col3"=>array("Text"=>"¥$CompanyAmount","FontSize"=>"13","Margin"=>"-10,0,10,0"),
								                       "Legend2"=>$LegendArray
								                   );
					       $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1183","onTap"=>"1","hidden"=>"$hidden","data"=>array()); 
		               }while($myRow = mysql_fetch_array($myResult));
						
				      $COUNT_2=round($TotalAmount/1000000) . "M";	               
			           $PercentValue=$TotalAmount>0?round($TotalOverAmount*100/$TotalAmount):0;
		               $TotalAmount=number_format($TotalAmount);
		               $TotalOverAmount=$TotalOverAmount>0?"¥" . number_format($TotalOverAmount):"";
		               
		               $dataArray=array();
		               $TitleSTR=versionToNumber($AppVersion)>=278?"":"总计";//Created by 2014/09/02
		                $colArray[]=array(
		                                       "Col1"=>array("Text"=>"$TotalOverAmount","Color"=>"#FF0000"),
						                       "Col2"=>array("Text"=>"¥$TotalAmount"));       
			            $tempArray=array(
						                      "Id"=>"",
						                      "Percent"=>array("Title"=>"$TitleSTR","Value"=>"$PercentValue"),
						                      "data"=>$colArray
						                   );
			               $dataArray[]=array("Tag"=>"Percent","data"=>$tempArray);
			              $tempArray2[]=array("head"=>array(),"ModuleId"=>"1183","data"=>$dataArray); 
			              array_splice($jsondata,0,0,$tempArray2);
		    }
		}
		else{
			$CountResult= mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount 
							FROM $DataIn.cw1_fkoutsheet S
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
							LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
							WHERE S.Estate=3 AND S.Amount>0 ",$link_id));
		       $COUNT_2=$CountResult["Amount"]==""?0:round($CountResult["Amount"]/1000000) . "M";	
		}
?>
*/
 $curDate=date("Y-m-d");
                $LastMonth1=date("Y-m",strtotime("$curDate  -1   month"));
                $LastMonth2=date("Y-m",strtotime("$curDate  -2   month"));
                

$m=0;     
	$dataArray=array();             
$mySql="SELECT S.CompanyId,P.Forshort,P.GysPayMode,P.Prepayment,SUM(S.Amount*C.Rate) AS Amount,
		                    SUM((CASE WHEN P.GysPayMode=0 AND S.Month<'$LastMonth1' THEN S.Amount
                                     WHEN P.GysPayMode=1 THEN S.Amount 
                                     WHEN P.GysPayMode=2 AND S.Month<'$LastMonth2' THEN S.Amount
                                     
                                     ELSE 0 END)*C.Rate) AS OverAmount 
							FROM $DataIn.cw1_fkoutsheet S
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
							LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
							WHERE S.Estate=3 AND S.Amount>0  AND S.Month='$CheckMonth'  
							GROUP BY S.CompanyId ORDER BY Amount DESC";
//	echo("$mySql");
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;
while($myRow = mysql_fetch_array($Result)) {
      $CompanyId=$myRow["CompanyId"];
	  $Forshort=$myRow["Forshort"];
	  $Qty=$myRow["Qty"];
	  $OverQty=$myRow["OverAmount"];
	  $Amount=round($myRow["Amount"],2);
	  $Qty=number_format($Qty);
	  $OverQty=$OverQty > 0 ? "¥".number_format($OverQty) : "";
	  $Amount=number_format($Amount);
	  $GysPayMode = $myRow["GysPayMode"];
	  
     $payText = "";
     
     switch ($GysPayMode) {
	     
	     case 0:
	     $payText = "30d";
	     break;
	     
	     case 1:
	     $payText = "现金";
	     break;
	    
	     case 2:
	     $payText = "60d";
	     break;
     }
	  $temp=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>"nopay_sub","Args"=>"$CompanyId|$CheckMonth"),
				                      "Title"=>array("Text"=>" $Forshort","light"=>"13","Margin"=>"2,7,0,0","top_lbl"=>array("Text"=>"$payText","light"=>"9","Color"=>"#358FC1","Margin"=>"4.5,-13,0,0")),//,"Color"=>"#0066FF"
				                      "Col1"=>array("Text"=>"$OverQty","Margin"=>"27,0,30,0","Color"=>"#ff0000"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210,7,103, 30","light"=>"13")
				                   ); 
				                   
	
	
		                         	                   
	    $dataArray[]=array("data"=>$temp,"hidden"=>"1","w_tap"=>"1","Tag"=>"Total"); 
	   // $hiddenSign=1;
}

$jsonArray = $dataArray;
?>