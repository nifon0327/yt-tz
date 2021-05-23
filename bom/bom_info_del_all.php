'<?php
include "../model/modelhead.php";

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del_all";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="BOM清空";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$delBom = "";

if ($proId == ""){
    $proId = addslashes($_POST['proId']);
}
if ($BuildingNo == ""){
    $BuildingNo = addslashes($_POST['BuildingNo']);
}
 if (!$proId || $proId=='' || !$BuildingNo || $BuildingNo=='') {
     $Log .= "<div style='color: red'>请选择项目楼栋！</div>";
 }else {
     $myResult = mysql_query("SELECT TradeNo,CompanyId FROM trade_info TI LEFT JOIN trade_object TT ON TT.Id = TI.TradeId WHERE TradeId = $proId", $link_id);
     if ($myRow = mysql_fetch_array($myResult)) {
         $TradeNo = $myRow['TradeNo'];
         $CompanyId = $myRow['CompanyId'];
     }
 }
if ($TradeNo && $CompanyId){
//if ($delBom == "info" || $delBom == "noInfo") {
     /* 清空半成品BOM */

     $mySql = "SELECT A.mStuffId 
        FROM $DataIn.semifinished_bom A
        LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
        LEFT JOIN $DataIn.stuffcostprice C ON C.StuffId=A.mStuffId 
        LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
        LEFT JOIN $DataIn.semifinished_deliverydate V ON V.mStuffId=A.mStuffId 
        LEFT JOIN $DataIn.pands S ON S.StuffId = D.StuffId
        LEFT JOIN $DataIn.productdata P ON S.ProductId = P.ProductId 
        where 1 AND P.CompanyId = $CompanyId AND D.StuffCname LIKE 'B-$TradeNo-$BuildingNo-%'  AND D.StuffId>0  GROUP BY A.mStuffId order by A.mStuffId DESC";
     $myResult = mysql_query($mySql, $link_id);
     if ($myRow = mysql_fetch_array($myResult)) {
         do {
             $mStuffId = $myRow['mStuffId'];
             if ($mStuffId != "") {
                 $checkVersion = mysql_fetch_array(mysql_query("SELECT MAX(VersionNo) AS VersionNo FROM $DataIn.semifinished_oldbom_main WHERE mStuffId='$mStuffId'", $link_id));
                 $VersionNo = $checkVersion['VersionNo'];
                 $VersionNo = $VersionNo == "" ? 1.00 : $VersionNo + 0.10;

                 $IN_recode = "INSERT INTO $DataIn.semifinished_oldbom_main(Id,mStuffId,VersionNO,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'$mStuffId','$VersionNo','','1','0',CURDATE(),'$Operator')";
                 //echo $IN_recode;
                 $IN_res = @mysql_query($IN_recode);
                 $Mid = mysql_insert_id();

                 $IN_recode2 = "INSERT INTO $DataIn.semifinished_oldbom_sheet(Id,Mid,mStuffId,StuffId,Relation,Date,Operator,creator,created) SELECT NULL,$Mid,mStuffId,StuffId,Relation,Date,Operator,creator,created FROM $DataIn.semifinished_bom WHERE mStuffId='$gStuffId'";
                 $IN_res2 = @mysql_query($IN_recode2);

                 $VersionNo = number_format($VersionNo, 2);
                 //$Log .= "&nbsp;&nbsp;$mStuffId - 保存原半成品BOM记录,Version:$VersionNo; <br>";
                 $SIds = $SIds == "" ? $mStuffId : ($SIds . "," . $mStuffId);
             }

         } while ($myRow = mysql_fetch_array($myResult));

         $DelSql = "DELETE FROM $DataIn.semifinished_bom WHERE mStuffId IN ($SIds)";
         $DelResult = mysql_query($DelSql);
         if ($DelResult) {
             $Log .= "半成品配件ID：$SIds 的BOM关系解除成功<br>";
             $delBom = "stuffdata";
         } else {
             $Log .= "<div class=redB>半成品配件ID：$mStuffId 的BOM关系解除失败 </div><br>";
             $OperationResult = "N";
         }

     }
// else {
//        $Log .= "<div class=redB>半成品BOM查询失败 </div><br>";
//        $OperationResult = "N";
//        $delBom = "noStuffdata";
//    }
//    /* 清空半成品BOM */
//}

    $Log .= "<br/>";
//if ($delBom == "stuffdata" || $delBom == "noStuffdata"){
     /* 清空成品配件关系 */

     $mySql = "SELECT A.ProductId
        FROM $DataIn.pands A
        LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
        where 1 AND P.CompanyId = $CompanyId AND P.cName LIKE '$BuildingNo-%'  GROUP BY A.ProductId order by A.ProductId DESC";
     //echo $mySql;
     $myResult = mysql_query($mySql, $link_id);
     if ($myRow = mysql_fetch_array($myResult)) {
         do {
             $ProductId = $myRow['ProductId'];
             $PIds = $PIds == "" ? $ProductId : ($PIds . "," . $ProductId);
         } while ($myRow = mysql_fetch_array($myResult));


         $DelSql = "DELETE FROM $DataIn.pands WHERE ProductId IN ($PIds) AND ProductId NOT IN (SELECT ProductId FROM $DataIn.yw1_ordersheet WHERE 1 AND Estate>0 GROUP BY ProductId)";
         $DelResult = mysql_query($DelSql);
         if ($DelResult) {
             $Log .= "产品ID在 $PIds 的产品BOM解除成功<br>";
             $DelSql2 = "DELETE FROM $DataIn.pands_unite WHERE ProductId IN ($PIds) ";
             $DelResult2 = mysql_query($DelSql2);
             $delBom = "pands";
         } else {
             $Log .= "<div class=redB>产品ID在 $PIds 的产品BOM解除失败 $DelSql</div><br>";
             $OperationResult = "N";
             $delBom = "no";
         }
     }
//    else{
//        $delBom = "npPands";
//    }
//    /* 清空成品配件关系 */
//}


    $Log .= "<br/>";

//if ($delBom == "productdata" || $delBom == "noProductdata"){
     /* 清空配件资料 - 构件半成品 */

     $mySql = "SELECT A.Id,A.StuffId 
        FROM $DataIn.stuffdata A 
        LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
        LEFT JOIN $DataIn.staffmain C ON C.Number=B.BuyerId 
        LEFT JOIN $DataIn.stuffunit D ON D.Id=A.Unit
        LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId  AND  E.ObjectSign IN (1,3) 
        LEFT JOIN $DataIn.staffgroup K ON K.Id=A.Pjobid 
        LEFT JOIN $DataIn.staffgroup F ON F.Id=A.Jobid 
        LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId  
        LEFT JOIN $DataIn.staffgroup M ON M.Id=G.Picjobid 
        LEFT JOIN $DataIn.staffgroup N ON N.Id=G.GicJobid 
        LEFT JOIN $DataIn.staffmain L ON L.Number=A.GcheckNumber 
        LEFT JOIN $DataIn.ck9_stocksheet H ON H.StuffId=A.StuffId
        LEFT JOIN $DataIn.stuffproperty  P ON P.StuffId=A.StuffId 
        LEFT JOIN $DataIn.stuffdevelop  DP ON DP.StuffId=A.StuffId  
        WHERE 1 AND A.TypeId = '9017' AND A.StuffCname LIKE 'B-$TradeNo-$BuildingNo-%' Group BY A.StuffId ORDER BY A.Estate DESC,A.Id DESC";
     $myResult = mysql_query($mySql , $link_id);
     if ($myRow = mysql_fetch_array($myResult)) {
         $d = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);
         do {
             $Id = $myRow["Id"];
             $StuffId = $myRow["StuffId"];
             if ($Id != "") {
                 $sResult = mysql_query("SELECT S.StuffCname,S.StuffId,S.Picture,S.Gfile FROM $DataIn.stuffdata S
                                                           WHERE S.Id='$Id'  
                                                            and NOT EXISTS ( SELECT G.StuffId FROM $DataIn.cg1_stocksheet G WHERE G.StuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT P.StuffId FROM $DataIn.pands P WHERE P.StuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT C.StuffId FROM $DataIn.semifinished_bom C WHERE C.StuffId=S.StuffId OR C.mStuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT B.StuffId FROM $DataIn.stuffcombox_bom B WHERE B.StuffId=S.StuffId OR B.mStuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT R.StuffId FROM $DataIn.ck1_rksheet R WHERE R.StuffId=S.StuffId)", $link_id);
                 if ($sRow = mysql_fetch_array($sResult)) {//可删除
                     $StuffCname = $sRow["StuffCname"];
                     $StuffId = $sRow["StuffId"];
                     $Picture = $sRow["Picture"];
                     $Gfile = $sRow["Gfile"];
                     $DelSql = "DELETE $DataIn.stuffdata,$DataIn.bps,$DataIn.ck9_stocksheet
                                                            FROM $DataIn.stuffdata 
                                                            LEFT JOIN $DataIn.bps ON $DataIn.bps.StuffId=$DataIn.stuffdata.StuffId
                                                            LEFT JOIN $DataIn.ck9_stocksheet ON $DataIn.ck9_stocksheet.StuffId=$DataIn.stuffdata.StuffId
                                                            WHERE $DataIn.stuffdata.StuffId='$StuffId'";
                     $DelResult = mysql_query($DelSql);
                     if ($DelResult) {
                         $Log .= " 构件半成品配件 $StuffCname / $StuffId 删除成功！<br>";
                         //删除属性

                         //清除图档
                         if ($Gfile == 1) {
                             $FilePath = "../download/stufffile/g" . $StuffId . ".jpg";
                             if (file_exists($FilePath)) {
                                 unlink($FilePath);
                                 $Log .= "&nbsp;&nbsp;相应的配件图档已清除.<br>";
                             }
                         }
                         //清除图片
                         $CheckImgSql = mysql_query("SELECT Picture FROM $DataIn.stuffimg WHERE StuffId='$StuffId' ORDER BY Id", $link_id);
                         if ($CheckImgRow = mysql_fetch_array($CheckImgSql)) {
                             do {
                                 $PictureTemp = $CheckImgRow["Picture"];
                                 $FilePath = "../download/stufffile/" . $PictureTemp;
                                 if (file_exists($FilePath)) {
                                     unlink($FilePath);
                                 }
                             } while ($CheckImgRow = mysql_fetch_array($CheckImgSql));
                             //删除图片记录
                             $DelImgSql = "DELETE FROM $DataIn.stuffimg WHERE StuffId='$StuffId'";
                             $DelImgResult = mysql_query($DelImgSql);

                         }
                         //删除配件属性
                         $DelPropertySql = "DELETE FROM $DataIn.stuffproperty  WHERE StuffId='$StuffId'";
                         $DelPropertyResult = mysql_query($DelPropertySql);
                     } else {
                         $Log .= $Log . "<div class='redB'>  删除构件半成品配件 $StuffId 的操作失败！</div><br>";
                         $OperationResult = "N";
                     }
                 } else {
                     $Log .= "<div class='redB'>构件半成品配件 $StuffId 有使用的历史记录或已设置产品配件关系，不能删除！</div><br>";
                     $OperationResult = "N";
                 }
                 $x++;
             }

         } while ($myRow = mysql_fetch_array($myResult));
     }
//    else {
//        $Log .= "<div class='redB'> 配件资料查询失败。</div><br>";
//        $OperationResult = "N";
//    }
//
//    /* 清空配件资料 */
//}
    $Log .= "<br/>";

     /* 清空配件资料 - 钢筋半成品 */

     $mySql = "SELECT A.Id,A.StuffId 
        FROM $DataIn.stuffdata A 
        LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
        LEFT JOIN $DataIn.staffmain C ON C.Number=B.BuyerId 
        LEFT JOIN $DataIn.stuffunit D ON D.Id=A.Unit
        LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId  AND  E.ObjectSign IN (1,3) 
        LEFT JOIN $DataIn.staffgroup K ON K.Id=A.Pjobid 
        LEFT JOIN $DataIn.staffgroup F ON F.Id=A.Jobid 
        LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId  
        LEFT JOIN $DataIn.staffgroup M ON M.Id=G.Picjobid 
        LEFT JOIN $DataIn.staffgroup N ON N.Id=G.GicJobid 
        LEFT JOIN $DataIn.staffmain L ON L.Number=A.GcheckNumber 
        LEFT JOIN $DataIn.ck9_stocksheet H ON H.StuffId=A.StuffId
        LEFT JOIN $DataIn.stuffproperty  P ON P.StuffId=A.StuffId 
        LEFT JOIN $DataIn.stuffdevelop  DP ON DP.StuffId=A.StuffId  
        WHERE 1 AND A.TypeId = '9002' AND A.StuffEname LIKE '%-$proId' Group BY A.StuffId ORDER BY A.Estate DESC,A.Id DESC";
     $myResult = mysql_query($mySql , $link_id);
     if ($myRow = mysql_fetch_array($myResult)) {
         $d = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);
         do {
             $Id = $myRow["Id"];
             $StuffId = $myRow["StuffId"];
             if ($Id != "") {
                 $sResult = mysql_query("SELECT S.StuffCname,S.StuffId,S.Picture,S.Gfile FROM $DataIn.stuffdata S
                                                           WHERE S.Id='$Id'  
                                                            and NOT EXISTS ( SELECT G.StuffId FROM $DataIn.cg1_stocksheet G WHERE G.StuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT P.StuffId FROM $DataIn.pands P WHERE P.StuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT C.StuffId FROM $DataIn.semifinished_bom C WHERE C.StuffId=S.StuffId OR C.mStuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT B.StuffId FROM $DataIn.stuffcombox_bom B WHERE B.StuffId=S.StuffId OR B.mStuffId=S.StuffId)
                                                            and NOT EXISTS ( SELECT R.StuffId FROM $DataIn.ck1_rksheet R WHERE R.StuffId=S.StuffId)", $link_id);
                 if ($sRow = mysql_fetch_array($sResult)) {//可删除
                     $StuffCname = $sRow["StuffCname"];
                     $StuffId = $sRow["StuffId"];
                     $Picture = $sRow["Picture"];
                     $Gfile = $sRow["Gfile"];
                     $DelSql = "DELETE $DataIn.stuffdata,$DataIn.bps,$DataIn.ck9_stocksheet
                                                            FROM $DataIn.stuffdata 
                                                            LEFT JOIN $DataIn.bps ON $DataIn.bps.StuffId=$DataIn.stuffdata.StuffId
                                                            LEFT JOIN $DataIn.ck9_stocksheet ON $DataIn.ck9_stocksheet.StuffId=$DataIn.stuffdata.StuffId
                                                            WHERE $DataIn.stuffdata.StuffId='$StuffId'";
                     $DelResult = mysql_query($DelSql);
                     if ($DelResult) {
                         $Log .= " 钢筋半成品配件 $StuffCname / $StuffId 删除成功！<br>";
                         //删除属性

                         //清除图档
                         if ($Gfile == 1) {
                             $FilePath = "../download/stufffile/g" . $StuffId . ".jpg";
                             if (file_exists($FilePath)) {
                                 unlink($FilePath);
                                 $Log .= "&nbsp;&nbsp;相应的配件图档已清除.<br>";
                             }
                         }
                         //清除图片
                         $CheckImgSql = mysql_query("SELECT Picture FROM $DataIn.stuffimg WHERE StuffId='$StuffId' ORDER BY Id", $link_id);
                         if ($CheckImgRow = mysql_fetch_array($CheckImgSql)) {
                             do {
                                 $PictureTemp = $CheckImgRow["Picture"];
                                 $FilePath = "../download/stufffile/" . $PictureTemp;
                                 if (file_exists($FilePath)) {
                                     unlink($FilePath);
                                 }
                             } while ($CheckImgRow = mysql_fetch_array($CheckImgSql));
                             //删除图片记录
                             $DelImgSql = "DELETE FROM $DataIn.stuffimg WHERE StuffId='$StuffId'";
                             $DelImgResult = mysql_query($DelImgSql);

                         }
                         //删除配件属性
                         $DelPropertySql = "DELETE FROM $DataIn.stuffproperty  WHERE StuffId='$StuffId'";
                         $DelPropertyResult = mysql_query($DelPropertySql);
                     } else {
                         $Log .= $Log . "<div class='redB'>  删除钢筋半成品配件 $StuffId 的操作失败！</div><br>";
                         $OperationResult = "N";
                     }
                 } else {
                     $Log .= "<div class='redB'>钢筋半成品配件 $StuffId 有使用的历史记录或已设置产品配件关系，不能删除！</div><br>";
                     $OperationResult = "N";
                 }
                 $x++;
             }

         } while ($myRow = mysql_fetch_array($myResult));
     }

    $Log .= "<br/>";



//if ($delBom == "pands" || $delBom == "noPands"){
    /* 成品资料删除 */

    $mySql = "SELECT P.Id
        FROM $DataIn.productdata P
        LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
        LEFT JOIN $DataIn.customscode H ON H.ProductId = P.ProductId
        LEFT JOIN $DataIn.productmq M ON M.Id = P.MaterialQ
        LEFT JOIN $DataIn.productuseway W ON W.Id = P.UseWay
        LEFT JOIN $DataIn.currencydata D ON D.Id=C.Currency
        LEFT JOIN $DataIn.taxtype BG ON BG.Id = P.taxtypeId
        LEFT JOIN $DataIn.productstock S ON S.ProductId = P.ProductId
        LEFT JOIN $DataIn.product_property B ON B.Id = P.buySign
        LEFT JOIN (
                    SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId            
                    FROM $DataIn.ch1_shipmain M 
                    LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
                    WHERE 1 GROUP BY S.ProductId ORDER BY M.Date DESC
            ) E ON E.ProductId=P.ProductId
        LEFT JOIN $DataIn.productstandimg PM ON PM.ProductId=P.ProductId
        WHERE 1 AND P.CompanyId = $CompanyId AND P.cName LIKE '$BuildingNo-%' ";

    $myResult = mysql_query($mySql, $link_id);
    if ($myRow = mysql_fetch_array($myResult)) {
        do {
            $Id = $myRow['Id'];
            if ($Id != "") {
                //检查产品资料是否锁定,是否可以删除
                $sResult = mysql_query("SELECT ProductId,cName,TestStandard,dzSign FROM $DataIn.productdata WHERE Id='$Id' 
                                AND ProductId NOT IN (SELECT ProductId FROM $DataIn.yw1_ordersheet GROUP BY ProductId )
                                AND ProductId NOT IN (SELECT ProductId FROM $DataIn.sale_ordersheet GROUP BY ProductId)", $link_id);
                if ($sRow = mysql_fetch_array($sResult)) {//可删除
                    $cName = $sRow["cName"];
                    $ProductId = $sRow["ProductId"];
                    $TestStandard = $sRow["TestStandard"];
                    $dzSign = $sRow["dzSign"];
                    //执行删除
                    $DelSql = "DELETE $DataIn.productdata,$DataIn.pands 
                                        FROM $DataIn.productdata 
                                        LEFT JOIN $DataIn.pands ON $DataIn.productdata.ProductId=$DataIn.pands.ProductId
                                        WHERE $DataIn.productdata.Id='$Id'";

                    $DelResult = mysql_query($DelSql);
                    if ($DelResult) {
                        $y++;
                        $Log .= " 产品 $cName /$ProductId 删除成功。<br>";
                        $delBom = "productdata";
                        if ($TestStandard == 1) {
                            $delFile = "T" . $ProductId . ".jpg";
                            $FilePath = "../download/teststandard/" . $delFile;
                            if (file_exists($FilePath)) {
                                unlink($FilePath);
                            }
                        }

                        if ($dzSign == 1) {
                            $CheckFile = mysql_fetch_array(mysql_query("SELECT COUNT(Picture) AS Count 
	                        				FROM $DataIn.product_certification WHERE ProductId='$ProductId'"));
                            $Cnt = $CheckFile["Count"];
                            for ($index = 1; $index <= $Cnt; $index++) {

                                $CerFile = $ProductId . "_" . $index . ".pdf";
                                echo "CerFile:" . $CerFile;
                                $CerFilePath = "../download/productcer/" . $CerFile;
                                if (file_exists($CerFilePath)) {
                                    unlink($CerFilePath);
                                }
                            }
                            $CerDelSql = "DELETE FROM $DataIn.product_certification WHERE ProductId=$ProductId";
                            $CerResult = mysql_query($CerDelSql);
                        }
                        $delSql = "delete from yw7_clientproduct where ProductId=$ProductId";
                        $delResult = mysql_query($delSql);
                    } else {
                        $Log .= "<div class='redB'> 产品 $cName /$ProductId 删除失败。</div><br>";
                        $OperationResult = "N";
                    }
                } else {
                    $Log .= "<div class='redB'> Id号为 $Id 的产品已有使用记录，不能删除。</div><br>";
                    $OperationResult = "N";
                }
            }

        } while ($myRow = mysql_fetch_array($myResult));
    }
//    else{
//        $delBom = "noProductdata";
//    }
//
//
//    /* 成品资料删除 */
//}
    $Log .= "<br/>";

     $DelSql = "DELETE FROM $DataIn.bom_info WHERE tradeId = $proId AND BuildingNo = '$BuildingNo'";
     $DelResult = mysql_query($DelSql);
     if ($DelResult && mysql_affected_rows() > 0) {
         $Log .= "BOM信息清空成功<br><br>";
//    } else {
//        $Log .= "<div class=redB>bom_info 数据清空失败 </div><br>";
//        $OperationResult = "N";
//    }
     } else {
         $Log .= "BOM信息清空失败<br><br>";
     }


 }

$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&proId=$proId&BuildingNos=$BuildingNo&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";