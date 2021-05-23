<?php
//骨架半成品  同一个项目构件编号和楼层唯一确定具体骨架半成品型号
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";
$proId = 39;
$Date = date('Y-m-d',time());
$Operator = '10058';
$mySql = " SELECT DISTINCT CmptNo,FloorNo,BuildingNo,CStr 
FROM $DataIn.trade_drawing 
where TradeId = $proId
GROUP BY CmptNo,FloorNo,BuildingNo ";

$result = mysql_query($mySql);
if ($result && $myRow = mysql_fetch_array($result)) {
    do {
        $SendFloor = 18;
        //根据规格查找配件资料
        $CmptNo = $myRow["CmptNo"];
        $FloorNo = $myRow["FloorNo"];
        $BuildingNo = $myRow["BuildingNo"];
        $CStr = $myRow["CStr"];
        $TradeNo = 'C2018_005';
        $StuffCname = 'B-' . $TradeNo . '-' . $BuildingNo . '-' . $FloorNo . '-' . $CmptNo;
        $TypeId = '9017';
        $stuffResult = mysql_query("select StuffId FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$StuffCname' limit 1");
        if ($stuffRow = mysql_fetch_array($stuffResult)) {
            //已经存在配件
            $mStuffId = $stuffRow["StuffId"];
            $BuyerId = 0;
            $CompanyId = 100002;
            // 钢筋半成品
            $steelSql = "select Distinct Specs, Sizes,Titles From $DataIn.trade_steel_data where TradeId = $proId and BuildingNo = $BuildingNo";
            $steelResult = mysql_query($steelSql);
            if ($steelResult && $steelRow = mysql_fetch_array($steelResult)) {
                do {
                    //钢筋规格(
                    $specs = json_decode($steelRow["Specs"]);
                    //钢筋下料尺寸
                    $sizes = json_decode($steelRow["Sizes"]);
                    $titles = json_decode($steelRow["Titles"]);
                    //钢筋数量统计
                    $qtys = array();
                    $steelQtyResult = mysql_query("SELECT a.id, a.Quantities, a.BuildingNo
                    from $DataIn.trade_steel a
                    where a.TradeId = $proId and a.CmptNo = '$CmptNo' and a.FloorNo = '$FloorNo' and a.BuildingNo = '$BuildingNo' limit 1 ");
                    if ($steelQtyResult && $steelQtyRow = mysql_fetch_array($steelQtyResult)) {
                        do {
                            $arr = json_decode($steelQtyRow["Quantities"]);
                            $qtys = $arr;
                        } while ($steelQtyRow = mysql_fetch_array($steelQtyResult));
                    }
                    for ($i = 0; $i < count($specs) && $i < count($sizes); $i++) {
                        $spec = $specs[$i];
                        $size = $sizes[$i];
                        $title = $titles[$i];
                        $StuffEname = $spec . "-" . $size . "-" . $title . "-" . $proId;
                        $TypeId = '9002';
                        $stuffResult = mysql_query("select StuffId FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$StuffEname' limit 1");
                        if ($stuffRow = mysql_fetch_array($stuffResult)) {
                            //钢筋半成品
                            $StuffId = $stuffRow["StuffId"];
                            //数量统计
                            if ($qtys[$i] > 0) {
                                $bomResult = mysql_query("select Id FROM $DataIn.semifinished_bom where mStuffId = $mStuffId and StuffId = $StuffId");
                                if ($bomRow = mysql_fetch_array($bomResult)) {
                                    //已经存在
                                }
                                else {
                                    $Relation = round($qtys[$i], 3);
                                    $InSql = "INSERT INTO $DataIn.semifinished_bom(mStuffId,
                            StuffId,
                            Relation,
                            Date,
                            Operator
                            )VALUES($mStuffId,
                            $StuffId,
                            '$Relation',
                            '$Date',
                            '$Operator')";
                                    //echo $InSql,"  ";
                                    $InRecode = @mysql_query($InSql);
                                }
                            }
                        }
                    }
                } while ($steelResult && $steelRow = mysql_fetch_array($steelResult));
            }
            mysql_free_result($steelResult); //释放内存
        }
    } while ($myRow = mysql_fetch_array($result));
}



