<?php
//电信-zxq 2012-08-01
session_start();
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$FromqcCause = 0;
$Date = date("Y-m-d");
$DateTime = date("Y-m-d H:i:s");
$Operator = $Login_P_Number;

if ($ActionId == 19) {
    $FromqcCause = 1;
    $Date = date("Y-m-d");
    $sumQty = $sumQty == 0 ? $CheckQty : $sumQty;
    $Estate = $sumQty == 0 ? 0 : 1;
    if ($CheckSign == 0) { //抽检
        $Estate = 0;  // 全部入库，不良品不做退换操作
    }

    $inSql = "INSERT INTO $DataIn.qc_badrecord (Id,shMid,Sid,StockId,StuffId,shQty,checkQty,Qty,AQL,Remark,Estate,Locks,Date,Operator,creator,created)
       SELECT NULL,Mid,'$Id',StockId,StuffId,Qty,
               '$CheckQty','$sumQty','$CheckAQL','','$Estate','0','$Date','$Operator','$Operator',NOW() 
               FROM  $DataIn.gys_shsheet WHERE Id='$Id' LIMIT 1";
    $inAction = $myPDO->exec($inSql);

    $Mid = $myPDO->lastInsertId();

    if ($Mid > 0) {
        $qcResult = "来料品检不良记录主表保存成功！";
        if ($sumQty > 0) {  //有不良品
            $FileType = ".jpg";
            $FilePath = "../download/qcbadpicture/";
            if (!file_exists($FilePath)) {
                makedir($FilePath);
            }
            $counts = count($badQty);
            for ($i = 0; $i < $counts; $i++) {
                if ($badQty[$i] > 0) {
                    //生成明细表
                    $insheetSql = "INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '$CauseId[$i]', '$badQty[$i]', '','0')";
                    $insheetAction = $myPDO->exec($insheetSql);

                    if (!$insheetAction) {
                        $qcError = 1;
                        break;
                    }
                    else {
                        //上传不良图片
                        $Sid = $myPDO->lastInsertId();
                        if ($fileinput[$i]) {
                            $PreFileName = "Q" . $Sid . $FileType;
                            $copymes = copy($fileinput[$i], "$FilePath" . "$PreFileName");
                            if ($copymes) {
                                //更新刚才的记录
                                $sql = "UPDATE $DataIn.qc_badrecordsheet 
                                                    SET Picture='1' WHERE Id=$Sid";
                                $result = $myPDO->exec($sql);
                            }
                            else {
                                $qcResult .= "\n 不良图片上传失败！";
                            }
                        }
                    }
                }
            }//end for
            //有其它不良原因
            if ($otherbadQty > 0) {
                //生成明细表
                $insheetSql = "INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '-1', '$otherbadQty', '$otherCause','0')";
                $insheetAction = $myPDO->exec($insheetSql);
                if (!$insheetAction) {
                    $qcError = 1;
                    break;
                }
                else {
                    $Sid = $myPDO->lastInsertId();
                    //上传不良图片
                    if ($otherfileinput) {
                        $PreFileName = "Q" . $Sid . $FileType;
                        $copymes = copy($_FILES["otherfileinput"]["tmp_name"], "$FilePath" . "$PreFileName");
                        if ($copymes) {
                            //更新刚才的记录
                            $sql = "UPDATE $DataIn.qc_badrecordsheet 
                                               SET Picture='1' WHERE Id=$Sid";
                            $result = $myPDO->exec($sql);
                        }
                        else {
                            $qcResult .= "\n不良图片上传失败！";
                        }
                    }
                }
            }

            if ($qcError == 1) $qcResult .= "\n来料品检不良明细记录保存失败！";
            else $qcResult .= "\n来料品检不良明细记录保存成功！";
        }
    }
    else {
        $qcError = 1;
        $qcResult = "\n来料品检不良记录保存失败！";
    }

    if ($CheckSign == 1) {       //全检审核，记录入库

        $ActionId = 17;
    }
    else {    //抽检，要分允收？拒收？
        if ($sumQty == 0 || $sumQty < $ReQty) {
            $ActionId = 17;  //允收
        }
        else {
            $ActionId = 15; //抽检拒收，全部退回不入库。
        }
    }


}

switch ($ActionId) {

    case 5: //仓管备注
        $Date = date("Y-m-d");
        $checkSql = "SELECT Id FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1";
        $checkResult = $myPDO->query($checkSql);
        if ($checkRow = $checkResult->fetch(PDO::FETCH_ASSOC)) {//更新
            $checkResult = null;
            $checkRow = null;
            $updateSql = "UPDATE $DataIn.ck6_shremark 
               SET Remark='$Remark',Date='$Date',Operator='$Operator' WHERE ShId='$Id'";
            $updateResult = $myPDO->exec($updateSql);
            if ($updateResult) echo "Y";
        }
        else {
            $checkResult = null;
            $checkRow = null;
            $addSql = "INSERT INTO $DataIn.ck6_shremark (Id, ShId, Remark, Date, Operator) 
              VALUES (NULL, '$Id', '$Remark', '$Date', '$Operator')";
            $addAction = $myPDO->exec($addSql);
            if ($addAction) echo "Y";
        }
        break;


    case 15://退回
        $updateSQL = "UPDATE $DataIn.gys_shsheet Q SET Estate=1,Locks=1 WHERE Q.Id='$Id'";
        $updateResult = $myPDO->exec($updateSQL);
        if ($updateResult) {
            if ($FromqcCause == 1) {
                echo "<SCRIPT LANGUAGE=JavaScript>alert('记录已退回 $qcResult');</script>";
            }
            else {
                echo "记录已退回";
            }

        }
        break;


    case 17://审核通过，记录入库
        if ($FromqcCause == 1) {
            include "item2_3_shrk.php";
//            echo "<SCRIPT LANGUAGE=JavaScript>
//                      var host = window.parent.selObj;
//                      host.parentNode.removeChild(host);
//                      alert('$OperResult $qcResult');
//                  </script>
//                  <script language=JavaScript>
//                  parent.location.reload();
//                  </script>";
            //by.lwh 页面刷新
        }
        else {
            echo $OperResult;
        }
        break;

    case 21: //更改配件的品检标志
        $updateSQL = "UPDATE $DataIn.stuffdata  SET checkSign='$Sign' WHERE StuffId='$StuffId'";
        $updateResult = $myPDO->exec($updateSQL);
        if ($updateResult) {
            echo "Y";
        }
        break;


    case 23: //批量品检合格

        /* $checkSql="SELECT D.Picture,D.CheckSign,T.AQL
		             FROM $DataIn.stuffdata D
                     LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		             WHERE D.StuffId='$StuffId' LIMIT 1";

         $checkResult = $myPDO->query($checkSql);
	     if($checkRow =$checkResult->fetch(PDO::FETCH_ASSOC)){//更新
			  $Picture=$checkRow["Picture"];
			  $CheckSign=$checkRow["CheckSign"];
			  $AQL=$checkRow["AQL"];
              $checkResult = null;
	          $checkRow = null;
              if($Picture!='1'){
                  echo "图档不存在或未审核";
                  break;
              }

              $SearchRow="AND S.Estate=2 AND M.CompanyId='$GysId' AND S.StuffId='$StuffId' ";
              if (trim($BillNumber!="")) $SearchRow.=" AND  M.BillNumber='$BillNumber' ";

             $sumSql="SELECT SUM(S.Qty) AS Qty
					  FROM $DataIn.gys_shsheet S
			          LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
					  WHERE 1 $SearchRow ORDER BY S.Id";
             $sumResult = $myPDO->query($sumSql);
             if($sumRow = $sumResult->fetch(PDO::FETCH_ASSOC)){

                 $Qty=$sumRow["Qty"]; //批量检查总数
                 $sumResult = null;
	             $sumRow = null;
                 //取得抽检标准
                 $checkAQLSql = "SELECT L.Ac,L.Re,L.Lotsize,S.SampleSize
                 FROM $DataIn.qc_levels L
                 LEFT JOIN  $DataIn.qc_lotsize S ON S.Code=L.Code
                 WHERE L.AQL='$AQL' AND S.Start<='$Qty' AND S.End>='$Qty'";
                 $checkAQLResult = $myPDO->query($checkAQLSql);
                 if ($checkAQLRow = $checkAQLResult->fetch(PDO::FETCH_ASSOC)){
                   $SampleSize=$checkAQLRow["SampleSize"];
                   $Lotsize=$checkAQLRow["Lotsize"];
                   if ($Lotsize>0) {$CheckQty=$Lotsize;}else{$CheckQty=$SampleSize;}
                }
                else{
                      $CheckQty=$Qty;
                }
                $checkAQLResult = null;
                $checkAQLRow    = null;
                //计算批量检查比例
                $checkScale=$CheckQty/$Qty;

                $Date=date("Y-m-d");
                $inSql="INSERT INTO $DataIn.qc_badrecord
                        SELECT NULL,S.Mid,S.StockId,S.StuffId,S.Qty,
                        IF( $checkScale*S.Qty<1,1, $checkScale*S.Qty),'0','$AQL',
                        '来自批量品检','0','0','$DateTime','$Operator','0','$Operator',
                        NOW(),'$Operator',NOW()
		                FROM  $DataIn.gys_shsheet S
		                LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		                WHERE 1 $SearchRow  ";

               $inAction=$myPDO->exec($inSql);
               if ($inAction)
               {
                    $qcResult="\n来料品检不良记录主表保存成功！";
               }
               else{
                    $qcResult="\n来料品检不良记录主表保存失败！";
                    echo $qcResult;
                    break;
               }
               //入库操作

               $rkSql="SELECT S.Id FROM $DataIn.gys_shsheet S
                        LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
                        WHERE 1 $SearchRow ORDER BY S.Id";
		       $rkResult = $myPDO->query($rkSql);
               while($rkRow = $rkResult->fetch(PDO::FETCH_ASSOC)){
                     $Id=$rkRow["Id"];

                     include "item2_3_shrk.php";
                     $qcResult.= "\n 送货单Id:" . $Id. $OperResult;
               }
               $rkRow = null;
               $rkResult = null;



               $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','来料审核','批量品检',' $qcResult','Y','$Operator')";
               $IN_res=$myPDO->exec($IN_recode);
               echo  $qcResult;
          }

       }*/


        break;

}

include "../basic/quit_pdo.php";


function unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f) $ret .= chr($val);
            else if ($val < 0x800) $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
            else $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            $i += 5;
        }
        else if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }
        else $ret .= $str[$i];
    }

    return $ret;
}

?>