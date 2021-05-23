<?php
//电信-zxq 2012-08-01
session_start();
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$FromqcCause=1;
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

$IdArr = explode(",", $Ids);
if ($ActionId==19){
    foreach ($IdArr as $Id) {

        $result=$myPDO->query("SELECT S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.TypeId,D.CheckSign,T.AQL,
                (G.AddQty+G.FactualQty) AS cgQty
                FROM $DataIn.gys_shsheet S
                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
                WHERE S.Id='$Id' LIMIT 1");

        if($upData = $result->fetch()){
            $StuffId=$upData["StuffId"];
            $StockId=$upData["StockId"];
            $Qty=$upData["Qty"];
            $cgQty=$upData["cgQty"];
            $StuffCname=$upData["StuffCname"];
            $TypeId=$upData["TypeId"];
            if ($TypeId != 9002 && $TypeId !=9006) {
                continue;
            }
            $CheckSign=$upData["CheckSign"];
            $AQL=$upData["AQL"];
            if ($CheckSign==0){
                $CheckSignStr="抽检";
                $CheckQtyStr="抽样数量";
                $AQL="";
                $ReQty = 1;
            }
            else{
                $AQL="";
                $CheckSignStr="全检";
                $CheckQtyStr="全检数量";
            }
            $CheckQty=$Qty;
            $SendSign=$upData["SendSign"];

            $sumQty=0;

            $Estate=$sumQty==0?0:1;
            if ($CheckSign==0) { //抽检
                $Estate=0;  // 全部入库，不良品不做退换操作
            }

            $CheckAQL = $AQL;
        }


        $inSql="INSERT INTO $DataIn.qc_badrecord (Id,shMid,Sid,StockId,StuffId,shQty,checkQty,Qty,AQL,Remark,Estate,Locks,Date,Operator,creator,created)
        SELECT NULL,Mid,'$Id',StockId,StuffId,Qty,
        '$CheckQty','$sumQty','$CheckAQL','','$Estate','0','$Date','$Operator','$Operator',NOW()
        FROM  $DataIn.gys_shsheet WHERE Id = '$Id' LIMIT 1";
//        echo $inSql;
        $inAction = $myPDO->exec($inSql);

        $Mid = $myPDO->lastInsertId();

        if ($Mid>0){
            $qcResult="来料品检不良记录主表保存成功！";
        }else{
            $qcError=1;
            $qcResult="来料品检不良记录保存失败！";
        }


        if ($CheckSign==1){       //全检审核，记录入库

            $ActionId=17;
        }
        else{    //抽检，要分允收？拒收？
            if ($sumQty==0 || $sumQty<$ReQty){
                $ActionId=17;  //允收
            }
            else{
                $ActionId=15; //抽检拒收，全部退回不入库。
            }
        }

        switch($ActionId){
            case 15://退回
                $updateSQL = "UPDATE $DataIn.gys_shsheet Q SET Estate=1,Locks=1 WHERE Q.Id='$Id'";
                $updateResult = $myPDO->exec($updateSQL);
                if ($updateResult){
                    if ($FromqcCause==1){
                        echo "<SCRIPT LANGUAGE=JavaScript>alert('记录已退回 $qcResult');</script>";
                    }
                    else{
                        echo"记录已退回";
                    }

                }
                break;


            case 17://审核通过，记录入库
                if ($FromqcCause==1){
                    include "item2_3_shrk.php";
                    echo "<SCRIPT LANGUAGE=JavaScript>
                    var host = window.parent.selObj;
                    host.parentNode.removeChild(host);
                    alert('$OperResult $qcResult');
                    </script>
                    <script language=JavaScript>
                    parent.location.reload();
                    </script>";
                    //by.lwh 页面刷新


                }
                else{
                    echo $OperResult;
                }
                break;
        }


    }
}



include "../basic/quit_pdo.php";


function unescape($str){
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++){
        if ($str[$i] == '%' && $str[$i+1] == 'u'){
            $val = hexdec(substr($str, $i+2, 4));
            if ($val < 0x7f) $ret .= chr($val);
            else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
            else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
            $i += 5;
        }
        else if ($str[$i] == '%'){
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }
        else $ret .= $str[$i];
    }
    return $ret;
}
?>