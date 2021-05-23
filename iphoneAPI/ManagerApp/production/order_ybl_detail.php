<?php 
//
$curDate = date("Y-m-d");
$dateResult = mysql_fetch_array(mysql_query("SELECT  YEARWEEK('$curDate',1) AS ThisWeek",$link_id));

$thisWeek = $dateResult["ThisWeek"];

$cztest = "11965"==$LoginNumber;
$UPDATE_SIGN = FALSE;


$deSecArray = array();$rowCount=0;$mainQty = 0;
$allMysql = mysql_query("SELECT M.Date as Mdate ,Y.Qty AS mainQty,
YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks ,
IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,om.OrderDate,om.OrderPO,
C.Forshort,P.cName,P.ProductId, P.Weight, P.eCode,P.TestStandard,Y.POrderId,Q.Estate as QEstate

FROM $DataIn.ck5_llsheet S 
LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
left join  $DataIn.yw1_ordermain om on om.OrderNumber=Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=Y.POrderId  
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=om.CompanyId  
Left join $DataIn.ck5_llconfirm Q on Q.POrderId=Y.POrderId
Left Join $DataIn.pands_unite A On Y.ProductId = A.ProductId
Left Join $DataIn.stuffproperty F On F.StuffId = A.uStuffId
WHERE  S.Estate=0 and M.Date='$dateArg'   AND G.POrderId>0 Group BY G.POrderId ORDER BY Weeks, S.Id DESC");

$mainQty = 0;
$Mdate = $DateTitle= $wName="";
while ($allMysqlR = mysql_fetch_assoc($allMysql)) {
	$Mdate = $allMysqlR["Mdate"];
	$DateTitle=date("m-d",strtotime($Mdate));
	$wName=date("D",strtotime($Mdate)); 
	$Qty = $allMysqlR["mainQty"];
	
	$POrderId=$allMysqlR["POrderId"];
	
	
	$ProductId=$allMysqlR["ProductId"];
	$QEstate = $allMysqlR["QEstate"];
	
	$productEcode = $allMysqlR["eCode"];
	$Weight = $allMysqlR["Weight"];
    $cForshort = $allMysqlR["Forshort"];                
	$AppFilePath="http://www.middlecloud.com/download/teststandard/T" .$ProductId.".jpg";
	$Weight=(float)$Weight;
	$WeightSTR="";
	$productId=$ProductId;
	include "../../model/subprogram/weightCalculate.php";
	if ($Weight>0){
		$extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
		$WeightSTR=$Weight>0?"$productEcode|$Weight|$boxPcs|$extraWeight":"";
	}


	$OrderPO=$allMysqlR["OrderPO"];
	$cName=$allMysqlR["cName"];
	
	$OrderDate=$allMysqlR["OrderDate"];
	$Leadtime=str_replace("*", "", $allMysqlR["Leadtime"]);
	$TestStandard=$allMysqlR["TestStandard"];
	include "order/order_TestStandard.php";
	$piWeek = $allMysqlR["Weeks"];
	$bgColor = $thisWeek > $piWeek ? "#FF0000":"";
	$piWeek = $piWeek >0 ? substr($piWeek,4,2) : "";
	$odDays=(strtotime($curDate)-strtotime($OrderDate))/3600/24;
	
	
	//检查订单备料情况
			$CheckblState="
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate  
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
				LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
				LEFT JOIN ( 
				    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
				    FROM  $DataIn.cg1_stocksheet G 
				    LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
				    WHERE  G.POrderId='$POrderId'  GROUP BY L.StockId 
				  )L ON L.StockId=G.StockId 
				WHERE G.POrderId='$POrderId' AND ST.mainType<2";
			
			$stockHead = mysql_query($CheckblState);
			if($mainRows = mysql_fetch_assoc($stockHead))
			{
			
				$mainBlQty = $mainRows["blQty"];
				$mainLlQty = $mainRows["llQty"];
				
				$R_K1 = $mainRows["K1"];
				$R_K2 = $mainRows["K2"];
				$R_blQty = $mainRows["blQty"];
				$R_llQty = $mainRows["llQty"];
				$R_Locks = $mainRows["Locks"];
				$R_llEstate = $mainRows["llEstate"];
				
				  $FromWebPage=$R_blQty==$R_llQty?"LBL":"KBL";
	             include "../../admin/order_datetime.php";
				 
				  $BlDate=$R_blQty==$R_llQty?$lbl_Date:$kbl_Date;
			}
			
			$BlDate = GetDateTimeOutString($timeFp==NULL?$BlDate:$timeFp,'');
				
			$hasLine = "";
	
			$missionPartSql = "Select B.GroupName  From $DataIn.sc1_mission A
							   Left Join $DataIn.staffgroup B On B.Id = A.Operator 
							   Where A.POrderId = '$POrderId'
							   And B.Estate = '1' Limit 1";
			$missionPartResult = mysql_query($missionPartSql);
			if($missionRow = mysql_fetch_assoc($missionPartResult))
			{
				$line = $missionRow["GroupName"];
				//$name = $missionRow["Name"];
		
				$hasLine = str_replace("组装", "", $line);
				
			}	else {
				continue;
			}
			$mainQty += $Qty;
	$rowColor = $CURWEEK_BGCOLOR;
	$swapDictC = array();$yblID="nodata";
	if ($QEstate!=NULL && $QEstate == 0) {
		$rowColor = "";
	} else {
		/*
		
		if ($UPDATE_SIGN) {
		//mysql_query("replace into $DataIn.ck5_llconfirm (Id,POrderId,Estate) values (null,'$POrderId',0)");
		} else 
		*/
		{
		$QEstateC++;
		$yblID = "dat11";
		$swapDictC = array("Right"=>"358FC1-确认");
		}
	}
 $tempArray=array(
                      "Id"=>"$POrderId","line"=>"$hasLine",
                      "RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$piWeek","bg"=>"$bgColor"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
					  "Col2"=> array("Text"=>"$cForshort","Color"=>"#358FC1"),"Col3"=> array("Text"=>"$OrderPO"),
                      "Col4"=>array("Text"=>"$Qty","bgColor"=>"$FactualQty_Color"),
                      "Col5"=>array("Text"=>"$BlDate","Color"=>"#858888"),"icon4"=>"scdj_11"
                      //"Remark"=>array("Text"=>"$Remark"),"icon4"=>"scdj_11",
                        //"rTopTitle"=>array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
                       
                   );
				   $rowCount++;
				   
				   $deSecArray[]=array("Tag"=>"data","data"=>$tempArray,"CellID"=>$yblID,"Args"=>"$POrderId",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"1","Args"=>"$POrderId"),"List"=>array(),"Swap"=>$swapDictC,"sbID"=>"acce11",
				   "Tap"=>"0","load"=>"0",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR")
				   );
				   
				   
}
$mainQty = $mainQty>0? number_format($mainQty)."($rowCount)":"";
$tempData=array(
		"RowSet"=>array("height"=>""),
		
				                      "Title"=>array("Text"=>"$DateTitle","FontSize"=>"13","rIcon"=>"$wName","Frame"=>"10,10,60,14"),
				                     
									  
				                      "Col3"=>array("Text"=>"$mainQty","Frame"=>"200, 10, 103, 14","FontSize"=>"13"),
				                      //"Rank"=>array("Icon"=>"1"),
				                      // "AddRows"=>$AddRows
									  "dateVal"=>$Mdate
				                   ); 
	$anewSecArr= array("data"=>$tempData,
						  "Tag"=>"day","CellID"=>"Sects","sbID"=>"dat11",
						  "Args"=>"$Mdate","List"=>$deSecArray,
						  "onTap"=>array("value"=>"1","Args"=>"$Mdate","hidden"=>"0","shrink"=>"UpAccessory_blue","Frame"=>"8,16.5,12,12"),
						  "load"=>"0"
						  );



$jsonArray = $anewSecArr;
		//echo json_encode($eachStock);
		//print_r($eachStock);
?>