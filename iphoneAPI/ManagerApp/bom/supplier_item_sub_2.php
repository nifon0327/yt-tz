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