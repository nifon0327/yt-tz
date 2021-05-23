<?php
//电信-EWEN
if ($ActionId == 17) {
    $MyPDOEnabled = 1;
    include "../model/modelhead.php";
    $nowWebPage = $funFrom . "_updated";
    $_SESSION["nowWebPage"] = $nowWebPage;
    $Log_Item = "其它出库记录";        //需处理
    $upDataSheet = "$DataIn.ck8_bfsheet";    //需处理
    $Log_Funtion = "更新";
    $TitleSTR = $SubCompany . " " . $Log_Item . $Log_Funtion;
    ChangeWtitle($TitleSTR);
    $DateTime = date("Y-m-d H:i:s");
    $Date = $Date == "" ? date("Y-m-d") : $Date;
    $Operator = $Login_P_Number;
    $OperationResult = "Y";
    switch ($ActionId) {

        case 17:
            $Log_Funtion = "审核";         //审核后修改库存数，审核后不能再操作了

            $Lens = count($checkid);
            for ($i = 0; $i < $Lens; $i++) {
                $Id = $checkid[$i];
                $myResult = null;
                $myRow = null;
                $myResult = $myPDO->query("CALL proc_ck8_bfsheet_updatedestate('$Id','2',$Operator);");
                $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
                $OperationResult = $myRow['OperationResult'] != "Y" ? $myRow['OperationResult'] : $OperationResult;

                $Log .= $OperationResult == "Y" ? $myRow['OperationLog'] : "<div class=redB>" . $myRow['OperationLog'] . "</div>";
                $Log .= "</br>";
            }
            $fromWebPage = $funFrom . "_m";
            break;
    }

}
else {
    include "../model/modelhead.php";
    $nowWebPage = $funFrom . "_updated";
    $_SESSION["nowWebPage"] = $nowWebPage;
    //步骤2：
    $Log_Item = "其它出库记录";        //需处理
    $upDataSheet = "$DataIn.ck8_bfsheet";    //需处理
    $Log_Funtion = "更新";
    $TitleSTR = $SubCompany . " " . $Log_Item . $Log_Funtion;
    ChangeWtitle($TitleSTR);
    $DateTime = date("Y-m-d H:i:s");
    $Date = $Date == "" ? date("Y-m-d") : $Date;
    $Operator = $Login_P_Number;
    $OperationResult = "Y";
    //步骤3：需处理，更新操作
    $x = 1;
    switch ($ActionId) {
        case 7:
            $Log_Funtion = "锁定";
            $SetStr = "Locks=0";
            include "../model/subprogram/updated_model_3d.php";
            break;
        case 8:
            $Log_Funtion = "解锁";
            $SetStr = "Locks=1";
            include "../model/subprogram/updated_model_3d.php";
            break;
        case 15://记录退回
            $Log_Funtion = "物料出库退回";
            $Lens = count($checkid);
            for ($i = 0; $i < $Lens; $i++) {
                $Id = $checkid[$i];
                if ($Id != "") {
                    $DelSql = "UPDATE $DataIn.ck8_bfsheet  SET Estate=2 WHERE Id = $Id AND Estate>0 ";
                    $DelResult = mysql_query($DelSql);
                    if ($DelResult && mysql_affected_rows() > 0) {
                        $Log = "&nbsp;&nbsp;ID在( $Id )的物料出库退回 成功.<br>";
                    }
                    else {
                        $OperationResult = "N";
                        $Log = "<div class='redB'>ID在( $Id )的物料出库退回 失败.</div><br>";
                    }
                }
            }
            $fromWebPage = $funFrom . "_m";
            break;

        case 17:
            $checkId = $_REQUEST['checkid'];
            $str = '(';
            foreach ($checkId as $k => $v) {
                if ($k == 0) {
                    $str .= $v;

                }else{
                    $str .= ','.$v;
                }
            }
            $str .= ')';
            $UpdateSql = "UPDATE  $DataIn.ck8_bfsheet  SET DealResult='' , estate = 0 WHERE Id in $str";
            $UpdateResult = @mysql_query($UpdateSql);
            if ($UpdateResult && mysql_affected_rows() > 0) {
                $inRecode = "INSERT INTO $DataIn.ck8_bfremark(Id,Mid,StuffId,Remark,Estate,Date,Operator) VALUES (NULL,'$Id','$StuffId','$DealResult','0','$DateTime','$Operator')";
                $inAction = @mysql_query($inRecode);
                $Log .= "配件ID $StuffId 其它出库的最终处理结果为： $DealResult <br>";
            }
            else {
                $Log .= "配件ID $StuffId 其它出库的最终处理结果 失败! $UpdateSql <br>";
                $OperationResult = "N";
            }
            break;
        default:

            $Remark = FormatSTR($Remark);
            //上传文件
            if ($Attached != "") {//有上传文件
                $FileType = ".jpg";
                $OldFile = $Attached;
                $FilePath = "../download/ckbf/";
                if (!file_exists($FilePath)) {
                    makedir($FilePath);
                }
                $PreFileName = "B" . $Id . $FileType;
                $Attached = UploadFiles($OldFile, $PreFileName, $FilePath);
                if ($Attached) {
                    $Log .= "&nbsp;&nbsp;单据上传成功！$inRecode <br>";
                    $Attached = 1;
                    //更新刚才的记录
                    $sql = "UPDATE $DataIn.ck8_bfsheet SET Bill='1' WHERE Id=$Id";
                    $result = mysql_query($sql);
                }
                else {
                    $Log .= "<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
                    $OperationResult = "N";
                }
            }
            $Type = $Type == "" ? 0 : $Type;
            $ddd = $changeQty * $Operators;
            $upSql = "UPDATE $DataIn.ck8_bfsheet F 
                      SET F.ProposerId='$ProposerId',F.Qty=F.Qty+$ddd,
                      F.Date='$bfDate',F.Remark='$Remark',F.Type='$Type',F.Estate='1',F.Locks='0',F.LocationId='$LocationId'
                      WHERE F.Id = $Id AND F.Estate>0  ";   // 只更当前，不更新库存
            $upResult = mysql_query($upSql);
            if ($upResult && mysql_affected_rows() > 0) {
                $Log .= "其它出库记录更新成功. <br>";
            }
            else {
                $Log .= "<div class='redB'>其它出库记录更新失败!</div>$upSql<br>";
                $OperationResult = "N";
            }

            break;

    }
    if ($fromWebPage == "") {
        $fromWebPage = $funFrom . "_read";
    }
    $ALType = "From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";
    $IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
    $IN_res = @mysql_query($IN_recode);
}
include "../model/logpage.php";
?>