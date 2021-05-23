<?php
$Th_Col="选项|30|配件|40|ID|30|客户|80|业务单号|90|产品中文名|80|期限|60|工单流水号|90|半成品ID|60|采购流水号|90|半成品名称|320|生管备注|180|Qty|60|已生产|60|登记数|60|登记|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
$nowInfo="当前:半成品生产";
//步骤4：需处理-条件选项
$checkScSign=3;//可生产标识
//$SearchRows=" AND SC.WorkShopId='$fromWorkshop' AND SC.ActionId='$fromActionId' AND SC.scFrom>0 AND SC.Estate>0 ";
$SearchRows=" AND  SC.ActionId='$fromActionId' AND SC.scFrom>0 AND SC.Estate>0 ";

if (strlen($tempStuffCname)>0){
	$SearchRows.=" AND D.StuffCname LIKE '%$tempStuffCname%' ";
	$searchList1="<span class='ButtonH_25'  id='cancelQuery' value='取消' onclick='ResetPage(1,1)'>取消</span>";
    }
else{
	$searchList1="<input type='text' name='tempStuffCname' id='tempStuffCname' value='' width='20'/> &nbsp;<span class='ButtonH_25'  id='okQuery' value='查询' onclick='ResetPage(1,1)'>查询</span>";
 }



echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr height='40px'><td colspan='".($Cols-6)."'  class=''>";


$WorkShopResult= mysql_query("SELECT SC. WorkShopId,W.Name AS WorkShopName 
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
WHERE 1 $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign GROUP BY SC.WorkShopId  order by SC.WorkShopId DESC ",$link_id);
if ($WorkShopRow = mysql_fetch_array($WorkShopResult)){
    echo "<select name='kWorkShopId' id='kWorkShopId'  onChange='ResetPage(1,1)'>";
    $i=1;
    do{
        $theWorkShopId=$WorkShopRow["WorkShopId"];
        $theWorkShopName=$WorkShopRow["WorkShopName"];
        $kWorkShopId=$kWorkShopId==""?$theWorkShopId:$kWorkShopId;
        if($kWorkShopId==$theWorkShopId){
            echo "<option value='$theWorkShopId' selected>$theWorkShopName</option>";
            $SearchRows.=" AND SC.WorkShopId='$theWorkShopId'";
        }
        else{
            echo "<option value='$theWorkShopId'>$theWorkShopName</option>";
        }
        $i++;
    }while($WorkShopRow = mysql_fetch_array($WorkShopResult));
    echo "</select>&nbsp;";
}

//增加业务单号下拉筛选
$clientResult = mysql_query("
        SELECT Y.OrderPO
        FROM $DataIn.yw1_scsheet SC
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
        WHERE 1  $SearchRows and Y.OrderPO is not null AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign GROUP BY Y.OrderPO
        ",$link_id);

if($clientRow = mysql_fetch_array($clientResult)) {
    echo"<select name='OrderPO' id='OrderPO' onchange='ResetPage(1,1)'>";
    do{
        $thisOrderPO=$clientRow["OrderPO"];
        $OrderPO=$OrderPO==""?$thisOrderPO:$OrderPO;
        if($OrderPO==$thisOrderPO){
            echo"<option value='$thisOrderPO' selected>$thisOrderPO</option>";
            $SearchRows.=" and Y.OrderPO='$thisOrderPO' ";
        }
        else{
            echo"<option value='$thisOrderPO'>$thisOrderPO</option>";
        }
    }while ($clientRow = mysql_fetch_array($clientResult));
    echo"</select>&nbsp;";
}

//增加客户下拉筛选
$ForshortResult = mysql_query("SELECT O.Forshort 
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
LEFT JOIN $DataIn.stuffdata SD  ON SD.StuffId = M.StuffId
WHERE 1  $SearchRows  AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign GROUP BY O.Forshort",$link_id);

if($ForshortRow = mysql_fetch_array($ForshortResult)) {
    echo"<select name='khCompanyId' id='khCompanyId' onchange='ResetPage(1,1)'>";
    do{
        $thisForshort=$ForshortRow["Forshort"];
        $khCompanyId=$khCompanyId==""?$thisForshort:$khCompanyId;
        if($khCompanyId==$thisForshort){
            echo"<option value='$thisForshort' selected>$thisForshort</option>";
            $SearchRows.=" and O.Forshort='$thisForshort' ";
        }
        else{
            echo"<option value='$thisForshort'>$thisForshort</option>";
        }
    }while ($ForshortRow = mysql_fetch_array($ForshortResult));
    echo"</select>&nbsp;";
}

 echo"</td><td colspan='4' align='right' class=''><span class='ButtonH_25'  id='batchUpdate'  onclick='batchOperate();'>保存</span>&nbsp;$searchList1&nbsp;</td><td colspan='2'  class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";


$DefaultBgColor=$theDefaultColor;
$j=2;$i=1;
$mySql="SELECT O.Forshort,SC.Id,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId AS scStockId,SC.Remark,
D.StuffId,D.StuffCname,D.Picture,SD.TypeId,
G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,G.Mid,SC.mStockId,Y.OrderPO,P.cName
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
LEFT JOIN $DataIn.stuffdata SD  ON SD.StuffId = M.StuffId
WHERE 1  $SearchRows  AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign GROUP BY SC.Id  ORDER BY G.DeliveryWeek ASC";
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
        $DeliveryWeek=$myRow["DeliveryWeek"];//本配件的交期
        //取最上层半成品的交期
	    $DeliveryWeekRow=mysql_fetch_array(mysql_query("SELECT G.DeliveryWeek,G.DeliveryDate  
	    FROM $DataIn.cg1_semifinished  S   
	    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.mStockId
	    WHERE S.POrderId='$POrderId' AND G.Level = 1 LIMIT 1",$link_id));
        $DeliveryWeek = $DeliveryWeekRow["DeliveryWeek"];
        $DeliveryDate = $DeliveryWeekRow["DeliveryDate"];
        include "../model/subprogram/deliveryweek_toweek.php";
		$sumQty=$sumQty+$Qty;

		$scStockId = $myRow["scStockId"];
		$mStockId = $myRow["mStockId"];
		$TypeId  = $myRow["TypeId"];
		$Estate  = $myRow["Estate"];

		$OrderPO  = $myRow["OrderPO"];
		$cName  = $myRow["cName"];

        /*
                    $llSign = 0 ; $blSign = 0 ;
                    $k = 0 ; $tempblK = 0 ;
                    $templlK = 0 ;
                    $CheckllResult = mysql_query("SELECT S.OrderQty,S.StockId
                    FROM $DataIn.yw1_stocksheet S
                    LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId = S.StockId
                    WHERE S.sPOrderId = $sPOrderId  AND G.blsign = 1",$link_id);
                    while($CheckllRow  = mysql_fetch_array($CheckllResult)){

                        $llOrderQty  =  $CheckllRow["OrderQty"];
                        $llStockId   =  $CheckllRow["StockId"];

                        //备料情况
                        $checkblQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS blQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = $sPOrderId AND StockId='$llStockId'",$link_id));
                        $thisblQty=$checkblQtyResult["blQty"];
                        if($thisblQty >0)$blSign = 1; //部分领料
                        if($llOrderQty ==$thisblQty)$tempblK++;

                        //领料情况

                        $checkllQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = $sPOrderId AND StockId='$llStockId' AND Estate=0",$link_id));
                        $thisllQty=$checkllQtyResult["llQty"];
                        if($thisllQty >0)$llSign = 1; //部分领料
                        if($llOrderQty ==$thisllQty)$templlK++;
                        $k++;
                    }

                    if($tempblK == $k)$blSign = 2 ;//全部备料
                    if($templlK == $k)$llSign = 2 ;//全部领料
                    $bgColor="";
                    if($blSign ==2 && $llSign >0){
                       $bgColor = "bgcolor='#FFB6C1'";
                    }else if($blSign ==2 && $llSign ==0){
                       $bgColor = "bgcolor='#A2CD5A'";
                    }else if($blSign == 1){
                       $bgColor = "bgcolor='#BFEFFF'";
                    }
        */
                  //领完料未下采购单的，自动下采购单

                 $cgMid = $myRow["Mid"];
                 if($cgMid==0 ){
                    include "item1_1_auto_cg.php";
                 }


        $czSign=1;//操作标记
		//已完成的工序数量
		$CheckscQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE  C.sPOrderId = '$sPOrderId' AND C.StockId = $scStockId ",$link_id));
		$scQty=$CheckscQty["scQty"];
		//已生产数字显示方式
		switch($scQty){
			case 0:$scQty="&nbsp;";break;
			default://生产数量非0
				if($Qty==$scQty){//生产完成
					$scQty="<div class='greenB'>$scQty</div>";$czSign=0;//不能操作
					}
				else{
					if($Qty>$scQty){//未完成
						$scQty="<div class='yellowB'>$scQty</div>";
						}
					else{//多完成
						$scQty="<div class='redB'>$scQty</div>";
						}
				}
			break;
		  }


		 //操作权限:如果权限=31 则可以操作,否则不能操作
		 $UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
		 $CheckData="<input type='checkbox' disabled />";
		 if($czSign==1){//可以操作
			//if($SubAction==31  ){//有权限:需要是该类别下的小组成员，方有权登记
			      $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
                  $UpdateClick="onclick='RegisterQty($POrderId,$sPOrderId,$scStockId,2)'";
                  $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$POrderId|$sPOrderId|$scStockId' />";
             // }
           }
        /*
                   if($llSign!=2){
                        $UpdateClick = "";
                    }

                     if($blSign ==0){
                            $UpdateIMG = "<span class='redB'>未备料</span>";
                            $UpdateClick="";
                        }else if ($blSign ==1){
                            $UpdateIMG = "<span class='blueB'>部分备料</span>";
                            $UpdateClick="";
                        }else if ($blSign ==2 && $llSign == 0){
                            $UpdateIMG = "<span class='greenB'>未领料</span>";
                            $UpdateClick="";
                        }

                    */
		   if($Estate==0){//生产完毕
		       $czSign = 0;

			  $UpdateIMG="";
			  $UpdateClick="bgcolor='#339900'";
			  $CheckData="<input type='checkbox' disabled />";
			}

			//动态读取配件资料
			$showPurchaseorder="[ + ]";
			$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";

			echo"<tr $bgColor><td class='A0111' align='center'>$CheckData</td><td class='A0101' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,\"$POrderId\",\"$sPOrderId\",\"showScOrder\");' >$showPurchaseorder</td>";
			echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
			echo"<td class='A0101' align='center' >$Forshort</td>";
			echo"<td class='A0101' align='center' >$OrderPO</td>";
			echo"<td class='A0101' align='center' >$cName</td>";
			echo"<td class='A0101' align='center' >$DeliveryWeek</td>";
			echo"<td class='A0101' align='center' >$sPOrderId</td>";
			echo"<td class='A0101' align='center' >$StuffId</td>";
			echo"<td class='A0101' align='center' >$mStockId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='center'>$Remark</td>";
			echo"<td class='A0101' align='right'>$Qty</td>";
			echo"<td class='A0101' align='center'>$scQty</td>";
			if ($czSign == 1) {
			    $updateQty = $Qty-$scQty;
			    echo"<td class='A0101' align='center'><input type='text' value='$updateQty' initval='$updateQty' id='updateQty$i' style='width:50px' /></td>";
			} else {
			    echo"<td class='A0101' align='center'>&nbsp;</td>";
			}
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			echo $ListRow;
			$j=$j+2;$i++;
			}while ($myRow = mysql_fetch_array($myResult));
		}
else{
	echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'>
	         <div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
?>
<script>
function batchOperate() {
	//$POrderId|$sPOrderId|$scStockId
	var choosedRow=0;
	var qtyerror=false;

	var POrderIds;
	var sPOrderIds;
	var scStockIds;
	var qtys;
	jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {

			var Ids = jQuery(this).val();

			var splitArr = Ids.split('|');
			if (splitArr.length != 3) {
				alert('数据错误!');
				return;
			}

			var index = jQuery(this).attr('id').replace('checkId', '');
			var updateQtyObj = jQuery('#updateQty' + index);
			var updateQty = updateQtyObj.val();

			var initval = updateQtyObj.attr('initval');

			var CheckSTR=fucCheckNUM(updateQty,"Price");
			if(CheckSTR==0){
				updateQtyObj.css('background','#FFCC00')
				alert('不是规范的数字！');
				qtyerror=true;
				return false;
			}

			var thisValue=Number(updateQty);
			var MaxValue=Number(initval);

			if((thisValue>MaxValue) || thisValue==0){

				updateQtyObj.css('background','#FFCC00')
				alert('超出范围！');
				qtyerror=true;
				return false;
			}

			choosedRow=choosedRow+1;

    		if (choosedRow == 1) {
    			POrderIds=splitArr[0];
    			sPOrderIds=splitArr[1];
    			scStockIds=splitArr[2];
    			qtys=updateQty;
			} else {

    			POrderIds=POrderIds+ '|' + splitArr[0];
    			sPOrderIds=sPOrderIds+ '|' + splitArr[1];
    			scStockIds=scStockIds+ '|' + splitArr[2];
    			qtys=qtys+ '|' + updateQty;
			}
        }
	});

	if (qtyerror) return;
	if (choosedRow == 0) {
		alert("该操作要求选定记录！");
		return;
	}

	var url="item1_1_scdj_1_ajax.php";
    var ajax=InitAjax();
    ajax.open("POST",url,true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    ajax.onreadystatechange =function(){
	 if(ajax.readyState==4){// && ajax.status ==200
		// console.log(ajax.responseText);
		 if(ajax.responseText.trim()=="Y"){//更新成功
			 //document.form1.submit();
			 ResetPage(1,1);
			}
		 else{
		    alert ("保存失败！");
		  }
		}
	 }
   ajax.send("POrderIds="+POrderIds+"&sPOrderIds="+sPOrderIds+"&scStockIds="+scStockIds+"&qtys="+qtys);
}

</script>
