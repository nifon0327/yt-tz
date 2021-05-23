<?php
$Th_Col="选项|30|配件|40|序号|30|客户名称|100|业务单号|100|产品名称|100|交期|100|工单流水号|100|采购单号|100|配件ID|40|配件名称|220|工单数量|70|生产数量|70|已送数量|70|可送数量|70|本次送货|70";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
    $i=$i+1;
    $widthArray[]=$wField[$i];
    $tableWidth+=$wField[$i];
}
//if (isSafari6()==1){
$tableWidth=$tableWidth+ceil($Count*1.5)+1;
//}
$nowInfo="当前:半成品生产入库";
$SearchRows="  AND SC.ScQty>0";//生产分类里的ID

if (strlen($tempStuffCname)>1){
    $SearchRows.=" AND (D.StuffCname LIKE '%$StuffCname%' OR D.StuffId='$StuffCname') ";
    $GysList1="<span class='ButtonH_25'  id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'>取消查询</span>";
}
else{

    $shSign = $shSign == ""?1:$shSign;
    $TempShSignSTR="shSignStr".strval($shSign);
    $$TempShSignSTR="selected";
    $shList="<select name='shSign' id='shSign' onchange='ResetPage(4,5)'>";
    $shList.="<option value='1' $shSignStr1>未送货</option>";
    $shList.="<option value='0' $shSignStr0>已送货</option></select>";
    if($shSign=='0'){
        $SearchRows.=" AND SC.Estate = '$shSign'";
    }else{
        $SearchRows.=" AND SC.Estate >='$shSign'";
    }

    $WorkShopList = "";
    $WorkShopResult= mysql_query("SELECT SC.WorkShopId,W.Name AS WorkShopName 
	FROM  $DataIn.yw1_scsheet SC  
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
	WHERE 1 $SearchRows GROUP BY SC.WorkShopId  order by SC.WorkShopId",$link_id);
    if ($WorkShopRow = mysql_fetch_array($WorkShopResult)){
        $WorkShopList="<select name='WorkShopId' id='WorkShopId' style = 'width:100px' onChange='ResetPage(20,5)'>";
        $i=1;
        do{
            $theWorkShopId=$WorkShopRow["WorkShopId"];
            $theWorkShopName=$WorkShopRow["WorkShopName"];
            $WorkShopId=$WorkShopId==""?$theWorkShopId:$WorkShopId;
            if($WorkShopId==$theWorkShopId){
                $WorkShopList.="<option value='$theWorkShopId' selected>$theWorkShopName</option>";
                $SearchRows.=" AND SC.WorkShopId='$theWorkShopId'";
            }
            else{
                $WorkShopList.="<option value='$theWorkShopId'>$theWorkShopName</option>";
            }
            $i++;
        }while($WorkShopRow = mysql_fetch_array($WorkShopResult));
        $WorkShopList.="</select>";
    }


    $DeliveryWeekList="";
    if($shSign==0){
        $DeliveryWeekResult = mysql_query("SELECT G.DeliveryWeek
		 FROM $DataIn.yw1_scsheet SC 
		 INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
		 LEFT JOIN $DataIn.cg1_stocksheet  G  ON G.StockId=SC.mStockId 
		 WHERE 1 $SearchRows  GROUP BY G.DeliveryWeek ORDER BY G.DeliveryWeek DESC",$link_id);
        if($DeliveryWeekRow = mysql_fetch_array($DeliveryWeekResult)){
            $DeliveryWeekList= "<select name='DeliveryWeek' id='DeliveryWeek' onchange='ResetPage(20,5)'>";
            //$DeliveryWeekList.= "<option value='' selected>全部</option>";
            do{
                $DeliveryWeekValue = $DeliveryWeekRow["DeliveryWeek"];

                if($DeliveryWeekValue>0){
                    $week=substr($DeliveryWeekValue, 4,2);
                    $weekName="Week " . $week;
                }else{
                    $weekName ="未设置";
                }
                $DeliveryWeek = $DeliveryWeek==""?$DeliveryWeekValue:$DeliveryWeek;
                if($DeliveryWeek==$DeliveryWeekValue){
                    $DeliveryWeekList.="<option value='$DeliveryWeekValue' selected>$weekName</option>";
                    $SearchRows.=" AND G.DeliveryWeek='$DeliveryWeekValue' ";
                }
                else{
                    $DeliveryWeekList.="<option value='$DeliveryWeekValue'>$weekName</option>";
                }
            }while($DeliveryWeekRow = mysql_fetch_array($DeliveryWeekResult));
            $DeliveryWeekList.= "</select>&nbsp;";
        }
    }

    //客户下拉筛选
    $ForshortSql="SELECT O.Forshort 
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
WHERE 1  $SearchRows  GROUP BY O.Forshort ";
    $ForshortResult = mysql_query($ForshortSql,$link_id);
    if($ForshortRow = mysql_fetch_array($ForshortResult)) {
        $ForshortList .= "<select name='kWorkShopId' id='kWorkShopId' onchange='ResetPage(20,5)'>";
        $ForshortList .= "<option value='' selected>全部客户</option>";
        do{
            $thisForshort=$ForshortRow["Forshort"];
            //$OrderPO=$OrderPO==""?$thisOrderPO:$OrderPO;
            if($kWorkShopId==$thisForshort){
                $ForshortList .= "<option value='$thisForshort' selected>$thisForshort</option>";
                $SearchRows.=" and O.Forshort='$thisForshort' ";
            }
            else{
                $ForshortList .= "<option value='$thisForshort'>$thisForshort</option>";
            }
        }while ($ForshortRow = mysql_fetch_array($ForshortResult));
        $ForshortList .= "</select>&nbsp;";
    }


    //增加业务单号下拉筛选
    $OrderPOList="";
    $clientResult = mysql_query("
           SELECT Y.OrderPO
           FROM $DataIn.yw1_scsheet SC
           LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
           LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
            LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
            LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
           WHERE 1  $SearchRows and Y.OrderPO is not null GROUP BY Y.OrderPO order by Y.OrderPO
           ",$link_id);

    if($clientRow = mysql_fetch_array($clientResult)) {
        $OrderPOList .= "<select name='OrderPO' id='OrderPO' onchange='ResetPage(20,5)'>";
        $OrderPOList .= "<option value='' selected>全部业务单</option>";
        do{
            $thisOrderPO=$clientRow["OrderPO"];
            //$OrderPO=$OrderPO==""?$thisOrderPO:$OrderPO;
            if($OrderPO==$thisOrderPO){
                $OrderPOList .= "<option value='$thisOrderPO' selected>$thisOrderPO</option>";
                $SearchRows.=" and Y.OrderPO='$thisOrderPO' ";
            }
            else{
                $OrderPOList .= "<option value='$thisOrderPO'>$thisOrderPO</option>";
            }
        }while ($clientRow = mysql_fetch_array($clientResult));
        $OrderPOList .= "</select>&nbsp;";
    }

    $GysList1="<input name='StuffCname' type='text' id='StuffCname' size='20' value='配件Id或名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件Id或名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件Id或名称' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询'   onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(4,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}

// $ShipStr="<input class='ButtonH_25' type='button'  id='saveBtn' name='saveBtn' onclick='SaveQty()' value='生成送货单' disabled>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";
$ShipStr="<input class='ButtonH_25' type='button'  id='allBtn' name='allBtn' onclick='MYAll_elects()' value='全选' enabled>&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='notallBtn' name='notallBtn' onclick='MYInstead_elects()' value='反选' enabled>&nbsp;&nbsp;<span class='ButtonH_25' id='saveBtn' name='saveBtn' onclick='SaveQty()' disabled>生成送货单</span>&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";
//步骤5：
include '../basic/loading.php';
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='".($Cols-7)."' height='40px' class=''> $shList $WorkShopList $DeliveryWeekList $ForshortList $OrderPOList $GysList1  </td><td colspan = '4' align='center' class=''>$ShipStr</td> <td colspan='3' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'><tr>";
//输出表格标题
for($i=0;$i<$Count;$i=$i+2){
    $Class_Temp=$i==0?"A1111":"A1101";
    $j=$i;
    $k=$j+1;
    echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
}
echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;

$mySql="SELECT SC.Id,O.Forshort,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.StockId,SC.Remark,
D.StuffId,D.StuffCname,D.Picture,G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,M.mStockId,Y.OrderPO,P.cName
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
WHERE 1  $SearchRows  ORDER BY Y.POrderId desc,D.StuffId ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    do{
        $Id=$myRow["Id"];
        $Forshort=$myRow['Forshort'];
        $POrderId=$myRow["POrderId"];
        $sPOrderId=$myRow["sPOrderId"];
        $StuffId=$myRow["StuffId"];
        $Picture=$myRow["Picture"];
        $StuffCname=$myRow["StuffCname"];
        $PurchaseID=$myRow["PurchaseID"];
        $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
        include "../model/subprogram/stuffimg_model.php";
        include"../model/subprogram/stuff_Property.php";//配件属性
        $Qty=$myRow["Qty"];
        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
        $DeliveryDate=$myRow["DeliveryDate"];
        $DeliveryWeek=$myRow["DeliveryWeek"];
        include "../model/subprogram/deliveryweek_toweek.php";
        $sumQty=$sumQty+$Qty;

        $StockId = $myRow["StockId"];//半成品生产类配件的StockId
        $mStockId= $myRow["mStockId"]; //半成品StockId
        $CheckscQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE  C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId ",$link_id));
        $scQty=$CheckscQty["scQty"];

        //入库数量
        $rkTemp=mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet    WHERE StockId='$mStockId' AND StuffId='$StuffId' AND sPOrderId = '$sPOrderId'",$link_id);
        $rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
        //待检数量
        $gysTemp=mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.gys_shsheet    WHERE StockId='$mStockId' AND StuffId='$StuffId' AND sPOrderId = '$sPOrderId' AND Estate >0 ",$link_id);
        $gysQty=mysql_result($gysTemp,0,"Qty");	//收货总数
        $totalQty = $rkQty + $gysQty;
        if($scQty>$Qty){
            $lastQty = $Qty - $totalQty;
        }else{
            $lastQty = $scQty - $totalQty;
        }


        $UpdateIMG = "";
        $UpdateClick = "";
        $disabled = "";
        if($lastQty > 0){

            //$UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
            $UpdateIMG = $lastQty;
            $UpdateClick=" onclick='showKeyboard(this,$i,$lastQty,$lastQty,\"$sPOrderId@$mStockId\")'";
            //$UpdateSql = "UPDATE $DataIn.yw1_scsheet SET Estate = 1  WHERE sPOrderId ='$sPOrderId'";
            //$UpdateResult = mysql_query($UpdateSql);
        }else{
            $disabled = "disabled";
        }

        if($lastQty ==0)$lastQty="&nbsp;";

        if($totalQty == $Qty){
            $totalQty = "<span class ='greenB'>$totalQty</span>";
            //$UpdateSql = "UPDATE $DataIn.yw1_scsheet SET Estate = 0  WHERE sPOrderId ='$sPOrderId'";
            //$UpdateResult = mysql_query($UpdateSql);
        }else if($totalQty>0){
            $totalQty = "<span class ='yellowB'>$totalQty</span>";
        }else if ($totalQty==0){
            $totalQty = "&nbsp;";
        }

        if($scQty==$Qty){
            $scQty = "<span class ='greenB'>$scQty</span>";
        }else if ($scQty<$Qty){
            $scQty = "<span class ='yellowB'>$scQty</span>";
        }else{
            $scQty = "<span class ='redB'>$scQty</span>";
        }

        $OrderPO=$myRow["OrderPO"];
        $cName=$myRow["cName"];

        //动态读取配件资料
        $showPurchaseorder="[ + ]";
        $ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
        echo"<tr id='CheckRow$i'><td class='A0111' align='center' height='25'><input name='checkId$i' type='checkbox' id='checkId$i' value='$sPOrderId@$mStockId' onclick='checkId(this,$i)' $disabled></td>";
        echo"<td class='A0101' align='center' id='theCel$i'  valign='middle'  onClick='ShowOrHide(ListRow$i,theCel$i,$i,\"$POrderId\",\"$sPOrderId\",\"showScOrder\");' >$showPurchaseorder</td>";
        echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
        echo"<td class='A0101' align='center' >$Forshort</td>";
        echo"<td class='A0101' align='center'>$OrderPO</td>";
        echo"<td class='A0101' align='center'>$cName</td>";
        echo"<td class='A0101' align='center' >$DeliveryWeek</td>";
        echo"<td class='A0101' align='center' >$sPOrderId</td>";
        echo"<td class='A0101' align='center' >$PurchaseID</td>";
        echo"<td class='A0101' align='center' >$StuffId</td>";
        echo"<td class='A0101'>$StuffCname</td>";
        echo"<td class='A0101' align='right'>$Qty</td>";
        echo"<td class='A0101' align='center'>$scQty</td>";
        echo"<td class='A0101' align='center'>$totalQty</td>";
        echo"<td class='A0101' align='center'>$lastQty</td>";
        echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
        echo"</tr>";
        echo $ListRow;
        $i++;
    }while ($myRow = mysql_fetch_array($myResult));
    $cIdNumber = $i -1 ;
    echo "<input type='hidden' id='cIdNumber' name='cIdNumber' value='$cIdNumber'>";
}
else{
    echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'>
	         <div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
}
echo "</table>";

?>
</form>
</body>
</html>
<script src='showkeyboard.js' type=text/javascript></script>
<script src='taskstyle.js' type=text/javascript></script>
<script language="javascript">
    var keyboard=new KeyBoard();
    var tasksboard=new TasksBoard();
    var QtyArray=new Array();
    var IdArray=new Array();
    var eArray=new Array();
    var eImg="<img src='../images/register.png' width='30' height='30'>";

    function showKeyboard(e,index,OrderQty,lastQty,parameter){
        var addQtyFun=function(){
            var ListCheck=document.getElementById("checkId"+index);
            var eStr=parseFloat(e.innerHTML);

            if (eStr>=0){
                ListCheck.checked=true;

            }else{
                ListCheck.checked=false;
            }
            addQty(e,parameter);
        };
        keyboard.show(e,OrderQty,'<=',lastQty,addQtyFun);
    }



    function checkId(e,index){
        var ListCheck=document.getElementById("checkId"+index);
        var CheckRow = document.getElementById("CheckRow"+index);
        if (ListCheck.checked){
            CheckRow.cells[15].innerHTML=parseFloat(CheckRow.cells[14].innerHTML);
            addQty(CheckRow.cells[15],ListCheck.value);
        }
        else{
            CheckRow.cells[15].innerHTML=eImg;
            addQty(CheckRow.cells[15],ListCheck.value);
        }
    }



    function addQty(e,parameter){
        var eStr=parseFloat(e.innerHTML);
        if (eStr>=0){
            m= ArrayPostion(IdArray,parameter);
            if (m>=0){
                QtyArray[m]=eStr;
            }
            else{
                IdArray.unshift(parameter);
                eArray.unshift(e);
                QtyArray.unshift(eStr);
                BtnDisabled(false);
                e.style.color='#F00';
            }
        }
        else{
            m= ArrayPostion(IdArray,parameter);
            if (m>=0){
                IdArray.splice(m,1);
                QtyArray.splice(m,1);
                eArray.splice(m,1);
            }
            e.innerHTML=eImg;
        }
    }


    function ArrayPostion(Arr,Str){
        var backValue=-1;
        var sLen=Arr.length;
        if (sLen>0){
            for (i=0;i<sLen;i++){
                if (Arr[i]==Str){backValue=i;break;}
            }
            BtnDisabled(false);
        }
        return backValue;
    }

    function ArrayClear(){
        IdArray=[];
        QtyArray=[];
        var sLen=eArray.length;
        if (sLen>0){
            for (i=0;i<sLen;i++){eval(eArray[i]).innerHTML=eImg;}
        }
        eArray=[];
        var checkId,ListCheck;
        var cIdNumber=parseInt(document.getElementById("cIdNumber").value);

        for (var i=1;i<=cIdNumber;i++){
            checkId="checkId"+i;
            ListCheck=document.getElementById(checkId);
            if(ListCheck.checked){
                ListCheck.checked = false;
            }
        }
        BtnDisabled(true);
    }

    function BtnDisabled(Flag){
        document.getElementById("saveBtn").disabled=Flag;
        document.getElementById("cancelBtn").disabled=Flag;
    }


    function SaveQty(){
        jQuery('.response').show();
        if (IdArray.length<=0){
            alert ("请先添加送货工单数量！");
            window.location.reload();
            return false;
        }
        BtnDisabled(true);
        var msg = "确定生成送货单?";
        var Ids=IdArray.join("|");
        var Qty=QtyArray.join("|");
        if(confirm(msg)){
            var url="item5_20_ajax.php?Ids="+Ids+"&Qty="+Qty+"&ActionId=1";
            var ajax=InitAjax();
            ajax.open("GET",url,true);
            ajax.onreadystatechange =function(){
                if(ajax.readyState==4){// && ajax.status ==200
                    if(ajax.responseText.trim()=="Y"){//更新成功
                        document.form1.submit();
                        window.location.reload();
                    }
                    else{
                        alert ("生成入库单失败！");
                        document.getElementById("saveBtn").disabled="";
                        window.location.reload();
                    }
                }
            }
            ajax.send(null);
        }
    }

    var showmenuFlag=0 //new
    var showIpad=1;
    function showmenuie5(event){
        event.preventDefault();

        if (showIpad==0) showmenuFlag=2;
        else showmenuFlag=1; //new

        var menu = $("ie5menu");
        var Color = $("ColorSide");

        menu.style.display="block";
        menu.style.visibility ="visible";

        var rightedge=table.offsetWidth-event.clientX;
        var bottomedge=table.offsetHeight-event.clientY;

        if(rightedge<menu.offsetWidth){
            menu.style.left=window.scrollX+event.clientX-menu.offsetWidth + "px";
        }
        else{
            menu.style.left=window.scrollX+event.clientX + "px";
        }
        if(bottomedge<menu.offsetHeight){
            menu.style.top=window.scrollY+event.clientY-menu.offsetHeight + "px";
        }
        else{
            menu.style.top=window.scrollY+event.clientY + "px";
        }
        Color.style.height=menu.offsetHeight + "px";
        return false;
    }

    function hidemenuie5(){
        var menu = $("ie5menu");
        menu.style.display="none";
        menu.style.visibility ="hidden";
        showmenuFlag=0; //new
    }

    function $(objName){

        if(document.getElementById){
            return document.getElementById(objName );
        }
        else
        if(document.layers){
            return eval("document.layers['" + objName +"']");
        }
        else{
            return eval('document.all.' + objName);
        }
    }

    function myover(obj){
        obj.className = "itemshovor";
    }

    function myout(obj){
        obj.className = "menuitems";
    }

    var table = document.getElementById("ListTable");
    table.oncontextmenu=showmenuie5;

    table.onclick=function(){  //new
        if(showmenuFlag==1) {hidemenuie5();}

    };

    function MYAll_elects() {
        jQuery('input[name^="checkId"]:checkbox').each(function() {

            if (jQuery(this).attr("disabled") == "disabled") {
            } else {
                if(this.checked==false){
                    this.checked=true;
                    var index = jQuery(this).attr('id').replace('checkId', '');
                    checkId(this, index);
                }
            }
        });
        // hidemenuie5();
    }
    function All_elects() {
        jQuery('input[name^="checkId"]:checkbox').each(function() {

            if (jQuery(this).attr("disabled") == "disabled") {
            } else {
                if(this.checked==false){
                    this.checked=true;
                    var index = jQuery(this).attr('id').replace('checkId', '');
                    checkId(this, index);
                }
            }
        });
        hidemenuie5();
    }
    function MYInstead_elects() {
        jQuery('input[name^="checkId"]:checkbox').each(function() {

            if (jQuery(this).attr("disabled") == "disabled") {
            } else {

                if(this.checked==false){
                    this.checked=true;
                } else {
                    this.checked=false;
                }
                //jQuery(this).attr("checked", false);
                var index = jQuery(this).attr('id').replace('checkId', '');
                checkId(this, index);
            }
        });
        // hidemenuie5();
    }
    function Instead_elects() {
        jQuery('input[name^="checkId"]:checkbox').each(function() {

            if (jQuery(this).attr("disabled") == "disabled") {
            } else {

                if(this.checked==false){
                    this.checked=true;
                } else {
                    this.checked=false;
                }
                //jQuery(this).attr("checked", false);
                var index = jQuery(this).attr('id').replace('checkId', '');
                checkId(this, index);
            }
        });
        hidemenuie5();
    }
</script>