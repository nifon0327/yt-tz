<?php
//构件半成品  构件编号
$stype = $stufftype["构件半成品"];
if (!$stype) {
    echo json_encode(array(
                         'rlt' => false,
                         'msg' => "配件类型(构件半成品)不存在",

                     ));

    mysql_query("ROLLBACK");
    mysql_query("END");
    exit;
}
//同一个项目构件编号和楼层唯一确定具体产品型号
$mySql = "SELECT DISTINCT a.CmptNo, a.CStr, a.FloorNo
from $DataIn.trade_drawing a
where a.TradeId = $proId GROUP BY a.CmptNo, a.CStr, a.FloorNo ";

$result = mysql_query($mySql);
if ($result && $myRow = mysql_fetch_array($result)) {
    do {
        $SendFloor = 18; //送到一楼
        $CStr = $myRow["CStr"];
        $FloorNo = $myRow["FloorNo"];
        //根据规格查找配件资料
        $CmptNo = $myRow["CmptNo"];
        $StuffCname = 'A-'.$TradeNo . '-' . $FloorNo .'-' . $CmptNo; //项目编号、楼層、构件号

        //TypeId 9014 构件半成品
        $TypeId = $stufftype["构件半成品"];

        $stuffResult = mysql_query("select StuffId FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$StuffCname' limit 1");
        if ($stuffRow = mysql_fetch_array($stuffResult)) {
            //已经存在配件
            $mStuffId = $stuffRow["StuffId"];
            $IsNew = "否";
        }
        else {
            $IsNew = "是";
            //添加配件
            // 配件ID 最大id查找
            $maxSql = mysql_query("SELECT MAX(StuffId) AS MStuffId FROM $DataIn.stuffdata", $link_id);
            $mStuffId = mysql_result($maxSql, 0, "MStuffId");
            $mStuffId = $mStuffId + 1;
            //PriceDetermined 1 待定
            //Unit 5-个 6-吨
            $BuyerId = 0;
            $CompanyId = 100002;
            include "./stuffdata_insert_before.php";

            $InSql = "INSERT INTO $DataIn.stuffdata(StuffId,
            StuffCname,
            StuffEname,
            TypeId,
            Spec,NoTaxPrice,
            Price,CostPrice,
            PriceDetermined,
            Unit,
            ComboxSign,
            Remark,Gfile,Gremark,Picture,Pjobid,PicNumber,JobId,GicNumber,GcheckNumber,
            SendFloor,CheckSign,bomEstate,
            Date,
            Operator,
            OPdatetime
            )VALUES($mStuffId,
            '$StuffCname',
            '$StuffCname',
            '$TypeId',
            '$StuffCname','$NoTaxPrice',
            0.00,'$CostPrice',
            1,
            5,
            2,
            '','','',0,'$Pjobid','$PicNumber','$GicJobid','$GicNumber','$GcheckNumber',
            '$SendFloor','$CheckSign',0,
            '$Date',
            '$Operator',
            '$DateTime')";

            //echo $InSql,"  ";
            $InRecode = @mysql_query($InSql);

            //属性
            $InSql = "INSERT INTO $DataIn.stuffproperty(StuffId,
            Property
            )VALUES($mStuffId,
            13)";
            $InRecode = @mysql_query($InSql);

            //配件采购供应商关系表
            $inRecode1 = "INSERT INTO $DataIn.bps (StuffId,BuyerId,CompanyId,Locks) VALUES ('$mStuffId','$BuyerId','$CompanyId','0')";
            $inRres1 = @mysql_query($inRecode1);

            //配件库存表
            $inRecode2 = "INSERT INTO $DataIn.ck9_stocksheet (StuffId,dStockQty,tStockQty,oStockQty,mStockQty,Date) VALUES ('$mStuffId','0','0','0','0','$Date')";
            $inRes2 = @mysql_query($inRecode2);


            //查找对应的 骨架半成品 建立构件半成品和骨架半成品之间的关系
            $StuffCname = 'B-' . $TradeNo . '-' . $FloorNo . '-' . $CmptNo; //项目编号、楼层、构件编号

            $TypeId = $stufftype["骨架半成品"];
            $stuffResult = mysql_query("select StuffId FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$StuffCname' limit 1");
            if ($stuffRow = mysql_fetch_array($stuffResult)) {
                //钢筋半成品
                $StuffId = $stuffRow["StuffId"];

                $bomResult = mysql_query("select Id FROM $DataIn.semifinished_bom where mStuffId = $mStuffId and StuffId = $StuffId");
                if ($bomRow = mysql_fetch_array($bomResult)) {
                    //已经存在
                }
                else {
                    $InSql = "INSERT INTO $DataIn.semifinished_bom(mStuffId,
                StuffId,
                Relation,
                Date,
                Operator
                )VALUES($mStuffId,
                $StuffId,
                '1',
                '$Date',
                '$Operator')";

                    //echo $InSql,"  ";
                    $InRecode = @mysql_query($InSql);
                }
            }

            // 混泥土
            //$StuffId = $stuffArr["C35"];
            $TypeId = $stufftype["混凝土成品"];
            $cstrresult = mysql_query("select StuffId, StuffCname From $DataIn.stuffdata where
                TypeId='$TypeId' and StuffEname = '$CStr' limit 1 ");
            if ($cstrresult && $myRowcstr = mysql_fetch_array($cstrresult)) {

                $StuffId = $myRowcstr["StuffId"];

                $bomResult = mysql_query("select Id FROM $DataIn.semifinished_bom where mStuffId = $mStuffId and StuffId = $StuffId");
                if ($bomRow = mysql_fetch_array($bomResult)) {
                    //已经存在
                }
                else {
                    $CVol = "0";
                    // 设计图纸的[混泥土体积(㎡)]
                    $qtyResult = mysql_query("SELECT a.CVol from $DataIn.trade_drawing a
                        where a.TradeId = $proId and a.CmptNo = '$CmptNo' limit 1");
                    if ($qtyResult && $qtyRow = mysql_fetch_array($qtyResult)) {
                        $CVol = $qtyRow["CVol"];
                    }
                    $CVol = round($CVol, 3);
                    $InSql = "INSERT INTO $DataIn.semifinished_bom(mStuffId,
                StuffId,
                Relation,
                Date,
                Operator
                )VALUES($mStuffId,
                $StuffId,
                '$CVol',
                '$Date',
                '$Operator')";

                    //echo $InSql,"  ";
                    $InRecode = @mysql_query($InSql);
                }
            }
            else {
                echo json_encode(array(
                    'rlt' => false,
                    'msg' => "配件(混凝土:$CStr)不存在",

                ));
                mysql_query("ROLLBACK");
                mysql_query("END");
                exit;
            }

            //杂费统计90013
            $StuffId = $stuffArr["杂费统计"];
            $bomResult = mysql_query("select Id FROM $DataIn.semifinished_bom where mStuffId = $mStuffId and StuffId = $StuffId");
            if ($bomRow = mysql_fetch_array($bomResult)) {
                //已经存在
            }
            else {

                $InSql = "INSERT INTO $DataIn.semifinished_bom(mStuffId,
            StuffId,
            Relation,
            Date,
            Operator
            )VALUES($mStuffId,
            $StuffId,
            '1',
            '$Date',
            '$Operator')";

                //echo $InSql,"  ";
                $InRecode = @mysql_query($InSql);
            }

            //浇捣养护
            $StuffId = $stuffArr["浇捣养护"];
            $bomResult = mysql_query("select Id FROM $DataIn.semifinished_bom where mStuffId = $mStuffId and StuffId = $StuffId");
            if ($bomRow = mysql_fetch_array($bomResult)) {
                //已经存在
            }
            else {

                $InSql = "INSERT INTO $DataIn.semifinished_bom(mStuffId,
            StuffId,
            Relation,
            Date,
            Operator
            )VALUES($mStuffId,
            $StuffId,
            '1',
            '$Date',
            '$Operator')";

                //echo $InSql,"  ";
                $InRecode = @mysql_query($InSql);
            }

        }



    } while ($myRow = mysql_fetch_array($result));


    //----------------------------------------------------------------------


    //BOM信息  ---- 统计所有成品构件使用混泥土的总量

    $cmptTypeResult = mysql_query("SELECT a.CmptTypeId, a.CmptType, a.CStr, SUM(ROUND(a.CVol,2)) as CVol, count(a.CVol) as Count, b.StuffId
            from trade_drawing a 
            LEFT JOIN stuffdata b on a.CStr=b.StuffEname
            where a.TradeId = $proId
            GROUP BY a.CmptTypeId, a.CmptType, a.CStr,b.StuffId ");

    while ($cmptTypeRow = mysql_fetch_array($cmptTypeResult)) {

        $CmptTypeId = $cmptTypeRow["CmptTypeId"];
        $CmptTypeName = $cmptTypeRow["CmptType"];
        $CStr = $cmptTypeRow["CStr"];
        $Total = $cmptTypeRow["CVol"];
        $Count = $cmptTypeRow["Count"];
        $StuffId = $cmptTypeRow["StuffId"];

        //把混凝土换成采购泪原材料
        $loss = 0;  //损耗

        //损耗取得
        $lossResult = mysql_query("select ThisStd from $DataIn.bom_loss where TradeId = $proId and CmptTypeId = $TypeId and StuffId=$StuffId limit 1");

        if ($lossResult && $lossRow = mysql_fetch_array($lossResult)) {
            $thisStd = $lossRow["ThisStd"];

            $b = preg_match('/[-+]?[0-9]*\.?[0-9]+/', $thisStd, $arr);
            $std = $arr[0];
            if (strpos($thisStd, "%")) {
                $std = $std / 100;
            }

            if ($std) {
                $loss = $Total * $std;
            }
        }

        $MainTypeId = $stuffmaintype["采购类配件"];
        $StuffTypeId = $stufftype['混凝土成品'];
        $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and CmptTypeId = $CmptTypeId and MaterNo = $StuffId");
        if ($bomRow = mysql_fetch_array($bomResult)) {
            //已经存在
            $InSql = "update $DataIn.bom_info
                    set Quantity=Quantity+$Total,Loss=Loss+$loss
                    where TradeId = $proId and CmptTypeId = $CmptTypeId and MaterNo = $StuffId";

            $InRecode = @mysql_query($InSql);

        }
        else {
            $InSql = "INSERT INTO $DataIn.bom_info(TradeId,
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
        )VALUES($proId,
        $CmptTypeId, '$CmptTypeName',
        $StuffTypeId, '混凝土成品',
        '$StuffId',
        $MainTypeId, '采购类配件',
        '否',
        '',
        '个',
        '$Total',
        '0.00',
        '0',
        '$loss',
        '',
        '$Operator',
        '$DateTime')";

            // echo $InSql;
            $InRecode = @mysql_query($InSql);
        }
    }

    //BOM信息  ---- 杂费统计 浇捣养护
    $cmptTypeResult = mysql_query("SELECT a.CmptTypeId, a.CmptType, SUM(a.CVol) as CVol, count(a.CVol) as Count
            from trade_drawing a
            where a.TradeId = $proId
            GROUP BY a.CmptTypeId, a.CmptType");

    while ($cmptTypeRow = mysql_fetch_array($cmptTypeResult)) {

        $CmptTypeId = $cmptTypeRow["CmptTypeId"];
        $CmptTypeName = $cmptTypeRow["CmptType"];
        $total = $cmptTypeRow["Count"];

        //杂费统计 (构件半成品 )
        $StuffId = $stuffArr["杂费统计"];
        $StuffTypeId = $stufftype["杂费统计"];
        $unit = $stuffUnitArr["杂费统计"];
        $MainTypeId = $stuffmaintype["统计类配件"];
        //$total = count($spec->sizeArr);

        $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and CmptTypeId = $CmptTypeId and MaterNo = $StuffId");
        if ($bomRow = mysql_fetch_array($bomResult)) {
            //已经存在
            $InSql = "update $DataIn.bom_info
            set Quantity=Quantity+$total
            where TradeId = $proId and CmptTypeId = $CmptTypeId and MaterNo = $StuffId";

            $InRecode = @mysql_query($InSql);

        }
        else {
            $InSql = "INSERT INTO $DataIn.bom_info(TradeId,
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
            ) VALUES ($proId,
            $CmptTypeId, '$CmptTypeName',
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

        //浇捣养护 (构件半成品 )
        $StuffId = $stuffArr["浇捣养护"];
        $StuffTypeId = $stufftype["浇捣养护"];

        $unit = $stuffUnitArr["浇捣养护"];
        $MainTypeId = $stuffmaintype["生产类配件"];
        //$total = count($spec->sizeArr);

        $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and CmptTypeId = $TypeId and MaterNo = $StuffId");
        if ($bomRow = mysql_fetch_array($bomResult)) {
            //已经存在
            $InSql = "update $DataIn.bom_info
            set Quantity=Quantity+$total
            where TradeId = $proId and CmptTypeId = $Cmpttype and MaterNo = $StuffId";

            $InRecode = @mysql_query($InSql);

        }
        else {
            $InSql = "INSERT INTO $DataIn.bom_info(TradeId,
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
            ) VALUES ($proId,
            $CmptTypeId, '$CmptTypeName',
            $StuffTypeId, '浇捣养护',
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
    }
}
