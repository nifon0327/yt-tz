<?php
//钢筋半成品
//BOM信息  ---- 钢筋总量

$result = mysql_query("select P.TypeId, P.TypeName From $DataIn.producttype P LEFT JOIN $DataIn.trade_steel_data T ON T.TypeName = P.TypeName WHERE T.TradeId = $proId");
if($result && $myRow = mysql_fetch_array($result)){
    do {
        $myTypeName = $myRow["TypeName"];
        $myTypeId = $myRow["TypeId"];
        
        $steelResult = mysql_query("select Titles,Specs,Sizes,BuildingNo From $DataIn.trade_steel_data where TradeId = $proId and TypeName ='$myTypeName'");

        if ($steelResult && $steelRow = mysql_fetch_array($steelResult)) {
            do {
                // 尺寸*尺寸
                $Titles = json_decode($steelRow["Titles"]);
                // 长短
                $Sizes = json_decode($steelRow["Sizes"]);
                // 规格类型
                $specs = json_decode($steelRow["Specs"]);
                //echo $specs;
                $BuildingNo = $steelRow["BuildingNo"];
                //数量统计
                $qtys = array();
                $CmptTypeIds = array();
                $embeddedQtyResult = mysql_query("SELECT  a.BuildingNo,a.CmptTypeId, a.CmptType,a.Quantities,count(a.CmptNo) as CmptCount
                    from trade_steel a
                    where a.TradeId = $proId and a.BuildingNo = '$BuildingNo'  and a.CmptTypeId=$myTypeId
                   GROUP BY  a.BuildingNo,a.CmptTypeId, a.CmptType,a.Quantities "); //统计相同楼栋相同预钢筋分布的构ng件总数以及数量信息
                if ($embeddedQtyResult && $embeddedQtyRow = mysql_fetch_array($embeddedQtyResult)) {
                    do {
                        $arr = json_decode($embeddedQtyRow["Quantities"]);
                        $CmptTypeId = $embeddedQtyRow["CmptTypeId"];
                        $TypeName = $embeddedQtyRow["CmptType"];
                        $CmptCount = $embeddedQtyRow["CmptCount"];
                        $qtys = [];
                        for ($i = 0; $i < count($arr); $i++) {    //累计
                            $arrTemp = $arr[$i];
                            $qtys[$i] = $qtys[$i] + $arrTemp * $CmptCount;
                        }
                        for ($i = 0; $i < count($qtys); $i++) {
                            if ($qtys[$i] > 0) {
                                $Title = $Titles[$i];
                                $spec = $specs[$i];
                                $StuffEname = $spec;
                                $TypeId = $stufftype["钢材"];
                                $MainTypeId = $stuffmaintype["采购类配件"];
                                $Total = $qtys[$i];
                                $size = $Sizes[$i];
                                $loss = 0;  //损耗

                                //---检查原材料
                                $stuffResult = mysql_query("select StuffId,Spec FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$spec' limit 1");
                                if ($stuffRow = mysql_fetch_array($stuffResult)) {
                                    //已经存在配件
                                    $StuffId = $stuffRow["StuffId"];
                                    $StuffSpec = $stuffRow["Spec"];
                                    //  $SteelName = $stuffRow["StuffCname"];
                                    $IsNew = "否";
                                }
                                else {
                                    $IsNew = "是";
                                    echo json_encode(array(
                                        'rlt' => false,
                                        'msg' => "物料名称：" . $spec . " 钢材不存在，请完善后再进行BOM初始化操作",
                                    ));
                                    mysql_query("ROLLBACK");
                                    mysql_query("END");
                                    exit;
                                }


                                //---损耗和长度总重获取
                                $stuffResult = mysql_query("select a.StuffId,a.Spec,b.ThisStd FROM stuffdata a 
                              LEFT JOIN stuff_loss b on a.StuffId=b.StuffId and b.CmptTypeId = '$CmptTypeId'
                             where a.TypeId='$TypeId' and a.StuffEname = '$spec'  limit 1");
                                if ($stuffRow = mysql_fetch_array($stuffResult)) {
                                    // 钢筋(原材料)
                                    $StuffId = $stuffRow["StuffId"];
                                    $thisStd = $stuffRow["ThisStd"];
                                    $thisSpec = $stuffRow["Spec"];
                                    $b = preg_match('/[-+]?[0-9]*\.?[0-9]+/', $thisStd, $arr);
                                    $std = $arr[0];
                                    if (strpos($thisStd, "%")) {
                                        $std = $std / 100;
                                    }
                                    if ($std) {
                                        $loss = $Total * $std;
                                    }

                                    $lt = (strpos($thisSpec, "["));
                                    $rt = (strpos($thisSpec, "]"));
                                    $specTemp = substr($thisSpec, $lt + 1, $rt - 1);

                                    //$specTemp = mb_substr($spec,1,mb_strlen($spec,"UTF-8")-1,"UTF-8");  //去掉首字母
                                    $b = preg_match('/\d+/', $size, $arr);
                                    $sizeTemp = $arr[0];
                                    //print_r($arr);
                                    //$Relation = $specTemp * $specTemp * $sizeTemp  * 0.00617 / 1000;
                                    $Relation = $specTemp * $sizeTemp / 1000;
                                    $Total = $Total * $Relation;
                                    $loss = $loss * $Relation;
                                    $Relation = round($Relation, 3);
                                }


                                //----半成品钢筋资料生成
                                $StuffEname = $spec . "-" . $size . "-" . $Title . '-' . $proId;
                                $StuffCname = $StuffEname . "-" . $StuffSpec;
                                $TypeId = $stufftype["钢筋半成品"];
                                $SendFloor = 18;
                                $stuffResult = mysql_query("select StuffId FROM $DataIn.stuffdata where TypeId='$TypeId' and StuffEname = '$StuffEname' limit 1");
                                if ($stuffRow = mysql_fetch_array($stuffResult)) {
                                    $mStuffId = $stuffRow["StuffId"];
                                }
                                else {
                                    //添加配件
                                    // 配件ID 最大id查找
                                    $maxSql = mysql_query("SELECT MAX(StuffId) AS MStuffId FROM $DataIn.stuffdata", $link_id);
                                    $mStuffId = mysql_result($maxSql, 0, "MStuffId");
                                    $mStuffId = $mStuffId + 1;
                                    //PriceDetermined 1 待定
                                    //Unit 5-个 6-吨
                                    $BuyerId = 10024;
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
                                    '$StuffEname',
                                    '$TypeId',
                                    '$StuffEname','$NoTaxPrice',
                                    0.00,'$CostPrice',
                                    1,
                                    5,
                                    2,
                                    '','','',0,'$Pjobid','$PicNumber','$GicJobid','$GicNumber','$GcheckNumber',
                                    '$SendFloor','$CheckSign',0,
                                    '$Date',
                                    '$Operator',
                                    '$DateTime')";

                                    $InRecode = @mysql_query($InSql);

                                    //属性
                                    $InSql = "INSERT INTO $DataIn.stuffproperty(StuffId,
                                    Property
                                    )VALUES($mStuffId,
                                    18)";

                                    $InRecode = @mysql_query($InSql);

                                    //配件采购供应商关系表
                                    $inRecode1 = "INSERT INTO $DataIn.bps (StuffId,BuyerId,CompanyId,Locks) VALUES ('$mStuffId','$BuyerId','$CompanyId','0')";
                                    $inRres1 = @mysql_query($inRecode1);

                                    //配件库存表
                                    $inRecode2 = "INSERT INTO $DataIn.ck9_stocksheet (StuffId,dStockQty,tStockQty,oStockQty,mStockQty,Date) VALUES ('$mStuffId','0','0','0','0','$Date')";
                                    $inRes2 = @mysql_query($inRecode2);
                                }

                                //半成品钢筋资料BOM表生成-------基础钢筋信息
                                $bomResult = mysql_query("select Id FROM $DataIn.semifinished_bom_relation where mStuffId = $mStuffId and StuffId = $StuffId");
                                if ($bomRow = mysql_fetch_array($bomResult)) {
                                    //已经存在
                                }
                                else {
                                    $InSql = "INSERT INTO $DataIn.semifinished_bom_relation(mStuffId,
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


                                //用料统计----------------------
                                $StuffTypeId = $stufftype["钢材"];
                                $bomResult = mysql_query("select Id FROM $DataIn.bom_info where TradeId = $proId and BuildingNo = '$BuildingNo' and CmptTypeId = $CmptTypeId and MaterNo = $StuffId");
                                if ($bomRow = mysql_fetch_array($bomResult)) {
                                    //已经存在
                                    $InSql = "update $DataIn.bom_info set Quantity=Quantity+$Total,Loss=Loss+$loss
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
                                    $CmptTypeId, '$TypeName',
                                    $StuffTypeId, '钢材',
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
                }

            } while ($steelResult && $steelRow = mysql_fetch_array($steelResult));
        }

    }while($result && $myRow = mysql_fetch_array($result));
}

