<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
switch($ActionId){
case "2":

           $TempM=$chooseMonth;
         $chooseDate=$chooseMonth."-28";
          $kkCheckResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Amount),0) AS kkAmount
 	      FROM $DataIn.cw2_hksheet S WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$TempM' AND  S.CompanyId=$CompanyId",$link_id));
           if($kkCheckResult["kkAmount"]==0){
                 $In_Sql="INSERT INTO $DataIn.cw2_hksheet(Id, Mid, Did, CompanyId, Amount, Rate,Attached, Remark, Date, Estate, Locks, Operator)
VALUES(NULL,'0','0','$CompanyId','$SumAmount','$thisRate','0','当月货款按比率返款金额','$chooseDate','1','0','$thisBuyerId')";
            }
         else{
                      $In_Sql="UPDATE  $DataIn.cw2_hksheet  SET  Amount=$SumAmount,Operator='$thisBuyerId',Date='$chooseDate' WHERE  DATE_FORMAT(Date,'%Y-%m')='$TempM' AND CompanyId=$CompanyId";
                      }
           $In_Result=@mysql_query($In_Sql);
            if($In_Result&& mysql_affected_rows()>0){
                echo "Y";
                 }
     break;

 case "8"://计算供应商请款货款
       $CheckhkRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Amount),0) AS Amount
		FROM $DataIn.cw1_fkoutsheet S 
		WHERE  1  AND S.Month='$MonthTemp'    AND  S.CompanyId=$CompanyId",$link_id));
          $thisAmount=sprintf("%.2f",$CheckhkRow["Amount"]);
          echo $thisAmount;
     break;
}

?>