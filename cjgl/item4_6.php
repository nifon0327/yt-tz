<?php
$SearchRows=" AND SC.scFrom>0 AND SC.Estate>0 AND SC.Qty>SC.ScQty";//生产分类里的ID
$nowInfo="当前:变更生产车间";
$WorkShopList = "";
$updateWebPage = "item4_6_change.php";
$nullable = ' AND O.Forshort != \'南京上坊\' ';

$WorkShopResult= mysql_query("SELECT SC.ActionId as WorkShopId,W.Name AS WorkShopName 
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.workorderaction W  ON W.ActionId = SC.ActionId
WHERE 1 $SearchRows GROUP BY SC.ActionId  order by SC.ActionId DESC ",$link_id);


if ($WorkShopRow = mysql_fetch_array($WorkShopResult)){
    $WorkActionList="<select name='kWorkShopId' id='kWorkShopId' onChange='ResetPage(1,4)'>";
    $SearchRows.="  ";
    $i=1;
    do{
        $theWorkActionId=$WorkShopRow["WorkShopId"];
        $theWorkActionName=$WorkShopRow["WorkShopName"];
         $kWorkShopId=$kWorkShopId==""?$theWorkActionId:$kWorkShopId;
        if($kWorkShopId==$theWorkActionId){
            $WorkActionList.="<option value='$theWorkActionId' selected>$theWorkActionName</option>";
            $SearchRows.=" AND SC.ActionId='$theWorkActionId'";
            $nowInfo .=" - $theWorkActionName";
        }
        else{
            $WorkActionList.="<option value='$theWorkActionId'>$theWorkActionName</option>";
        }
        $i++;
    }while($WorkShopRow = mysql_fetch_array($WorkShopResult));
    $WorkActionList.="</select>";
}

$WorkShopResult= mysql_query("SELECT SC.WorkShopId,W.Name AS WorkShopName 
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
WHERE 1 $SearchRows GROUP BY SC.WorkShopId  order by SC.WorkShopId",$link_id);


if ($WorkShopRow = mysql_fetch_array($WorkShopResult)){
    $WorkShopList="<select name='WorkShopId' id='WorkShopId' onChange='ResetPage(1,4)'>";
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

$ForshortResult= mysql_query("SELECT DISTINCT O.Forshort
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = S.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT  JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
    LEFT JOIN $DataIn.ck5_llsheet ck ON ck.sPOrderId = SC.sPOrderId
    LEFT JOIN  $DataIn.staffmain SF ON SF.Number = ck.Operator
WHERE 1  $SearchRows $nullable AND G.Mid>0 ",$link_id);
if ($ForshortRow = mysql_fetch_array($ForshortResult)){
    $ForshortList="<select name='Forshort' id='Forshort' onChange='ResetPage(1,4)'>";
    $i=1;
    do{
        $theForshort=$ForshortRow["Forshort"];
        $Forshort=$Forshort==""?$theForshort:$Forshort;
        if($Forshort==$theForshort){
            $ForshortList.="<option value='$theForshort' selected>$theForshort</option>";
            $SearchRows.=" AND O.Forshort='$theForshort'";
        }
        else{
            $ForshortList.="<option value='$theForshort'>$theForshort</option>";
        }
        $i++;
    }while($ForshortRow = mysql_fetch_array($ForshortResult));
    $ForshortList.="</select>";
}

//$lldateResult= mysql_query("SELECT ck.Date
//	FROM  $DataIn.yw1_scsheet SC
//	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
//	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
//	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
//	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
//		    LEFT JOIN $DataIn.ck5_llsheet ck ON ck.sPOrderId = SC.sPOrderId
//    LEFT JOIN  $DataIn.staffmain SM ON SM.Number = ck.Operator
//WHERE 1 $SearchRows and ck.Date is not null GROUP BY ck.Date  order by ck.Date",$link_id);
//if ($lldateRow = mysql_fetch_array($lldateResult)){
//    $lldateList="<select name='lldate' id='lldate' onChange='ResetPage(1,4)'>";
//    $i=1;
//    do{
//        $thelldate=$lldateRow["Date"];
//        $lldate=$lldate==""?$thelldate:$lldate;
//        if($lldate==$thelldate){
//            $lldateList.="<option value='$thelldate' selected>$thelldate</option>";
//            $SearchRows.=" AND ck.Date='$thelldate'";
//        }
//        else{
//            $lldateList.="<option value='$thelldate'>$thelldate</option>";
//        }
//        $i++;
//    }while($lldateRow = mysql_fetch_array($lldateResult));
//    $lldateList.="</select>";
//}

$OrderPOList = '';
$OrderPOResult= mysql_query("SELECT S.OrderPO FROM $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet S on SC.POrderId=S.POrderId
INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
LEFT JOIN $DataIn.ck5_llsheet ck ON ck.sPOrderId = SC.sPOrderId
WHERE SC.POrderId in
(
SELECT POrderId from $DataIn.ck5_llsheet 
GROUP BY POrderId
) $SearchRows GROUP BY S.OrderPo",$link_id);

if ($OrderPORow = mysql_fetch_array($OrderPOResult)){
    $OrderPOList .="<select name='OrderPO' id='OrderPO' onChange='ResetPage(1,4)'>";
    $OrderPOList .= "<option value='all' >全部PO</option>";
    $i=1;
    do{
        $theOrderPO=$OrderPORow["OrderPO"];
        $OrderPO=$OrderPO==""?$theOrderPO:$OrderPO;
        if($OrderPO==$theOrderPO){
            $OrderPOList.="<option value='$theOrderPO' selected>$theOrderPO</option>";
            $SearchRows.=" AND S.OrderPO='$theOrderPO'";
        }
        else{
            $OrderPOList.="<option value='$theOrderPO'>$theOrderPO</option>";
        }
        $i++;
    }while($OrderPORow = mysql_fetch_array($OrderPOResult));
    $OrderPOList.="</select>";
}


// 操作人
$creatorList = '';
$creatorResult = mysql_query("select b.Number,b.Name from $DataIn.ck5_llsheet a left join staffmain b on a.creator = b.number group by a.creator",$link_id);
if ($creatorRow = mysql_fetch_array($creatorResult)) {
    $creatorList .= "<select name='creator' id='creator' onchange='ResetPage(1,4)'>";
    $creatorList .= "<option value='all' >全部</option>";
    do{
        $Number=$creatorRow["Number"];
        $Name=$creatorRow["Name"];
        $creator = $creator == ""?$Number:$creator;
        if($creator==$Number){
            $creatorList .= "<option value='$Number' selected>$Name</option>";
            $SearchRows.=" and SF.Number='$Number' ";
            $stmRows = " where b.Number='$Number' ";
        }
        else{
            $creatorList .= "<option value='$Number'>$Name</option>";
        }
    }while ($creatorRow = mysql_fetch_array($creatorResult));
    $creatorList .= "</select>&nbsp;";
}

// 操作时间
$timeList = '';
$timeResult = mysql_query("
SELECT DISTINCT DATE_FORMAT(SC.scDate,'%Y-%m-%d') as scDate
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = S.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT  JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
    LEFT JOIN $DataIn.ck5_llsheet ck ON ck.sPOrderId = SC.sPOrderId
    LEFT JOIN  $DataIn.staffmain SF ON SF.Number = ck.Operator
WHERE 1  $SearchRows AND G.Mid>0 order by DATE_FORMAT(SC.scDate,'%Y-%m-%d') desc",$link_id);
if ($timeRow = mysql_fetch_array($timeResult)) {
    $timeList .= "<select name='created' id='created' onchange='ResetPage(1,4)'>";
    $timeList .= "<option value='all' >全部</option>";
    do{
        $operation=$timeRow["scDate"];
        $created = $created == ""?$operation:$created;
        if($created==$operation){
            $timeList .= "<option value='$operation' selected>$operation</option>";
            $SearchRows.=" and DATE_FORMAT(SC.scDate,'%Y-%m-%d') = '$operation' ";
        }
        else{
            $timeList .= "<option value='$operation'>$operation</option>";
        }
    }while ($timeRow = mysql_fetch_array($timeResult));
    $timeList .= "</select>&nbsp;";
}


$BatchButton="&nbsp;&nbsp;<span class='ButtonH_25' onclick='batchChangeWorkShop(this);' >变更车间</span>";

$batchChangeTime="&nbsp;&nbsp;<span class='ButtonH_25' onclick='batchChangescTime(this);' >设置生产时间</span>";

$toExcelAll='&nbsp;&nbsp;<a href="item4_6_excel.php?kWorkShopId='.$kWorkShopId.'&SearchRows='.$SearchRows.'" class="ButtonH_25">导　出</a>';

$batchFlowSheet = "&nbsp;&nbsp;<span class='ButtonH_25' onclick='batchFlowExport(this);' >批量导出流转单</span>";

$batchCodeSheet = "&nbsp;&nbsp;<span class='ButtonH_25' onclick='batchQRCodeExport(this);' >批量导出二维码</span>";
include '../basic/loading.php';
echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr> <td colspan='109' height='40px' class=''> $WorkActionList $WorkShopList  $ForshortList $lldateList $OrderPOList $creatorList $timeList $BatchButton $batchChangeTime  $toExcelAll $batchFlowSheet $batchCodeSheet</td>";


if($kWorkShopId == '101'){
	include "item4_6_1.php"; //组装类


}else{
	include "item4_6_2.php"; //半成品类的

}
?>
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script language = "JavaScript">
function changeWorkshop(){
    var id = document.getElementById("Id").value;
    var e = document.getElementById("ShowChangeWorkShop"+id);
    var sPOrderId = document.getElementById("sPOrderId").value;
    var changeWorkShop = document.getElementById("changeWorkShopId");
    var changeWorkShopId = changeWorkShop.value;
    var htmlValue = changeWorkShop.options[changeWorkShop.options.selectedIndex].text;
    if(changeWorkShopId >0){
	    var url="item4_6_ajax.php?sPOrderId="+sPOrderId+"&changeWorkShopId="+changeWorkShopId;
		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				//更新该单元格内容
				if (ajax.responseText=="Y"){
			       e.innerHTML="<div style='background-color:#FF0000'>"+htmlValue+"</div>";
			       closeWinDialog();
				}else{
					alert ("生产车间更新失败！");
				}
			}
		}
	　	ajax.send(null);
	}else{
		alert ("请选择变更的生产车间！");
	}
}


var selectIds;
var selectsPOrderIds;

function batchChangeWorkShop(e) {
	var choosedRow = 0;
	selectIds = new Array();

	jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
			var value = jQuery(this).val();
			var splitArr = value.split('|');
			if (splitArr.length != 2) {
				return;
			}
			choosedRow=choosedRow+1;
			selectIds[choosedRow-1]=splitArr[0];

    		if (choosedRow == 1) {
    			selectsPOrderIds=splitArr[1];
			} else {
				selectsPOrderIds=selectsPOrderIds+ '|' + splitArr[1];
			}
        }
	});

	if (choosedRow == 0) {
		alert("该操作要求选定记录！");
		return;
	}

	openWinDialog(e,"item4_6_change2.php",405,300,'bottom');
}

var selectIds;
var selectsPOrderIds;
function batchChangescTime(e) {
    var choosedRow = 0;
    selectIds = new Array();

    jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
            var value = jQuery(this).val();
            var splitArr = value.split('|');
            if (splitArr.length != 2) {
                return;
            }
            choosedRow=choosedRow+1;
            selectIds[choosedRow-1]=splitArr[0];

            if (choosedRow == 1) {
                selectsPOrderIds=splitArr[1];
            } else {
                selectsPOrderIds=selectsPOrderIds+ '|' + splitArr[1];
            }
        }
    });


    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
    openWinDialog(e,"item4_6_change3.php",405,300,'bottom');

}
function getDate(e){
    jQuery(e).attr("value",jQuery(e).val());
}
function batchChangescTime1() {
    jQuery.ajax({
        url: 'set_time_for_product.php',
        type: 'post',
        data: {
            date : jQuery('#scDate').val(),
            POrderId : selectsPOrderIds
        },
        dataType: 'json',
        beforeSend: function () {
            jQuery('.response').show();
        },
        success: function (result) {
            if (result.rlt === true ){
                window.location.reload();
            }else{
                alert('车间更新失败！请重试！');
                window.location.reload();
            }
        }
    }).done(function () {
        window.location.reload();

    });
}

function batchChangeWorkshop() {
    var changeWorkShop = document.getElementById("changeWorkShopId");
    var changeWorkShopId = changeWorkShop.value;
    var htmlValue = changeWorkShop.options[changeWorkShop.options.selectedIndex].text;

	if(changeWorkShopId >0){

		var url="item4_6_ajax2.php";
	    var ajax=InitAjax();
	    ajax.open("POST",url,true);
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			//alert(ajax.responseText);
			 if(ajax.responseText=="Y"){//更新成功
				 for(j = 0,len=selectIds.length; j < len; j++) {
						var id = selectIds[j];
					    var e = document.getElementById("ShowChangeWorkShop"+id);
					 	e.innerHTML="<div style='background-color:#FF0000'>"+htmlValue+"</div>";
				 }

			       closeWinDialog();
				}
			 else{
				alert ("生产车间更新失败！");
			  }
			}
		 }
	    ajax.send("sPOrderIds="+selectsPOrderIds+"&changeWorkShopId="+changeWorkShopId);

	　	ajax.send(null);

		//alert(selectIds);
		//alert(selectsPOrderIds);


	}else{
		alert ("请选择变更的生产车间！");
	}

}

var selectIds;
function batchFlowExport(e) {
    var choosedRow = 0;
    selectIds = '';

    jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
            var value = jQuery(this).val();
            var splitArr = value.split('|');
            if (splitArr.length != 2) {
                return;
            }
            choosedRow=choosedRow+1;

            if (choosedRow == 1) {
                selectsPOrderIds=splitArr[1];
            } else {
                selectsPOrderIds=selectsPOrderIds+ '|' + splitArr[1];
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    // 批量生成二维码
    jQuery.ajax({
        url: '../admin/batchCreateQRCode.php',
        type: 'post',
        data: {
            selectsPOrderIds : selectsPOrderIds
        },
        async: false,
        dataType: 'json'

    });
    var workShop = jQuery('#WorkShopId option:selected').html();
    var Forshort = jQuery('#Forshort option:selected').html();
    var created = jQuery('#created option:selected').html();

    document.form1.action = "../admin/item4_6_flow_excel.php?selectsPOrderIds=" + selectsPOrderIds+"&workShop="+workShop+"&Forshort="+Forshort+"&created="+created;
    document.form1.target = "download";
    document.form1.submit();

}

var selectIds;
function batchQRCodeExport(e) {
    var choosedRow = 0;
    selectIds = '';

    jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
            var value = jQuery(this).val();
            var splitArr = value.split('|');
            if (splitArr.length != 2) {
                return;
            }
            choosedRow=choosedRow+1;

            if (choosedRow == 1) {
                selectsPOrderIds=splitArr[1];
            } else {
                selectsPOrderIds=selectsPOrderIds+ '|' + splitArr[1];
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    // 批量生成二维码
    jQuery.ajax({
        url: '../admin/batchCreateQRCode.php',
        type: 'post',
        data: {
            selectsPOrderIds : selectsPOrderIds
        },
        async: false,
        dataType: 'json'

    });
    var workShop = jQuery('#WorkShopId option:selected').html();
    var Forshort = jQuery('#Forshort option:selected').html();
    var OrderPO = jQuery('#OrderPO option:selected').html();
    var created = jQuery('#created option:selected').html();

    document.form1.action = "../admin/item4_6_code_excel.php?selectsPOrderIds=" + selectsPOrderIds+"&workShop="+workShop+"&Forshort="+Forshort+"&OrderPO="+OrderPO+"&created="+created;
    document.form1.target = "download";
    document.form1.submit();

}

</script>