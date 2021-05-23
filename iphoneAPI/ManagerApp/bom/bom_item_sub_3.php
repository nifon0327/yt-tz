<?php
//特采
$SearchRows=$SearchBuyerId==""?"":" AND M.BuyerId='$SearchBuyerId' "; 
if ($SegmentIndex==3){
		        $TotalQty=0;$TotalAmount=0; 
		        $myResult=mysql_query("SELECT S.StuffId,S.Price,S.StockRemark,S.AddRemark,M.BuyerId,M.PurchaseID,M.CompanyId,
					               DATE_FORMAT(M.Date,'%Y-%m') AS Month,DATEDIFF(CURDATE(),M.Date) AS Days,P.Forshort,
					               (A.Qty-A.rkQty) AS Qty,D.Rate,D.PreChar,N.Name,U.StuffCname,U.Picture 
					     FROM (
									SELECT B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
									   FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 AND S.POrderId='' $SearchRows GROUP BY S.StockId
									   UNION ALL 
									          SELECT  S.StockId,0 AS Qty,0 AS rkQty,SUM(S.Qty) AS SendQty 
									          FROM $DataIn.gys_shsheet S 
                                              LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                                              LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
									          WHERE  S.SendSign=0 AND S.Estate>0 AND  S.StockId>0 AND G.POrderId='' AND  G.rkSign>0  AND G.Mid>0  $SearchRows GROUP BY S.StockId
									   )B  GROUP BY B.StockId  
									)A 
						LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN $DataIn.stuffdata U ON U.StuffId=S.StuffId    
						LEFT JOIN $DataPublic.staffmain N ON N.Number=M.BuyerId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
						WHERE A.Qty>A.rkQty  ORDER BY Month DESC,CompanyId ",$link_id);

				 if($myRow = mysql_fetch_array($myResult)){
				       $oldCompanyId=$myRow["CompanyId"];
				       $Rate=$myRow["Rate"];
				       $oldPreChar=$myRow["PreChar"];
				       $Forshort=$myRow["Forshort"];
				       $oldMonth=$myRow["Month"];
				       $MonthQty=0;$MonthCount=0;$MonthAmount=0;
		               $CompnayQty=0;$CompnayAmount=0;$CompnayCount=0;
		               $hidden=0;$Pos=0;
		              do{
		                     $CompanyId=$myRow["CompanyId"];
		                     $Month=$myRow["Month"];
		                     if ($CompanyId!=$oldCompanyId || $Month!=$oldMonth){
					              $MonthCount+=$CompnayCount;
			                      $MonthQty+=$CompnayQty;
			                     $MonthAmount+=$CompnayAmount*$Rate;
		                          
                                  $CompnayQty=number_format($CompnayQty);
		                          $CompnayAmount=number_format($CompnayAmount);

					               $tempArray=array(
							                      "Id"=>"$oldCompanyId",
							                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF","FontSize"=>"14"),
							                       "Col1"=>array("Text"=>"$CompnayQty","RLText"=>"($CompnayCount)","RLColor"=>"#BBBBBB","Margin"=>"26,0,0,0","FontSize"=>"14"),
							                       "Col3"=>array("Text"=>"$oldPreChar$CompnayAmount","FontSize"=>"14","Margin"=>"-10,0,0,0")
							                   );
							    $tempArray2=array();                
							    $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);       
			                    array_splice($dataArray,$pos,0,$tempArray2);
				                $pos=count($dataArray);
				                
			                    $oldCompanyId=$myRow["CompanyId"];
			                    $Rate=$myRow["Rate"];
			                    $oldPreChar=$myRow["PreChar"];
			                    $Forshort=$myRow["Forshort"];
			                    $CompnayQty=0;$CompnayAmount=0;$CompnayCount=0;
		                     }
		                     
		                       if ($Month!=$oldMonth){
						               $TotalQty+=$MonthQty;
						               $TotalAmount+=$MonthAmount;
							           $MonthQty=number_format($MonthQty);
							           $MonthAmount=number_format($MonthAmount);
								       $tempArray=array(
										                      "Id"=>"$oldMonth",
										                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
										                      "Title"=>array("Text"=>"$oldMonth","Color"=>"#0066FF","FontSize"=>"14"),
										                       "Col1"=>array("Text"=>"$MonthQty","RLText"=>"($MonthCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
										                       "Col3"=>array("Text"=>"¥$MonthAmount","FontSize"=>"14")
										                   );
							       $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"11840","onTap"=>"1","hidden"=>"$hidden","data"=>$dataArray); 
							        
							        $oldMonth=$myRow["Month"];
							        $MonthQty=0;$MonthCount=0;$MonthAmount=0;
							        $dataArray=array();$pos=0;
						      }
			       
				      $Qty=$myRow["Qty"];
				      $Price=$myRow["Price"];
				      $PreChar=$myRow["PreChar"];
				      $Amount=$Qty*$Price;
				       
				      $CompnayQty+=$Qty;
				      $CompnayAmount+= $Amount;
				       
				      $StuffId=$myRow["StuffId"];         
				      $PurchaseID=$myRow["PurchaseID"];
				      $StuffCname=$myRow["StuffCname"];
				      
				      $Picture=$myRow["Picture"];
	                  include "submodel/stuffname_color.php";
	
				      $Qty=number_format($Qty);
				      $Amount=number_format($Amount);
				      $Price=number_format($Price,2);
				      
				      $Remark="";
				      $StockRemark=$myRow["StockRemark"];
				      $AddRemark=$myRow["AddRemark"];
				      $Remark=$StockRemark==""?"":$StockRemark;
				      $Remark.=$AddRemark==""?"":$AddRemark;
				      
				      $Name=$myRow["Name"];
				      $Id=$myRow["Id"];
		              $Days=$myRow["Days"];
		               
		               $tempArray=array(
		                       "Id"=>"$Id",
		                       "Index"=>array("Text"=>"$Days","bgColor"=>""), 
		                       "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
		                       "Col1"=> array("Text"=>"$PurchaseID"),
		                       "Col2"=>array("Text"=>"$Qty"),
		                       "Col4"=>array("Text"=>"$PreChar$Price"),
		                       "Col5"=>array("Text"=>"$Name"),
		                      "Remark"=>array("Text"=>"$Remark","Date"=>"$Date","Operator"=>"$OperatorName")
		                      //"rIcon"=>"ship$ShipType"
		                   );
		              $dataArray[]=array("Tag"=>"data","data"=>$tempArray);
		               
		              $CompnayCount++;
		              $COUNT_3++;
		       }while($myRow = mysql_fetch_array($myResult));
		             $MonthCount+=$CompnayCount;
                     $MonthQty+=$CompnayQty;
                     $MonthAmount+=$CompnayAmount*$Rate;
                      
                      $CompnayQty=number_format($CompnayQty);
                      $CompnayAmount=number_format($CompnayAmount);

		              $tempArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
				                       "Col1"=>array("Text"=>"$CompnayQty","RLText"=>"($CompnayCount)","RLColor"=>"#BBBBBB","Margin"=>"26,0,0,0","FontSize"=>"14"),
				                       "Col3"=>array("Text"=>"$oldPreChar$CompnayAmount","FontSize"=>"14","Margin"=>"-10,0,0,0")
				                   );
				     $tempArray2=array();              
				     $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);       
                     array_splice($dataArray,$pos,0,$tempArray2);
			        
			           $TotalQty+=$MonthQty;
		               $TotalAmount+=$MonthAmount;
			           $MonthQty=number_format($MonthQty);
			           $MonthAmount=number_format($MonthAmount);
				       $tempArray=array(
						                      "Id"=>"$oldMonth",
						                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
						                      "Title"=>array("Text"=>"$oldMonth","Color"=>"#0066FF","FontSize"=>"14"),
						                       "Col1"=>array("Text"=>"$MonthQty","RLText"=>"($MonthCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
						                       "Col3"=>array("Text"=>"¥$MonthAmount","FontSize"=>"14")
						                   );
			       $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"11840","onTap"=>"1","hidden"=>"$hidden","data"=>$dataArray); 
							                          			         
			         $TotalQty=number_format($TotalQty);
		            $TotalAmount=number_format($TotalAmount);
			        $tempArray=array(
					                      "Id"=>"",
					                      "Title"=>array("Text"=>"总计","Color"=>"#000000","FontSize"=>"14","Bold"=>"1"),
					                       "Col1"=>array("Text"=>"$TotalQty","Margin"=>"-30,0,30,0"),
					                       "Col3"=>array("Text"=>"¥$TotalAmount")
					                   );
				   $tempArray2=array();	                  
		           $tempArray2[]=array("head"=>$tempArray,"ModuleId"=>"11840","data"=>array()); 
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
											         WHERE  S.Mid>0 AND  S.rkSign>0  AND S.POrderId='' $SearchRows GROUP BY S.StockId
											    UNION ALL 
											          SELECT  S.StockId,0 AS Qty,0 AS rkQty,SUM(S.Qty) AS SendQty 
											          FROM $DataIn.gys_shsheet S 
		                                              LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		                                              LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
											          WHERE  S.SendSign=0 AND S.Estate>0 AND  S.StockId>0 AND G.POrderId='' AND  G.rkSign>0  AND G.Mid>0  $SearchRows GROUP BY S.StockId
											   )B   GROUP BY B.StockId  
											)A 
		                      WHERE A.Qty>A.rkQty ",$link_id));
		$COUNT_3=$CountResult["Nums"]==""?0:$CountResult["Nums"];	
}
?>