<?php
//电信-EWEN
$MyPDOEnabled = 1;
include "../model/modelhead.php";
$nowWebPage = $funFrom . "_updated";
$_SESSION["nowWebPage"] = $nowWebPage;
$fromWebPage = $funFrom . "_read";
//步骤2：
$Log_Item = "备品转入";//需处理
$upDataSheet = "$DataIn.ck7_bprk";    //需处理
$TitleSTR = $SubCompany . " " . $Log_Item . $Log_Funtion;
$DateTime = date("Y-m-d H:i:s");
$Operator = $Login_P_Number;
$OperationResult = "Y";
$Date = date("Y-m-d");
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
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
    case 17:
        $Log_Funtion = "审核";
        $Lens = count($checkid);
        for ($i = 0; $i < $Lens; $i++) {
            $Id = $checkid[$i];
            $myResult = null;
            $myRow = null;
            $myResult = $myPDO->query("CALL proc_ck7_bprk_updatedestate('$Id',$Operator);");
            $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
            $OperationResult = $myRow['OperationResult'] != "Y" ? $myRow['OperationResult'] : $OperationResult;

            $Log .= $OperationResult == "Y" ? $myRow['OperationLog'] : "<div class=redB>" . $myRow['OperationLog'] . "</div>";
        }
        $Log .= "</br>";
        $fromWebPage = $funFrom . "_m";
        break;
    case 162:
        {
            $Id = $checkid[0];
            $updateStuffStateSql = "update $DataIn.ck7_bprk set Estate = '2' Where Id = '$Id'";
            $passSql = mysql_query($updateStuffStateSql);
            $returnReasonSql = "Insert Into $DataPublic.returnreason (Id, tableId, targetTable, Reason, DateTime) Values (NULL, '$Id', '$DataIn.ck7_bprk','$ReturnReasons', '$DateTime')";
            mysql_query($returnReasonSql);
            if ($passSql) {
                $Log .= "审核退回成功!<br>";
            } else {
                $Log .= "<div class='redB'>审核退回失败! $pass</div><br>";
                $OperationResult = "N";
            }
            $fromWebPage = $funFrom . "_m";
        }
        break;
    default:
        $Log_Funtion = "更新";
        $Remark = FormatSTR($Remark);
        $changeQty = $changeQty == "" ? 0 : $changeQty;
        $upSql = "UPDATE $DataIn.ck7_bprk F SET F.Qty=F.Qty+$changeQty*$Operators,
			          F.Date='$bpDate',F.Remark='$Remark',F.Estate='1',F.Locks='0',F.LocationId='$LocationId'
				      WHERE F.Id='$Id'   AND F.Estate > 0 ";

        $upResult = mysql_query($upSql);
        if ($upResult && mysql_affected_rows() > 0) {
            $Log .= "备品转入更新成功. <br>";
        } else {
            $Log .= "<div class='redB'>备品转入更新失败!</div>$upSql<br>";
            $OperationResult = "N";
        }

}
$IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res = @mysql_query($IN_recode);

include "../model/logpage.php";
?>