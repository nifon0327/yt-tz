
<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once($path.'/factoryCheck/checkSkip.php');
//电信-zxq 2012-08-01
$Th_Col="日期|70|送货单|80|供应商|80|配件ID|50|配件名称|250|单位|30|操作|38|序号|40|订单PO|100|采购总数|60|本次入库|60|出库数量|60|客户|80|包装编号|60|库位编号|60|修改|40|打印|40|本次总入库|60|打印|40";
//$Th_Col="日期|70|送货单|80|供应商|80|配件ID|50|配件名称|250|单位|30|操作|38|序号|40|订单PO|100|采购总数|60|本次入库|60|出库数量|60|需求单流水号|100|客户|80|包装编号|60|库位编号|60|修改|40|本次总入库|60|打印|40";
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
$SearchRows="";
//供应商过滤
$GysList="";
$nowInfo="当前:物料入库数据";
$funFrom="item5_2";
$addWebPage=$funFrom . "_add.php";
if (strlen($tempStuffCname)>1){
    $SearchRows.=" AND (D.StuffCname LIKE '%$StuffCname%' OR D.StuffId='$StuffCname') ";
    $GysList1="<span class='ButtonH_25' id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'>取消查询</span>";
}
else{

    $khSql = mysql_query("SELECT C.Forshort, C.CompanyId
FROM $DataIn.ck1_rksheet S
RIGHT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId 
WHERE 1 
GROUP BY C.Forshort, C.CompanyId
ORDER BY C.CompanyId ",$link_id);

    if($khSqlRow = mysql_fetch_array($khSql)){
        $GysList= "<select name='khCompanyId' id='khCompanyId' onchange='ResetPage(0,5)'>";
        //$GysList.="<option value='' selected>全部</option>";
        do{

            $khForshort=$khSqlRow["Forshort"];
            $khthisCompanyId=$khSqlRow["CompanyId"];
            $khCompanyId=$khCompanyId==""?$khthisCompanyId:$khCompanyId;
            if ($khForshort == ""){

            }elseif ($khCompanyId == $khthisCompanyId){
                $GysList.="<option value='$khthisCompanyId' selected>$khForshort </option>";
                $SearchRows.=" and C.CompanyId='$khthisCompanyId'";
                $khCompanyId = $khthisCompanyId;
            }
            else{
                $GysList.="<option value='$khthisCompanyId'>$khForshort</option>";
            }
        }while ($khSqlRow = mysql_fetch_array($khSql));
        $GysList.="</select>&nbsp;";
    }


    $date_Result = mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month FROM $DataIn.ck1_rksheet S 
 LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId 
WHERE 1 $SearchRows GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY DATE_FORMAT(Date,'%Y-%m') DESC",$link_id);

    if($dateRow = mysql_fetch_array($date_Result)) {
        $GysList.="<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(1,5)'>";
        do{
            $monthValue=$dateRow["Month"];
            $chooseMonth=$chooseMonth==""?$monthValue:$chooseMonth;
            if($chooseMonth==$monthValue){
                $GysList.="<option value='$monthValue' selected>$monthValue</option>";
                $SearchRows.=" and DATE_FORMAT(M.Date,'%Y-%m') = '$monthValue'";
            }
            else{
                $GysList.="<option value='$monthValue'>$monthValue</option>";
            }
        }while($dateRow = mysql_fetch_array($date_Result));
        $GysList.="</select>&nbsp;";
    }
//项目客户


    $providerSql = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.ck1_rksheet S
LEFT JOIN  $DataIn.ck1_rkmain M ON S.Mid=M.Id
LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId 
 LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId
WHERE 1 $SearchRows GROUP BY M.CompanyId ORDER BY P.Letter desc ",$link_id);

    if($providerRow = mysql_fetch_array($providerSql)){
        $GysList.= "<select name='CompanyId' id='CompanyId' onchange='ResetPage(2,5)'>";
        //$GysList.="<option value='' selected>全部</option>";
        do{
            $Letter=$providerRow["Letter"];
            $Forshort=$providerRow["Forshort"];
            $Forshort=$Letter.'-'.$Forshort;
            $thisCompanyId=$providerRow["CompanyId"];
            $CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
            /* if ($CompanyId == "" || $CompanyId == NULL){
                 $GysList.="<option value='$thisCompanyId' selected>$Forshort </option>";
                 $SearchRows.=" and M.CompanyId='$thisCompanyId'";
                 $CompanyId = $thisCompanyId;
             }else*/if($CompanyId==$thisCompanyId){
                $GysList.="<option value='$thisCompanyId' selected>$Forshort </option>";
                $SearchRows.=" and M.CompanyId='$thisCompanyId'";
                $selFlag=1;
            }
            else{
                $GysList.="<option value='$thisCompanyId'>$Forshort</option>";
            }
        }while ($providerRow = mysql_fetch_array($providerSql));

        $GysList.="</select>&nbsp;";
    }
    $GysList1="<input name='StuffCname' type='text' id='StuffCname' size='16' value='配件Id或名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件Id或名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件Id或名称' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询'   onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(4,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}
//有权限
$addBtnDisabled=$SubAction==31?"":"disabled";
//$GysList2="<input class='ButtonH_25' type='button'  id='addBtn' value='新 增'  onclick=\"openWinDialog(this,'$addWebPage',880,560,'center')\" $addBtnDisabled/>";
//开送货单品捡入库，不能直接入库


//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td  width='60%' height='40px' class=''>$GysList &nbsp;&nbsp;&nbsp;&nbsp; $GysList1</td><td width='20%'  class=''>$GysList2</td><td width='20%'  align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
//输出表格标题
for($i=0;$i<$Count;$i=$i+2){
    $Class_Temp=$i==0?"A1111":"A1101";
    $j=$i;
    $k=$j+1;
    echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
}
//echo"</tr></table>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT M.BillNumber,M.Date,M.Remark,
S.Id,S.Mid,S.gys_Id,S.StockId,S.StuffId,S.Qty,S.Locks,S.llQty AS outQty,S.llSign,S.Type AS RkType,S.TaskId AS STaskId, 
D.SeatId,D.StuffCname,D.TypeId,D.Picture,P.Forshort,
G.POrderId,G.FactualQty+G.AddQty AS cgQty,MP.Remark AS Position,U.Name AS UnitName,
Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,
PI.Leadtime,C.Forshort AS Client,CP.cName,CP.TestStandard,W.LocationId,W.TaskId  
FROM $DataIn.ck1_rksheet S
LEFT JOIN $DataIn.ck1_rkmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stufftype T ON T.Id=D.TypeId
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.base_mposition MP ON MP.Id=D.SendFloor  
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId 
LEFT JOIN $DataIn.wms_taskin W ON S.StockId=W.StockId
WHERE 1 $SearchRows group by Y.OrderPO ORDER BY M.Date DESC,M.Id DESC";
//echo $mySql;
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
    $tbDefalut=0;
    $midDefault="";
    $midStuffId=""; //配件id
    do{
        $m=1;
        //主单信息
        $Mid=$mainRows["Mid"];
        $Date=$mainRows["Date"];

        /******************验厂过滤********************/
        $groupLeaderSql = "SELECT GroupLeader From $DataIn.staffgroup WHERE GroupId = 701 ";
        $groupLeaderResult = mysql_query($groupLeaderSql);
        $groupLeaderRow = mysql_fetch_assoc($groupLeaderResult);
        $Leader = $groupLeaderRow['GroupLeader'];
        $skip = false;
        if($FactoryCheck == 'on' and skipData($Leader, $Date, $DataIn, $DataPublic, $link_id)){
            continue;
        }else if($FactoryCheck == 'on'){
            $Date = substr($Date, 0, 10);
        }
        /***************************************/

        $Date="<font color='blue'>$Date</font>";
        $BillNumber=$mainRows["BillNumber"];
        $gys_Id=$mainRows["gys_Id"];
        $Remark=$mainRows["Remark"]==""?"":"title='$Remark'";
        //检查是否存在文件

        if($BillNumber!=""){
            $FilePath1="../download/deliverybill/$BillNumber.jpg";
            if(file_exists($FilePath1)){
                $BillNumber="<a href='$FilePath1' target='_blank'>$BillNumber</a>";
            }
            else {
                if(is_numeric($BillNumber)){
                    $GysSql=mysql_query("SELECT Id,GysNumber FROM $DataIn.gys_shmain 
							WHERE BillNumber=$BillNumber",$link_id);
                    if(mysql_num_rows($GysSql) > 0)
                    {
                        $GysMid=mysql_result($GysSql,0,"Id");
                        $GysNumber=mysql_result($GysSql,0,"GysNumber");
                    }
                    $MidSTR=anmaIn($GysMid,$SinkOrder,$motherSTR);
                    $BillNumber="<a href='../supplier/shorder_view.php?f=$MidSTR' target='_blank'>$GysNumber</a>";
                }
            }
        }

        $Forshort=$mainRows["Forshort"];
        $Name=$mainRows["Name"];
        $upMian="onclick='showRkWin($Mid,2)'";
        //明细资料
        $StuffId=$mainRows["StuffId"];
        if($StuffId!=""){
            $checkidValue=$mainRows["Id"];
            $checkidValuesAll = $mainRows[""];
            $StuffCname=$mainRows["StuffCname"];
            $SeatId = $mainRows['SeatId'];
            $STaskId = $mainRows['STaskId'];
            $LocationId = $mainRows["LocationId"];
            $Qty=$mainRows["Qty"];
            $cgQty=$mainRows["cgQty"];
            $outQty=$mainRows["outQty"];
            $RkType=$mainRows["RkType"];
            $StockId=$mainRows["StockId"];
            $Locks=$mainRows["Locks"];
            $Picture=$mainRows["Picture"];
            $TypeId=$mainRows["TypeId"];
            $UnitName=$mainRows["UnitName"];
            $Position=$mainRows["Position"]==""?"未设置":$mainRows["Position"];
            $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
            //检查是否有图片
            include "../model/subprogram/stuffimg_model.php";
            include"../model/subprogram/stuff_Property.php";//配件属性

            if(strlen($StockId)>10 && $RkType == 1){

                //子配件的采购zong'shu
                $checkComboxResult = mysql_query("SELECT (AddQty+FactualQty) AS OrderQty FROM $DataIn.cg1_stuffcombox WHERE StockId = $StockId",$link_id);
                if($checkComboxRow = mysql_fetch_array($checkComboxResult)){
                    $cgQty  =  $checkComboxRow["OrderQty"];

                }


                $rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
                $rkQty=mysql_result($rkTemp,0,"Qty");
                $rkQty=$rkQty==""?0:$rkQty;
                if($rkQty==$cgQty){
                    $rkBgColor="class='greenB'";
                }
                else{
                    $rkBgColor="class='redB'";
                }


                $POrderId=$mainRows["POrderId"];
                $ProductId=$mainRows["ProductId"];
                $OrderPO=$mainRows["OrderPO"];
                $PQty=$mainRows["PQty"];
                $PackRemark=$mainRows["PackRemark"];
                $sgRemark=$mainRows["sgRemark"];
                $ShipType=$mainRows["ShipType"];
                $Leadtime=$mainRows["Leadtime"];
                $cName=$mainRows["cName"];
                $Client=$mainRows["Client"];
                $TestStandard=$mainRows["TestStandard"];
                include "../admin/Productimage/getPOrderImage.php";

                $showPurchaseorder="[ + ]";
                $ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='11'><br>&nbsp;<span class='redB'>订单PO：</span>$OrderPO&nbsp;&nbsp;<span class='redB'>业务单流水号：</span>$POrderId ($Client : $TestStandard)&nbsp;&nbsp;<span class='redB'>数量：</span>$PQty &nbsp; &nbsp;<span class='redB'>订单备注：</span>$PackRemark &nbsp;&nbsp;<span class='redB'>出货方式：</span>$ShipType &nbsp;&nbsp;<span class='redB'>生管备注：</span>$sgRemark &nbsp;&nbsp;<span class='redB'>PI交期：</span>$Leadtime<br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
            }else{

                $showPurchaseorder = "";
            }
            //检查权限
            $UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
            if($SubAction==31){//有权限
                $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";$UpdateClick="onclick='showRkWin($checkidValue,1)'";
            }
            else{//无权限
                if($SubAction==1){
                    $UpdateClick="";
                    $UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
                }
            }
            if($RkType == 2 ){

                $UpdateIMG="&nbsp;";
                $UpdateClick="&nbsp;";
            }

            if($outQty == $Qty){
                $outBgColor  ="class='greenB'";
                $UpdateIMG="&nbsp;";
                $UpdateClick="&nbsp;";

            }else if($outQty>$Qty){
                $outBgColor  ="class='redB'";
            }else{
                $outBgColor  ="class='yellowB'";
            }
            if($outQty ==0 )$outQty="&nbsp;";

            $Sid=anmaIn($StockId,$SinkOrder,$motherSTR);

            if ($i==1) {
                $midDefault=$Mid;
                $midStuffId = $StuffId;
            }

            //if($tbDefalut==0 && $midDefault==""){//首行
            if($i==1 || $midDefault != $Mid){ //首行 或者 送货单变化

                if ($i > 1) { // 同一个送货单的上一个配件统计
                    $m = 35;
                    echo "<td class='A0101'  width='$Field[$m]' align='center'>$zQty</td>";//总数量

                    $m = $m + 2;
                    echo "<td class='A0101'  width='$Field[$m]' height='20' align='center' onclick='showPrintCodeWin($midStuffId,1,$zQty,$midDefault)' ><img src='../images/printer.png' width='30' height='30'></td>";//更新
                    $zQty=0;
                    $m = 1;
                    //新行开始
                    echo"</td></tr></table>";
                    echo"</td></tr></table>";//结束上一个表格
                }

                //并行列
                echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
                echo"<td scope='col' class='A0111' width='$Field[$m]' align='center' $upMian>$Date</td>";//更新
                $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $Remark>$BillNumber</td>";		//下单备注
                $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]'>$Forshort</td>";		//供应商
                $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
                //echo"<td width='$unitWidth' class='A0101'>";
                echo"<td width='' class='A0101'>";
            }


            if($i==1 || $midDefault != $Mid || $midStuffId != $StuffId){ //首行 或者 送货单变化

                if ($midStuffId != $StuffId && $midDefault == $Mid )
                {
                    //    // 上一个送货单的最后一个配件统计
                    $m = 35;
                    echo "<td class='A0101'  width='$Field[$m]' align='center'>$zQty</td>";//总数量

                    $m = $m + 2;
                    echo "<td class='A0101'  width='$Field[$m]' height='20' align='center' onclick='showPrintCodeWin($midStuffId,1,$zQty,$midDefault)' ><img src='../images/printer.png' width='30' height='30'></td>";//更新
                    $zQty=0;
                }

                if ($i > 1 && $midDefault == $Mid) {
                    //新行开始
                    echo"</td></tr></table>";//结束上一个表格
                }



                $m=7;
                echo"<table width='100%' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
                echo"<tr>";
                $unitFirst=$Field[$m]-1;
                echo"<td class='A0101' width='$unitFirst' align='center'>$StuffId</td>";	//配件ID
                $m=$m+2;
                echo"<td class='A0101' width='$Field[$m]'>$StuffCname</td>";	//配件
                $m=$m+2;
                echo"<td class='A0101' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
                $m=$m+2;
                echo"<td width='' class='A0101'>";
            }

            $m=13;

            //合并 配件id
            echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
            echo"<tr>";
            $unitFirst=$Field[$m]-1;

            echo"<td class='A0001' width='$unitFirst' align='center' id='theCel$i' onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId);'>$showPurchaseorder</td>";			//序号
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$OrderPO</td>";			//PO
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='right'>$cgQty</td>";		//需求数量
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$Qty</div></td>";		//入库数量
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='right'><div $outBgColor>$outQty</div></td>";	//出库数量
//            $m=$m+2;
//            echo"<td  class='A0001' width='$Field[$m]' align='center'><a href='../public/ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a></td>";//需求流水号
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$Client</td>";	//客户
            $m=$m+2;
            //echo"<td class='A0001' width='$Field[$m]' align='center'>$Position</td>";	//配件存储位置
            echo"<td class='A0001' width='$Field[$m]' align='center'>$STaskId</td>";	//包装编号
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$SeatId</td>";	//库位编号
            $m=$m+2;
            echo"<td class='A0001'  width='$Field[$m]' height='20' align='center' $UpdateClick>$UpdateIMG</td>";//更新
            $m=$m+2;
            echo"<td class='A0000'  width='' height='20' align='center' > </td>";//更新
            //echo"<td class='A0000'  width='' height='20' align='center' onclick='showPrintCodeWin($checkidValue,2)' ><img src='../images/printer.png' width='30' height='30'></td>";//更新

            echo"</tr>$ListRow</table>";
            $zQty = $zQty+$Qty;
            $i++;
            $j++;

            $midDefault=$Mid;
            $midStuffId = $StuffId;
        }
    }while($mainRows = mysql_fetch_array($mainResult));
        // 最后一个配件统计
        $m = 35;
        echo "<td class='A0101'  width='$Field[$m]' align='center'>$zQty</td>";//总数量

        $m = $m + 2;
        echo "<td class='A0101'  width='$Field[$m]' height='20' align='center' onclick='showPrintCodeWin($midStuffId,1,$zQty,$midDefault)' ><img src='../images/printer.png' width='30' height='30'></td>";//更新
        $zQty=0;
    echo"</tr></table>";
}
else{
    echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
}
echo"</td></tr></table>";//结束上一个表格
echo "</td></tr></table>";
?>

</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);width: 200%"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script>
    function CnameChanged(e){
        var StuffCname=e.value;
        if (StuffCname.length>=1){
            e.style.color='#000';
            document.getElementById("stuffQuery").disabled=false;
        }
        else{
            e.style.color='#DDD';
            document.getElementById("stuffQuery").disabled=true;
        }
    }

    function showPrintCodeWin(Id,type,zQty,Mid) {
        document.getElementById("divShadow").innerHTML="";
        switch(type){
            case 1: //总量
                var url="item5_2_print.php?Id="+Id+"&type="+type+"&zQty="+zQty+"&MId="+Mid;
                break;
            case 2:
                var url="item5_2_print.php?Id="+Id+"&type="+type;
                break;

        }

        var ajax=InitAjax();
        ajax.open("GET",url,true);
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4 && ajax.status ==200){
                document.getElementById("divShadow").innerHTML=ajax.responseText;
            }
        }
        ajax.send(null);
        //定位对话框
        divShadow.style.left = window.pageXOffset+(window.innerWidth-440)/2+"px";
        divShadow.style.top = window.pageYOffset+(window.innerHeight-300)/2+"px";
        document.getElementById('divPageMask').style.display='block';
        document.getElementById('divShadow').style.display='block';
        document.getElementById('divPageMask').style.width = document.body.scrollWidth;
        document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
    }

    function toPrint() {
        var oldstr = document.body.innerHTML;
        var bdhtml = window.document.body.innerHTML;
        var sprnstr = "<!--startprint-->";
        var eprnstr = "<!--endprint-->";
        var headstr = "<html><head><title></title></head><body>";  //打印头部
        var footstr = "</body></html>";  //打印尾部
        var prnhtml = bdhtml.substr(bdhtml.indexOf(sprnstr) + 17);
        prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr));
        window.document.body.innerHTML = headstr + prnhtml + footstr;
        window.print();
        document.body.innerHTML = oldstr;
        document.getElementById('divShadow').style.display='none';
        document.getElementById('divPageMask').style.display='none';
        //parent.location.reload();
        document.form1.submit();
        return false;
    }

    function showRkWin(Id,Flag){
        document.getElementById("divShadow").innerHTML="";
        switch(Flag){
            case 1:
                var url="item5_Rkdata.php?Id="+Id;
                break;
            case 2:
                var url="item5_Rkmain.php?Mid="+Id;
                break;
            default:
                return false;
                break;
        }
        var ajax=InitAjax();
        ajax.open("GET",url,true);
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4 && ajax.status ==200){
                document.getElementById("divShadow").innerHTML=ajax.responseText;
            }
        }
        ajax.send(null);
        //定位对话框
        divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
        divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
        document.getElementById('divPageMask').style.display='block';
        document.getElementById('divShadow').style.display='block';
        document.getElementById('divPageMask').style.width = document.body.scrollWidth;
        document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
    }

    //更新物料入库数据
    function UpdateRk(Id,Qty,tStockQty,MantissaQty,outQty){
        //后台保存
        var Message="";
        var Operators=parseInt(document.getElementById("Operators").value);
        var changeQty=parseInt(document.getElementById("changeQty").value);

        var laseQty = parseInt(Qty) - parseInt(outQty);

        if(changeQty=="" || changeQty==0){
            Message="不是规范或不允许的值！";
        }
        else{
            if(Operators>0){//增加数量:不可大于未收数量或0或非法数字
                if(changeQty>MantissaQty){
                    Message="超出未收货数量的范围!";
                }
            }
            else{			//减少数量：不可大于在库数量,或大于等于本次入库的数量
                //库存小于剩余出库数量， 只能减少库存数
                if(laseQty>tStockQty){
                    laseQty = tStockQty;
                }

                if(changeQty>=laseQty){
                    Message="只能减少的数量应小于:"+laseQty;
                }
            }
        }

        if(Message!=""){
            alert(Message);
            document.getElementById("changeQty").value = laseQty;
            return false;
        }
        else{
            var url="item5_2_ajax.php?Id="+Id+"&changeQty="+changeQty+"&Operators="+Operators+"&ActionId=21";
            var ajax=InitAjax();
            ajax.open("GET",url,true);
            ajax.onreadystatechange =function(){
                if(ajax.readyState==4){// && ajax.status ==200
                    alert(ajax.responseText);
                    if(ajax.responseText=="Y"){//更新成功
                        document.form1.submit();
                    }
                }
            }
            ajax.send(null);
        }
    }
    //更新送货单数据
    function UpdateRkmain(Mid){
        var rkdate=document.getElementById("Date").value;
        var BillNumber=document.getElementById("BillNumber").value;
        var Remark=document.getElementById("Remark").value;
        var Message="";
        var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/;
        if (rkdate.match(reg)==null) {
            Message="请输入正确的送货日期！";
        }
        if (BillNumber.trim()==""){
            Message=Message+"请输入送货单号！";
        }
        if(Message!=""){
            alert(Message);
            return false;
        }
        else{
            var url="item5_2_ajax.php?Mid="+Mid+"&Date="+rkdate+"&BillNumber="+BillNumber+"&Remark="+Remark+"&ActionId=20";
            var ajax=InitAjax();
            ajax.open("GET",url,true);
            ajax.onreadystatechange =function(){
                if(ajax.readyState==4){// && ajax.status ==200
                    if(ajax.responseText=="Y"){//更新成功
                        document.form1.submit();
                    }
                }
            }
            ajax.send(null);
        }

    }

    function viewStuffdata() {
        var diag = new Dialog("live");
        var CompanyId2=document.getElementById("CompanyId2").value;
        document.getElementById("TempCompanyId").value=CompanyId2;
        diag.Width = 840;
        diag.Height = 600;
        diag.Title = "配件资料";
        diag.URL = "viewrk_s1.php?Action=1&selModel=2&searchSTR="+CompanyId2;
        diag.ShowMessageRow = false;
        diag.MessageTitle ="";
        diag.Message = "";
        diag.ShowButtonRow = true;
        diag.selModel=2; //1只选一条；2多选；
        diag.OKEvent=function(){
            var backData=diag.backValue();
            if (backData){
                editTabRecord(backData);
                diag.close();
            }
        };
        diag.show();
    }

    function CheckForm(){
        var Message="";
        var tempQty="";
        var BillNumber=document.getElementById("BillNumber").value;
        if (BillNumber==""){
            Message="请输入送货单号!";
        }
        if(ListTable.rows.length<1){
            Message+="没有设置入库配件的数据!";
        }
        var qtyInput=document.getElementsByTagName("input");
        for (var i=0;i<qtyInput.length;i++){
            var e=qtyInput[i];
            var NameTemp=e.name;
            var Name=NameTemp.search("IndepotQTY") ;
            if(Number(e.value)<=0 && Name!=-1){
                Message+="入库数量不能为空!";
                break;
            }
            if ( Name!=-1){
                if (tempQty==""){tempQty=e.value;} else {tempQty=tempQty + "|" + e.value;}
            }
        }
        if(Message!=""){
            alert(Message);return false;
        }
        else{
            var StockValues="";
            //读取加入的数据
            //var ListTable=document.getElementById("ListTable");
            var arrQty=tempQty.split("|");
            for(i=0;i <arrQty.length;i++){
                if(i>0) StockValues=StockValues+"|";
                StockValues=StockValues+ListTable.rows[i].cells[2].innerText+"!"+ListTable.rows[i].cells[3].innerText+"!"+arrQty[i];
            }
            //alert(StockValues);
            document.getElementById("AddIds").value=StockValues;
            return true;
        }
    }

    function editTabRecord(BackStuffId){
        var Rowstemp=BackStuffId.split(",");
        var Rowslength=Rowstemp.length;
        for(var i=0;i<Rowslength;i++){
            var Message="";
            var FieldArray=Rowstemp[i].split("^^");//$StockId^^$StuffId^^$StuffCname^^$FactualQty^^$AddQty^^$CountQty^^$Unreceive
            //过滤相同的产品订单ID号
            for(var j=0;j<ListTable.rows.length;j++){
                var StuffIdtemp=ListTable.rows[j].cells[0].data;//隐藏ID号存于操作列
                if(FieldArray[0]==StuffIdtemp){//如果流水号存在
                    Message="配件: "+FieldArray[1]+"的资料已在列表!跳过继续！";
                    break;
                }
            }
            if(Message==""){
                oTR=ListTable.insertRow(ListTable.rows.length);
                tmpNum=oTR.rowIndex+1;
                //第一列:操作
                oTD=oTR.insertCell(0);
                oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
                oTD.data=""+FieldArray[0]+"";
                oTD.onmousedown=function(){
                    window.event.cancelBubble=true;
                };
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="40";
                oTD.height="20";

                //第二列:序号
                oTD=oTR.insertCell(1);
                oTD.innerHTML=""+tmpNum+"";
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="40";

                //三、需求流水号
                oTD=oTR.insertCell(2);
                oTD.innerHTML=""+FieldArray[0]+"";
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="90";

                //四：配件ID
                oTD=oTR.insertCell(3);
                oTD.innerHTML=""+FieldArray[1]+"";
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="50";

                //五:配件名称
                oTD=oTR.insertCell(4);
                oTD.innerHTML=""+FieldArray[2]+"";
                oTD.className ="A0101";
                oTD.width="300";

                //六：配求数量
                oTD=oTR.insertCell(5);
                oTD.innerHTML=""+FieldArray[3]+"";
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="60";

                //七：增购数量
                oTD=oTR.insertCell(6);
                oTD.innerHTML=""+FieldArray[4]+"";
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="60";
                var SumQty=FieldArray[3]*1+FieldArray[4]*1;
                //八：实购数量
                oTD=oTR.insertCell(7);
                oTD.innerHTML=""+SumQty+"";
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="60";

                //第九列:未收数量
                oTD=oTR.insertCell(8);
                oTD.innerHTML=""+FieldArray[6]+"";
                oTD.className ="A0101";
                oTD.align="center";
                oTD.width="60";

                //第十列:本次入库
                oTD=oTR.insertCell(9);
                oTD.innerHTML="<input type='text' name='IndepotQTY[]' id='IndepotQTY' size='4' class='I0000L' value='"+FieldArray[6]+"' onblur='Indepot(this,"+FieldArray[6]+")' onfocus='toTempValue(this.value)'>";
                oTD.className ="A0100";
                oTD.align="center";
                oTD.width="80";

                document.getElementById("BuyerId").value=FieldArray[7];
            }
            else{
                alert(Message);
            }//if(Message=="")
        }//for(var i=0;i<Rowslength;i++)
    }

    function toTempValue(textValue){
        document.getElementById("TempValue").value=textValue;
    }

    function Indepot(thisE,SumQty){
        var oldValue=document.getElementById("TempValue").value;
        var thisValue=thisE.value;
        if(thisValue!=""){
            var CheckSTR=fucCheckNUM(thisValue,"");
            if(CheckSTR==0){
                alert("不是规范的数字！");
                thisE.value=oldValue;
                return false;
            }
            else{
                if((thisValue>SumQty) || thisValue==0){
                    alert("不在允许值的范围！在库:"+SumQty);
                    thisE.value=oldValue;
                    return false;
                }
            }
        }
    }
    //删除指定行
    function deleteRow(rowIndex){
        ListTable.deleteRow(rowIndex);
        ShowSequence(ListTable);
    }

    function ShowSequence(TableTemp){
        for(i=0;i<TableTemp.rows.length;i++){
            var j=i+1
            TableTemp.rows[i].cells[1].innerText=j;
        }
    }

    //删除表格数据
    function deleteAllRow(e){
        rowLen=ListTable.rows.length;
        var tempIds=document.getElementById("TempCompanyId").value;
        var selId=e.value;
        if (rowLen>0 && tempIds!=selId){
            alert('改变供应商将清除现已添加数据!');
            for (i=rowLen;i>0;i--){
                ListTable.deleteRow(i-1);
            }
            document.getElementById("TempCompanyId").value=selId;
        }
    }
</script>