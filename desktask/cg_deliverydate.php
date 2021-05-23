<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>

<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
ChangeWtitle("$SubCompany 采购交期明细");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=970;
$subTableWidth=950;
$i=1;

//取得文件所在目录
$FromDir=get_currentDir(1);
?>
<body>
<form name="form1" method="post" action="">
<div id='Jp' style='position:absolute; left:341px; top:229px; width:300px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">供应商交货日期明细</td>
    </tr>
	<tr>
		<td height="24" colspan="5">
	    <input type="radio" name="Action" value="0" id="Action0" onClick="javascript:document.form1.action='cg_deliverydate0.php';document.form1.submit()"><label for="Action0">以采购分类(方便采购追货)</label>
	    <input name="Action" type="radio" value="1" id="Action1" checked><label for="Action1">以交货日期分类(方便主管监督)</label>
	    </td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" height="25">&nbsp;&nbsp;&nbsp;&nbsp;交货日期</td>
  </tr>
</table>
<?php   
//读取未结付货款：包手部分结付的单ifnull(null,0)
//$ShipResult = mysql_query("SELECT S.DeliveryDate FROM $DataIn.cg1_stocksheet S WHERE 1 AND S.rkSign>0 AND S.Mid>0 AND S.DeliveryDate!='0000-00-00' GROUP BY S.DeliveryDate ORDER BY S.DeliveryDate",$link_id);
/*
$DateTime=date("Y-m-d");   //从现在开始，到上一年的。采购的，其它就不显示了  add mby zx 2010-12-30
$StartDate=date("Y-m-d",strtotime("$DateTime-1 years"));
$SearchRows=" AND S.DeliveryDate>='$StartDate' ";
$SearchCk=" AND M.Date>='$StartDate' ";
$mySql1="SELECT S.DeliveryDate from 
		(SELECT S.DeliveryDate,(S.AddQty+S.FactualQty) AS cgQty,S.StockId FROM $DataIn.cg1_stocksheet S
		WHERE 1  $SearchRows AND S.rkSign>0 AND S.Mid>0 AND S.DeliveryDate!='0000-00-00') S
		LEFT JOIN 
		(SELECT SUM(C.Qty) AS Qty,C.StockId  FROM $DataIn.ck1_rksheet C
		LEFT JOIN $DataIn.ck1_rkmain M ON M.Id=C.Mid
		WHERE 1 $SearchCk  group by C.StockID) C  ON C.StockID=S.StockID
		where (S.cgQty>C.Qty) OR C.Qty IS NULL GROUP BY S.DeliveryDate";
*/
$mySql="SELECT S.DeliveryDate FROM $DataIn.cg1_stocksheet S
		WHERE 1  AND S.rkSign>0 AND S.Mid>0 AND S.DeliveryDate!='0000-00-00'
		AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C
		WHERE 1 AND C.StockId=S.StockId) GROUP BY S.DeliveryDate ORDER BY S.DeliveryDate";
$ShipResult = mysql_query($mySql,$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$DeliveryDate=$ShipRow["DeliveryDate"];
		//传递交货日期
		$DivNum="a".$i;
		$TempId="$DeliveryDate|$DivNum";			
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_deliverydate_a\",\"$FromDir\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏下级资料. ' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
				//交货日期颜色
		$SetDate=DateDiff($DeliveryDate);
		if($SetDate<2){		//离交期不大于一天，为红色
			switch($SetDate){
			case 0:
				$DeliveryDate="<span class='redB'>".$DeliveryDate." :已到预定交期</span>";break;
			case 1:
				$DeliveryDate="<span class='redB'>".$DeliveryDate." :离预定交期还有 $SetDate 天</span>";break;
			default:
				$SetDate=-$SetDate;
				$DeliveryDate="<span class='redB'>".$DeliveryDate." :已超过预定交期 $SetDate 天</span>";break;
				}
			}
		else{
			if($SetDate<5){
				$DeliveryDate="<span class='yellowB'>".$DeliveryDate." :离预定交期还有 $SetDate 天</span>";
				}
			else{
				$DeliveryDate="<span class='greenB'>".$DeliveryDate." :离预定交期还有 $SetDate 天</span>";
				}
			}

?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $DeliveryDate?></td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
	$DeliveryDate='0000-00-00';
	$DivNum="a".$i;
	$TempId="$DeliveryDate|$DivNum";	
	$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_deliverydate_a\",\"$FromDir\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;未设置交货日期的采购单</td>
		</tr>
	</table>
<?php    echo $HideTableHTML?>
</form>
</body>
</html>
<script language="JavaScript" type="text/JavaScript">
<!--
function updateJq(TableId,RowId,runningNum){//行即表格序号;列，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;//表格名称
		InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='14' class='TM0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+RowId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		theDiv.filters.revealTrans.apply();//防止错误
		theDiv.filters.revealTrans.play(); //播放
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	theDiv.filters.revealTrans.apply();
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	}

function aiaxUpdate(RowId){
	var tempTableId=document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var tempDeliveryDate=document.form1.DeliveryDate.value;
	myurl="purchaseorder_updated.php?StockId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=jq";
	retCode=openUrl(myurl);
	if (retCode!=-2){
		//更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
		if(tempDeliveryDate==""){
			eval(tempTableId).rows[RowId].cells[2].innerHTML="<div class='yellowN'>未设置</div>";
			}
		else{
			var ColorDate=Number(DateDiff(tempDeliveryDate));
			if(ColorDate<2){
				eval(tempTableId).rows[RowId].cells[2].innerHTML="<div class='redB'>"+tempDeliveryDate+"</div>";
				}
			else{
				if(ColorDate<5){
					eval(tempTableId).rows[RowId].cells[2].innerHTML="<div class='yellowB'>"+tempDeliveryDate+"</div>";
					}
				else{
					eval(tempTableId).rows[RowId].cells[2].innerHTML="<div class='greenB'>"+tempDeliveryDate+"</div>";
					}
				}
			}
		CloseDiv();
		}
	}
</script>