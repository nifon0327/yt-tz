<?php
//未收
//$SearchRows="";
$SearchRows=$SearchBuyerId==""?"":" AND M.BuyerId='$SearchBuyerId' ";
if ($SearchBuyerId==10882)  $SearchRows=" AND M.BuyerId IN (10161,10882) "; 
if ($SearchBuyerId==10795)  $SearchRows=" AND M.BuyerId IN (10399,10795) "; 
if ($SegmentIndex==0 ||  $SegmentIndex==2){
		       $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
                $curWeeks=$dateResult["CurWeek"];
		        $TotalQty=0;$TotalAmount=0; $TotalOverQty=0;$TotalOverCount=0;
		        
		        $SearchRows.=" AND NOT EXISTS(SELECT OP.StuffId FROM $DataIn.stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) ";
		        $myResult=mysql_query("SELECT S.POrderId,S.StuffId,M.BuyerId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
		               (A.Qty-A.rkQty) AS Qty,((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount,R.Mid  
					     FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0   $SearchRows GROUP BY S.StockId
									)A 
						LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN $DataPublic.staffmain N ON N.Number=M.BuyerId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
					    LEFT JOIN $DataIn.cg1_stockreview R ON  R.Mid=S.Mid 
						WHERE A.Qty>A.rkQty  ORDER BY BuyerId,Weeks",$link_id);

				 if($myRow = mysql_fetch_array($myResult)){
				       $oldBuyerId=$myRow["BuyerId"];
				       $BuyerName=$myRow["Name"];
				       $oldWeeks=$myRow["Weeks"];
				       $WeekQty=0;$WeekCount=0;$WeekAmount=0;
		               $BuyerQty=0;$BuyerAmount=0;$BuyerCount=0;
		               $OverQty=0; $OverAmount=0;$OverCount=0;
		               $TotalOverQty=0; $TotalOverAmount=0;$TotalOverCount=0;
		               $TotalWeekQty=0;$TotalWeekAmount=0;
		               $lastQty=0;$lastCount=0;$hidden=0;
		               $ReviewQty=0;
		              do{
		                     $Weeks=$myRow["Weeks"];
		                     $BuyerId=$myRow["BuyerId"];
		                     if ($Weeks!=$oldWeeks || $BuyerId!=$oldBuyerId){
		                      
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
		                          /*
		                          $ReviewPre_2=round($ReviewQty/$WeekQty*100);
		                          $ReviewPre_1=100-$ReviewPre_2;
		                           $LegendArray=array("$ReviewPre_1","$ReviewPre_2");
		                          */ 
		                          $ReviewQty=$ReviewQty>0?$ReviewQty:"";
                                  $Qty=number_format($WeekQty);
		                          $Amount=number_format($WeekAmount);
		                          $weekColor=$oldWeeks<$curWeeks && $oldWeeks>0?"#FF0000":"";
			                       $tempArray=array(
					                       "Id"=>"$oldWeeks",
					                       "Col0"=>array("Text"=>"$ReviewQty"),
					                       "RowSet"=>array("bgColor"=>"$rowColor"),
					                       "Col1"=>array("Text"=>"$weekNumber","BelowText"=>array("Text"=>"$dateSTR"),"bgColor"=>"$weekColor"),
					                       "Col2"=>array("Text"=>"$Qty","Color"=>"$ColTextColor","RLText"=>"($WeekCount)","RLColor"=>"#BBBBBB",
					                                                 "AboveText"=>$AboveArray),
					                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor"),
					                       //"Legend"=>$LegendArray
					                     );
					              $onTapArray=array("Title"=>"$BuyerName / $weekName","Tag"=>"stuff","Args"=>"$oldBuyerId|$oldWeeks");
			                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray);
			                     
			                     $oldWeeks=$myRow["Weeks"]; 
			                     $WeekQty=0;$WeekCount=0;$WeekAmount=0;
			                     $lastQty=0;$lastCount=0;$ReviewQty=0;
		                     }
		                     
		                       if ($BuyerId!=$oldBuyerId){
						               $TotalQty+=$BuyerQty;
						               $TotalAmount+=$BuyerAmount;
						               $TotalOverQty+=$OverQty;
						               $TotalOverAmount+=$OverAmount;
						               $TotalOverCount+=$OverCount;
						               
							            $OverPre_2=$BuyerAmount>0?round($OverAmount/$BuyerAmount*100):0;
                                        $OverPre_1=100-$OverPre_2;
                                       $LegendArray=array("$OverPre_1","$OverPre_2");
                           
                                       $AddRow=array();
							           if ($OverQty>0){
							                 $OverQty=number_format($OverQty);
							                 $OverAmount=number_format($OverAmount);
								            $AddRow=array(
													          array("Text"=>"$OverQty","Color"=>"#FF0000","Copy"=>"Col1",
																				       "RLText"=>"($OverCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
										                     array("Text"=>"¥$OverAmount","Copy"=>"Col3","Color"=>"#FF0000","FontSize"=>"14")
										                   );
							           }
							           
							           $BuyerQty=number_format($BuyerQty);
							           $BuyerAmount=number_format($BuyerAmount);
								       $tempArray=array(
										                      "Id"=>"$oldBuyerId",
										                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
										                      "Title"=>array("Text"=>" $BuyerName","Color"=>"#0066FF","FontSize"=>"14"),
										                       "Col1"=>array("Text"=>"$BuyerQty","RLText"=>"($BuyerCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
										                       "Col3"=>array("Text"=>"¥$BuyerAmount","FontSize"=>"14","Margin"=>"-10,0,10,0"),
										                       "Legend"=>$LegendArray,
										                       "AddRow"=>$AddRow
										                   );
							       $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1650","onTap"=>"1","hidden"=>"$hidden","data"=>$dataArray); 
							        
							        $oldBuyerId=$BuyerId;
							        $BuyerName=$myRow["Name"];
							        $BuyerQty=0;$BuyerAmount=0;$BuyerCount=0;
							         $OverQty=0; $OverAmount=0;$OverCount=0;
							        $dataArray=array();
						      }
			                  
			                 $StuffId=$myRow["StuffId"];
					         $POrderId=$myRow["POrderId"];
					         $Qty=$myRow["Qty"];
		                     $Amount=$myRow["Amount"];
		                     
		                     $ReviewQty+=$myRow["Mid"]>0?0:1;
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
		                   $rowColor=$curWeeks==$oldWeeks?$CURWEEK_BGCOLOR:"#FFFFFF";
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
                          
                          /*
                           $ReviewPre_2=round($ReviewQty/$WeekQty*100);
                          $ReviewPre_1=100-$ReviewPre_2;
                           $LegendArray=array("$ReviewPre_1","$ReviewPre_2");
                           */
                           $ReviewQty=$ReviewQty>0?$ReviewQty:"";
                           $Qty=number_format($WeekQty);
                           $Amount=number_format($WeekAmount);
                           $weekColor=$oldWeeks<$curWeeks && $oldWeeks>0?"#FF0000":"";
                           
	                       $tempArray=array(
			                       "Id"=>"$oldWeeks",
			                       "RowSet"=>array("bgColor"=>"$rowColor"),
			                       "Col0"=>array("Text"=>"$ReviewQty"),
			                       "Col1"=>array("Text"=>"$weekNumber","BelowText"=>array("Text"=>"$dateSTR"),"bgColor"=>"$weekColor"),
			                       "Col2"=>array("Text"=>"$Qty","Color"=>"$ColTextColor","RLText"=>"($WeekCount)","RLColor"=>"#BBBBBB"),
			                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor")
			                      // "Legend"=>$LegendArray
			                     );//,"AboveText"=>array("Text"=>"$OverQty","RLText"=>"($OverCount)","RLColor"=>"#BBBBBB")
			               $onTapArray=array("Title"=>"$BuyerName / $weekName","Tag"=>"stuff","Args"=>"$oldBuyerId|$oldWeeks");
	                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray);
		               
		                $TotalQty+=$BuyerQty;
		                $TotalAmount+=$BuyerAmount;
		                $TotalOverQty+=$OverQty;
		                $TotalOverAmount+=$OverAmount;
		                $TotalOverCount+=$OverCount;
						
						
						$OverPre_2=$BuyerAmount>0?round($OverAmount/$BuyerAmount*100):0;
                        $OverPre_1=100-$OverPre_2;
                        $LegendArray=array("$OverPre_1","$OverPre_2");
                         
                         $AddRow=array();
                          if ($OverQty>0){
					                $OverQty=number_format($OverQty);
					                $OverAmount=number_format($OverAmount);
						            $AddRow=array(
											          array("Text"=>"$OverQty","Color"=>"#FF0000","Copy"=>"Col1",
																		       "RLText"=>"($OverCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
								                     array("Text"=>"¥$OverAmount","Copy"=>"Col3","Color"=>"#FF0000","FontSize"=>"14")
								                   );
							 }
                             
			           $BuyerQty=number_format($BuyerQty);
			           $BuyerAmount=number_format($BuyerAmount);
				       $tempArray=array(
						                      "Id"=>"$oldBuyerId",
						                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
						                      "Title"=>array("Text"=>" $BuyerName","Color"=>"#0066FF","FontSize"=>"14"),
						                       "Col1"=>array("Text"=>"$BuyerQty","RLText"=>"($BuyerCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
						                       "Col3"=>array("Text"=>"¥$BuyerAmount","FontSize"=>"14","Margin"=>"-10,0,10,0"),
						                        "Legend"=>$LegendArray,
						                        "AddRow"=>$AddRow
						                   );
			         $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1650","onTap"=>"1","hidden"=>"$hidden","data"=>$dataArray); 
			         $OverQty=0; $OverAmount=0;$OverCount=0;
			         
			         include "bom_item_sub_2s.php";
			         
			          $TotalOverQty+=$OverQty;
		              $TotalOverAmount+=$OverAmount;
		               $TotalOverCount+=$OverCount;
		               
			         $PercentValue=$TotalAmount>0?round($TotalOverAmount*100/$TotalAmount):0;
			         $TotalQty=number_format($TotalQty);
		             $TotalAmount=number_format($TotalAmount);
		           // if ($LoginNumber==10868){
		               $TotalWeekQty=number_format($TotalWeekQty);
		               $TotalWeekAmount=number_format($TotalWeekAmount);
		                
		               $OverQty=number_format($TotalOverQty);
		               $OverAmount=number_format($TotalOverAmount);
		             
		                $dataArray=array();
		                $colArray[]=array(
		                                      "Icon"=>array("Name"=>"label_all"),
		                                       "Col1"=>array("Text"=>"$TotalQty"),
						                       "Col2"=>array("Text"=>"¥$TotalAmount"));
						                       
						 $colArray[]=array(
								               "Icon"=>array("Name"=>"label_week"),
		                                       "Col1"=>array("Text"=>"$TotalWeekQty","Color"=>"$CURWEEK_TITLECOLOR"),
						                       "Col2"=>array("Text"=>"¥$TotalWeekAmount","Color"=>"$CURWEEK_TITLECOLOR"));   
						                                              
						 $colArray[]=array(
						                      "Icon"=>array("Name"=>"label_over"),
		                                       "Col1"=>array("Text"=>"$OverQty","Color"=>"#FF0000"),
						                       "Col2"=>array("Text"=>"¥$OverAmount","Color"=>"#FF0000"));                      
		                $TitleSTR=versionToNumber($AppVersion)>=278?"":"总计";//Created by 2014/09/02
			            $tempArray=array(
						                      "Id"=>"",
						                      "Percent"=>array("Title"=>"$TitleSTR","Value"=>"$PercentValue"),
						                      "data"=>$colArray
						                   );
			               $dataArray[]=array("Tag"=>"Percent","data"=>$tempArray);
			              $tempArray2[]=array("head"=>array(),"ModuleId"=>"1650","data"=>$dataArray); 
			              array_splice($jsondata,0,0,$tempArray2);
		    }
		}
		else{
		$CountResult= mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums  
					     FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 $SearchRows GROUP BY S.StockId
									   UNION ALL 
									       SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									          LEFT JOIN $DataIn.stuffproperty  OP ON OP.StuffId=S.StuffId AND OP.Property=2  
									         WHERE   S.rkSign>0 AND S.Mid=0  AND (S.FactualQty+S.AddQty)>0 AND OP.Property=2  GROUP BY S.StockId 
									)A 
                      WHERE A.Qty>A.rkQty ",$link_id));
		  /*
			$CountResult= mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums  
					     FROM (
								SELECT B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
									   FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 $SearchRows GROUP BY S.StockId
									    UNION ALL 
									          SELECT  S.StockId,0 AS Qty,0 AS rkQty,SUM(S.Qty) AS SendQty 
									          FROM $DataIn.gys_shsheet S 
                                              LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                                              LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
									          WHERE  S.SendSign=0 AND S.Estate>0 AND  S.StockId>0 AND  G.rkSign>0  AND G.Mid>0  $SearchRows GROUP BY S.StockId
									   )B   GROUP BY B.StockId  
									)A 
                      WHERE A.Qty>A.rkQty ",$link_id));
                 */
		       $COUNT_2=$CountResult["Nums"]==""?0:$CountResult["Nums"];	
		}
?>