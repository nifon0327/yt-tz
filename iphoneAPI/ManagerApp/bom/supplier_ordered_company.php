<?php
//未收
$SearchRows=$CompanyId==""?"":" AND M.CompanyId='$CompanyId' ";
$SearchRows.=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckMonth' ";
// echo("test");

/*
	
	<?php 
//已下单记录
$m=0;
$today=date("Y-m-d");
$endDate=date("Y-m-d",strtotime("$today -3 month"));                    
$mySql="SELECT M.Date,SUM(S.FactualQty+S.AddQty) AS Qty,SUM((S.FactualQty+S.AddQty)*S.Price*D.Rate) AS Amount,SUM(IF(S.POrderId>0,0, (S.FactualQty+S.AddQty)*S.Price*D.Rate)) AS outAmount  
	FROM $DataIn.cg1_stocksheet S   
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE  M.Date>='$endDate' GROUP BY  M.Date ORDER BY Date DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;
while($myRow = mysql_fetch_array($Result)) {
	  $Date=$myRow["Date"];
	  $DateSTR=date("m-d",strtotime($Date));  
	  $wName=date("D",strtotime($Date));
	  
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);

      
      $outAmount=round($myRow["outAmount"],2);
      $outPre_2=$Amount>0?round($outAmount/$Amount*100):0;
      $outPre_1=100-$outPre_2;
      $LegendArray=array("$outPre_1","$outPre_2");                         
      
	  $Qty=number_format($Qty);
	  $OverQty=number_format($OverQty);
	  $Amount=number_format($Amount);
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Date"),
				                      "Title"=>array("Text"=>" $DateSTR","RTIcon"=>"$wName"),
				                      "Col1"=>array("Text"=>"$Qty","Margin"=>"-20,0,20,0"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 2, 103, 30"),
				                      "Legend"=>$LegendArray
				                   ); 
				                   
		$dataArray=array();
				                   
		if ($hiddenSign==0){
				$FromPage="Read";  $CheckDate =$Date;
				 include "cg_porder_list.php";
		} 
		                         	                   
	    $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","data"=>$dataArray); 
	    $hiddenSign=1;
}

$jsonArray=array("data"=>$jsondata); 
?>
*/

//$d=strtotime("-12 Months");
//$CheckMonth=date("Y-m",$d);   
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"210,32,48, 15","Align"=>"L"));
                         
 //图标设置           
 $IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"113.5,35,10,10"),
                          "Col3"=>array("Name"=>"cgdj_1","Frame"=>"200,35,10,10")
                          );
 

{
		       $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
                $curWeeks=$dateResult["CurWeek"];
                
		        $TotalQty=0;$TotalAmount=0; $TotalOverQty=0;$TotalOverCount=0;
		        
		        $myResult=mysql_query("SELECT 
			           A.CompanyId ,P.Forshort,
			           COUNT(*) AS Counts,
			            SUM(A.Qty) AS Qty,
		               SUM((A.Qty)*S.Price*D.Rate) AS Amount,
			           SUM(if((A.Qty-A.rkQty)>0,1,0)) AS noCounts,
		               SUM(A.Qty-A.rkQty) AS noQty,
		               SUM(if(A.Qty>S.OrderQty,1,0)) AS abQty,
		               SUM((A.Qty-A.rkQty)*S.Price*D.Rate) AS noAmount
					     FROM (
									    SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE S.Mid>0 AND M.CompanyId NOT IN (getSysConfig(106)) $SearchRows GROUP BY S.StockId 
									  
									)A 
						LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId
							LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
						WHERE 1 $SearchRows  GROUP BY  A.CompanyId ORDER BY Amount DESC",$link_id);
   $CompanyQty=0;$CompanyAmount=0;$CompanyCount=0;
		               $OverQty=0; $OverAmount=0;$OverCount=0;
		               $TotalOverQty=0; $TotalOverAmount=0;$TotalOverCount=0;
		               $hidden=1;
				 if($myRow = mysql_fetch_array($myResult)){
		            
		              do{


$Forshort = $myRow["Forshort"];
$CompaynyId = $myRow["CompanyId"];
		                    // $Month=$myRow["Month"];
		                     $CompanyQty=$myRow["Qty"];
		                     $CompanyAmount=$myRow["Amount"];
		                     $CompanyCount=$myRow["Counts"];
		                      $COUNT_1+=$CompanyCount;
		                      
		                     $OverCount=$myRow["noCounts"];
		                     $OverQty=$myRow["noQty"];
		                     $OverAmount=$myRow["noAmount"];
		                     
		                       $TotalQty+=$CompanyQty;
				               $TotalAmount+=$CompanyAmount;
				               $TotalOverQty+=$OverQty;
				               $TotalOverAmount+=$OverAmount;
				               $TotalOverCount+=$OverCount;
						               
					            $OverPre_2=$CompanyAmount>0?round($OverAmount/$CompanyAmount*100):0;
                                $OverPre_1=100-$OverPre_2;
                            //   $LegendArray=array("$OverPre_1","$OverPre_2");
                   
                               $AddRow=array();
					           if ($OverQty>0 || $LoginNumber==11965){
					                 $OverQty=number_format($OverQty);
					                 $OverAmount=number_format($OverAmount);
						            $AddRow=array(
											          array("Text"=>"$OverQty","Color"=>"#FF0000","Copy"=>"Col_1",
																		       "RLText"=>"($OverCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,-7,30,0"),
								                     array("Text"=>"¥$OverAmount","Copy"=>"Col_3","Color"=>"#FF0000","Margin"=>"0,-7,0,0")
								                   );
					           }
					           
					           $upMargin = count($AddRow) > 0 ? 7 : 0;
					           
					           
					            //统计交货准时率
							    $PuncSelectType=2;
							    $checkMonth=date("Y-m");
						     //    include "submodel/cg_punctuality.php";
					           
					           $CompanyQty=number_format($CompanyQty);
					           $CompanyAmount=number_format($CompanyAmount);
					           
					            $abQty = $myRow['abQty'];
					           $abQty = $abQty>0?$abQty:'';
					           
						       $tempArray=array(
								                      "Id"=>"$CompanyId",
								                      //"RowSet"=>array("bgColor"=>"#EEEEEE"),
								                      "onTap"=>array("Target"=>"ordered_sub","Args"=>"$CompaynyId|$CheckMonth"),
								                      "Title"=>array("Text"=>"  $Forshort","light"=>"13","Margin"=>"0,$upMargin,0,0"),
								                  'Col2'=>array('Text'=>$abQty.'', 'Color'=>'#FF0000','light'=>'12',"Margin"=>"-88,$upMargin,0,0",'Align'=>'L'),  "Col1"=>array("Text"=>"$CompanyQty","light"=>"13","RLText"=>"($CompanyCount)",
														                       "RLColor"=>"#BBBBBB","Margin"=>"0,0,27,0","Color"=>"#000000"),
								                       "Col3"=>array("Text"=>"¥$CompanyAmount","light"=>"13","Margin"=>"-10,0,10,0"),
								                 //      "Legend"=>$LegendArray,
								                       "AddRow"=>$AddRow
								                   );
					       $jsondata[]=array("data"=>$tempArray,"Tag"=>"Total","hidden"=>"1","w_tap"=>"1"); 
					        
					        $dataArray=array();
						   
		               }while($myRow = mysql_fetch_array($myResult));
		                   		               
			        
		    }
		}
				
$jsonArray=$jsondata; 
?>