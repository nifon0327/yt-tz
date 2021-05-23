<?php 
//每日生产记录明细
$today=date("Y-m-d"); 
$SearchRows=$dModuleId=="1111"?" AND D.TypeId<>'7100' ":" AND D.TypeId='7100' ";

$GroupSql="SELECT D.GroupId,G.GroupName,M.Name,SUM(D.Qty) AS Qty   
		FROM $DataIn.sc1_cjtj D
		LEFT JOIN $DataPublic.staffmain M ON M.Number=D.Leader
		LEFT JOIN $DataIn.staffgroup G ON G.GroupId=D.GroupId  
		WHERE  DATE_FORMAT(D.Date,'%Y-%m-%d')='$checkDate' $SearchRows GROUP BY D.GroupId ORDER BY GroupId";
 $GroupResult = mysql_query($GroupSql,$link_id);
 $dataArray=array();
 while($GroupRow = mysql_fetch_array($GroupResult)) 
{
       $GroupId=$GroupRow["GroupId"];
       $GroupName=$GroupRow["GroupName"];
       $Name=$GroupRow["Name"];
       $sumQty = number_format($GroupRow["Qty"]);

        $GroupNums=0;
        $checkNums= mysql_query("SELECT * FROM $DataIn.sc1_memberset  S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE S.Date='$checkDate' AND S.GroupId='$GroupId' AND M.cSign='7'
AND NOT EXISTS(SELECT Number FROM $DataPublic.kqqjsheet K WHERE K.Number=M.Number  AND K.StartDate<=CURRENT_DATE AND K.EndDate>=CURRENT_DATE)  ",$link_id);
      $GroupNums=@mysql_num_rows($checkNums);
      
       $totalArray=array(
				                      "Title"=>array("Text"=>"$GroupName","Color"=>"#0066FF"),
				                      "Col1"=>array("Text"=>"$Name","Margin"=>"-35,0,0,0"),
				                      "Col2"=>array("Text"=>"$GroupNums". "人","Margin"=>"-38,0,0,0"),
				                      "Col3"=>array("Text"=>"$sumQty")
				                   );  
		$dataArray[]=array("Tag"=>"Total","data"=>$totalArray); 
		
		//生产记录 
        $scdjSql="SELECT S.OrderPO,YM.OrderDate,P.TestStandard,S.ProductId,P.cName,D.POrderId,SUM(D.Qty) AS Qty,D.TypeId,PI.Leadtime
		,M.Name,T.TypeName,YEARWEEK(substring(PI.Leadtime,1,10),1) AS Weeks, YEARWEEK(D.Date,1) AS curWeeks
		FROM $DataIn.sc1_cjtj D
		LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
		LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataPublic.staffmain M ON M.Number=D.Leader
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
		WHERE 1  AND D.GroupId='$GroupId'   AND DATE_FORMAT(D.Date,'%Y-%m-%d')='$checkDate' $SearchRows GROUP BY D.POrderId ORDER BY D.Date DESC";
		     $scdjResult = mysql_query($scdjSql);
		      while($scdjRow = mysql_fetch_array($scdjResult)){
		                    $POrderId=$scdjRow["POrderId"];
		                    $OrderPO=$scdjRow["OrderPO"];
		                    $OrderDate=$scdjRow["OrderDate"];
		                    $Leadtime=$scdjRow["Leadtime"];
		                     $cName=$scdjRow["cName"];
		                    $TestStandard=$scdjRow["TestStandard"];
		                    include "order/order_TestStandard.php";
		                   
		                    $Name=$scdjRow["Name"];
		                    $TypeId=$scdjRow["TypeId"];
		                    $Qty=$scdjRow["Qty"];
		                    //订单总数
		                    $checkOrderSql=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS OrderQty FROM $DataIn.cg1_stocksheet G LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId WHERE 1 AND G.POrderId='$POrderId' AND D.TypeId='$TypeId'",$link_id));
		                    $OrderQty=$checkOrderSql["OrderQty"];
		                    //登记总数
		                    $cjtjSql=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS cjtjQty FROM $DataIn.sc1_cjtj WHERE 1 AND TypeId='$TypeId' AND POrderId='$POrderId'",$link_id));
		                    $cjtjQty=$cjtjSql["cjtjQty"];
		              
	                        //检查是否需更改标准图
	                        $checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
	                        if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
	                            $TestStandard=9;
	                        }
	                        
	                         $curWeeks=$scdjRow["curWeeks"];
                              $Weeks=$scdjRow["Weeks"];
                              $bgColor= $Weeks<$curWeeks?"#FF0000":"";
                              $Weeks=substr($Weeks, 4,2);
                              
                             $scColor=$OrderQty==$cjtjQty?"#00A945":"";
		                     $Qty=number_format($Qty);
		                     $OrderQty=number_format($OrderQty);
		                     $cjtjQty=number_format($cjtjQty); 
		                   
		                    $ScLineResult=mysql_query("SELECT G.GroupName FROM $DataIn.sc1_mission S
							   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
							   WHERE S.POrderId='$POrderId' AND G.Id>0",$link_id);
							if($ScLineRow = mysql_fetch_array($ScLineResult)){
							      $GroupName=$ScLineRow ["GroupName"];
							      $ScLine=substr($GroupName,-1);
							}

		                     $tempArray=array(
					                  "Id"=>"$POrderId",
					                   "RowSet"=>array("bgColor"=>"$rowColor"),
					                   "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","Badge"=>"$ScLine"),//,"iIcon"=>"$Locks"
					                   "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
					                   "Col1"=> array("Text"=>"$OrderPO"),
					                   "Col2"=>array("Text"=>"$OrderQty"),
					                   "Col3"=>array("Text"=>"$cjtjQty","Color"=>"$scColor"),
					                   "Col5"=>array("Text"=>"$Qty")
					                //  "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
					                 // "rIcon"=>"ship$ShipType"
					               );
					        $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);   
				}  
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>