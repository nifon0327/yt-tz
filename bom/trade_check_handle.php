<?php
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

@$proId = addslashes($_POST['proId']);
@$Estate = addslashes($_POST['Estate']);
@$txtReasons = addslashes($_POST['txtReasons']);
@$chooseState = addslashes($_POST['chooseState']);

$mySql="select Estate from $DataIn.bom_object WHERE TradeId='$proId' ";
$result = mysql_query($mySql);
if($result && $myRow = mysql_fetch_array($result)){
    $Estate = $myRow["Estate"];

    //审核
    if ($Estate != 1) {
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '项目审核状态不对'
        ));
        return;
    }
}
else{
    echo json_encode(array(
            'rlt'=> false,
            'msg'=> '数据错误,请重新检索'
    ));
    return;
}

//审核
$mySql="update $DataIn.bom_object set Estate='$chooseState',
Checker='$Operator',
Checked='$DateTime',
CReasons='$txtReasons'
where TradeId='$proId' ";

$result = mysql_query($mySql);
if($result && mysql_affected_rows()>0){
    
    //审核通过
    if ($chooseState == 2) {
        //修改产品审核状体
        $mySql="UPDATE $DataIn.productdata a, $DataIn.trade_object b 
            set a.Estate = 2
        where a.CompanyId = b.CompanyId and b.Id = '$proId' ";
        $result = mysql_query($mySql);
    }
     
    echo json_encode(array(
            'rlt'=> true
    ));
}
else{
    echo json_encode(array(
            'rlt'=> false,
            'msg'=> '审核操作出错'
    ));
}
