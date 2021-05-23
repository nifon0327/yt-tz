<?php
$Th_Col="选项|30|配件|30|序号|30|客户名称|80|交期|100|业务单号|120|订单流水号|100|产品名称|150|订单数量|60|生产数量|60|质检审核|100|已确认入库|60|未确认入库|60|订单备注|100|操作|100";

$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
if (strlen($tempcName)>0){
    $SearchRows.=" AND P.cName LIKE '%$tempcName%' ";
    $searchList1="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消' onclick='ResetPage(1,1)'/>";
}
else{
    $searchList1="<input type='text' name='tempcName' id='tempcName' value='' width='20'/> &nbsp;<span class='ButtonH_25' id='okQuery' onclick='ResetPage(1,1)'>查询</span>";
}



include '../basic/loading.php';
echo "<div id='winDialog' style='position:fixed;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;' onDblClick='closeWinDialog()'></div>
";
echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;position:absolute;left:30px;top:40px'>
	<tr style='position:fixed;left:30px;top:0;width:100%;background-color: #F2F3F5;'>
	<td colspan='".($Cols-6)."' height='40px' class=''>$ForshortList &nbsp; $WorkShopList &nbsp; $dataList &nbsp; $OrderPOList &nbsp;$TypeList  &nbsp;$EstateList &nbsp;  <span class='ButtonH_25' id='batchPassbutton' onclick='batchPassRkdata(this)'>入库确认</span> <span class='ButtonH_26' id='batchPassbutton' onclick='batchPassQC(0)'>合格</span><span class='ButtonH_27' id='batchPassbutton' onclick='batchPassQC(3)'>不合格</span></td><td colspan='4' align='left' class=''>$searchList1</td><td colspan='2' class=''><input name='NowInfo' style=\"width:160px;\" type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
echo "<tr style='position:fixed;left:30px;top:40px;'>";
for($i=0;$i<$Count;$i=$i+2){
    $Class_Temp=$i==0?"A1111":"A1101";
    $j=$i;
    $k=$j+1;
    echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
}
echo"</tr>";
//输出表格标题
echo "<tr>";
for($i=0;$i<$Count;$i=$i+2){
    $Class_Temp=$i==0?"A1111":"A1101";
    $j=$i;
    $k=$j+1;
    echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
}
echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;

$mySql="SELECT O.Forshort,Y.POrderId,Y.OrderPO,Y.Qty AS OrderQty,SUM(S.Qty) AS Qty,S.StockId,
    P.ProductId,P.cName,P.eCode,P.TestStandard,P.pRemark,S.Estate,
    U.Name AS Unit,S.Date,
    PI.Leadtime,PI.Leadweek
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate <> 0  $SearchRows AND G.Level = 1 GROUP BY S.StockId";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    do{
        $Id=$myRow["Id"];
        $Forshort=$myRow['Forshort'];
        $POrderId=$myRow["POrderId"];
        $ProductId=$myRow["ProductId"];
        $OrderPO=toSpace($myRow["OrderPO"]);
        $cName=$myRow["cName"];
        $eCode=toSpace($myRow["eCode"]);
        $TestStandard=$myRow["TestStandard"];
        $Estate = $myRow["Estate"];
        include "../admin/Productimage/getProductImage.php";
        $WorkShopName=$myRow["WorkShopName"];

        $pDate = $myRow["Date"];
        $Qty=$myRow["Qty"];
        $OrderQty=$myRow["OrderQty"];
        $pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
        $OrderDate=$myRow["OrderDate"];
        $Leadtime=$myRow["Leadtime"];
        $Leadweek=$myRow["Leadweek"];
        include "../model/subprogram/PI_Leadweek.php";
        $StockId =$myRow["StockId"];

        $Estate = $myRow['Estate'];

        if ($chooseDate != 'all'){
            $thisDate = "AND DATE_FORMAT(C.Date,'%Y-%m-%d') = '$chooseDate'";
        }else{
            $thisDate ="";
        }


        $CheckscQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE  C.POrderId = '$POrderId' AND C.StockId = $StockId 
		AND C.Estate = 0 $thisDate ",$link_id));
        $scQty0=$CheckscQty["scQty"]; //已确认数

        $CheckscQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE  C.POrderId = '$POrderId' AND C.StockId = $StockId 
		AND C.Estate in (1,2,3)  $thisDate ",$link_id));
        $scQty1=$CheckscQty["scQty"]; //未确认数

        $UpdateIMG = "";
        $UpdateClick ="";
        $CheckData="<input type='checkbox' disabled />";
        if($SubAction==31  && $scQty1 >0 ){
            $UpdateIMG="<img src='../images/register.png' width='30' height='30' onclick=\"passRkdata(this, $POrderId, $StockId, 1)\" >";
            $UpdateClick="";
            $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$POrderId|$StockId|$pDate' />";

        }
        if($scQty1==0 && $scQty0>0){
            $UpdateIMG = "<span class ='blueB'>已确认</span>";
            $UpdateClick ="";
            $CheckData="<input type='checkbox' disabled />";
        }

        //动态读取配件资料
        $showPurchaseorder="[ + ]";
        $ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";

        echo"<tr><td class='A0111' align='center'>$CheckData</td><td class='A0101' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId);' >$showPurchaseorder</td>";
        echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
        echo"<td class='A0101' align='center' onclick='Elects($i)'>$Forshort</td>";
        echo"<td class='A0101' align='center' onclick='Elects($i)'>$Leadweek</td>";
        echo"<td class='A0101' align='center' onclick='Elects($i)'>$OrderPO</td>";
        echo"<td class='A0101' align='center' onclick='Elects($i)'>$POrderId</td>";
        echo"<td class='A0101' onclick='Elects($i)'>$TestStandard</td>";
        echo"<td class='A0101' align='right' onclick='Elects($i)'>$OrderQty</td>";
        echo"<td class='A0101' align='right' onclick='Elects($i)'>$Qty</td>";

        if ($Estate == 3){
            echo"<td class='A0101' align='center' style='color: #9A0000'>不合格</td>";
        }elseif ($Estate == 2){
            echo"<td class='A0101' align='center' style='color: #00AA00'>合格</td>";
        }
        else if ($Estate == 1) {
            echo"<td class='A0101' align='center' style='color: #9A0000'>未审核</td>";
        }

        if($scQty0>0){
            $scQty0 = "<span class ='blueB'>$scQty0</span>";
        }
        else if ($scQty0 == 0 )$scQty0 = "";
        echo"<td class='A0101' align='right'>$scQty0</td>";
        if($scQty1>0){
            $scQty1 = "<span class ='yellowB'>$scQty1</span>";
        }
        else if ($scQty1 == 0 )$scQty1 = "&nbsp;";
        echo"<td class='A0101' align='right'>$scQty1</td>";
        echo"<td class='A0101' >$pRemark</td>";
        if ($Estate == 1 || $Estate == 3){
            echo"<td class='A0101' align='center' ></td>";
        }elseif ($Estate == 2){
            echo"<td class='A0101' align='center' id='updateTd$i' >  &nbsp; &nbsp; $UpdateIMG</td>";
            //<img src='../images/printer.png' width='30' height='30' onclick=\"showPrintCodeWin($POrderId)\">
        }

        echo"</tr>";
        echo $ListRow;
        $i++;
    }while ($myRow = mysql_fetch_array($myResult));
}
else{
    echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
}
echo "</table>";
echo"<DIV id='TotalStatusBar' style='display:none;'>&nbsp;</div>";
?>
<script>
    function Elects(e) {
        jQuery('input[name=checkId'+e+']:checkbox').each(function () {

            if (jQuery(this).attr("disabled") == "disabled") {
            } else {
                if (this.checked == false) {
                    this.checked = true;
                    
                }else {
                    this.checked = false;
                }
                chooseRow();
            }
        });
    }

</script>
