<?php
//成品
function getDrawingFile($path, $filename)
{

    $rt = "";
    if (!$filename) return $rt;

    if (!is_dir($path)) return $rt;

    $tmp = scandir($path);

    foreach ($tmp as $f) {
        // 过滤. ..
        if ($f == '.' || $f == '..')
            continue;

        $dir = $path . $f;

        if (is_file($dir)) { // 如果是文件

            $houzhui = substr(strrchr($f, '.'), 1);
            $result = basename($f, "." . $houzhui);

            if (strcasecmp($result, $filename) == 0) {
                $rt = $f;
                break;
            }
        }
    }

    return $rt;
}

// 客户CompanyId 取得
$mySql = "SELECT CompanyId from trade_object where Id = $proId ";
$result = mysql_query($mySql);
if ($result && $myRow = mysql_fetch_array($result)) {
    $CompanyId = $myRow["CompanyId"];
}

$mySql = "select  TradeId, CmptTypeId, BuildingNo, FloorNo, CmptNo,Area,DwgVol,CVol,Weight, EndDwg, Weight, Id, SN, CStr 
from $DataIn.trade_drawing where TradeId = $proId";

$result = mysql_query($mySql);
if ($result && $myRow = mysql_fetch_array($result)) {
    do {
        $TradeId = $myRow["TradeId"];
        $CmptTypeId = $myRow["CmptTypeId"];
        $BuildingNo = $myRow["BuildingNo"];
        $FloorNo = $myRow["FloorNo"];
        $CmptNo = $myRow["CmptNo"];
        $Weight = $myRow["Weight"];
        $Area = $myRow["Area"];
        $DwgVol = $myRow["DwgVol"];
        $CVol = $myRow["CVol"];
        $Weight = $myRow["Weight"];
        $drawingId = $myRow["Id"];
        $SN = $myRow["SN"];
        $CStr = $myRow["CStr"];

        $cName = $BuildingNo . "-" . $FloorNo . "-" . $CmptNo . "-" . $SN .".". $TradeId;

        $productResult = mysql_query("select ProductId FROM $DataIn.productdata where TypeId='$CmptTypeId' and eCode = '$cName' AND drawingId = '$drawingId' limit 1");
        if ($productRow = mysql_fetch_array($productResult)) {
            //已经存在配件
            //  $mProductId = $productRow["ProductId"];
        }
        else {
            //添加成品
            //产品ID
            $maxSql = mysql_query("SELECT MAX(ProductId) AS MProductId FROM $DataIn.productdata", $link_id);
            $mProductId = mysql_result($maxSql, 0, "MProductId");
            $mProductId = $mProductId + 1;
            $InSql = "INSERT INTO $DataIn.productdata(ProductId,
            cName,eCode,
            TypeId,
            Price, Unit, Moq,Weight,
            CompanyId,
            Description,Remark,pRemark,bjRemark,TestStandard,
            `Date`,PackingUnit,dzSign,productsize,
            ReturnReasons,Estate,
            Operator,
            creator,
            created,
            drawingId,
            area,
            dwgVol,
            cVol,
            wweight,
            CmptNo,
            CStr
            )VALUES($mProductId,
            '$cName','$cName',
            $CmptTypeId,
            '0.00', 1, 0, '$Weight',
            $CompanyId,
            '','','','',0,
            '$Date',1,0,'',
            '',1,
            '$Operator',
            '$Operator',
            '$DateTime',
            '$drawingId',
            '$Area',
            '$DwgVol',
            '$CVol',
            '$Weight',
            '$CmptNo',
            '$CStr')";

            //echo $InSql,"  ";
            $InRecode = @mysql_query($InSql);

            //查找对应的 构件半成品
            $StuffCname = 'B-' . $TradeNo . '-' . $BuildingNo . '-' . $FloorNo . '-' . $CmptNo;
            $TypeId = $stufftype["构件半成品"];
            $stuffResult = mysql_query("select StuffId FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$StuffCname' limit 1");
            if ($stuffRow = mysql_fetch_array($stuffResult)) {
                //已经存在配件
                $StuffId = $stuffRow["StuffId"];
                $bomResult = mysql_query("select Id FROM $DataIn.pands where ProductId = $mProductId and StuffId = $StuffId");
                if ($bomRow = mysql_fetch_array($bomResult)) {
                    //已经存在
                }
                else {
                    $InSql = "INSERT INTO $DataIn.pands(ProductId,
                    StuffId,
                    Relation,
                    Date,
                    Operator
                    )VALUES($mProductId,
                    $StuffId,
                    '1',
                    '$Date',
                    '$Operator')";

                    //echo $InSql,"  ";
                    $InRecode = @mysql_query($InSql);
                }
            }

            // 脱模出厂
            $StuffId = $stuffArr["脱模入库"];
            if ($StuffId) {
                $bomResult = mysql_query("select Id FROM $DataIn.pands where ProductId = $mProductId and StuffId = $StuffId");
                if ($bomRow = mysql_fetch_array($bomResult)) {
                    //已经存在
                }
                else {
                    $InSql = "INSERT INTO $DataIn.pands(ProductId,
                    StuffId,
                    Relation,
                    Date,
                    Operator
                    )VALUES($mProductId,
                    $StuffId,
                    '1',
                    '$Date',
                    '$Operator')";

                    //echo $InSql,"  ";
                    $InRecode = @mysql_query($InSql);
                }
            }

            //杂费统计90013
            $StuffId = $stuffArr["杂费统计"];
            if ($StuffId) {
                $bomResult = mysql_query("select Id FROM $DataIn.pands where ProductId = $mProductId and StuffId = $StuffId");
                if ($bomRow = mysql_fetch_array($bomResult)) {
                    //已经存在
                }
                else {
                    $InSql = "INSERT INTO $DataIn.pands(ProductId,
                    StuffId,
                    Relation,
                    Date,
                    Operator
                    )VALUES($mProductId,
                    $StuffId,
                    '1',
                    '$Date',
                    '$Operator')";

                    //echo $InSql,"  ";
                    $InRecode = @mysql_query($InSql);
                }
            }

            //增加图纸信息链接
            $EndDwg = $myRow["EndDwg"];
            $Path = "../design/dwgFiles/" . $proId . "/Pord/";
            chmod($path, 0777);
            $file = getDrawingFile($Path, $EndDwg);
            if ($file) {
                $Field = explode(".", $file);
                $FType = $Field[1];
                $PreFileName = $EndDwg . "." . $FType;
                $FileRemark = str_replace("'", "''", $cName);
                $sql = "INSERT INTO $DataIn.doc_standarddrawing(Id,FileType,FileRemark,
                FileName,CompanyId,ProductType,Estate,Locks,Date,Operator) VALUES
                (NULL,'1','$FileRemark','$PreFileName','$CompanyId',
                '$mProductId','1','0','$Date','$Operator')";
                //$returnstr.= " $sql ";
                @mysql_query($sql, $link_id);
                $delResult = mysql_query("DELETE FROM $DataIn.productstandimg WHERE ProductId='$mProductId'", $link_id);
                $upGoods = "INSERT INTO $DataIn.productstandimg (Id, ProductId, Picture, Estate,`Date`, Operator)
                VALUES (NULL,'$mProductId','$PreFileName','2','$Date','$Operator')";
                $upAction = mysql_query($upGoods, $link_id);

                if ($upAction) {
                    @mysql_query("update $DataIn.productdata set TestStandard=1 WHERE ProductId='$mProductId'", $link_id);
                }
            }

        }

    } while ($myRow = mysql_fetch_array($result));

    //BOM信息  ---- 脱模出厂 杂费统计
    $cmptTypeResult = mysql_query("SELECT a.BuildingNo,a.CmptTypeId, a.CmptType, count(a.Id) as Count
        from trade_drawing a
        where a.TradeId = $proId
        GROUP BY a.BuildingNo,a.CmptTypeId, a.CmptType");
    if ($cmptTypeRow = mysql_fetch_array($cmptTypeResult)) {
        do {

            $BuildingNo = $cmptTypeRow["BuildingNo"];
            $CmptTypeId = $cmptTypeRow["CmptTypeId"];
            $CmptType = $cmptTypeRow["CmptType"];
            $total = $cmptTypeRow["Count"];

            //脱模出厂 (成品 )
            $StuffId = $stuffArr["脱模入库"];
            $StuffTypeId = $stufftype["脱模入库"];
            $unit = $stuffUnitArr["脱模入库"];
            $MainTypeId = $stuffmaintype["生产类配件"];
            //$total = count($spec->sizeArr);

            $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId");
            if ($bomRow = mysql_fetch_array($bomResult)) {
                //已经存在
                $InSql = "update $DataIn.bom_info
                set Quantity=Quantity+$total
                where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId";

                $InRecode = @mysql_query($InSql);

            }
            else {
                $InSql = "INSERT INTO $DataIn.bom_info(TradeId,BuildingNo,
                CmptTypeId, CmptType,
                StuffTypeId, StuffType,
                MaterNo,
                MStuffTypeId, MStuffType,
                IsNew,
                Spec,
                Unit,
                Quantity,
                Price,
                Total,
                Loss,
                Remark,
                creator,
                created
                ) VALUES ($proId,'$BuildingNo',
                $TypeId, '$CmptType',
                $StuffTypeId, '脱模入库',
                '$StuffId',
                $MainTypeId, '生产类配件',
                '否',
                '',
                '$unit',
                '$total',
                '0.00',
                '0',
                '0',
                '',
                '$Operator',
                '$DateTime')";

                $InRecode = @mysql_query($InSql);
            }

            //杂费统计 (成品 )
            $StuffId = $stuffArr["杂费统计"];
            $StuffTypeId = $stufftype["杂费统计"];
            $unit = $stuffUnitArr["杂费统计"];
            $MainTypeId = $stuffmaintype["统计类配件"];
            //$total = count($spec->sizeArr);

            $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId");
            if ($bomRow = mysql_fetch_array($bomResult)) {
                //已经存在
                $InSql = "update $DataIn.bom_info
                set Quantity=Quantity+$total
                where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId";

                $InRecode = @mysql_query($InSql);

            }
            else {
                $InSql = "INSERT INTO $DataIn.bom_info(TradeId,,BuildingNo,
                CmptTypeId, CmptType,
                StuffTypeId, StuffType,
                MaterNo,
                MStuffTypeId, MStuffType,
                IsNew,
                Spec,
                Unit,
                Quantity,
                Price,
                Total,
                Loss,
                Remark,
                creator,
                created
                ) VALUES ($proId,'$BuildingNo',
                $CmptTypeId, '$CmptType',
                $StuffTypeId, '杂费统计',
                '$StuffId',
                $MainTypeId, '统计类配件',
                '否',
                '',
                '$unit',
                '$total',
                '0.00',
                '0',
                '0',
                '',
                '$Operator',
                '$DateTime')";

                $InRecode = @mysql_query($InSql);
            }

        } while ($cmptTypeRow = mysql_fetch_array($cmptTypeResult));
    }
}