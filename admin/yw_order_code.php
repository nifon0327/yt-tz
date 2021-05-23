<?php
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新订单资料");//需处理
$fromWebPage=$funFrom."_read";      
$nowWebPage =$funFrom."_update";    
$toWebPage  =$funFrom."_codeUpdated";   
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：

$upData =mysql_fetch_array(mysql_query("SELECT A.POrderId, B.CompanyId, P.cName,P.ProductId FROM $DataIn.yw1_ordersheet A
                                        INNER JOIN $DataIn.yw1_ordermain B ON B.OrderNumber = A.OrderNumber
                                        INNER JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
                                        WHERE A.Id='$Id' ORDER BY A.Id LIMIt 1",$link_id));
$upCompanyId=$upData["CompanyId"];
$POrderId=$upData["POrderId"];
$cName=$upData["cName"];
$ProductId = $upData['ProductId'];

//获取itf和lotto码--对应companyId = 100024/1004/1059
$hasProductParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' and Estate = 1 Order by Id Limit 1";
$hasProductParameterResult = mysql_query($hasProductParameterSql);
$hasProductParameterRow = mysql_fetch_assoc($hasProductParameterResult);
if($hasProductParameterRow){
    $lotto = $hasProductParameterRow["Lotto"];
    $itf = $hasProductParameterRow["itf"];
}else{
    $hasProductParameterSql = "Select * From $DataIn.productprintparameter Where productId = '$ProductId' and Estate = 1 Order by Id Limit 1";
    $hasProductParameterResult = mysql_query($hasProductParameterSql);
    $hasProductParameterRow = mysql_fetch_assoc($hasProductParameterResult);
    if($hasProductParameterRow){
        $lotto = $hasProductParameterRow["Lotto"];
        $itf = $hasProductParameterRow["itf"];
    }
}

if($lotto != ''){
    $lottoColor = "class='redB'";
}
else{
    if($upCompanyId == '100024'){
        $lotto = "ART01";
    }
    else if($CompanyId == '2668'){
        $lotto = "LOP01";
    }
    else{
        $lotto = "ASH01";
    }
}

if($itf != ''){
     $itfColor = "class='redB'";
}else{
     $itf = "4";
}

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,POrderId,$POrderId";
echo "<input type = 'hidden' name='CompanyId' id='CompanyId' value = '$upCompanyId'>";
echo "<input type = 'hidden' name='POrderId' id='POrderId' value = '$POrderId'>";
echo "<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'><tr><td class='A0011'>
        <table width='750' height='143' border='0' cellspacing='5'>
            <tr>
                <td width='150' height='18' align='right' valign='top' scope='col'>产品名称</td>
                <td valign='middle' scope='col'>$cName</td>
            </tr>
            <tr>
                <td width='150' height='18' align='right' valign='top' scope='col'>订单流水号</td>
                <td valign='middle' scope='col'>$POrderId</td>
            </tr>
            <tr>
                <td align='right'>lotto码</td>
                <td><input $lottoColor type='text' name='lotto' style='width: 380px;' id='lotto' value='$lotto'>
                </input></td>
            </tr>
            <tr>
                <td align='right'>itf码</td>
                <td><input $itfColor type='text' name='itf' style='width: 380px;' id='itf' value='$itf'>
            </input></td>
            </tr>
        </table>
    </table>";

include "../model/subprogram/add_model_b.php";
?>