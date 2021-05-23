<?php
/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2019/2/21
 * Time: 17:39
 */
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;

switch ($ActionId) {
    case 'account':
        $dateRes = explode("-", $dates);
        //检查重复
        $startDate=$dateRes[0];
        $endDate=$dateRes[1];
        $checkSql = "select count(*) c from ch_account A
            left join trade_object T ON T.CompanyId = A.COMPANY_ID
            where T.Id = $proId
            and ((ACCOUNT_START_DATE<='$startDate' and  ACCOUNT_END_DATE>='$startDate')
            or (ACCOUNT_START_DATE<='$endDate' and  ACCOUNT_END_DATE>='$endDate')
            or (ACCOUNT_START_DATE>='$startDate' and  ACCOUNT_END_DATE<='$endDate'))";

        $myResult = mysql_query($checkSql, $link_id);
        if(!$myResult){
            echo "生成失败，检查出错！";
            return false;
        }
        if ($myResult && $myRow = mysql_fetch_array($myResult) && $myRow['c']>0) {
            echo "生成失败，所选时间段与已有对账单时间段重叠！";
            return false;
        }

//        echo $checkSql;
//        echo $myResult;

        $mysql = "SELECT TT.Id, TT.CompanyId, TT.Forshort, CI.Tel, CI.Area, CI.Company, S.NAME AS staff_Name, L.NAME, L.Mobile
            FROM trade_object TT
                LEFT JOIN companyinfo CI ON CI.CompanyId = TT.CompanyId AND CI.Type = 8
                LEFT JOIN currencydata C ON C.Id = TT.Currency
                LEFT JOIN staffmain S ON S.Number = TT.Staff_Number
                LEFT JOIN linkmandata L ON L.CompanyId = TT.CompanyId AND L.Type = 8 AND L.Defaults = 0
            WHERE 1 AND MOD(TT.CompanySign, 7) = 0 AND TT.ObjectSign = '2' AND TT.Id = $proId
            ORDER BY TT.Estate DESC, TT.Letter LIMIT 1 ";
        $myResult = mysql_query($mysql, $link_id);
        if ($myResult && $myRow = mysql_fetch_array($myResult)) {
            $CompanyId = $myRow['CompanyId'];
            $Forshort = $myRow['Forshort'];
            $Company = $myRow['Company'];
            $SUPPLY_USER = $myRow['staff_Name'];
            $RECEIVING_USER = $myRow['NAME'];

            $inRecod = "INSERT INTO $DataIn.ch_account(id, RUN_ID, ACCOUNT_NAME, PROJECT_NAME, SUPPLY, SUPPLY_USER, SUPPLY_DATE, RECEIVING, RECEIVING_USER, RECEIVING_DATE, CG_ACCOUNT_REMARK, ACCOUNT_CREATEUSER, ACCOUNT_START_DATE, ACCOUNT_END_DATE, CREATED_TIME, MODIFYED_TIME, ATTACHMENT, STATUS, COMPANY_ID) VALUES 
            (NULL, NULL, '$accountName', '$Forshort', '$CompanyNameStr', '$SUPPLY_USER', '$supplyDate', '$Company', '$RECEIVING_USER', '$receivingDate', '备注', '$Operator', '$dateRes[0]', '$dateRes[1]', '$DateTime', NULL, NULL, '0', $CompanyId);";
            $inAction = mysql_query($inRecod, $link_id);


            if ($inAction) {
                echo "Y";

                $updateRunIDSql = "UPDATE  $DataIn.ch_account set RUN_ID=id where id = ".mysql_insert_id();
                mysql_query($updateRunIDSql, $link_id);

            } else {
                echo "对账单生成失败！";
            }
        }

        break;
    case 'submit':
        //header("Location: ch_shippinglist_account_webservice.php?id=".$id);
        include_once 'ch_shippinglist_account_ws_class.php';

        $ws = new AccountWebservice();

        echo $ws->callSendAccount($id);


        break;
    case 'del':
        include_once( 'ch_shippinglist_account_ws_class.php');

        $ws = new AccountWebservice();
        echo $ws->callDeleteAccount($value);
        break;
}
