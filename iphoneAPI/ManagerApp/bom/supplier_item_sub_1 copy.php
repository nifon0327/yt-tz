<?php
//未收
$SearchRows=$SearchBuyerId==""?"":" AND M.BuyerId='$SearchBuyerId' ";
$SearchRows.=" AND NOT EXISTS(SELECT OP.StuffId FROM $DataIn.stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) ";
if ($SegmentIndex==1){
		       $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
                $curWeeks=$dateResult["CurWeek"];
                
		        $TotalQty=0;$TotalAmount=0; $TotalOverQty=0;$TotalOverCount=0;
		        
		        $myResult=mysql_query("SELECT S.POrderId,S.StuffId,M.CompanyId,P.Forshort,YEARWEEK(S.DeliveryDate,1) AS Weeks,
		               (A.Qty-A.rkQty) AS Qty,((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount,R.Mid  
					     FROM (
									SELECT B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
									   FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 $SearchRows GROUP BY S.StockId
									   UNION ALL 
									          SELECT  S.StockId,0 AS Qty,0 AS rkQty,SUM(IFNULL(S.Qty,0)) AS SendQty 
									          FROM $DataIn.gys_shsheet S 
                                              LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                                              LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
									          WHERE  S.SendSign=0 AND S.Estate>0 AND  S.StockId>0 AND  G.rkSign>0  AND G.Mid>0  $SearchRows GROUP BY S.StockId
									   )B  GROUP BY B.StockId  
									)A 
						LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
					    LEFT JOIN $DataIn.cg1_stockreview R ON  R.Mid=S.Mid 
						WHERE A.Qty>A.rkQty  ORDER BY CompanyId,Weeks",$link_id);

				 if($myRow = mysql_fetch_array($myResult)){
				       $oldCompanyId=$myRow["CompanyId"];
				       $Forshort=$myRow["Forshort"];
				       $oldWeeks=$myRow["Weeks"];
				       $WeekQty=0;$WeekCount=0;$WeekAmount=0;
		               $CompanyQty=0;$CompanyAmount=0;$CompanyCount=0;
		               $OverQty=0; $OverAmount=0;$OverCount=0;
		               $TotalOverQty=0; $TotalOverAmount=0;$TotalOverCount=0;
		               $lastQty=0;$lastCount=0;$hidden=1;
		               $ReviewQty=0;
		              do{
		                     $Weeks=$myRow["Weeks"];
		                     $CompanyId=$myRow["CompanyId"];
		                     if ($Weeks!=$oldWeeks || $CompanyId!=$oldCompanyId){
		                      
			                         $ColTextColor="#000000";
				                     $rowColor=$curWeeks==$oldWeeks?"#CCFF99":"#FFFFFF";
				                     if ($oldWeeks==""){
					                     $weekName="交期待定"; $weekNumber="00";$dateSTR="  交期待定";
				                     }else{
					                     $weekName="Week ". substr($oldWeeks,4,2);
					                     $weekNumber=substr($oldWeeks,4,2);
					                     $dateArray= GetWeekToDate($oldWeeks,"m/d");
		                                  $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		                                  if ($oldWeeks<=$curWeeks) {
		                                          $OverQty+=$WeekQty; $OverAmount+=$WeekAmount;$OverCount+=$WeekCount;
		                                          $ColTextColor="#FF0000";
		                                   }
				                   }
				                   
					             $CompanyCount+=$WeekCount;
			                     $CompanyQty+=$WeekQty;
			                     $CompanyAmount+=$WeekAmount;
		                          
		                          $AboveArray=array();
		                          if ($lastCount>0){
			                          $lastQty=number_format($lastQty);
			                          $AboveArray=array("Text"=>"$lastQty","RLText"=>"($lastCount)","RLColor"=>"#BBBBBB");
		                          }
		                          $ReviewPre_2=$WeekQty>0?round($ReviewQty/$WeekQty*100):0;
		                          $ReviewPre_1=100-$ReviewPre_2;
		                           $LegendArray=array("$ReviewPre_1","$ReviewPre_2");
		                           
                                  $Qty=number_format($WeekQty);
		                          $Amount=number_format($WeekAmount);
			                       $tempArray=array(
					                       "Id"=>"$oldWeeks",
					                       "RowSet"=>array("bgColor"=>"$rowColor"),
					                       "Col1"=>array("Text"=>"$weekNumber","BelowText"=>array("Text"=>"$dateSTR")),
					                       "Col2"=>array("Text"=>"$Qty","Color"=>"$ColTextColor","RLText"=>"($WeekCount)","RLColor"=>"#BBBBBB",
					                                                 "AboveText"=>$AboveArray),
					                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor"),
					                       "Legend"=>$LegendArray
					                     );
					              $onTapArray=array("Title"=>"$weekName","Tag"=>"stuff","Args"=>"$oldBuyerId|$oldWeeks");
			                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray);
			                     
			                     $oldWeeks=$myRow["Weeks"]; 
			                     $WeekQty=0;$WeekCount=0;$WeekAmount=0;
			                     $lastQty=0;$lastCount=0;$ReviewQty=0;
		                     }
		                     
		                       if ($CompanyId!=$oldCompanyId){
						               $TotalQty+=$CompanyQty;
						               $TotalAmount+=$CompanyAmount;
						               $TotalOverQty+=$OverQty;
						               $TotalOverAmount+=$OverAmount;
						               $TotalOverCount+=$OverCount;
						               
							            $OverPre_2=$CompanyQty>0?round($OverQty/$CompanyQty*100):0;
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
							           
							           $CompanyQty=number_format($CompanyQty);
							           $CompanyAmount=number_format($CompanyAmount);
								       $tempArray=array(
										                      "Id"=>"$oldCompanyId",
										                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
										                      "Title"=>array("Text"=>" $Forshort","Color"=>"#0066FF","FontSize"=>"14"),
										                       "Col1"=>array("Text"=>"$CompanyQty","RLText"=>"($CompanyCount)",
																                       "RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
										                       "Col3"=>array("Text"=>"¥$CompanyAmount","FontSize"=>"14","Margin"=>"-10,0,10,0"),
										                       "Legend"=>$LegendArray,
										                       "AddRow"=>$AddRow
										                   );
							       $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1650","onTap"=>"1","hidden"=>"$hidden","data"=>$dataArray); 
							        
							        $oldCompanyId=$CompanyId;
							        $Forshort=$myRow["Forshort"];
							        $CompanyQty=0;$CompanyAmount=0;$CompanyCount=0;
							         $OverQty=0; $OverAmount=0;$OverCount=0;
							        $dataArray=array();
						      }
			                  
			                 $StuffId=$myRow["StuffId"];
					         $POrderId=$myRow["POrderId"];
					         $Qty=$myRow["Qty"];
		                     $Amount=$myRow["Amount"];
		                     
		                     $ReviewQty+=$myRow["Mid"]>0?0:$Qty;
		                     /*
					         if ($POrderId>0 && $Weeks<=$curWeeks){
						         //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
								include "../../model/subprogram/stuff_blcheck.php";
						         if ($LastBlSign==1)  {$lastCount++;$lastQty+=$Qty;}
					        }
                            */
		                     $WeekCount++;
		                     $WeekQty+=$Qty;
		                     $WeekAmount+=$Amount;
		                     $COUNT_1++;
		               }while($myRow = mysql_fetch_array($myResult));
		                   $CompanyCount+=$WeekCount;
	                        $CompanyQty+=$WeekQty;
	                       $CompanyAmount+=$WeekAmount;
                           
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
			                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor"),
			                       "Legend"=>$LegendArray
			                     );//,"AboveText"=>array("Text"=>"$OverQty","RLText"=>"($OverCount)","RLColor"=>"#BBBBBB")
			               $onTapArray=array("Title"=>"$weekName","Tag"=>"stuff","Args"=>"$oldBuyerId|$oldWeeks");
	                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray);
		               
		                $TotalQty+=$CompanyQty;
		                $TotalAmount+=$CompanyAmount;
		                $TotalOverQty+=$OverQty;
		                $TotalOverAmount+=$OverAmount;
		                $TotalOverCount+=$OverCount;
						
						
						$OverPre_2=$CompanyQty>0?round($OverQty/$CompanyQty*100):0;
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
                             
			           $CompanyQty=number_format($CompanyQty);
			           $CompanyAmount=number_format($CompanyAmount);
				       $tempArray=array(
						                      "Id"=>"$oldCompanyId",
						                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
						                      "Title"=>array("Text"=>" $BuyerName","Color"=>"#0066FF","FontSize"=>"14"),
						                       "Col1"=>array("Text"=>"$CompanyQty","RLText"=>"($CompanyCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
						                       "Col3"=>array("Text"=>"¥$CompanyAmount","FontSize"=>"14","Margin"=>"-10,0,10,0"),
						                        "Legend"=>$LegendArray,
						                        "AddRow"=>$AddRow
						                   );
			         $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1650","onTap"=>"1","hidden"=>"$hidden","data"=>$dataArray); 
			         $OverQty=0; $OverAmount=0;$OverCount=0;
			         
			          $TotalOverQty+=$OverQty;
		              $TotalOverAmount+=$OverAmount;
		               $TotalOverCount+=$OverCount;
		               
			         $PercentValue=$TotalAmount>0?round($TotalOverAmount*100/$TotalAmount):0;
			         $TotalQty=number_format($TotalQty);
		             $TotalAmount=number_format($TotalAmount);
		           // if ($LoginNumber==10868){
		              
		                
		               $OverQty=number_format($TotalOverQty);
		               $OverAmount=number_format($TotalOverAmount);
		             
		                $dataArray=array();
		                $colArray[]=array(
		                                       "Col1"=>array("Text"=>"$TotalQty"),
						                       "Col2"=>array("Text"=>"¥$TotalAmount"));
						 $colArray[]=array(
		                                       "Col1"=>array("Text"=>"$OverQty","Color"=>"#FF0000"),
						                       "Col2"=>array("Text"=>"¥$OverAmount","Color"=>"#FF0000"));                      
		                
			            $tempArray=array(
						                      "Id"=>"",
						                      "Percent"=>array("Title"=>"总计","Value"=>"$PercentValue"),
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
		       $COUNT_1=$CountResult["Nums"]==""?0:$CountResult["Nums"];	
		}
?>