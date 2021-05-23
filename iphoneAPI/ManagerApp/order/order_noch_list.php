<?php 
//未出按周显示明细
//权限
$ReadPower=1;
  if ($LoginNumber!=""){
			    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
			    if($TRow = mysql_fetch_array($TResult)){
			       $ReadPower=1;
			    }
			    else{
			       $ReadPower=0;
			    }
} 

 $SearchWeek=$checkWeek>0 ?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)='$checkWeek'":"";
 $SearchWeek=$checkWeek=="TBC" ?" AND  YEARWEEK(PI.Leadtime,1)  IS NULL AND  YEARWEEK(PL.Leadtime,1)  IS NULL ":$SearchWeek;
 $SearchCompany=$checkCompanyId>0? " AND M.CompanyId='$checkCompanyId' ":"";
 $OrderBySTR=" ORDER BY CompanyId,Leadtime ";


$LockTotalQty=0;$OverTotalQty=0;$BlTotalQty=0;

 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];

$mySql="SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,S.Estate,S.scFrom,P.cName,P.TestStandard,C.Forshort, 
                             PI.Leadtime,YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks 
			FROM $DataIn.yw1_ordermain M
			LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
            LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		    WHERE S.Estate>0  $SearchCompany $SearchWeek $OrderBySTR";
		    //echo $mySql;
    $curDate=date("Y-m-d");
    $dataArray=array(); $jsondata=array(); 
    $viewHidden=0;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $sumQty=0;$sumAmount=0;
            $m=0;$pos=0;
            
            $CompanyId=$myRow["CompanyId"];
            $Forshort=$myRow["Forshort"];
            $oldCompanyId=$CompanyId;
             include "../subprogram/currency_read.php";//$Rate、$PreChar
            do 
            {	
                    $POrderId=$myRow["POrderId"];
                    $OrderPO=$myRow["OrderPO"];
                    $cName=$myRow["cName"];
                    $Qty = $myRow["Qty"];
                    $Unit=$myRow["Unit"]=="PCS"?"pcs":$myRow["Unit"];
                    $Price=$myRow["Price"];
                    $Amount=sprintf("%.2f",$Qty*$Price);		

                    $OrderDate=$myRow["OrderDate"];
                    $Leadtime=str_replace("*", "", $myRow["Leadtime"]);
                    $TestStandard=$myRow["TestStandard"];
                    include "order/order_TestStandard.php";
                    
                   //下单到现在时间
                    $odDays=(strtotime($curDate)-strtotime($OrderDate))/3600/24;
                     if ($Leadtime!=""){
	                     $colorSign=$curDate>=$Leadtime?4:0;
                     }
                     else{
	                      $colorSign=0;
                     }
                     
                    $OrderSignColor=0;$cgRemark="";
                       //检查BOM表配件是否锁定
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
					
					$Remark="";
                    $checkExpress=mysql_query("SELECT S.Type,S.Remark,S.Date,M.Name FROM $DataIn.yw2_orderexpress  S 
                    LEFT JOIN $DataPublic.staffmain  M ON M.Number=S.Operator
                    WHERE S.POrderId='$POrderId' AND S.Type=2 LIMIT 1",$link_id);
					if($checkExpressRow = mysql_fetch_array($checkExpress)){
					    $OrderSignColor=4;
					    $Remark=$checkExpressRow["Remark"];
					     $RemarkDate=date("Y/m/d",strtotime($checkExpressRow["Date"]));
		                 $RemarkOperator=$checkExpressRow["Name"];
					}
					
					$Remark.=$cgRemark;	
					$CompanyId=$myRow["CompanyId"];	
                      
                     if ($oldCompanyId!=$CompanyId ){  
	                         $sumQty=number_format($sumQty);
					          $sumAmount=number_format($sumAmount);
					          $totalArray=array();
					          $tempArray=array(
					                  "Id"=>"$oldCompanyId",
					                  "Title"=>array("Text"=>"$Forshort","Color"=>"#0000FF"),
					                  "Col1"=>array("Text"=>"$sumQty","RLText"=>"($m)","RLColor"=>"#BBBBBB","Margin"=>"26,0,0,0"),
					                  "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"-20,0,0,0")
					               );
					          $totalArray[]=array("Tag"=>"Total","data"=>$tempArray); 
                               array_splice($jsonArray,$pos,0,$totalArray);
                              $pos=count($jsonArray);
		                      $sumQty=0;$sumAmount=0;$m=0;
		                      $oldCompanyId=$CompanyId;
		                       include "../subprogram/currency_read.php";//$Rate、$PreChar
                              $Forshort=$myRow["Forshort"];
                     }
                            $Locks=0;
			                 if ($OrderSignColor==4 || $OrderSignColor==2 || $OrderSignColor==6)
		                      {
			                       $Locks=$OrderSignColor==4?1:2;
			                       $Locks=$OrderSignColor==6?3:$Locks;
			                       if ($OrderSignColor==4)$LockTotalQty+=$Qty;
			                      // $odDays="锁";
		                      }
		               //生产数量
		               $ScQty=""; $rowColor="#FFFFFF";
		               $ScQtyResult=mysql_query("SELECT boxId,SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='7100'",$link_id);
						if($ScQtyRow = mysql_fetch_array($ScQtyResult)){
						      $ScQty=$ScQtyRow["Qty"]==0?"":number_format($ScQtyRow["Qty"]);
						      $ScLine=substr($ScQtyRow["boxId"], 0,1);
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
						
					  $Estate=$myRow["Estate"];	
					  $scFrom=$myRow["scFrom"];	
						if ($scFrom>0){
							 //备料数量
		                      $blResult=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IFNULL(llEstate,0)) AS llEstate  
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
											 WHERE  S.POrderId='$POrderId'  and L.Estate=0 GROUP BY L.StockId
										 ) L ON L.StockId=G.StockId 
								WHERE S.POrderId='$POrderId' AND ST.mainType<2  AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
		                        ",$link_id));
		                      $blQty=$blResult["blQty"]==""?0:$blResult["blQty"];
		                      $llQty=$blResult["llQty"]==""?0:$blResult["llQty"];
		                      $llEstate=$blResult["llEstate"]==""?0:$blResult["llEstate"];
			    			  if ($blQty==$llQty && $blQty>0){
				    			    $BlTotalQty+=$Qty; 
				    			    $rowColor=$llEstate>0?"#F3EBC4":"#CCFFCC";
			    			  }
						}
						else{
							  if ($scFrom==0) $rowColor="#CCFF99";
						}
						
                      $ShipType=$myRow["ShipType"];
                      $timeColor=$curDate>=$Leadtime?"#FF0000":"";
                      $Leadtime=date("m/d",strtotime($Leadtime)) . "|$timeColor";
                      $QtySTR=number_format($Qty);
                      $Weeks=$myRow["Weeks"]==""?" ":substr($myRow["Weeks"],4,2);
                      //$cName=date("Y-m-d H:m:s");
                      if ($myRow["Weeks"]<$curWeek && $checkWeek<>"TBC"){
	                       $OverTotalQty+=$Qty;$OverCount++;
                      }
                      $bgColor=($myRow["Weeks"]<$curWeek && $myRow["Weeks"]!="") ?"#FF0000":"";//#00BA61
                      
                      $WeekCount="Week_" . $myRow["Weeks"];
                      $$WeekCount=$$WeekCount==""?1:$$WeekCount+1;
                      
                      if ($ReadPower==1){
		                     /*毛利计算*//////////// 
		                    //$saleRmbAmount=sprintf("%.3f",$Amount*$Rate);//转成人民币的卖出金额
		                    //include "order_Profit.php";
		                    include "../../model/subprogram/getOrderProfit.php";
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
                      "Col5"=>array("Text"=>"$profitRMB2PC%","Color"=>"$profitColor"),
                      "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                      "rTopTitle"=>array("Text"=>"$odDays"."d","Margin"=>"-22,0,0,0","Color"=>"#0000FF"),
                      "rIcon"=>"ship$ShipType"
                   );
                    $jsonArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
                     $sumQty+=$Qty;
                     $sumAmount+=$Amount;

                    $m++;
            } while($myRow = mysql_fetch_assoc($myResult));
            
          $sumQty=number_format($sumQty);
          $sumAmount=number_format($sumAmount);
          $totalArray=array();
          $tempArray=array(
                  "Id"=>"$oldCompanyId",
                  "Title"=>array("Text"=>"$Forshort","Color"=>"#0000FF"),
                  "Col1"=>array("Text"=>"$sumQty","RLText"=>"($m)","RLColor"=>"#BBBBBB","Margin"=>"26,0,0,0"),
                  "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"-20,0,0,0")
               );
          $totalArray[]=array("Tag"=>"Total","data"=>$tempArray); 
          array_splice($jsonArray,$pos,0,$totalArray);

   }
?>