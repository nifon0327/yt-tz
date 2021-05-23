<?php
//未收
$SearchRows=$SearchBuyerId==""?"":" AND M.BuyerId='$SearchBuyerId' ";
if ($SegmentIndex==1 || $SegmentIndex==0){
		       $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
                $curWeeks=$dateResult["CurWeek"];
                
		        $TotalQty=0;$TotalAmount=0; $TotalOverQty=0;$TotalOverCount=0;
		        
		        $myResult=mysql_query("SELECT A.CompanyId,P.Forshort,COUNT(*) AS Counts,
		               SUM(A.Qty-A.rkQty) AS Qty,SUM((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount,
		               SUM(IF(YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCounts,
		               SUM(IF(YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1),A.Qty-A.rkQty,0)) AS OverQty,
					   SUM(IF(YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1),(A.Qty-A.rkQty)*S.Price*D.Rate,0)) AS OverAmount 
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
						WHERE A.Qty>A.rkQty GROUP BY  A.CompanyId ORDER BY Amount DESC",$link_id);

				 if($myRow = mysql_fetch_array($myResult)){
		               $CompanyQty=0;$CompanyAmount=0;$CompanyCount=0;
		               $OverQty=0; $OverAmount=0;$OverCount=0;
		               $TotalOverQty=0; $TotalOverAmount=0;$TotalOverCount=0;
		               $hidden=1;
		              do{
		                     $CompanyId=$myRow["CompanyId"];
		                     $Forshort=$myRow["Forshort"];
		                     $CompanyQty=$myRow["Qty"];
		                     $CompanyAmount=$myRow["Amount"];
		                     $CompanyCount=$myRow["Counts"];
		                      $COUNT_1+=$CompanyCount;
		                      
		                     $OverCount=$myRow["OverCounts"];
		                     $OverQty=$myRow["OverQty"];
		                     $OverAmount=$myRow["OverAmount"];
		                     
		                       $TotalQty+=$CompanyQty;
				               $TotalAmount+=$CompanyAmount;
				               $TotalOverQty+=$OverQty;
				               $TotalOverAmount+=$OverAmount;
				               $TotalOverCount+=$OverCount;
						               
					            $OverPre_2=$CompanyAmount>0?round($OverAmount/$CompanyAmount*100):0;
                                $OverPre_1=100-$OverPre_2;
                               $LegendArray=array("$OverPre_1","$OverPre_2");
                   
                               $AddRow=array();
					           if ($OverQty>0){
					                 $OverQty=number_format($OverQty);
					                 $OverAmount=number_format($OverAmount);
						            $AddRow=array(
											          array("Text"=>"$OverQty","Color"=>"#FF0000","Copy"=>"Col1",
																		       "RLText"=>"($OverCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
								                     array("Text"=>"¥$OverAmount","Copy"=>"Col3","Color"=>"#FF0000")
								                   );
					           }
					           
					            //统计交货准时率
							    $PuncSelectType=2;
							    $checkMonth=date("Y-m");
						         include "submodel/cg_punctuality.php";
					           
					           $CompanyQty=number_format($CompanyQty);
					           $CompanyAmount=number_format($CompanyAmount);
						       $tempArray=array(
								                      "Id"=>"$CompanyId",
								                      //"RowSet"=>array("bgColor"=>"#EEEEEE"),
								                      "onTap"=>array("Target"=>"Week","Args"=>"$CompanyId"),
								                      "Title"=>array("Text"=>" $Forshort","FontSize"=>"14"),
								                       "Col1"=>array("Text"=>"$CompanyQty","FontSize"=>"13","RLText"=>"($CompanyCount)",
														                       "RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0",
														                       "AboveText"=>array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon")),
								                       "Col3"=>array("Text"=>"¥$CompanyAmount","FontSize"=>"13","Margin"=>"-10,0,10,0"),
								                       "Legend"=>$LegendArray,
								                       "AddRow"=>$AddRow
								                   );
					       $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1650","onTap"=>"1","hidden"=>"$hidden","data"=>array()); 
					        
					        $dataArray=array();
						   
		               }while($myRow = mysql_fetch_array($myResult));
		                   		               
			         $PercentValue=$TotalAmount>0?round($TotalOverAmount*100/$TotalAmount):0;
			         $TotalQty=number_format($TotalQty);
		             $TotalAmount=number_format($TotalAmount);
		           // if ($LoginNumber==10868){
		              
		                
		               $OverQty=number_format($TotalOverQty);
		               $OverAmount=number_format($TotalOverAmount);
		             
		                //统计交货准时率
					    $PuncSelectType=1;
					    $checkMonth=date("Y-m");
				         include "submodel/cg_punctuality.php";
						         
		                $dataArray=array();
		                 $colArray[]=array(
		                                       "Col1"=>array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon"),
						                       "Col2"=>array("Text"=>""));
		                $colArray[]=array(
		                                       "Icon"=>array("Name"=>"label_all"),
		                                       "Col1"=>array("Text"=>"$TotalQty"),
						                       "Col2"=>array("Text"=>"¥$TotalAmount"));
						 $colArray[]=array(
						                        "Icon"=>array("Name"=>"label_over"),
		                                       "Col1"=>array("Text"=>"$OverQty","Color"=>"#FF0000"),
						                       "Col2"=>array("Text"=>"¥$OverAmount","Color"=>"#FF0000"));                      
		                $TitleSTR=versionToNumber($AppVersion)>=278?"":"总计";//Created by 2014/09/02
			            $tempArray=array(
						                      "Id"=>"",
						                      //"RLText"=>array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color"),
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
									     SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.rkSign>0 AND S.Mid>0 $SearchRows GROUP BY S.StockId 
									     UNION ALL 
									        SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.bps M ON M.StuffId=S.StuffId 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									           LEFT JOIN $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2 
									         WHERE  S.rkSign>0 AND S.Mid=0 AND OP.Property=2  $SearchRows GROUP BY S.StockId   
									)A 
                      WHERE A.Qty>A.rkQty ",$link_id));
		       $COUNT_1=$CountResult["Nums"]==""?0:$CountResult["Nums"];	
		}
?>