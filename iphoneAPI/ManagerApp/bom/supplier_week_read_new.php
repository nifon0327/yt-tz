<?php 
//供应商按周显示明细
 $SearchRows=$CompanyId==""?"":" AND M.CompanyId='$CompanyId' ";
 
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
 $curWeeks=$dateResult["CurWeek"];
             	        		        
$myResult=mysql_query("SELECT S.POrderId,S.StuffId,A.CompanyId,P.Forshort,YEARWEEK(S.DeliveryDate,1) AS Weeks,
       (A.Qty-A.rkQty) AS Qty,((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount,R.Mid  
	     FROM (
					     SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.rkSign>0 AND S.Mid>0 AND M.CompanyId NOT IN (getSysConfig(106)) $SearchRows GROUP BY S.StockId 
									     UNION ALL 
									        SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.bps M ON M.StuffId=S.StuffId 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									           LEFT JOIN $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2 
									         WHERE  S.rkSign>0 AND S.Mid=0 AND OP.Property=2  $SearchRows GROUP BY S.StockId 
					)A 
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId   
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId  
		LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
	    LEFT JOIN $DataIn.cg1_stockreview R ON  R.Mid=S.Mid 
		WHERE A.Qty>A.rkQty  ORDER BY Weeks",$link_id);

				 if($myRow = mysql_fetch_array($myResult)){
				       $oldWeeks=$myRow["Weeks"];
				       $Forshort=$myRow["Forshort"];
				       $WeekQty=0;$WeekCount=0;$WeekAmount=0;
		               $lastQty=0;$lastCount=0;$hidden=1;
		               $ReviewQty=0;
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
		                                          $ColTextColor="#FF0000";
		                                   }
				                   }
		                          
		                          $AboveArray=array();
		                          if ($lastCount>0){
			                          $lastQty=number_format($lastQty);
			                          $AboveArray=array("Text"=>"$lastQty","RLText"=>"($lastCount)","RLColor"=>"#BBBBBB");
		                          }
		                          /*
		                          $ReviewPre_2=$WeekQty>0?round($ReviewQty/$WeekQty*100):0;
		                          $ReviewPre_1=100-$ReviewPre_2;
		                           $LegendArray=array("$ReviewPre_1","$ReviewPre_2");
		                           */
		                           $weekColor=$oldWeeks<$curWeeks && $oldWeeks>0?"#FF0000":"";
		                           $ReviewQty=$ReviewQty>0?$ReviewQty:"";
                                  $Qty=number_format($WeekQty);
		                          $Amount=number_format($WeekAmount);
			                       $tempArray=array(
					                       "Id"=>"$oldWeeks",
					                       "RowSet"=>array("bgColor"=>"$rowColor"),
					                       "Col0"=>array("Text"=>"$ReviewQty"),
					                            "onTap"=>array("Target"=>"WeekSub","Args"=>"$CompanyId|$oldWeeks"),
					                       "Col1"=>array("Text"=>"$weekNumber","BelowText"=>array("Text"=>"$dateSTR"),"bgColor"=>"$weekColor"),
					                       "Col2"=>array("Text"=>"$Qty","Color"=>"$ColTextColor","RLText"=>"($WeekCount)","RLColor"=>"#BBBBBB",
					                                                 "AboveText"=>$AboveArray),
					                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor","Margin"=>"10,0,0,0")
					                       //"Legend"=>$LegendArray
					                     );
					              $onTapArray=array("Title"=>"$Forshort / $weekName","Tag"=>"stuff","Args"=>"$CompanyId|$oldWeeks");
			                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray,"w_tap"=>"1","hidden"=>"1");
			                     
			                     $oldWeeks=$myRow["Weeks"]; 
			                     $WeekQty=0;$WeekCount=0;$WeekAmount=0;
			                     $lastQty=0;$lastCount=0;$ReviewQty=0;
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
		               }while($myRow = mysql_fetch_array($myResult));
                           
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
			                       "onTap"=>array("Target"=>"WeekSub","Args"=>"$CompanyId|$oldWeeks"),
			                       "Col0"=>array("Text"=>"$ReviewQty"),
			                       "Col1"=>array("Text"=>"$weekNumber","BelowText"=>array("Text"=>"$dateSTR"),"bgColor"=>"$weekColor"),
			                       "Col2"=>array("Text"=>"$Qty","Color"=>"$ColTextColor","RLText"=>"($WeekCount)","RLColor"=>"#BBBBBB"),
			                       "Col3"=>array("Text"=>"¥$Amount","Color"=>"$ColTextColor","Margin"=>"10,0,0,0")
			                    //   "Legend"=>$LegendArray
			                     );//,"AboveText"=>array("Text"=>"$OverQty","RLText"=>"($OverCount)","RLColor"=>"#BBBBBB")
			               $onTapArray=array("Title"=>"$Forshort / $weekName","Tag"=>"stuff","Args"=>"$CompanyId|$oldWeeks");
	                       $dataArray[]=array("Tag"=>"Week","onTap"=>$onTapArray,"data"=>$tempArray,"w_tap"=>"1","hidden"=>"1");
	                       $jsonArray=$dataArray;
	             }
?>
