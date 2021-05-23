<?php 
//采购交期变动信息

//当前采购单交期
$dateResult= mysql_fetch_array(mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Week FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId' LIMIT 1",$link_id));
$newWeeks=$dateResult["Week"];


 $mySql="SELECT A.Date,M.Name,YEARWEEK(A.DeliveryDate,1) AS Week FROM $DataIn.cg1_deliverydate A  
            LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
            WHERE A.StockId='$StockId' ORDER BY A.Id DESC";
$myResult = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_assoc($myResult)){
    do{
         $Weeks=$myRow["Week"];
         if ($Weeks!=$newWeeks){
                 $WeekSTR=substr($Weeks,4,2);
                 $newWeekSTR=substr($newWeeks,4,2);
                 $strTtile=$Weeks>$newWeeks?"配件交期提前，原交期week" . $WeekSTR . "改为week" . $newWeekSTR:"配件交期推后，原交期week" . $WeekSTR . "改为week" . $newWeekSTR;
		         $jsonArray[]=array(
		                      "Title"=>$strTtile,
		                      "Col1"=>$myRow["Date"],
		                      "Col3"=>$myRow["Name"]
		                   );
		          $newWeeks=$Weeks;
           }
        }while($myRow = mysql_fetch_assoc($myResult));
}
?>