<?php
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";

@$id = addslashes($_POST['id']);
@$state = addslashes($_POST['state']);

$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

//提交审核
$mySql="update $DataIn.bom_object set Estate = '$state',Submited='$DateTime' WHERE TradeId='$id' ";
$result = mysql_query($mySql);
if($result && mysql_affected_rows()>0){
    echo json_encode(array(
            'rlt'=> true
    ));
}
else{
    echo json_encode(array(
            'rlt'=> false
    ));
}


