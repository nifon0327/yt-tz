<?php
/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2019/1/25
 * Time: 10:41
 */
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新提货单记录");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_update";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT M.*,C.Forshort
FROM $DataIn.ch_account M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
WHERE M.Id=$Mid",$link_id));
$DeliveryNumber=$upData["DeliveryNumber"];
$Id = $Row['id'];
$startValue = $Row['ACCOUNT_START_DATE'];
$endValue = $Row['ACCOUNT_END_DATE'];
$clientValue = $Row['COMPANY_ID'];
$Forshort = $Row['Forshort'];
$caozuoren = $Row['ACCOUNT_CREATEUSER'];
$tim = $Row['CTEATED_TIME'];