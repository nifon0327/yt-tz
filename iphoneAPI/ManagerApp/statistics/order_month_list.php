<?php 
//δΈεζη»
$today=date("Y-m-d");                      
 $orderResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount    
        FROM $DataIn.yw1_ordersheet S
    	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$checkMonth' ",$link_id));
$AllOrderAmount=sprintf("%.0f",$orderResult["Amount"]); 

$mySql="SELECT M.CompanyId,C.Forshort,D.PreChar,SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty) AS Amount,SUM(S.Price*S.Qty*D.Rate) AS SortAmount 
			FROM $DataIn.yw1_ordermain M
			LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
             LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency  
		    WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$checkMonth'  GROUP BY M.CompanyId ORDER BY SortAmount DESC";
 $myResult = mysql_query($mySql,$link_id);
 $dataArray=array();
 while($myRow = mysql_fetch_array($myResult)) 
{
       $CompanyId=$myRow["CompanyId"];
       $Forshort=$myRow["Forshort"];
       $sumQty = number_format($myRow["Qty"]);
       $sumAmount=number_format($myRow["Amount"]);
       $PreChar=$myRow["PreChar"];
       
       $SortAmount=$myRow["SortAmount"];
       $Percent=($SortAmount/$AllOrderAmount)*100;
       $Percent=$Percent>=1?round($Percent)."%":""; 
       
       $totalArray=array(
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF"),
				                      "Col1"=>array("Text"=>"$Percent","Margin"=>"-35,0,0,0"),
				                      "Col2"=>array("Text"=>"$sumQty","Margin"=>"-35,0,0,0"),
				                      "Col3"=>array("Text"=>"$PreChar$sumAmount")
				                   );  
		$dataArray[]=array("Tag"=>"Total","data"=>$totalArray); 
		
		$orderResult=mysql_query("SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,S.Estate,S.scFrom,P.cName,P.TestStandard,
                             PI.Leadtime,YEARWEEK(PI.Leadtime,1)  AS Weeks 
			FROM $DataIn.yw1_ordermain M
			LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
            LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
		    WHERE S.Id>0 AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$checkMonth' AND M.CompanyId='$CompanyId' ORDER BY OrderDate",$link_id);
		    while($orderRow = mysql_fetch_array($orderResult)) {
			        $POrderId=$orderRow["POrderId"];
                    $OrderPO=$orderRow["OrderPO"];
                    $cName=$orderRow["cName"];
                    $Qty = $orderRow["Qty"];
                    $Price=$orderRow["Price"];
                    
                    $OrderDate=$orderRow["OrderDate"];
                    $Leadtime=str_replace("*", "", $orderRow["Leadtime"]);
                    $TestStandard=$orderRow["TestStandard"];
                    include "order/order_TestStandard.php";
                    
                    //ζ£ζ₯BOMθ‘¨ιδ»Άζ―ε¦ιε?
                    $OrderSignColor=0;$cgRemark="";$RemarkDate="";$Remark="";
					$checkcgLockSql=mysql_query("SELECT count(*) AS Locks,SUM(if(GL.Locks=0 AND ST.mainType=3 and D.TypeId<>7100,1,0)) AS gLocks,GL.Remark,GL.Date,M.Name  FROM $DataIn.cg1_stocksheet G 
					LEFT JOIN $DataIn.cg1_lockstock GL  ON G.StockId=GL.StockId 
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
					LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
					LEFT JOIN $DataPublic.staffmain  M ON M.Number=GL.Operator 
					WHERE  G.POrderId='$POrderId' AND GL.Locks=0 ",$link_id);
				    if($checkcgLockRow = mysql_fetch_array($checkcgLockSql)){
				        $cgRemark=$checkcgLockRow["Remark"];
				        $RemarkDate=date("Y/m/d",strtotime($checkcgLockRow["Date"]));
			            $RemarkOperator=$checkcgLockRow["Name"];
			            
						if ($checkcgLockRow["Locks"]>0){
							    $OrderSignColor=$checkcgLockRow["gLocks"]>0?2:6;   
							  }
					}
					
                    $checkExpress=mysql_query("SELECT S.Type,S.Remark,S.Date,M.Name FROM $DataIn.yw2_orderexpress  S 
                    LEFT JOIN $DataPublic.staffmain  M ON M.Number=S.Operator
                    WHERE S.POrderId='$POrderId' AND S.Type=2 LIMIT 1",$link_id);
						if($checkExpressRow = mysql_fetch_array($checkExpress)){
						    $OrderSignColor=4;
						    $Remark=trim($checkExpressRow["Remark"])==""?"ζͺε‘«ε?εε ":$checkExpressRow["Remark"];
						     $RemarkDate=date("Y/m/d",strtotime($checkExpressRow["Date"]));
			                 $RemarkOperator=$checkExpressRow["Name"];
						}
					$Remark.=$cgRemark;	
					
					//ηδΊ§ζ°ι
	               $ScQty=""; $rowColor="#FFFFFF";$ScLine="";
	               $ScQtyResult=mysql_query("SELECT boxId,SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='7100'",$link_id);
					if($ScQtyRow = mysql_fetch_array($ScQtyResult)){
					      $ScQty=$ScQtyRow["Qty"]==0?"":number_format($ScQtyRow["Qty"]);
					      $ScLine=substr($ScQtyRow["boxId"], 0,1);
					      $bgColor=$ScQtyRow["Qty"]==$Qty?"#99C764":"";
					}
					
					if ($ScQty==""){
						  $ScLineResult=mysql_query("SELECT G.GroupName FROM $DataIn.sc1_mission S
						   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
						   WHERE S.POrderId='$POrderId' AND G.Id>0",$link_id);
						if($ScLineRow = mysql_fetch_array($ScLineResult)){
						      $GroupName=$ScLineRow ["GroupName"];
						      $ScLine=substr($GroupName,-1);
						}
				 }
				 
				$Estate=$orderRow["Estate"];	
				$scFrom=$orderRow["scFrom"];	
				if ($scFrom>0){
					 //ε€ζζ°ι
                      $blResult=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IFNULL(L.llEstate,0)) AS llEstate 
                        FROM $DataIn.yw1_ordersheet S 
                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
						             SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(L.Estate) AS llEstate 
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  S.POrderId='$POrderId'  GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId 
						WHERE S.POrderId='$POrderId' AND ST.mainType<2  AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
                        ",$link_id));
                      $blQty=$blResult["blQty"]==""?0:$blResult["blQty"];
                      $llQty=$blResult["llQty"]==""?0:$blResult["llQty"];
                      $llEstate=$blResult["llEstate"];
	    			  if ($blQty==$llQty){
		    			    $rowColor=$llEstate>0?"#F3EBC4":"#CCFFCC";
	    			  }
				}

				if ($Estate==0){
					 $ScLine="iship";$rowColor="";
				}
						
                  $ShipType=$orderRow["ShipType"];
                  //$timeColor=$curDate>=$Leadtime?"#FF0000":"";
                  $QtySTR=number_format($Qty);
                  $Weeks=$orderRow["Weeks"]==""?" ":substr($orderRow["Weeks"],4,2);
                  
                   //δΈεε°η°ε¨ζΆι΄
                   if ($Estate==0){
	                    $chResult=mysql_fetch_array(mysql_query("SELECT MAX(M.Date) AS chDate FROM  $DataIn.ch1_shipsheet S 
						   LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid	
						   WHERE S.POrderId='$POrderId'",$link_id));
						$chDate=$chResult["chDate"];
						 $odDays=(strtotime($chDate)-strtotime($OrderDate))/3600/24;
                   }
                   else{
                      $odDays=(strtotime($today)-strtotime($OrderDate))/3600/24;
                  }
                  $Locks=0;
                 if ($OrderSignColor==4 || $OrderSignColor==2 || $OrderSignColor==6)
                  {
                       $Locks=$OrderSignColor==4?1:2;
                       $Locks=$OrderSignColor==6?3:$Locks;
                  }

      
                  $Price=number_format($Price,2);
                  $tempArray=array(
                  "Id"=>"$POrderId",
                   "RowSet"=>array("bgColor"=>"$rowColor"),
                   "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                   "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                   "Col1"=> array("Text"=>"$OrderPO"),
                   "Col2"=>array("Text"=>"$QtySTR"),
                   "Col3"=>array("Text"=>"$ScQty"),
                   "Col4"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$odDays"."d","Color"=>"#0000FF"),
                  "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                  "rIcon"=>"ship$ShipType"
               );
               $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
	   }   
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>