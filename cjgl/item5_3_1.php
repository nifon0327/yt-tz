<div id="menudiv531">
<?php
$Th_Col="选项|30|交期|60|业务单号|60|客户名称|80|产品名称|120|生产数量|60|车间|60|选项|30|序号|30|配件ID|50|供应商|70|配件名称|260|仓库<br/>楼层|40|单位|30|在库|60|备料数量|55|已备数量|55|本次备料|40";
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
$GysList="";
$nowInfo="当前:车间需备料订单";

$checkBlSign=1;//可备料标识

//$GysList1="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='saveBtn' value='保存' onclick='saveQty()' disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";
$GysList1="&nbsp;&nbsp;<span class='ButtonH_25' id='saveBtn' onclick='saveQty()' disabled>保存</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";

//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='".($Cols-6)."' height='40px' class=''>$WorkShopList &nbsp;&nbsp; $ForshortList  $BuildNoList  $OrderPOList $typeList $LineList $BatchButton $batchChangeTime</td><td colspan='5' align='center' class=''>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr></table>";
$DefaultBgColor=$theDefaultColor;
$i=1;

/*
$mySql  = "SELECT * FROM (
			SELECT S.POrderId,S.OrderPO,S.Qty ,SC.sPOrderId, SC.Qty AS scQty,SC.ActionId,
			P.cName,P.TestStandard,P.ProductId,
			PI.Leadweek,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,
			SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),
			(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
			SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,
			SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty
			FROM  $DataIn.yw1_scsheet  SC
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SC.POrderId
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.packingunit U ON U.Id=P.PackingUnit
			LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
			LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId
			LEFT JOIN $DataIn.yw1_stocksheet  G ON G.sPOrderId=SC.sPOrderId
			LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
			LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id = ST.mainType
			LEFT JOIN (
			             SELECT G.StockId,SC.sPOrderId,SUM(L.Qty) AS Qty
			             FROM $DataIn.yw1_scsheet SC
			             LEFT JOIN $DataIn.yw1_stocksheet  G ON G.sPOrderId=SC.sPOrderId
			             LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId
			             AND L.sPOrderId = SC.sPOrderId
			             WHERE 1  AND SC.scFrom >0 AND SC.Estate>0 AND SC.Level = 1
			             GROUP BY G.StockId
			         ) L ON L.StockId=G.StockId AND L.sPOrderId = SC.sPOrderId

			WHERE 1  $SearchRows $SearchRowsWeek AND SC.Level = 1 AND MT.blsign = 1   GROUP BY SC.sPOrderId ) A  WHERE   1 AND  A.K1>=A.K2 AND A.blQty!=A.llQty ORDER BY Leadtime";
*/
if ($ForshortList != "") {
    $mySql = "SELECT * FROM (
			SELECT M.CompanyId,C.Forshort ,S.POrderId,S.OrderPO,S.Qty ,SC.sPOrderId,getCanStock(SC.sPOrderId,$checkBlSign) AS canSign, WA.NAME AS workshopName,
			SC.Qty AS scQty,SC.ActionId,
			P.cName,P.TestStandard,P.ProductId,
			PI.Leadweek,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime
			FROM  $DataIn.yw1_scsheet  SC 
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SC.POrderId
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
			LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT  JOIN $DataIn.workshopdata WA ON WA.Id = SC.WorkShopId
			INNER JOIN $DataIn.productdata PD ON PD.ProductId = S.ProductId
			LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
			LEFT JOIN $DataIn.packingunit U ON U.Id=P.PackingUnit 
			LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
			LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
			WHERE 1  $SearchRows $SearchRowsWeek AND SC.Level = 1 AND SC.scFrom=1  GROUP BY SC.sPOrderId ) A  WHERE canSign=$checkBlSign and leadtime > 0
 ORDER BY Leadtime";
    $mainResult = mysql_query($mySql, $link_id);
    if ($mainRows = mysql_fetch_array($mainResult)) {
        do {
            $m = 1;
            //echo $mainRows["canSign"];
            $cName = $mainRows["cName"];
            $POrderId = $mainRows["POrderId"];
            $TestStandard = $mainRows["TestStandard"];
            $workshopName = $mainRows["workshopName"];
            $TestStandardSign = $TestStandard;
            $ProductId = $mainRows["ProductId"];
            include "../admin/Productimage/getOnlyPOrderImage.php";
            $OrderPO = $mainRows["OrderPO"];
            $Qty = $mainRows["Qty"];

            $Leadtime = $mainRows["Leadtime"];
            $Leadweek = $myRow["Leadweek"];
            include "../model/subprogram/PI_Leadweek.php";

            $scQty = $mainRows["scQty"];
            $sPOrderId = $mainRows["sPOrderId"];
            $ActionId = $mainRows["ActionId"];


            //加急订单锁定操作，整单锁和单个配件锁都不能备料
            $Lock_Result = mysql_fetch_array(mysql_query("
		          SELECT POrderId FROM $DataIn.yw2_orderexpress   
		          WHERE POrderId='$POrderId' AND Type='2'
		          UNION ALL
		          SELECT POrderId FROM (SELECT LEFT(GL.StockId,12) AS POrderId,GL.Locks 
		          FROM $DataIn.cg1_lockstock GL,$DataIn.cg1_stocksheet G 
		          WHERE G.POrderId='$POrderId' AND G.Level = 1  AND GL.Locks=0 AND GL.StockId=G.StockId  GROUP BY POrderId) K 
		          WHERE K.POrderId='$POrderId'", $link_id));
            $newPOrderId = $Lock_Result["POrderId"];
            $Locks = $newPOrderId == "" ? 1 : 0;
            $LockRemarks = "";
            $LockRemarks = $Locks == 1 ? "" : "<span class='redB'>订单已锁</span>";
            if ($LockRemarks != "" || $Locks == 0) continue;

            /*if($SubAction==31){//有权限
                 $checkAllclick="onclick=\"checkAll(this,$i);\" ";
                 $checkDisabled="";
                }
            else{
                $checkAllclick="";
                $checkDisabled="disabled";
              }*/

            //<input name='checkAll$i' type='checkbox' id='checkAll$i' $checkAllclick>
            //输出订单信息
            echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
            echo "<td scope='col' class='A0111' width='$Field[$m]' align='center'>$i</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >$Leadweek</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >$OrderPO</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >" . $mainRows['Forshort'] . "</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' onclick='selectAllb1Stuff(this)' onmouseover=\"this.style.cssText='cursor:pointer'\" onmouseover=\"this.style.cssText='cursor:default'\">$TestStandard<br>$sPOrderId</td>";//产品名称
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$scQty</td>";//生产数量
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td scope='col' class='A0101' width='$Field[$m]' align='center' >$workshopName</td>";
            $unitWidth = $unitWidth - $Field[$m];
            $m = $m + 2;
            echo "<td width='' class='A0101'>";
            //订单产品对应的配件信息

            $checkStockSql = mysql_query("SELECT G.OrderQty*(S.Qty/$Qty) AS OrderQty,G.OrderQty AS YwOrderQty,
			    G.StockId,K.tStockQty,D.StuffId,
		        D.StuffCname,D.Picture,F.Remark,M.Name,P.Forshort,U.Name AS UnitName,U.Decimals 
				FROM  $DataIn.yw1_scsheet  S 
				INNER JOIN $DataIn.cg1_stocksheet G ON G.POrderId = S.POrderId  
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT  JOIN $DataIn.staffmain M ON M.Number=G.BuyerId 
				LEFT  JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT  JOIN $DataIn.base_mposition F ON F.Id=D.SendFloor
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				WHERE  S.sPOrderId='$sPOrderId' AND G.Level=1 AND MT.blSign=1 ORDER BY D.SendFloor", $link_id);
            if ($checkStockRow = mysql_fetch_array($checkStockSql)) {
                echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                $j = 1;
                $llCount = 0;
                do {
                    $Name = $checkStockRow["Name"];
                    $Forshort = $checkStockRow["Forshort"];
                    $StockId = $checkStockRow["StockId"];
                    $StuffId = $checkStockRow["StuffId"];
                    $StuffCname = $checkStockRow["StuffCname"];
                    $UnitName = $checkStockRow["UnitName"];
                    $Picture = $checkStockRow["Picture"];
                    $Decimals = $checkStockRow["Decimals"];
                    //echo $Decimals;

                    $tStockQty = round($checkStockRow["tStockQty"], $Decimals);
                    $OrderQty = round($checkStockRow["OrderQty"], $Decimals);
                    $YwOrderQty = round($checkStockRow["YwOrderQty"], $Decimals);
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


                    //检查本工单已领料数据
                    $checkllQtyResult = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) 
						AS llQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = '$sPOrderId' 
						AND  StockId='$StockId'", $link_id));
                    $llQty = $checkllQtyResult["llQty"];

                    $llQtyTemp = $OrderQty - $llQty;
                    $checkDisabled = "";

                    ////检查外发配件已领料的配件数据
                    $stockllQtyResult = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) 
						AS llQty FROM $DataIn.ck5_llsheet WHERE POrderId = '$POrderId'  AND  StockId='$StockId'", $link_id));
                    $stockllQty = $stockllQtyResult["llQty"];
                    $stockllTemp = $YwOrderQty - $stockllQty;

                    $passValue = "$POrderId@$sPOrderId@$StockId@0@$kWorkShopId@$ActionId@$StuffId";
                    if ($llQtyTemp <= 0 || $stockllTemp <= 0) {//是否已领料
                        $llIMG = "";
                        $llonClick = "bgcolor='#96FF2D'";
                        $checkDisabled = "disabled";
                    } else {
                        if ($tStockQty <= 0) {//判断在库量是否可进行领料
                            $llIMG = "<img src='../images/registerNo.png' width='30' height='30'>";
                            $llonClick = "";
                            $checkDisabled = "disabled";
                        } else {
                            //检查权限
                            if ($SubAction == 31) {//有权限  && $Estate==1
                                $temQty = $llQtyTemp > $tStockQty ? $tStockQty : $llQtyTemp;
                                //$llIMG="<img src='../images/register.png' width='30' height='30'>";
                                $llIMG = $OrderQty;
                                $llonClick = " onclick='showKeyboard(this,$i,$j,$temQty, $temQty,\"$passValue\",$tempk,$comboxSign)'";
                            } else {
                                $llonClick = "";
                                $llIMG = "<img src='../images/registerNo.png' width='30' height='30'>";
                                $checkDisabled = "disabled";
                            }
                        }

                    }
                    if ($Locks == 0) {//锁定订单和没标准图订单不能备料
                        $llonClick = "";
                        $llIMG = "<img src='../images/registerNo.png' width='30' height='30'>";
                        $checkDisabled = "disabled";
                    }

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
                    echo "<td class='A0101' width='$Field[$m]' align='center'>$StuffId</td>";    //采购
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' $PrintClick>$Forshort $PrintIMG</td>";
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]'>$StuffCname</td>";    //配件名称
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='center'>$Remark</td>";  //仓库楼层
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='center'>$UnitName</td>";  //单位
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='right'>$tStockQty</td>";//库存数量
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='right'>$OrderQty</td>";//备料数
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' align='right'>$llQty</td>";//已领料数量
                    $m = $m + 2;
                    echo "<td class='A0100' align='center' style='color:#FF0000;' $llonClick>$llIMG</td>";    //领料
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

        } while ($mainRows = mysql_fetch_array($mainResult));
        $i = $i - 1;
        echo " <input name='cIdNumber' type='hidden' id='cIdNumber' value='$i'/>";
        echo " <input name='fromPage' type='hidden' id='fromPage' value='1'/>";

    }
}
	else{
		echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
		}
?>
</div>