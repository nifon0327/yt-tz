<?php
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新订单资料");//需处理
$fromWebPage=$funFrom."_read";      
$nowWebPage =$funFrom."_spec";    
$toWebPage  =$funFrom."_specUpdated";   
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：



$upData =mysql_fetch_array(mysql_query("SELECT M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Operator,C.Forshort
                                        FROM $DataIn.ch0_shipmain M
                                        INNER JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
                                        WHERE M.Id='$Id' ORDER BY M.Id LIMIt 1",$link_id));

$upCompanyId=$upData["Forshort"];
$invoiceNo=$upData["InvoiceNO"];

$noteContentSql = "SELECT note From $DataIn.ch13_othernote WHERE shipId='$Id' and type='emu'";
echo $noteContentSql;
$noteResult = mysql_query($noteContentSql);
$noteRow = mysql_fetch_assoc($noteResult);
$spec = $noteRow['note'];

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,POrderId,$POrderId";
//echo "<input type = 'hidden' name='CompanyId' id='CompanyId' value = '$upCompanyId'>";
echo "<input type = 'hidden' name='ShipId' id='ShipId' value = '$Id'>";
echo "<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'><tr><td class='A0011'>
        <table width='750' height='143' border='0' cellspacing='5'>
            <tr>
                <td width='150' height='18' align='right' valign='top' scope='col'>客户名称</td>
                <td valign='middle' scope='col'>$upCompanyId</td>
            </tr>
            <tr>
                <td width='150' height='18' align='right' valign='top' scope='col'>Invoice</td>
                <td valign='middle' scope='col'>$invoiceNo</td>
            </tr>
            <tr>
                <td align='right'>指定内容</td>
                <td><input type='spec' name='spec' style='width: 380px;' id='spec' value='$spec'>
                </input></td>
            </tr>
        </table>
    </table>";

include "../model/subprogram/add_model_b.php";
?>