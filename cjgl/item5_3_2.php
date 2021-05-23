<?php
$Th_Col = "序号|30|交期|50|工单流水号|80|业务单号|80|产品名称|100|客户名称|80|半成品名称|250|车间|60|生产时间<span class='redB'>（必填）</span>|120|生产数量|50|选项|30|序号|30|配件ID|50|供应商|65|原材料名称|260|仓库<br/>楼层|40|单位|30|在库|60|备料数量|55|已备数量|55|本次备料|40";
$nowInfo = "当前:需备料生产订单";
$Field = explode("|", $Th_Col);
$Count = count($Field);
$Cols = $Count / 2;
$wField = $Field;
$widthArray = array();
for ($i = 0; $i < $Count; $i++) {
    $i = $i + 1;
    $widthArray[] = $wField[$i];
    $tableWidth += $wField[$i];
}
//if (isSafari6()==1){
$tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
//}

$today = date("Y-m-d");
$addDays = 28;//可备逾期、本周、下周
//$addDays=date('w')>=4 || date('w')==0?7:0;
//$addDays=date('w')>=4?14:7;
//if ($today=='2016-03-23') $addDays=7;
$checkBlSign = 1;//可备料标识

//台车
$TCList = "";
$TCList .= "<select name='TCNO' id='TCNO' style = 'width:100px' onChange='ResetPage(1,5)'>";
$TCNO = $TCNO == "" ? "YES" : $TCNO;
if ($TCNO == "YES") {
    $BUG = "selected";
    $SearchRows .= " AND S.liningNo IS NOT NULL ";
}elseif ($TCNO == "NO"){
    $millennium = "selected";
    $SearchRows .= " AND S.liningNo IS NULL ";
}
$TCList .= "<option value='YES' $BUG>已设置台车</option>";


$TCList .= "<option value='NO' $millennium>未设置台车</option>";


$TCList .= "</select>";


$nextWeekDate = date("Y-m-d", strtotime("$today  +$addDays   day"));
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek", $link_id));
$nextWeek = $dateResult["NextWeek"];
$SearchRows .= " AND CG.DeliveryWeek<=$nextWeek  ";
$BatchButton="&nbsp;&nbsp;<span class='ButtonH_25' onclick='batchChangeWorkShop(this);' >变更车间</span>";

$batchChangeTime="&nbsp;&nbsp;<span class='ButtonH_25' onclick='batchChangescTime(this);' >设置生产时间</span>";

$GysList1 = "&nbsp;&nbsp;<span class='ButtonH_25' id='saveBtn' onclick='saveQty(this)' disabled>保存</span>&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";
// $GysList1="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='saveBtn' value='保存' onclick='saveQty()' disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr>
<td colspan='" . ($Cols - 8) . "' height='40px' class=''>$WorkShopList &nbsp;&nbsp; $ForshortList  $BuildNoList  $OrderPOList $typeList $LineList  $TCList  $BatchButton $batchChangeTime</td><td colspan='4' class=''  align='right'>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
//输出表格标题
for ($i = 0; $i < $Count; $i = $i + 2) {
    $Class_Temp = $i == 0 ? "A1111" : "A1101";
    $j = $i;
    $k = $j + 1;
    echo "<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
}
echo "</tr></table>";
$DefaultBgColor = $theDefaultColor;
$i = 1;

    /*$OrderPOPost = $_POST["OrderPO"];

    if ($OrderPOPost !="" && $OrderPOPost != null ){*/
if ($ForshortList != "") {
    $mySql = "SELECT A.* FROM (
	SELECT  SC.POrderId,SC.sPOrderId,O.Forshort,SC.Qty,SC.mStockId,SC.ActionId,SC.scDate,WA.Name AS WorkShopName,
	getCanStock(SC.sPOrderId,$checkBlSign) AS canSign,(CG.addQty+CG.FactualQty) AS xdQty,
	D.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,
	IF(CG.DeliveryWeek>0,CG.DeliveryDate,'2099-12-31') AS DeliveryDate,CG.DeliveryWeek,
	S.OrderPO,P.cName,S.liningNo
	FROM  $DataIn.yw1_scsheet SC 
	LEFT  JOIN $DataIn.yw1_ordersheet    S  ON S.POrderId = SC.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
    LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	LEFT  JOIN $DataIn.workorderaction      W  ON W.ActionId = SC.ActionId
	LEFT  JOIN $DataIn.workshopdata      WA  ON WA.Id = SC.WorkShopId
	LEFT  JOIN $DataIn.cg1_semifinished  SM ON SM.mStockId = SC.mStockId
	LEFT  JOIN $DataIn.cg1_stocksheet    CG ON CG.StockId = SC.mStockId
	INNER JOIN $DataIn.productdata PD ON PD.ProductId = S.ProductId
	LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = CG.StuffId
    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	WHERE  1 $SearchRows AND CG.DeliveryWeek>0 GROUP BY SC.sPOrderId) A  WHERE A.canSign=$checkBlSign  ORDER BY DeliveryDate";

// echo $mySql;
    /*
    $mySql="SELECT A.* FROM (
        SELECT  SC.POrderId,SC.sPOrderId,O.Forshort,SC.Qty,SC.mStockId,SC.ActionId,
        getCanStock(SC.sPOrderId,$checkBlSign) AS canSign,(CG.addQty+CG.FactualQty) AS xdQty,
        D.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,
        IF(CG.DeliveryWeek>0,CG.DeliveryDate,'2099-12-31') AS DeliveryDate,CG.DeliveryWeek,
        getOrderStockTime(SC.sPOrderId) as StockTime,S.OrderPO,P.cName
        FROM  $DataIn.yw1_scsheet SC
        LEFT  JOIN $DataIn.yw1_ordersheet    S  ON S.POrderId = SC.POrderId
        LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
        LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
        LEFT  JOIN $DataIn.workshopdata      W  ON W.Id = SC.WorkShopId
        LEFT  JOIN $DataIn.cg1_semifinished  SM ON SM.mStockId = SC.mStockId
        LEFT  JOIN $DataIn.cg1_stocksheet    CG ON CG.StockId = SC.mStockId
        LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = CG.StuffId
        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
        WHERE  1 $SearchRows AND CG.DeliveryWeek>=0 GROUP BY SC.sPOrderId) A  WHERE A.canSign=$checkBlSign  ORDER BY DeliveryDate,StockTime";
    */
//echo $mySql;

    /*
        $mySql="SELECT * FROM (
        SELECT  SC.POrderId,SC.sPOrderId,SC.Qty,M.PurchaseID,M.Remark,SM.mStockId,SC.ActionId,
        D.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,
        IF(CG.DeliveryWeek>0,CG.DeliveryDate,'2099-12-31') AS DeliveryDate,CG.DeliveryWeek,
        SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
        SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,
        IFNULL(SUM(G.OrderQty),0)  AS blQty,
        IFNULL(SUM(L.Qty),0) AS llQty
        FROM  $DataIn.yw1_scsheet SC
        LEFT  JOIN $DataIn.yw1_stocksheet    G  ON G.sPOrderId = SC.sPOrderId
        LEFT  JOIN $DataIn.cg1_stocksheet    SG ON SG.StockId = G.StockId
        LEFT  JOIN $DataIn.workshopdata      W  ON W.Id = SC.WorkShopId
        LEFT  JOIN $DataIn.cg1_semifinished  SM ON SM.StockId = SC.StockId
        LEFT  JOIN $DataIn.cg1_stocksheet    CG  ON CG.StockId = SM.mStockId
        LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = SM.mStuffId
        LEFT  JOIN $DataIn.stufftype         T  ON T.TypeId = D.TypeId
        LEFT  JOIN $DataIn.cg1_stockmain     M  ON M.Id = CG.Mid
        LEFT  JOIN $DataIn.ck9_stocksheet    K ON K.StuffId=G.StuffId
        LEFT  JOIN (
                     SELECT L.StockId,SC.sPOrderId,SUM(L.Qty) AS Qty
                     FROM $DataIn.yw1_scsheet SC
                     LEFT JOIN $DataIn.yw1_stocksheet  G ON G.sPOrderId=SC.sPOrderId
                     LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId  AND L.sPOrderId = SC.sPOrderId
                     WHERE 1  AND SC.scFrom >0 AND SC.Estate>0 AND SC.Level != 1
                     GROUP BY SC.sPOrderId,L.StockId
                    ) L ON L.StockId=G.StockId AND L.sPOrderId = SC.sPOrderId
        WHERE  1 $SearchRows AND CG.DeliveryWeek>0 AND SG.blsign = 1 GROUP BY SC.sPOrderId ORDER BY SC.Id) A  WHERE 1 AND  A.K1>=A.K2 AND A.blQty!=A.llQty ORDER BY DeliveryDate,sPOrderId";
    }
    //echo $mySql; //AND  A.K1>=A.K2 AND A.blQty!=A.llQty
    */
    $myResult = mysql_query("$mySql", $link_id);
    if ($myRow = mysql_fetch_array($myResult)) {
        $i = 1;
        do {
            $m = 1;
            $Id = $myRow["Id"];
            $Qty = $myRow["Qty"];
            $xdQty = $myRow["xdQty"];
            //$Relation=round($Qty/$xdQty,3);
            $Relation = $Qty / $xdQty;
            $POrderId = $myRow["POrderId"];
            $sPOrderId = $myRow["sPOrderId"];
            $WorkShopName = $myRow["WorkShopName"];
            $scDate= $myRow["scDate"] != ''?date('Y-m-d',strtotime($myRow["scDate"])):'';
            $mStockId = $myRow["mStockId"];
            $ActionId = $myRow["ActionId"];
            $StuffId = $myRow["StuffId"];
            $StuffCname = $myRow["StuffCname"];
            $Picture = $myRow["Picture"];
            $Forshort = $myRow["Forshort"];
            $DeliveryDate = $myRow["DeliveryWeek"] > 0 ? $myRow["DeliveryDate"] : "";
            $DeliveryWeek = $myRow["DeliveryWeek"];//本配件的交期

            $OrderPO = $myRow["OrderPO"];
            $cName = $myRow["cName"];

            $TCliningNo = $myRow["liningNo"];

            include "../model/subprogram/stuffimg_model.php";
            include "../model/subprogram/stuff_Property.php";//配件属性

            $Lock_Row = mysql_fetch_array(mysql_query("SELECT StockId,Locks FROM $DataIn.cg1_lockstock  WHERE  StockId ='$mStockId'", $link_id));
            $CheckLockRow = mysql_fetch_array(mysql_query("SELECT Id,Remark FROM $DataIn.yw1_sclock WHERE sPOrderId ='$sPOrderId' AND Locks=0 LIMIT 1", $link_id));
            $ScId = $CheckLockRow["Id"];
            $Locks = $Lock_Row["Locks"];
            $newStockId = $Lock_Row["StockId"];

            //取最上层半成品是否锁定
            $LocksResult = mysql_fetch_array(mysql_query("SELECT getStockIdLock('$mStockId') AS Locks", $link_id));
            $mLocks = $LocksResult['Locks'];

            // echo "$POrderId: $newStockId / $Locks / $ScId <br>";
            if (($newStockId != "" && $Locks == 0) || $ScId > 0 || $mLocks > 0) continue;


            //取最上层半成品的交期
            /*
            $DeliveryWeekRow=mysql_fetch_array(mysql_query("SELECT G.DeliveryWeek,G.DeliveryDate
            FROM $DataIn.cg1_semifinished  S
            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.mStockId
            WHERE S.POrderId='$POrderId' AND G.Level = 1 LIMIT 1",$link_id));
            $DeliveryWeek = $DeliveryWeekRow["DeliveryWeek"];
            $DeliveryDate = $DeliveryWeekRow["DeliveryDate"];
            */
            include "../model/subprogram/deliveryweek_toweek.php";

            $mStuffId = $StuffId;

            //<input name='checkAll$i' type='checkbox' id='checkAll$i' $checkAllclick>
            echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
            echo "<td scope='col' class='A0111' width='$Field[$m]' align='center'>$i</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;

            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >$DeliveryWeek</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >$sPOrderId</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >$OrderPO</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$cName</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >$Forshort</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]'  onclick='selectAllSuivre(this)' onmouseover=\"this.style.cssText='cursor:pointer'\" onmouseover=\"this.style.cssText='cursor:default'\"><input type='checkbox' name='checkId$i' id='checkId$i' value='$i|$POrderId|$scDate|$TCliningNo' />$StuffCname</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$WorkShopName</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$scDate</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Qty</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td width='' class='A0101'>";

            //订单产品对应的配件信息

            $checkStockSql = mysql_query("SELECT (A.OrderQty*$Relation) AS OrderQty,A.StockId,
		        K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,
		        P.Forshort,U.Name AS UnitName,U.Decimals 
				FROM  $DataIn.cg1_semifinished   A 
                INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				LEFT JOIN  $DataIn.staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN  $DataIn.base_mposition F ON F.Id=D.SendFloor
				WHERE  A.POrderId='$POrderId' AND A.mStockId='$mStockId' AND MT.blSign=1 ORDER BY D.SendFloor", $link_id);
//        echo $checkStockSql;
            if ($checkStockRow = mysql_fetch_array($checkStockSql)) {
                echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                $j = 1;
                $llCount = 0;
                $passValue = "";
                do {
                    $Name = $checkStockRow["Name"];
                    $Forshort = $checkStockRow["Forshort"];
                    $StockId = $checkStockRow["StockId"];
                    $Decimals = $checkStockRow["Decimals"];

                    $StuffId = $checkStockRow["StuffId"];
                    $StuffCname = $checkStockRow["StuffCname"];
                    $UnitName = $checkStockRow["UnitName"];
                    $Picture = $checkStockRow["Picture"];
                    $tStockQty = round($checkStockRow["tStockQty"], $Decimals);
                    $OrderQty = round($checkStockRow["OrderQty"], $Decimals);
                    $Remark = $checkStockRow["Remark"];
                    //检查是否有图片
                    $d = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);
                    include "../model/subprogram/stuffimg_model.php";
                    include "../model/subprogram/stuff_Property.php";//配件属性

                    $CheckComboxSql = mysql_query("SELECT A.StockId,A.StuffId,A.OrderQty,D.StuffCname,
						U.Name AS UnitName,K.tStockQty,A.Relation
						FROM $DataIn.cg1_stuffcombox A  
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
						LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
						WHERE  A.mStockId='$StockId' 
						", $link_id);
                    $showComboxTable = "";
                    $comboxSign = 0;
                    $tempk = 1;
                    if ($CheckComboxRow = mysql_fetch_array($CheckComboxSql)) {
                        $comboxSign = 1;
                        $showComboxTable = "<tr >
			                  <td colspan='11' class='A0100'><div  width='720'><table  cellspacing='1' border='1' align='left' style='margin-left:60px;margin-top:10px;margin-bottom:5px' >";
                        $showComboxTable .= "<tr height='25' style='background:#83b6b9;'>
			                  <td width='50' align='center'>配件ID</td>
			                  <td width='260' align='center' >配件名称</td>
			                  <td width='50' align='center'>单位</td>
			                  <td width='50' align='center'>对应关系</td>
			                  <td width='60' align='center'>在库</td>
			                  <td width='60' align='center'>备料数量</td>
			                  <td width='60' align='center'>已备数量</td>
			                 
			                  <td width='60' align='center'>本次备料</td>
			                  </tr >";
                        do {
                            $comboxStuffId = $CheckComboxRow["StuffId"];
                            $comboxStockId = $CheckComboxRow["StockId"];
                            $comboxStuffCname = $CheckComboxRow["StuffCname"];
                            $comboxOrderQty = $CheckComboxRow["OrderQty"];
                            $comboxUnitName = $CheckComboxRow["UnitName"];
                            $comboxtStockQty = $CheckComboxRow["tStockQty"];
                            $comboxRelation = $CheckComboxRow["Relation"];
                            $checkcomboxllRow = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$comboxStockId'", $link_id));
                            $comboxllQty = $checkcomboxllRow["llQty"];
                            $comboxllQty = $comboxllQty == "" ? 0 : $comboxllQty;
                            $showComboxTable .= "<tr >
			                  <td  align='center'>$comboxStuffId</td>
			                  <td  >$comboxStuffCname</td>
			                  <td align='center'>$comboxUnitName</td>
			                  <td align='center' id = 'comboxRelation$i$j$tempk' >$comboxRelation</td>
			                  <td align='right'>$comboxtStockQty</td>
			                  <td align='right'>$comboxOrderQty</td>
			                  <td align='right'>$comboxllQty</td>
			                  
			                  <td align='right' id = 'comboxblQty$i$j$tempk'>&nbsp;</td>
			                  </tr >";
                            $tempk++;
                        } while ($CheckComboxRow = mysql_fetch_array($CheckComboxSql));
                        $showComboxTable .= "</table></div></td></tr>";

                    }


                    //检查已领料数据
                    $checkllQty = mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = $sPOrderId AND  StockId='$StockId'", $link_id);
                    $llQty = mysql_result($checkllQty, 0, "llQty");
                    $llQty = $llQty == "" ? 0 : $llQty;
                    $llQtyTemp = $OrderQty - $llQty;
                    $checkDisabled = "";
                    $passValue = "$POrderId@$sPOrderId@$StockId@$mStockId@$kWorkShopId@$ActionId@$StuffId";
                    if ($llQtyTemp <= 0) {//是否已领料
                        $llIMG = "";
                        $llonClick = "bgcolor='#96FF2D'";
                        $llCount += 1;//已领料数据
                        $checkDisabled = "disabled";
                    } else {
                        if ($tStockQty <= 0 || $mLocks > 0) {//判断在库量是否可进行领料
                            $llIMG = "<img src='../images/registerNo.png' width='30' height='30' title='$LockRemark'>";
                            $llonClick = "";
                            $checkDisabled = "disabled";
                        } else {
                            //检查权限
                            if ($SubAction == 31) {//有权限  && $Estate==1
                                $temQty = $llQtyTemp > $tStockQty ? $tStockQty : $llQtyTemp;
                                //$llIMG="<img src='../images/register.png' width='30' height='30'>";
                                $llIMG = $temQty;
                                $llonClick = " class='prepare' contenteditable = \"true\" onblur='validate(this)'";
                            } else {
                                $llonClick = "";
                                $llIMG = "<img src='../images/registerNo.png' width='30' height='30'>";
                                $checkDisabled = "disabled";
                            }
                        }
                    }


                    $ShuiPrintIMG = "<a href='slicebom_report.php?POrderId=$POrderId&sPOrderId=$sPOrderId&mStockId=$mStockId&Qty=$Qty' target='_blank'><img src='../images/printer.png' width='16' height='16'></a>";


                    //库存量是否充足
                    $bgColor = ($OrderQty - $llQty > $tStockQty && $llQtyTemp > 0) ? "bgcolor='#FFCC66'" : "";
                    $checkIdclick = "onclick=\"checkId(this,$i,$j,$tempk,$comboxSign);\" ";
                    echo "<tr height='30'>";
                    $unitFirst = $Field[$m] - 1;
                    echo "<td class='A0101' width='$unitFirst' align='center'>
						  <input name='checkId$i$j' type='checkbox' id='checkId$i$j' value='$passValue' $checkIdclick $checkDisabled></td>";
                    $m = $m + 2;
                    echo "<td class='A0101' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='center'>$StuffId</td>";  //采购
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]'  $PrintClick>$Forshort </td>";//供应商
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]'>$StuffCname $ShuiPrintIMG</td>";  //配件名称
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='center'>$Remark</td>"; //仓库楼层
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='center'>$UnitName</td>";  //单位
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='right'>$tStockQty</td>";//库存数量
                    $m = $m + 2;
                    echo "<td class='A0101 prepareReal' width='$Field[$m]' align='right'>$OrderQty</td>";//备料数量
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='right'>$llQty</td>";//已领料数量
                    $m = $m + 2;
                    echo "<td class='A0100' align='center' style='color:#FF0000;' $llonClick>$llIMG</td>";  //领料
                    echo "</tr>";
                    $j++;

                    if ($comboxSign == 1) $j++; //存在子配件多一行
                    echo $showComboxTable;

                } while ($checkStockRow = mysql_fetch_array($checkStockSql));
                echo "</table>";
                echo " <input name='tempj$i' type='hidden' id='tempj$i' value='$j'/>";
                echo " <input name='checkId$i$j' type='checkbox' id='checkId$i$j'  disabled style='display:none;'>";//防止只有一条配件记录而产生错误
                $i++;
            }
            echo "</td></tr></table>";

        } while ($myRow = mysql_fetch_array($myResult));
        $i = $i - 1;
        echo " <input name='cIdNumber' type='hidden' id='cIdNumber' value='$i'/>";
        echo " <input name='fromPage' type='hidden' id='fromPage' value='2'/>";
    }
//}
}
else {
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='" . $Cols . "' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
}
?>
