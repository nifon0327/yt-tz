<?php
//客供
//$SearchRows=$SearchBuyerId==""?"":" AND S.BuyerId='$SearchBuyerId' ";
$myResult=mysql_query("SELECT S.POrderId,S.StuffId,M.BuyerId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
           (A.Qty-A.rkQty) AS Qty,((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount,R.Mid  
		     FROM (
						    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty   
						          FROM $DataIn.cg1_stocksheet S 
						          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
						          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
						          LEFT JOIN $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2
						         WHERE    S.rkSign>0 AND (S.FactualQty>0 OR S.AddQty>0) AND OP.Property=2  GROUP BY S.StockId
						)A 
			LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId   
			LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
			LEFT JOIN $DataPublic.staffmain N ON N.Number=M.BuyerId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
			LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
		   LEFT JOIN $DataIn.cg1_stockreview R ON  R.Mid=S.Mid 
			WHERE A.Qty>A.rkQty  ORDER BY Weeks",$link_id);

				 if($myRow = mysql_fetch_array($myResult)){
				       $oldBuyerId=0;
				       $BuyerName="客供";
				       $oldWeeks=$myRow["Weeks"];
				       $WeekQty=0;$WeekCount=0;$WeekAmount=0;
		               $BuyerQty=0;$BuyerAmount=0;$BuyerCount=0;
		               //$OverQty=0; $OverAmount=0;$OverCount=0;
		               $dataArray=array(); $lastQty=0;$lastCount=0; $ReviewQty=0;
		              do{
		                     $Weeks=$myRow["Weeks"];
		                     if ($Weeks!=$oldWeeks){
		                      
			                         $ColTextColor="#000000";
				                     $rowColor=$curWeeks==$oldWeeks?$CURWEEK_BGCOLOR:"#FFFFFF";
				                     if ($oldWeeks==""){
					                     $weekName="交期待定"; $weekNumber="00";$dateSTR="  交期待定";
				                     }else{
					                     $weekName="Week ". substr($oldWeeks,4,2);
					                     $weekNumber=substr($oldWeeks,4,2);
					                     $dateArray= GetWeekToDate($oldWeeks,"m/d");
		                                  $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		                                  if ($oldWeeks<$curWeeks) {
		                                          $OverQty+=$WeekQty; $OverAmount+=$WeekAmount;$OverCount+=$WeekCount;
		                                          $ColTextColor="#FF0000";
		                                   }
				                   }
				                   
					             $BuyerCount+=$WeekCount;
			                     $BuyerQty+=$WeekQty;
			                     $BuyerAmount+=$WeekAmount;
		                          
		                           if ($oldWeeks==$curWeeks){
				                     $TotalWeekQty+=$WeekQty;
				                     $TotalWeekAmount+=$WeekAmount;
			                     }

		                          $AboveArray=array();
		                          if ($lastCount>0){
			                          $lastQty=number_format($lastQty);
			                          $AboveArray=array("Text"=>"$lastQty","RLText"=>"($lastCount)","RLColor"=>"#BBBBBB");
		                          }
		                          
		                           $ReviewPre_2=round($ReviewQty/$WeekQty*100);
		                          $ReviewPre_1=100-$ReviewPre_2;
		                           $LegendArray=array("$ReviewPre_1","$ReviewPre_2");
		                           
                                  $Qty=number_format($WeekQty);
		                          $Amount=number_format($WeekAmount);
		                          
		                          $weekColor=$oldWeeks<$curWeeks && $oldWeeks>0?"#FF0000":"";
		                          
			                       $tempArray=array(
					                       "Id"=>"$oldWeeks",
					                       "RowSet"=>array("bgColor"=>"$rowColor"),
					                       "Col1"=>array("Text"=>"$weekNumber","BelowText"=>array("Text"=>"$dateSTR"),"bgColor"=>"$weekColor"),
					                       "Col2"=>array("Text"=>"$Qty","Color"=>"$ColTextColor","RLText"=>"($WeekCount)","RLColor"=>"#BBBBBB",
					                                                 "AboveText"=>$AboveArray),
					                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor")
					                      // "Legend"=>$LegendArray
					                     );
					              $onTapArray=array("Title"=>"$weekName","Tag"=>"stuff","Args"=>"$oldBuyerId|$oldWeeks");
			                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray);
			                     
			                     $oldWeeks=$myRow["Weeks"]; 
			                     $WeekQty=0;$WeekCount=0;$WeekAmount=0;
			                     $lastQty=0;$lastCount=0;$ReviewQty=0;
		                     }
			                  
			                 $StuffId=$myRow["StuffId"];
					         $POrderId=$myRow["POrderId"];
					         $Qty=$myRow["Qty"];
		                     $Amount=$myRow["Amount"];
		                     $ReviewQty+=$myRow["Mid"]>0?0:$Qty;
		                     
					         if ($POrderId>0 && $Weeks<$curWeeks){
						         //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
								include "../../model/subprogram/stuff_blcheck.php";
						         if ($LastBlSign==1)  {$lastCount++;$lastQty+=$Qty;}
					        }

		                     $WeekCount++;
		                     $WeekQty+=$Qty;
		                     $WeekAmount+=$Amount;
		                     $COUNT_2++;
		               }while($myRow = mysql_fetch_array($myResult));
		                   $BuyerCount+=$WeekCount;
	                        $BuyerQty+=$WeekQty;
	                       $BuyerAmount+=$WeekAmount;
                           
                           $ColTextColor="#000000";
		                   $rowColor=$curWeeks==$oldWeeks?"#CCFF99":"#FFFFFF";
		                     if ($oldWeeks==""){
			                     $weekName="交期待定"; $weekNumber="00";$dateSTR="  交期待定";
		                     }else{
			                     $weekName="Week ". substr($oldWeeks,4,2);
			                     $weekNumber=substr($oldWeeks,4,2);
			                     $dateArray= GetWeekToDate($oldWeeks,"m/d");
	                              $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
	                              if ($oldWeeks<$curWeeks) {
	                                      $OverQty+=$WeekQty;$OverCount+=$WeekCount;
	                                      $ColTextColor="#FF0000";
	                               }
		                   }
                           
                           $ReviewPre_2=round($ReviewQty/$WeekQty*100);
                          $ReviewPre_1=100-$ReviewPre_2;
                          $LegendArray=array("$ReviewPre_1","$ReviewPre_2");
		                           
                           $Qty=number_format($WeekQty);
                           $Amount=number_format($WeekAmount);
	                       $tempArray=array(
			                       "Id"=>"$oldWeeks",
			                       "RowSet"=>array("bgColor"=>"$rowColor"),
			                       "Col1"=>array("Text"=>"$weekNumber","BelowText"=>array("Text"=>"$dateSTR")),
			                       "Col2"=>array("Text"=>"$Qty","Color"=>"$ColTextColor","RLText"=>"($WeekCount)","RLColor"=>"#BBBBBB"),
			                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor")
			                      //"Legend"=>$LegendArray
			                     );//,"AboveText"=>array("Text"=>"$OverQty","RLText"=>"($OverCount)","RLColor"=>"#BBBBBB")
			               $onTapArray=array("Title"=>"$weekName","Tag"=>"stuff","Args"=>"$oldBuyerId|$oldWeeks");
	                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray);
		               
		                $TotalQty+=$BuyerQty;
		                $TotalAmount+=$BuyerAmount;
		               
			           $BuyerQty=number_format($BuyerQty);
			           $BuyerAmount=number_format($BuyerAmount);
				       $tempArray=array(
						                      "Id"=>"$oldBuyerId",
						                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
						                      "Title"=>array("Text"=>" $BuyerName","Color"=>"#0066FF","FontSize"=>"14"),
						                       "Col1"=>array("Text"=>"$BuyerQty","RLText"=>"($BuyerCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
						                       "Col3"=>array("Text"=>"¥$BuyerAmount","FontSize"=>"14")
						                   );
			         $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1650","onTap"=>"1","hidden"=>"$hidden","data"=>$dataArray); 
			}
	?>