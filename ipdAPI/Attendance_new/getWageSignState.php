<?php

function checkWageSign($number, $DataIn, $DataPublic, $link_id){
    $nonSign = 'no';
    $wageSql = "SELECT S.Month,S.Amount
                      FROM  $DataIn.cwxzsheet S
                      WHERE S.Number='$number'
                      AND S.Estate in (0)
                      order by S.Month DESC Limit 1";
    //echo $wageSql;
    $wageResult = mysql_query($wageSql, $link_id);
    if(mysql_num_rows($wageResult) > 0){
        $wageRow = mysql_fetch_assoc($wageResult);
        $wageMonth = $wageRow["Month"];
        $checkSign=mysql_query("SELECT Id,sign FROM $DataPublic.wage_list_sign WHERE Number='$number' AND SignMonth='$wageMonth' LIMIT 1",$link_id);

        $checkSignResult = mysql_fetch_assoc($checkSign);
        $sign = $checkSignResult["sign"];

        if(mysql_num_rows($checkSign) == 0 || $sign=="" || strlen($sign) <= 30){
            $nonSign = $wageMonth;
        }
    }
    return "$nonSign";
}


?>
