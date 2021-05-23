<?php 
//月份参数info的索引位置为1
//$monthParam = $info[1];  
function outputValue($POrderId, $qty, $DataIn, $link_id)
	{
		$sumPrice = 0.0;
		$selectProdcutValueSql = "Select A.Price From $DataIn.cg1_stocksheet A
								  Left join $DataIn.StuffData B On B.StuffId = A.StuffId
								  Left join $DataIn.StuffType C On C.TypeId = B.TypeId
								  Where A.POrderId = '$POrderId'
								  And C.mainType = '3'";


		$productPriceResult = mysql_query($selectProdcutValueSql, $link_id);
		if(mysql_num_rows($productPriceResult) > 0)
		{
			$productPriceRow = mysql_fetch_assoc($productPriceResult);
			$price = $productPriceRow["Price"];
			$sumPrice = round($price * $qty, 2);

		}

		return sprintf("%.2f",$sumPrice);

	}
$SearchRows=$dModuleId=="1111"?" AND D.TypeId<>'7100' ":" AND D.TypeId='7100' ";

if (count($info)>1 && $info[1]=="Col2") {
		$DateDay = $info[2];
		//人均产值
		
		$totalProPrice = 0;
		$sumProPriceSql = "select D.POrderId, D.Qty from $DataIn.sc1_cjtj D
						    WHERE 1  AND DATE_FORMAT(D.Date,'%Y-%m-%d')='$DateDay'  $SearchRows "; 
		$sumProPriceRs=mysql_query($sumProPriceSql, $link_id);
		 while ($sumProPriceRow = mysql_fetch_array($sumProPriceRs)) {
			 $totalProPrice += outputValue($sumProPriceRow["POrderId"], $sumProPriceRow["Qty"], $DataIn, $link_id);
		 }
	
          
  $totalProPrice = round($totalProPrice,1);
		
	  $jsonArray = array("Col2"=>"$totalProPrice");
	    
		
		
} else {


$mySql="SELECT DATE_FORMAT(D.Date,'%Y-%m-%d') AS Date,
		 SUM(D.Qty) AS Qty 
		 FROM $DataIn.sc1_cjtj D 
		 WHERE 1  AND DATE_FORMAT(D.Date,'%Y-%m')='$monthParam'  $SearchRows
		 GROUP BY DATE_FORMAT(D.Date,'%Y-%m-%d')  ORDER BY Date DESC";
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;

while($myRow = mysql_fetch_array($Result)) {
	  $Date=$myRow["Date"];
	  
	  	$sqlRemark = mysql_query("select DATE_FORMAT(lg.OPdatetime,'%Y/%m/%d') AS OPdatetime,
									lg.Remark as remark,sm.Name as smName 
									from $DataIn.sc1_cjtj_log lg 
									left join $DataPublic.staffmain sm on lg.operator=sm.Number 
									where lg.GroupId='0' and lg.estate=1 and lg.date='$Date' ");
	$remark = $remarkOper =$logtime ="";
	if ($sqlRemarkRow = mysql_fetch_array($sqlRemark)) {
	$remark = $sqlRemarkRow["remark"];
	$remarkOper = $sqlRemarkRow["smName"];
	$logtime = $sqlRemarkRow["OPdatetime"];
	
	}

	  
	  $Qty=number_format($myRow["Qty"]);
	  	 //检查当天的员工数
	 $GroupNums=mysql_fetch_array(mysql_query("SELECT count(*) as tempCount FROM $DataIn.sc1_memberset  S
	  			 LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
	  			 Left join $DataIn.staffgroup g on g.GroupId=S.GroupId
	  			 WHERE S.Date='$Date'  AND M.cSign='7' and M.KqSign>0 and g.typeid='7100'
	  			 AND NOT EXISTS(SELECT Number FROM $DataPublic.kqqjsheet K WHERE K.Number=M.Number  
				 AND  DATE_FORMAT(K.StartDate,'%Y-%m-%d')<='$Date'
				 AND DATE_FORMAT(K.EndDate,'%Y-%m-%d')>='$Date') 
				 and S.number in 
				 	(SELECT C.Number FROM $DataIn.checkinout C where DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$Date') ",$link_id));
	 $GroupNums = $GroupNums["tempCount"];
	 /*
	 $GroupResult=mysql_query("SELECT D.GroupId  
            FROM $DataIn.sc1_cjtj D
            WHERE  D.TypeId>0  AND  DATE_FORMAT(D.Date,'%Y-%m-%d')='$Date'  $SearchRows  GROUP BY  D.GroupId ",$link_id);
     while($GroupRow = mysql_fetch_array($GroupResult)) {
          $GroupId=$GroupRow["GroupId"];
          $checkNums= mysql_query("SELECT * FROM $DataIn.sc1_memberset  S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE S.Date='$Date' AND S.GroupId='$GroupId' AND M.cSign='7' and M.KqSign>0
AND NOT EXISTS(SELECT Number FROM $DataPublic.kqqjsheet K WHERE K.Number=M.Number  AND  DATE_FORMAT(K.StartDate,'%Y-%m-%d')<='$Date' AND DATE_FORMAT(K.EndDate,'%Y-%m-%d')>='$Date') and S.number in (SELECT C.Number FROM $DataIn.checkinout C where DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$Date') ",$link_id);
      $GroupNums+=@mysql_num_rows($checkNums);
     }        
       */   
	  	
		//
	
		/*

 
	  
	  //人均产值
		
		$totalProPrice = 0;
		$sumProPriceSql = "select D.POrderId, D.Qty from $DataIn.sc1_cjtj D  WHERE 1  AND DATE_FORMAT(D.Date,'%Y-%m-%d')='$Date'  $SearchRows "; 
		$sumProPriceRs=mysql_query($sumProPriceSql, $link_id);
		 while ($sumProPriceRow = mysql_fetch_array($sumProPriceRs)) {
			 $totalProPrice += outputValue($sumProPriceRow["POrderId"], $sumProPriceRow["Qty"], $DataIn, $link_id);
		 }
	  $totalProPrice = round($totalProPrice/($GroupNums > 0 ? $GroupNums : 1),1);
	    */
	  $DateTitle=date("m-d",strtotime($Date));		
	  $wName=date("D",strtotime($Date)); 
	  $headArray=array(
	  "ShrinkFrame"=>"13,19,8.5,8.5",
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Date"),
				                      "Title"=>array("Text"=>"$DateTitle","FontSize"=>"14","rIcon"=>"$wName"),
				                      "Col1"=>array("Text"=>"$GroupNums" . "人","Frame"=>"50, 2, 80, 30"), //110->60
									  
//加入人均产量 －－cz 
										"Col2"=>array("Text"=>"loading..","Frame"=>"120, 2, 80, 30","FontSize"=>14,"Color"=>"#000000"),
											
									  
				                      "Col3"=>array("Text"=>"$Qty","Frame"=>"180, 2, 103, 30","FontSize"=>"14"),
				                      //"Rank"=>array("Icon"=>"1"),
				                      // "AddRows"=>$AddRows
									  "dateVal"=>$Date
				                   ); 
								   /*
		if ($hiddenSign==0){
		      $FromPage="Read";
		      $checkDate=$Date;
			  include "order_scdj_list.php";
		}
		else{
			$dataArray=array();
		}	                   	 
		*/                  
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign" /*,"Layout"=>$Layout,"IconSet"=>$IconSet,"data"=>$dataArray*/,"onEdit"=>1,"Remark"=>array("Text"=>"$remark","Operator"=>"$remarkOper","Date"=>"$logtime")); 
	   $hiddenSign=1;
}
$jsonArray=array("data"=>$jsondata); 
}
?>