<?php
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

@$proId = addslashes($_POST['id']);

$DelSql = "DELETE FROM $DataIn.bom_info WHERE tradeId = $proId";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
    echo json_encode(array(
            'rlt'=> true
    ));
}
else{
    echo json_encode(array(
            'rlt'=> false
    ));
}


