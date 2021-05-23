<?php 
//购房补助费用
$Housebz=0;
$houseResult = mysql_query("SELECT  IFNULL(SUM(A.Amount),0) AS Amount FROM $DataIn.cw21_housefeesheet A
LEFT JOIN $DataIn.staffmain M ON M.Number=A.Number
WHERE  A.Number='$Number'  AND A.Month='$chooseMonth'",$link_id);
if($houseRow = mysql_fetch_array($houseResult)){
    $Housebz=$houseRow["Amount"];
}
?>