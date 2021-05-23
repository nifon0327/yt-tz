<?php 
//BOM未收
$curDate=date("Y-m-d");
if ($BuyerId!=""){
	 $SearchRows.=" AND  M.BuyerId='$BuyerId' "; 
}
/*
switch($ColSign){
	case "Over5":
      $SearchRows.=" AND  DATEDIFF('$curDate',S.DeliveryDate)>5"; 
	   break;
	case "Over":
      $SearchRows.=" AND  DATEDIFF('$curDate',S.DeliveryDate)>0 AND  DATEDIFF('$curDate',S.DeliveryDate)<=5 "; 
	   break;
}
*/
$mySql="SELECT M.CompanyId,P.Forshort,(S.AddQty+S.FactualQty) AS Qty,((S.AddQty+S.FactualQty)*S.Price*E.Rate) AS Amount,S.StuffId,S.POrderId,
                S.DeliveryDate,PI.Leadtime   
			        FROM $DataIn.cg1_stocksheet S
			        LEFT JOIN  $DataIn.cg1_stockmain M ON S.Mid=M.Id 
			        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
				    LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=M.CompanyId
				    LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
				    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
	            	LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=Y.Id 
			        WHERE 1  AND S.rkSign>0 AND S.Mid>0  AND D.Estate=1   AND M.CompanyId<>'2166'  $SearchRows 
                         AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=S.StockId) 
                   ORDER BY  M.CompanyId ";

  $curSeconds=strtotime("$curDate");           
 $Result = mysql_query($mySql,$link_id);
 $totalQty=0;$totalAmount=0;
 $dataArray=array();$AmountArray=array();
 if($myRow = mysql_fetch_array($Result)) {
          $oldCompanyId=$myRow["CompanyId"];
          $Forshort=$myRow["Forshort"];
          $sumQty=0; $sumAmount=0;$lastCount=0;$Counts=0;
     do {
           $CompanyId=$myRow["CompanyId"];
           if ($CompanyId!=$oldCompanyId){
	            if ($Counts>0){
		               $totalQty+=$sumQty;
		               $totalAmount+=$sumAmount;
		                $AmountArray[$oldCompanyId]=$sumAmount;
		               $dataArray[$oldCompanyId]=array($oldCompanyId,$Forshort,$sumQty,$sumAmount,$lastCount);
	            }
	            $oldCompanyId=$myRow["CompanyId"];
			     $Forshort=$myRow["Forshort"];
	            $sumQty=0; $sumAmount=0;$lastCount=0;$Counts=0;
           }
           
        $continueSign=0;
        $Leadtime=$myRow["Leadtime"]; 
        $Leadtime=str_replace("*", "", $Leadtime);
        $DeliveryDate=$myRow["DeliveryDate"];
		switch($ColSign){
			case "Over5":
	          if ($Leadtime=="" || strtotime($Leadtime)-$curSeconds>=0 || $Leadtime=="0000-00-00"){
		           $continueSign=1;
	          }
			   break;
			case "Over":
			          if ($DeliveryDate=="0000-00-00"|| strtotime($DeliveryDate)-$curSeconds>=0){
				           $continueSign=1;
			          }
			   break;
		}
          if ($continueSign==1) continue;
          
           $Qty=$myRow["Qty"]; 
           $Amount=$myRow["Amount"]; 
           $sumQty+=$Qty;
           $sumAmount+=$Amount;
           
           $StuffId=$myRow["StuffId"];
           $POrderId=$myRow["POrderId"];
           //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
		   include "../../model/subprogram/stuff_blcheck.php";
            if ($LastBlSign==1)  $lastCount++;
		    $Counts++;        
        } while($myRow = mysql_fetch_array($Result));  
            if ($Counts>0){
                   $totalQty+=$sumQty;
		           $totalAmount+=$sumAmount;
		           $AmountArray[$oldCompanyId]=$sumAmount;
                  $dataArray[$oldCompanyId]=array($oldCompanyId,$Forshort,$sumQty,$sumAmount,$lastCount);
            }
            
         $totalQty=number_format($totalQty);
         $totalAmount=number_format($totalAmount);
	      $jsonArray[]= array(
					             "View"=>"Total",
					              //"Icon"=>array(array("Name"=>"statics","Frame"=>"60,5,25,25")),
					             "Col_A"=>array("Title"=>"合计","Align"=>"L","Margin"=>"-5,0,0,0"),
					             "Col_B"=>array("Title"=>"$totalQty","Margin"=>"10,0,0,0"),
					             "Col_C"=>array("Title"=>"¥$totalAmount")
					          ); 
					          
           arsort($AmountArray);
           foreach($AmountArray as $key=>$value){
                 $tempArray=$dataArray[$key];
                 $sumQty=number_format($tempArray[2]);
                 $sumAmount=number_format($tempArray[3]); 
                 $lastCount=$tempArray[4]==0?"":$tempArray[4];
                 //$margin=$tempArray[4]==0?"10,0,0,0":"10,4,0,0";,"Margin"=>"$margin"
                $jsonArray[]= array(
					             "View"=>"List",
					             "Id"=>"165",
					             "onTap"=>array("Title"=>"未收-$Forshort","Value"=>"1","Tag"=>"ExtList","Args"=>"$tempArray[0]"),
					             "Col_A"=>array("Title"=>"$tempArray[1]","Align"=>"L","Margin"=>"-5,0,0,0"),
					             "Col_B"=>array("Title"=>"$sumQty","Align"=>"R","Margin"=>"10,0,0,0",
					                                        "AboveTitle"=>"$lastCount","AboveColor"=>"#00A945"),
					             "Col_C"=>array("Title"=>"¥$sumAmount")
					          ); 
		      }
 }
?>