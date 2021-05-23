<?php
///////////////////////////////////////////////////////////////////
//预埋件

$result = mysql_query("select P.TypeId, P.TypeName From $DataIn.producttype P LEFT JOIN $DataIn.trade_embedded_data T ON T.TypeName = P.TypeName WHERE T.TradeId = $proId");

if($result && $myRow = mysql_fetch_array($result)){
    do {
        $myTypeName = $myRow["TypeName"];
        $myTypeId = $myRow["TypeId"];

        $embeddedSql = "select Specs From $DataIn.trade_embedded_data where TradeId = $proId and  TypeName ='$myTypeName'";
        $embeddedResult = mysql_query($embeddedSql);
        if ($embeddedResult && $embeddedRow = mysql_fetch_array($embeddedResult)) {
            do {
                //预埋件规格
                $specs = json_decode($embeddedRow["Specs"]);
                for ($i = 0; $i < count($specs); $i++) {
                    $spec = $specs[$i]; //规格
                    $TypeId = $stufftype["预埋"];
                    $stuffResult = mysql_query("select StuffId,Spec FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$spec' limit 1");
                    if ($stuffRow = mysql_fetch_array($stuffResult)) {
                        //已经存在配件

                    } else {
                        echo json_encode(array(
                            'rlt' => false,
                            'msg' => "物料名称：" . $spec . " 预埋件不存在，请完善后再进行BOM初始化操作"
                        ));
                        mysql_query("ROLLBACK");
                        mysql_query("END");
                        exit;
                    }
                }

            } while ($embeddedResult && $embeddedRow = mysql_fetch_array($embeddedResult));
        }

    }while ($result && $myRow = mysql_fetch_array($result));
}