<?php
//骨架半成品  同一个项目构件编号和楼层唯一确定具体骨架半成品型号

$mySql = " SELECT DISTINCT CmptNo,FloorNo,BuildingNo,CStr,CmptType 
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
        $myTypeName= $myRow["CmptType"];
        $CStr = $myRow["CStr"];
        $StuffCname = 'B-' . $TradeNo . '-' . $BuildingNo . '-' . $FloorNo . '-' . $CmptNo;
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
            //TypeId 9012 骨架半成品
            //PriceDetermined 1 待定
            $BuyerId = 0;
            $CompanyId = 100002;
            include "./stuffdata_insert_before.php";

            //Unit 5-个 6-吨
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


            //查找对应的 预埋件(原材料) 钢筋半成品  杂费
            // 预埋件(原材料)
            $embeddedResult = mysql_query("select Titles, Specs From $DataIn.trade_embedded_data where TradeId = $proId and BuildingNo = '$BuildingNo' and TypeName='$myTypeName'");
            if ($embeddedResult && $embeddedRow = mysql_fetch_array($embeddedResult)) {
                do {
                    $titles = json_decode($embeddedRow["Titles"]);
                    //预埋件规格
                    $specs = json_decode($embeddedRow["Specs"]);

                    //echo $specs;

                    //预埋件数量统计
                    $qtys = array();
                    $embeddedQtyResult = mysql_query("SELECT a.id, a.Quantities
                    from $DataIn.trade_embedded a
                    where a.TradeId = $proId and a.CmptNo = '$CmptNo' and a.FloorNo = '$FloorNo' and a.BuildingNo = '$BuildingNo' limit 1 ");//数量不累计
                    if ($embeddedQtyResult && $embeddedQtyRow = mysql_fetch_array($embeddedQtyResult)) {
                        do {
                            $arr = json_decode($embeddedQtyRow["Quantities"]);

                            for ($i = 0; $i < count($arr); $i++) {
                                $qtys[$i] = $qtys[$i] + $arr[$i];
                            }
                        } while ($embeddedQtyRow = mysql_fetch_array($embeddedQtyResult));
                    }

                    for ($i = 0; $i < count($specs) && $i < count($titles); $i++) {
                        $title = $titles[$i];
                        $spec = $specs[$i];

                        $StuffCname = $spec;

                        $TypeId = $stufftype["预埋"];
//                $TypeId = $stufftype[$title];
                        $stuffResult = mysql_query("select StuffId FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$spec' limit 1");
                        if ($stuffRow = mysql_fetch_array($stuffResult)) {
                            // 预埋件(原材料)
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

                } while ($embeddedResult && $embeddedRow = mysql_fetch_array($embeddedResult));

            }

            // 钢筋半成品
            $steelSql = "select Distinct Specs, Sizes, Titles From $DataIn.trade_steel_data where TradeId = $proId and BuildingNo = '$BuildingNo' and TypeName='$myTypeName'";
            $steelResult = mysql_query($steelSql);
            if ($steelResult && $steelRow = mysql_fetch_array($steelResult)) {
                do {
                    //钢筋规格(
                    $specs = json_decode($steelRow["Specs"]);
                    //钢筋下料尺寸
                    $sizes = json_decode($steelRow["Sizes"]);
                    // 钢筋类型
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
                        $TypeId = $stufftype["钢筋半成品"];
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
                        where a.TradeId = $proId and a.CmptNo = '$CmptNo' and a.FloorNo = '$FloorNo' limit 1");
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

            mysql_free_result($steelResult); //释放内存
            //90013杂费统计
            $sid = $stuffArr["杂费统计"];
            $bomResult = mysql_query("select Id FROM $DataIn.semifinished_bom where mStuffId = $mStuffId and StuffId = $sid");
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
            $sid,
            '1',
            '$Date',
            '$Operator')";

                //echo $InSql,"  ";
                $InRecode = @mysql_query($InSql);
            }

            $StuffId = $stuffArr["骨架搭建"];
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
}
//------------------------------------------------------------------------统计所有骨架使用的钢筋半成品的钢筋用量，预埋件量，相关的工序统计总数----------------------------------------

//BOM信息  ---- 杂费统计 骨架搭建
$cmptTypeResult = mysql_query("SELECT a.BuildingNo,a.CmptTypeId, a.CmptType,  count(a.Id) as Count
            from trade_drawing a
            where a.TradeId = $proId
            GROUP BY a.BuildingNo,a.CmptTypeId, a.CmptType");

while ($cmptTypeRow = mysql_fetch_array($cmptTypeResult)) {

    $CmptTypeId = $cmptTypeRow["CmptTypeId"];
    $CmptType = $cmptTypeRow["CmptType"];
    $total = $cmptTypeRow["Count"];
    $BuildingNo = $cmptTypeRow["BuildingNo"];

    //杂费统计 (构件半成品 )
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

    //骨架搭建 (构件半成品 )
    $StuffId = $stuffArr["骨架搭建"];
    $StuffTypeId = $stufftype["骨架搭建"];

    $unit = $stuffUnitArr["骨架搭建"];
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
            $CmptTypeId, '$CmptType',
            $StuffTypeId, '骨架搭建',
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


//BOM信息  ---- 统计预埋件的总数

// 预埋件(原材料)
$result = mysql_query("select TypeId, TypeName From $DataIn.producttype");
if($result && $myRow = mysql_fetch_array($result)){
    do {
        $myTypeName = $myRow["TypeName"];
        $myTypeId = $myRow["TypeId"];
        $embeddedResult = mysql_query("select Distinct Titles,Specs,BuildingNo From $DataIn.trade_embedded_data where TradeId = $proId and TypeName='$myTypeName'");
        if ($embeddedResult && $embeddedRow = mysql_fetch_array($embeddedResult)) {
            do {
                $titles = json_decode($embeddedRow["Titles"]);
                //预埋件规格
                $specs = json_decode($embeddedRow["Specs"]);
                //echo $specs;
                $BuildingNo = json_decode($embeddedRow["BuildingNo"]);
                //预埋件数量统计

                $CmptTypeIds = array();
                $embeddedQtyResult = mysql_query("SELECT  a.BuildingNo,a.CmptTypeId, a.CmptType,a.Quantities,count(a.CmptNo) as CmptCount
                    from trade_embedded a
                    where a.TradeId = $proId and a.BuildingNo = '$BuildingNo'  and a.CmptTypeId = $myTypeId
                   GROUP BY  a.BuildingNo,a.CmptTypeId, a.CmptType,a.Quantities "); //统计相同楼栋相同预埋件分布的构件总数以及数量信息
                if ($embeddedQtyResult && $embeddedQtyRow = mysql_fetch_array($embeddedQtyResult)) {
                    do {
                        $arr = json_decode($embeddedQtyRow["Quantities"]);
                        $CmptTypeId = $embeddedQtyRow["CmptTypeId"];
                        $CmptType = $embeddedQtyRow["CmptType"];
                        $CmptCount = $embeddedQtyRow["CmptCount"];
                        $qtys = array();
                        for ($i = 0; $i < count($arr); $i++) {    //累计对应预埋件的总数
                            $qtys[$i] = $qtys[$i] + $arr[$i] * $CmptCount;
                            if ($qtys[$i] > 0) {
                                $title = $titles[$i];
                                $spec = $specs[$i];
                                $StuffCname = $spec;
                                $TypeId = $stufftype["预埋"];
                                $MainTypeId = $stuffmaintype["采购类配件"];
                                $Total = $qtys[$i];
                                $loss = 0;  //损耗
                                $StuffId = '';
                                $stuffResult = mysql_query("select a.StuffId,b.ThisStd FROM stuffdata a 
                              LEFT JOIN stuff_loss b on a.StuffId=b.StuffId and b.CmptTypeId = '$CmptTypeId'
                             where a.TypeId='$TypeId' and a.StuffEname = '$spec'  limit 1");

                                if ($stuffRow = mysql_fetch_array($stuffResult)) {
                                    // 预埋件(原材料)
                                    $StuffId = $stuffRow["StuffId"];
                                    $thisStd = $stuffRow["ThisStd"];
                                    if ($thisStd) {
                                        $std = trim($thisStd, '≤%');
                                        $loss = $Total * $std / 100;
                                    }
                                }

                                $StuffTypeId = $stufftype["预埋"];

                                $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId");
                                if ($bomRow = mysql_fetch_array($bomResult)) {
                                    //已经存在
                                    $InSql = "update $DataIn.bom_info set Quantity=Quantity+$Total,Loss=Loss+$loss
                                         where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId";
                                    $InRecode = @mysql_query($InSql);
                                } else {
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
                                    )VALUES($proId,'$BuildingNo',
                                    $CmptTypeId, '$CmptType',
                                    $StuffTypeId, '预埋',
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
                        }

                    } while ($embeddedQtyRow = mysql_fetch_array($embeddedQtyResult));
                };
                mysql_free_result($embeddedQtyResult);
            } while ($embeddedResult && $embeddedRow = mysql_fetch_array($embeddedResult));

        };
    }while ($result && $myRow = mysql_fetch_array($result));
}

//BOM信息  ---- 统计所有成品构件使用混泥土的总量
$cmptTypeResult = mysql_query("SELECT a.BuildingNo,a.CmptTypeId, a.CmptType, a.CStr, SUM(ROUND(a.CVol,2)) as CVol, count(a.CVol) as Count, b.StuffId
            from trade_drawing a 
            LEFT JOIN stuffdata b on a.CStr=b.StuffEname
            where a.TradeId = $proId
            GROUP BY a.BuildingNo,a.CmptTypeId, a.CmptType, a.CStr,b.StuffId ");

while ($cmptTypeRow = mysql_fetch_array($cmptTypeResult)) {

    $CmptTypeId = $cmptTypeRow["CmptTypeId"];
    $CmptTypeName = $cmptTypeRow["CmptType"];
    $CStr = $cmptTypeRow["CStr"];
    $Total = $cmptTypeRow["CVol"];
    $Count = $cmptTypeRow["Count"];
    $StuffId = $cmptTypeRow["StuffId"];
    $BuildingNo = $cmptTypeRow["BuildingNo"];

    //把混凝土换成采购泪原材料
    $loss = 0;  //损耗

    //损耗取得
    $lossResult = mysql_query("select ThisStd from $DataIn.stuff_loss where TradeId = $proId and CmptTypeId = $TypeId and StuffId=$StuffId limit 1");

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
    $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId");
    if ($bomRow = mysql_fetch_array($bomResult)) {
        //已经存在
        $InSql = "update $DataIn.bom_info
                    set Quantity=Quantity+$Total,Loss=Loss+$loss
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
        )VALUES($proId,'$BuildingNo',
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








