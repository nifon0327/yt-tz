<?php 
//就学补助费用
$Studybz=0;

$studyResult = mysql_query("SELECT  IFNULL(SUM(A.Amount),0) AS Amount FROM $DataIn.childinfo A
LEFT JOIN $DataIn.staffmain M ON M.Number=A.Number
WHERE  A.Number='$Number' AND A.Estate=1",$link_id);
if($studyRow = mysql_fetch_array($studyResult)){
    $Studybz=$studyRow["Amount"];
}
?>