<?php 
include "D:/website/mc/basic/parameter.inc";
$TempM=date("Y-m");
$CheckhkResult=mysql_query("SELECT SUM(S.Amount) AS Amount ,S.CompanyId,G.BuyerId
		FROM $DataIn.cw1_fkoutsheet S 
        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
		WHERE S.Month='$TempM' AND  S.CompanyId IN (2416,2620) GROUP BY S.CompanyId",$link_id);
if($CheckhkRow=mysql_fetch_array($CheckhkResult)){
     do{
          $thisAmount=sprintf("%.2f",$CheckhkRow["Amount"]);
          $thisCompanyId=sprintf("%.2f",$CheckhkRow["CompanyId"]);
          $thisBuyerId=$CheckhkRow["BuyerId"];
          switch($thisCompanyId){
             case "2416":
                $thisRate=0.02;
                 break;
             case "2620":
                 $thisRate=0.05;
                 break;
            }
                $chooseDate=date("Y-m-d");
                $kkAmount=sprintf("%.2f",$thisAmount*$thisRate);
                 $kkCheckResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Amount),0) AS kkAmount
 	              FROM $DataIn.cw2_hksheet S WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$TempM' AND  S.CompanyId=$thisCompanyId",$link_id));
              if($kkCheckResult["kkAmount"]==0){
                       $In_Sql="INSERT INTO $DataIn.cw2_hksheet(Id, Mid, Did, CompanyId, Amount, Rate,Attached, Remark, Date, Estate, Locks, Operator)
VALUES(NULL,'0','0','$thisCompanyId','$kkAmount','$thisRate','0','当月货款按比率返款金额','$chooseDate','1','0','$thisBuyerId')";
                      $In_Result=@mysql_query($In_Sql);
               }
             if($kkAmount!=$kkCheckResult["kkAmount"] && $kkCheckResult["kkAmount"]>0){
                      $Update_Sql="UPDATE  $DataIn.cw2_hksheet  SET  Amount=$kkAmount,Operator='$thisBuyerId',Date='$chooseDate' WHERE DATE_FORMAT(Date,'%Y-%m')=$TempM AND CompanyId=$thisCompanyId";
                      $Update_Result=@mysql_query($Update_Sql);
                    }
         }while($CheckhkRow=mysql_fetch_array($CheckhkResult));
}
?>