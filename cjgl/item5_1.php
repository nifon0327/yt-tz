<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once($path.'/FactoryCheck/CheckSkip.php');

$Th_Col="操作|40|序号|30|选项|30|客户名称|80|业务单号|100|产品名称|80|送货单号|100|送货日期|100|需求单流水号|100|配件ID|50|配件名称|280|采购总数|60|未收数量|60|收货确认|60|单位|30|仓管备注|80|送货楼层|60|审核通过|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
$SearchRows=" AND S.Estate=1 AND S.Mid>0";//收货状态：仓管送货单审核
//供应商过滤
$GysList="";
$nowInfo="当前:送货单审核";
$funFrom="item5_1";
$addWebPage=$funFrom . "_add.php";

$GysResult= mysql_query("
SELECT M.CompanyId,P.Forshort 
	FROM $DataIn.gys_shsheet S
	LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
	WHERE 1 $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
if ($GysRow = mysql_fetch_array($GysResult)){
	$GysList="<select name='GysId' id='GysId' onChange='ResetPage(0,5)'>";//BillNumber重置
	$i=1;
	do{
		$theGysId=$GysRow["CompanyId"];
		$theForshort=$GysRow["Forshort"];
		$GysId=$GysId==""?$theGysId:$GysId;
		if($GysId==$theGysId){
			$GysList.="<option value='$theGysId' selected>$i 、$theForshort</option>";
			$SearchRows.=" AND M.CompanyId='$theGysId'";
			$nowInfo.=" - ".$theForshort;
			}
		else{
			$GysList.="<option value='$theGysId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($GysRow = mysql_fetch_array($GysResult));
		$GysList.="</select>";
	}
$BillNumber=$SignS==0?"":$BillNumber;//BillNumber重置
//送货单过滤
$checkNumSql = mysql_query("SELECT M.BillNumber,M.GysNumber 
    FROM $DataIn.gys_shmain M 
	LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
	WHERE 1 $SearchRows GROUP BY S.Mid ORDER BY M.BillNumber DESC",$link_id);
if($checkNumRow = mysql_fetch_array($checkNumSql)){
	$BillNumStr="<select name='BillNumber' id='BillNumber' onchange='ResetPage(1,5)'>";
    $BillNumStr.="<option value='all' >全部送货单号</option>";
	$i=1;
	do{
		$theBillNumber=$checkNumRow["BillNumber"];
		$tempBillNumber =  $theBillNumber;
		$theGysNumber =$checkNumRow["GysNumber"];
		$BillNumber=$BillNumber==""?$theBillNumber:$BillNumber;

		if($theBillNumber==$BillNumber){
			$BillNumStr.="<option value='$theBillNumber' selected>$theBillNumber</option>";
			$SearchRows.=" AND M.BillNumber='$theBillNumber'";
			}
		else{
			$BillNumStr.="<option value='$theBillNumber'>$theBillNumber</option>";
			}
		$i++;
		}while($checkNumRow = mysql_fetch_array($checkNumSql));
	    $BillNumStr.="</select>&nbsp;";
     }

     //增加业务单号下拉筛选
     $OrderPOList="";
     $clientResult = mysql_query("
             SELECT Y.OrderPO
             FROM $DataIn.gys_shsheet S
             LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
             LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
             LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId
             WHERE 1  $SearchRows and Y.OrderPO is not null GROUP BY Y.OrderPO order by Y.OrderPO
             ",$link_id);
$SearchRowes = $SearchRows;
     if($clientRow = mysql_fetch_array($clientResult)) {
         $OrderPOList .= "<select name='OrderPO' id='OrderPO' onchange='ResetPage(20,5)'>";
         $OrderPOList .= "<option value='all' >全部业务单</option>";
         do{
             $thisOrderPO=$clientRow["OrderPO"];
             $OrderPO=$OrderPO==""?$thisOrderPO:$OrderPO;
             if($OrderPO==$thisOrderPO){
                 $OrderPOList .= "<option value='$thisOrderPO' selected>$thisOrderPO</option>";

                 $SearchRowes.=" and Y.OrderPO='$thisOrderPO' ";
             }
             else{
                 $OrderPOList .= "<option value='$thisOrderPO'>$thisOrderPO</option>";
             }
         }while ($clientRow = mysql_fetch_array($clientResult));
         $OrderPOList .= "</select>&nbsp;";
     }



//增加客户下拉筛选
$ForshortList="";
$ForshortResult = mysql_query("SELECT C.Forshort 
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		LEFT JOIN (
				   select S.StuffId,Count(*) as CS FROM $DataIn.gys_shsheet S
				   LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
				   WHERE 1 $SearchRows 
				   Group by S.StuffId
				   ) H  ON H.StuffId=S.StuffId
		WHERE 1 $SearchRowes AND D.ComboxSign!=1 
UNION 
       SELECT C.Forshort 
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stuffcombox B ON B.StockId=S.StockId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=B.mStockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		LEFT JOIN (
				   select S.StuffId,Count(*) as CS FROM $DataIn.gys_shsheet S
				   LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
				   WHERE 1 $SearchRows 
				   Group by S.StuffId
				   ) H  ON H.StuffId=S.StuffId
		WHERE 1 $SearchRowes AND D.ComboxSign=1 
		GROUP BY C.Forshort",$link_id);

if($ForshortRow = mysql_fetch_array($ForshortResult)) {
    $ForshortList .= "<select name='Forshort' id='Forshort' onchange='ResetPage(20,5)'>";
    $ForshortList .= "<option value='all' selected>全部客户</option>";
    do{
        $thisForshort=$ForshortRow["Forshort"];
        if ($thisForshort == ""){

        }elseif ($Forshort==$thisForshort){
            $ForshortList .= "<option value='$thisForshort' selected>$thisForshort</option>";

            $SearchRowes.=" and C.Forshort='$thisForshort' ";
        }
        else{
            $ForshortList .= "<option value='$thisForshort'>$thisForshort</option>";
        }
    }while ($ForshortRow = mysql_fetch_array($ForshortResult));
    $ForshortList .= "</select>&nbsp;";
}


 //有权限
$addBtnDisabled=$SubAction==31?"":"disabled";
	$GysList2="<span class='ButtonH_25' id='addBtn' onclick=\"openWinDialog(this,'$addWebPage',950,560,'center')\" $addBtnDisabled>新 增</span>&nbsp;&nbsp;";

if ($GysList || $BillNumStr || $OrderPOList || $ForshortList) {
    //$toExcel = '<span class="ButtonH_25" onclick="toExcelAll($SearchRowes,$SearchRows)">导出送货单汇总</span>';
    $toExcel = '<a class="ButtonH_25" href="./item5_1_excel.php?SearchRowes='.$SearchRowes.'&SearchRows='.$SearchRows.'" target="_blank">导出送货汇总</a>';
}
//步骤5：

echo"<table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-right: 10px;'>
	<tr>
	<td colspan='".($Cols-7)."' height='40px' class=''>$GysList $BillNumStr $OrderPOList $ForshortList $toExcel</td>
    <td colspan='4' class=''>$GysList2&nbsp;&nbsp;<span class='ButtonH_25' id='checkBtn' onclick='batchCheck()' >审核通过</span> <span  class='ButtonH_25' id='checkBtn' onclick='showRemarkAll(this)'>备注</span></td>
    <td colspan='3' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' Class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;

$isCount=0;  //同一个配件大于1次的才统计显示
$SumcgQty=0;
$SumnoQty=0;
$SumQty=0;
$sameStuffId="";

$mySql="SELECT M.CompanyId,S.Id,S.Mid,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,D.SendFloor,(G.AddQty+G.FactualQty) AS cgQty,M.Date,U.Name AS UnitName,G.POrderId,Y.OrderPO,
Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard, M.BillNumber  
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		LEFT JOIN (
				   select S.StuffId,Count(*) as CS FROM $DataIn.gys_shsheet S
				   LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
				   WHERE 1 $SearchRows 
				   Group by S.StuffId
				   ) H  ON H.StuffId=S.StuffId
		WHERE 1 $SearchRowes AND D.ComboxSign!=1 
UNION ALL
       SELECT M.CompanyId,S.Id,S.Mid,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,D.SendFloor,(G.AddQty+G.FactualQty) AS cgQty,M.Date,U.Name AS UnitName,
                G.POrderId,Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard, M.BillNumber  
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stuffcombox B ON B.StockId=S.StockId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=B.mStockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		LEFT JOIN (
				   select S.StuffId,Count(*) as CS FROM $DataIn.gys_shsheet S
				   LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
				   WHERE 1 $SearchRows 
				   Group by S.StuffId
				   ) H  ON H.StuffId=S.StuffId
		WHERE 1 $SearchRowes AND D.ComboxSign=1 
		ORDER BY StuffId,Id";
	//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{	//有图片才可以审核
		$LockRemark="";
		$czSign=1;
		$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$UnitName=$myRow["UnitName"];
		$Id=$myRow["Id"];					//记录ID
		$Date=$myRow["Date"];				//送货单生成日期
		$BillNumber=$myRow["BillNumber"];
		/******************验厂过滤*******************
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
         * */
		/***************************************/
		$StockId=$myRow["StockId"];			//配件需求流水号
		$StuffId=$myRow["StuffId"];			//配件ID

		$StuffCname=$myRow["StuffCname"];	//配件名称
        include"../model/subprogram/stuff_Property.php";//配件属性
		if ($sameStuffId=="") {
			$sameStuffId=$StuffId;
			$sameStuffCname=$StuffCname;
			$sameUnitName=$UnitName;
		}

		$cgQty=$myRow["cgQty"];				//采购总数
		$Qty=$myRow["Qty"];					//供应商送货数量
		$Picture=$myRow["Picture"];			//配件图片
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;



	     if ($CompanyId==2270){
	            $sidSTR=anmaIn($StuffId,$SinkOrder,$motherSTR);
	            $ClientImage="<a href=\"../admin/swapdata/stuffimg_clientLoad.php?d=$cidSTR&f=$sidSTR&Type=&Action=1\" target='_blank'>view</a>";
	       }else{
	            $ClientImage="&nbsp;";
	        }

		if($Picture==1){//有PDF文件
			include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
			}
	    $Mid=$myRow["Mid"];
        $shBillFile="../download/ckshbill/S" . $Mid .".jpg";
        if(file_exists($shBillFile)){
            $Date="<a href='$shBillFile' target='_blank'>$Date</a>";
        }
		//add by zx 2011-0427  begin
		$CompanyId=$myRow["CompanyId"];
		$SendSign=$myRow["SendSign"];

        $POrderId=$myRow["POrderId"];
        $ProductId=$myRow["ProductId"];
        $OrderPO=$myRow["OrderPO"];
        $PQty=$myRow["PQty"];
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$ShipType=$myRow["ShipType"];
		$Leadtime=$myRow["Leadtime"];
        $cName=$myRow["cName"];
	    $Client=$myRow["Client"];
        $TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";

        $showPurchaseorder="[ + ]";
        $ListRow="<tr bgcolor='#FFF' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='".$Cols."'><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";

		$SignString="";
		//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品
		switch ($SendSign){
			case 1:
				$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
					   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
					   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$thQty=mysql_result($thSql,0,"thQty");

				$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
					   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
					   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$bcQty=mysql_result($bcSql,0,"bcQty");
				$cgQty=$thQty-$bcQty;
				$noQty=$cgQty;
				$SignString="(补货)";
				$StockId="本次补货";
			break;
			case 2:
			  $cgQty=0;
			  $noQty=0;
			  $SignString="(备品)";
			  $StockId="本次备品";
			break;
			default :
				$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
					WHERE R.StockId='$StockId'",$link_id);
				$rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
				$noQty=$cgQty-$rkQty;
			 break;
		}

	    if($noQty<=0  && $SendSign!=2){  //当前已全部入库，则显示，入库数量
			$LockRemark="错误，请通知供应商:该需求单已全部入库，请核查该送货单！";
			$czSign=0;
			}
		else {
			if($noQty-$Qty<-0.1 && $SendSign!=2){  //当前送货量比未送货量还大,则强行要改
			    $czSign=0;
				$LockRemark="错误，请通知供应商:本次送货的数量多于未送货的总数，送货数量需更新！";
				}
			else{
				 $QtyStr=$Qty;
			 }
		}
         //仓管备注
         $remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
         if($remarkRow=mysql_fetch_array($remarkSql)){
               $Remark=$remarkRow["Remark"];
             }
         else{
               $Remark="<img src='../images/remark.gif'/>";
             }

		//检查权限
		echo $LockRemark;
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;"; $UpdateReturnClick="&nbsp;";
		$CheckData="<input type='checkbox' disabled />";
		if($czSign==1){//有权限并且订单可以做审核状态
			if($SubAction==31 && $LockRemark==""){//有权限
		      $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
               $UpdateClick="onclick='ShowMessage($Id,this,\"$StockId\")'";
			   $RemarkClick="onClick='ShowRemark(this,$Id)'";

			   $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$Id' />";
            }
			else{//无权限
				if($SubAction==1){
					$UpdateClick=" title='$LockRemark'";
					$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
					}
                                 $RemarkClick="";
				}

			}
	       /*
			if($P icture!=1){//PDF文件
				 $UpdateClick=" title='无配件标准图'";
				 //$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
				 $UpdateIMG="无配件图";

			}
			*/
			//检查是否订单中最后一个需备料的配件
			/*$isLastBgColor = "";
			$isLastStockSql = "SELECT G.StockId, D.StuffId
										   FROM $DataIn.cg1_stocksheet G
										   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
										   Left Join( SELECT SUM(Qty) AS llQty ,StockId  FROM $DataIn.ck5_llsheet WHERE  SUBSTRING(StockId,0,12)='$POrderId' group by StockId ) L On L.StockId = G.StockId
										   WHERE G.POrderId = '$POrderId'
										   AND T.mainType <2
										   AND K.tStockQty < G.OrderQty";
			$isLastStockResult = mysql_query($isLastStockSql);
			if(mysql_num_rows($isLastStockResult) == 1)
			{
				$lastStockRow = mysql_fetch_assoc($isLastStockResult);
				$lastStuffId = $lastStockRow["StuffId"];
				if($lastStuffId == $StuffId)
				{
					$isLastBgColor = "bgcolor = '#CFFFA0'";
				}
			}*/


			if($sameStuffId!=$StuffId ) {
				if ($isCount>1) {  //同一个配件大于1次的才统计显示
					echo"<tr style='color:#03F; font-weight:bold' ><td class='A0111' align='center' height='25' ></td>";
					echo "<td class='A0101' align='center'>&nbsp;</td>";
					echo"<td class='A0101' align='center'>合计:</td>";
					echo"<td class='A0101' align='center'>&nbsp;</td>";
                    echo"<td class='A0101' align='center'>&nbsp;</td>";
                    echo"<td class='A0101' align='center'>&nbsp;</td>";
                    echo"<td class='A0101' align='center'>&nbsp;</td>";
                    echo"<td class='A0101' align='center'>&nbsp;</td>";
                    echo"<td class='A0101' align='center'>&nbsp;</td>";
					echo"<td class='A0101' align='center'>$sameStuffId</td>";
					echo"<td class='A0101'>&nbsp;</td>";
					echo"<td class='A0101' align='right'>&nbsp;</td>";	//采购总数
					echo"<td class='A0101' align='right'><div class='redB'>&nbsp;</div></td>";	//未收货数量
					echo"<td class='A0101' align='right'>$SumQty</td>";//送货数量
					echo"<td class='A0101' align='center'>&nbsp;</td>";
					echo"<td class='A0101' align='center'>&nbsp;</td>";
					echo"<td class='A0101' align='center' >&nbsp;</td>";
					echo"<td class='A0101' align='center' &nbsp;>&nbsp;</td>";
				}
				$isCount=1;  //同一个配件大于1次的才统计显示
				$SumcgQty=$cgQty;
				$SumnoQty=$noQty;
				$SumQty=$Qty;
				$sameStuffId=$StuffId;
				$sameStuffCname=$StuffCname;
				$sameUnitName=$UnitName;
			}
			else {
				$SumcgQty=$SumcgQty+$cgQty;
				$SumnoQty=$SumnoQty+$noQty;
				$SumQty=$SumQty+$Qty;
				$isCount=$isCount+1;

			}


			echo"<tr $isLastBgColor><td class='A0111' style='cursor: pointer' align='center' id='theCel$i' height='25' onClick='NewShowOrHide(ListRow$i,theCel$i,$i,$POrderId,$StockId);'>$showPurchaseorder</td>";
			echo "<td class='A0101' align='center'>$i</td>";
			echo "<td class='A0101' align='center'>$CheckData</td>";
			echo "<td class='A0101' align='center'>$Client</td>";

			//增加业务单号 产品编号 送货单号
			echo "<td class='A0101' align='center'>$OrderPO</td>";
			echo "<td class='A0101' align='center'>$cName</td>";
			echo "<td class='A0101' align='center'>$BillNumber</td>";

			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$StockId</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='right'>$cgQty</td>";	//采购总数
			echo"<td class='A0101' align='right'><div class='redB'>$noQty</div></td>";	//未收货数量
			echo"<td class='A0101' align='right'>$Qty</td>";//送货数量
			echo"<td class='A0101' align='center'>$UnitName</td>";
            echo"<td class='A0101' align='center' $RemarkClick>$Remark</td>";
            echo"<td class='A0101' align='center'>$SendFloor</td>";
			echo"<td class='A0101' align='center' id='updatetd$i' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			echo $ListRow;
			$i++;
		}while ($myRow = mysql_fetch_array($myResult));
		if ($isCount>1) {  //同一个配件大于1次的才统计显示
			echo"<tr style='color:#03F; font-weight:bold' ><td class='A0111' align='center' height='25' ></td>";
			echo "<td class='A0101' align='center'>&nbsp;</td>";
			echo"<td class='A0101' align='center'>合计:</td>";
			echo"<td class='A0101' align='center'>&nbsp;</td>";
            echo"<td class='A0101' align='center'>&nbsp;</td>";
            echo"<td class='A0101' align='center'>&nbsp;</td>";
            echo"<td class='A0101' align='center'>&nbsp;</td>";
            echo"<td class='A0101' align='center'>&nbsp;</td>";
            echo"<td class='A0101' align='center'>&nbsp;</td>";
			echo"<td class='A0101' align='center'>$sameStuffId</td>";
			echo"<td class='A0101'>&nbsp;</td>";
			echo"<td class='A0101' align='right'>&nbsp;</td>";	//采购总数
			echo"<td class='A0101' align='right'><div class='redB'>&nbsp;</div></td>";	//未收货数量
			echo"<td class='A0101' align='right'>$SumQty</td>";//送货数量
			echo"<td class='A0101' align='center'>&nbsp;</td>";
			echo"<td class='A0101' align='center'>&nbsp;</td>";
			echo"<td class='A0101' align='center' >&nbsp;</td>";
			echo"<td class='A0101' align='center' &nbsp;>&nbsp;</td>";
		}
	}
else{
	echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' bgcolor='#fff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
<div id='divMessage' class="divMessage" style="position:absolute;word-break:break-all; width:330px;display:none;z-index:10;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #AAA;background:#CCC;">
 <table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;padding-left:10px;'>
      <tr><td colspan="3" height="40" align="center" style="font-size:18px;color:#00F;">审核确认</td></tr>
      <tr><td colspan="3" height="25" align="center"><div id='divStockId' style="font-size:14px;color:#F00;"></div></td></tr>
      <tr><td colspan="3" height="5"></td></tr>
     <tr><td width="105px" align="center" height="35" ><span class='ButtonH_25' id='backMsgBtn' value='退  回'  onclick='MsgBtnClick(2)'>退  回</span></td>
         <td  width="105px" align="center"><span class='ButtonH_25'   id='canelMsgBtn' value='取  消'  onclick='MsgBtnClick(0)'>取  消</span></td>
         <td  width="105px" align="center"><span class='ButtonH_25' id='okMsgBtn' value='通  过'  onclick='MsgBtnClick(1)'> 通  过</span></td>
      </tr>
  </table>
</div>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script>
var curTarget=null;
var curId=null;
function ShowMessage(Id,ee,StockId)
{
    var divMessage=document.getElementById("divMessage");
    divMessage.style.left = window.pageXOffset+(window.innerWidth-350)/2+"px";
    divMessage.style.top = window.pageYOffset+(window.innerHeight-120)/2+"px";
    curId=Id;
    curTarget=ee;
    document.getElementById("divStockId").innerHTML="需求单号:"+StockId;
    divMessage.style.display='block';
}
var checkboxs = null;

function batchNote(){
    var choosedRow=0;
    var Ids = '';
    checkboxs = new Array();
    jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
            var index = jQuery(this).attr('id').replace('checkid', '');

            checkboxs[choosedRow] = index;

            choosedRow=choosedRow+1;
            if (choosedRow == 1) {
                Ids = jQuery(this).val();
            } else {
                Ids = Ids + "," + jQuery(this).val();
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    var Remark = jQuery('input[name="Remark"]').val();
    jQuery.ajax({
        url : 'item5_1_ajax.php',
        async: false,
        data : {
            ActionId: 44,
            Ids : Ids,
            Remark : Remark
        },
        type: "post",
        dataType : 'json',
        success : function(){
        }
    });


    window.location.reload();
}

function showRemarkAll(e){
    var choosedRow=0;
    var Ids = '';
    checkboxs = new Array();
    jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
            var index = jQuery(this).attr('id').replace('checkid', '');

            checkboxs[choosedRow] = index;

            choosedRow=choosedRow+1;
            if (choosedRow == 1) {
                Ids = jQuery(this).val();
            } else {
                Ids = Ids + "," + jQuery(this).val();
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
    openWinDialog(e,"item5_1_change.php",405,300,'bottom');
}

function batchCheck() {
	var choosedRow=0;
	var Ids;
	checkboxs = new Array();
	jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
			var index = jQuery(this).attr('id').replace('checkid', '');

        	checkboxs[choosedRow] = index;

			choosedRow=choosedRow+1;
    		if (choosedRow == 1) {
				Ids = jQuery(this).val();
			} else {
				Ids = Ids + "," + jQuery(this).val();
			}
        }
	});

	if (choosedRow == 0) {
		alert("该操作要求选定记录！");
		return;
	}

	var divMessage=document.getElementById("divMessage");

	divMessage.style.left = window.pageXOffset+(window.innerWidth-300)/2+"px";
	divMessage.style.top = window.pageYOffset+(window.innerHeight-120)/2+"px";
	curId=Ids;
	curTarget=null;
	document.getElementById("divStockId").innerHTML="确定操作当前全部选择";
	divMessage.style.display='block';
}

function MsgBtnClick(index)
{
    var divMessage=document.getElementById("divMessage");
    divMessage.style.display='none';

    switch(index){
        case 1:
            if (curId>0 || curId.length > 0){
               RegisterEstate(curId,curTarget,17);
            }
            break;
        case 2:
            if (curId>0 || curId.length > 0){
               RegisterEstate(curId,curTarget,15);
            }
            break;
        default:
            break;
    }
}

function RegisterEstate(Id,ee,ActionId){
  // var msg="确定审核通过";
  // if(confirm(msg)){
    var conFlag=true;
    if (ActionId==15)
     {
        var strResponse=prompt("退回原因：","");
        strResponse=strResponse.replace(/(^\s*)|(\s*$)/g,"");
        if (strResponse=="")  conFlag=false;
     }
     else
     {
         strResponse="";
     }

    if (conFlag){
        //alert(ActionId);
        //return;
        var url="item5_1_ajax.php";
        var data = "Id="+Id+"&ActionId="+ActionId+"&Remark="+strResponse;
        var ajax=InitAjax();
        ajax.open("POST",url,true);
        ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4){
                  var returnText=ajax.responseText;
                  returnText=returnText.replace(/(^\s*)|(\s*$)/g,"");
                        if(returnText=="Y"){
                            //alert("审核成功");
                            window.location.reload();
                            //更新该单元格底色和内容
                            if (ee != null) {
                                ee.innerHTML="&nbsp;";
                                if (ActionId==17){
                                    ee.style.backgroundColor="#339900";
                                 }
                                 else{
                                    ee.style.backgroundColor="#FF0000";
                                 }
                                 ee.onclick="";
                            } else {
								for (var i in checkboxs) {
									var checkbox = jQuery("#checkId" + checkboxs[i]);

									jQuery("#checkId" + checkboxs[i]).attr('checked', false);
									jQuery("#checkId" + checkboxs[i]).attr('disabled', 'disabled');

									var td = document.getElementById("updatetd" + checkboxs[i]);
									td.innerHTML ="&nbsp;";

									if (ActionId==17){
										td.style.backgroundColor="#339900";
	                                 }
	                                 else{
	                                	 td.style.backgroundColor="#FF0000";
	                                 }
									td.onclick="";

								}

                            }

                        }else{
                            alert("审核失败！数据更新出现错误。"+returnText);
                        }
                    }
                };
        ajax.send(data);

        jQuery.ajax({
            url : 'item2_3_2_ajax.php',
            async: false,
            data : {
                ActionId: 19,
                Ids : Id
            },
            type: "post",
            dataType : 'json',
            success : function(){
            }
        });
    }
}

 function ShowRemark(e,Id){
    var remark=e.innerHTML;
    if (remark.indexOf("<img")!=-1){
        remark="";
    }
    var strResponse=prompt("备注信息：",remark);
    strResponse=strResponse.replace(/(^\s*)|(\s*$)/g,"");
    if (strResponse){
       var url="item5_1_ajax.php?Id="+Id+"&ActionId=5&Remark="+strResponse;

	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
               if (ajax.responseText=="Y")
		   e.innerHTML=strResponse;
	     }
	 }
　	ajax.send(null);
    }
}

function viewStuffdata() {
	var diag = new Dialog("live");
	var CompanyId2=document.getElementById("CompanyId2").value;
	diag.Width = 880;
	diag.Height = 500;
	diag.Title = "配件资料";
	diag.URL = "stuffnotsent_s1.php?myCompanyId="+CompanyId2;
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

function editTabRecord(BackStuffId){
  		var Rowstemp=BackStuffId.split(",");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldArray=Rowstemp[i].split("^^");
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

				//三、配件ID
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";

				//四：配件名称
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="220";

				//五:订单需求数
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="70";

				//六：采购总数
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//七：已送货总数
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//八：未送货数
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//第九列:本次送货
				oTD=oTR.insertCell(8);
				oTD.innerHTML="<input type='text' name='sendQty[]' id='sendQty"+tmpNum+"' size='6' class='I0000L' value='"+FieldArray[5]+"' onblur='Indepot(this,"+FieldArray[5]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//第十列:未补货数
				oTD=oTR.insertCell(9);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//第十列:本次补货
				oTD=oTR.insertCell(10);
				oTD.innerHTML="<input type='text' name='BSQty[]' id='BSQty"+tmpNum+"' size='6' class='I0000L' value='' onblur='Indepot(this,"+FieldArray[6]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//第十一列:本次备品
				oTD=oTR.insertCell(11);
				oTD.innerHTML="<input type='text' name='BPQty[]' id='BPQty"+tmpNum+"' size='6' class='I0000L' value=''  onfocus='toTempValue(this.value)'>";
				oTD.className ="A0100";
				oTD.align="center";
				oTD.width="69";

				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
}

function CheckForm(){
	var Message="";
	var tempQty="";
	if(ListTable.rows.length<1){
		Message+="没有设置送货配件的数据!";
		}
	var TempGysNumber=document.getElementById("TempGysNumber").value;

	if(TempGysNumber==""){alert("请输入供应商送货单号");return false;}

	for (var i=0;i<ListTable.rows.length;i++){
	      var index=i+1;
	      var sendQty=document.getElementById("sendQty"+index).value;
	      var BSQty=document.getElementById("BSQty"+index).value;
	      var BPQty=document.getElementById("BPQty"+index).value;

		  if(sendQty==""){alert("本次送货不能空");return false;}
		  if (tempQty==""){tempQty=sendQty+"^^"+BSQty+"^^"+BPQty;}
		  else {tempQty=tempQty + "|" + sendQty+"^^"+BSQty+"^^"+BPQty;}
	   }
	if(Message!=""){
		alert(Message);return false;
		}
	else{
		var StockValues="";
		var arrQty=tempQty.split("|");
		for(i=0;i <arrQty.length;i++){
			if(i>0) StockValues=StockValues+"|";
			StockValues=StockValues+ListTable.rows[i].cells[2].innerText+"_"+arrQty[i];
		}
		//alert(StockValues);
		document.getElementById("AddIds").value=StockValues;
		return true;
		}
}


function toTempValue(textValue){
	document.getElementById("TempValue").value=textValue;
	}


function Indepot(thisE,SumQty){
	var oldValue=document.getElementById("TempValue").value;
	var thisValue=thisE.value;
	if(thisValue!=""){
		var CheckSTR=fucCheckNUM(thisValue,"Price");
		if(CheckSTR==0){
			alert("不是规范的数字！");
			thisE.value=oldValue;
			return false;
			}
		else{
			if((thisValue>SumQty) ){
				alert("不在允许值的范围:"+SumQty);
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
	var tempIds=document.g
        etElementById("TempCompanyId").value;
	var selId=e.value;
	if (rowLen>0 && tempIds!=selId){
		alert('改变供应商将清除现已添加数据!');
	    for (i=rowLen;i>0;i--){
	       ListTable.deleteRow(i-1);
	     }
	   document.getElementById("TempCompanyId").value=selId;
	 }
}

function toExcelAll(SearchRowes,SearchRows) {
    document.form1.action = "item5_1_export.php?SearchRowes=" + SearchRowes + "&SearchRows=" + SearchRows;
    document.form1.target = "download";
    document.form1.submit();
}

</script>