<?php   
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/style/popupframe.css'>";//弹出窗口css

$From=$From==""?"read":$From;
//需处理参数
ChangeWtitle("$SubCompany  皮套-未生产");
$funFrom="pt_order";
$nowWebPage=$funFrom."_read";
$tableMenuS=600;
$sumCols="6,8";
$ColsNumber=16;				

$Th_Col="操作|55|序号|30|PO|80|下单日期|70|半成品名称|350|单位|40|数量|60|单价|60|金额|80|生产数量|70|入库数量|70|待检数量|70|订单备注|150|交货日期|80|送货楼层|65|采购流水号|90|操作员|55";	
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,23,26";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){  
	$WorkShopList = "";
	/*$WorkShopResult= mysql_query("SELECT SC.WorkShopId,W.Name AS WorkShopName 
	FROM  $DataIn.yw1_scsheet SC 
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
	WHERE 1 $SearchRows GROUP BY SC.WorkShopId  order by SC.WorkShopId",$link_id);
	if ($WorkShopRow = mysql_fetch_array($WorkShopResult)){
		$WorkShopList="<select name='WorkShopId' id='WorkShopId' style = 'width:100px' onChange='ResetPage(1,5)'>";
		$i=1;
		do{
			$theWorkShopId=$WorkShopRow["WorkShopId"];
			$theWorkShopName=$WorkShopRow["WorkShopName"];
			$WorkShopId=$WorkShopId==""?$theWorkShopId:$WorkShopId;
			if($WorkShopId==$theWorkShopId){
				$WorkShopList.="<option value='$theWorkShopId' selected>$theWorkShopName</option>";
				//$SearchRows.=" AND SC.WorkShopId='$theWorkShopId'";
				}
			else{
				$WorkShopList.="<option value='$theWorkShopId'>$theWorkShopName</option>";
				}
			$i++;
		}while($WorkShopRow = mysql_fetch_array($WorkShopResult));
		$WorkShopList.="</select>";
	}*/
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='PopupDiv' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);


$mySql="SELECT  G.Id,G.POrderId,G.StockId AS mStockId, G.StuffId,(G.AddQty + G.FactualQty) AS Qty,G.CostPrice AS Price,G.Operator,
IFNULL(Y.OrderPO,M.PurchaseID) AS OrderPO,IFNULL(YM.OrderDate,M.Date) AS Date,M.Remark,D.StuffCname,D.Picture,U.Name AS UnitName,U.decimals,M.BuyerId,G.DeliveryDate,D.SendFloor 
FROM  $DataIn.cg1_stocksheet  G  
LEFT JOIN $DataIn.cg1_stockmain M  ON  M.Id = G.Mid 
LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = G.StuffId 
LEFT JOIN $DataIn.stufftype  T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=Y.OrderNumber 
WHERE  1 $SearchRows  AND (G.AddQty + G.FactualQty)>0 AND G.rkSign>0 
AND FIND_IN_SET(T.mainType,getSysConfig(103))>0 AND FIND_IN_SET(G.CompanyId,getSysConfig(106))>0  
AND NOT EXISTS(
     SELECT S.StockId FROM $DataIn.yw1_scsheet S 
     LEFT JOIN $DataIn.cg1_semifinished SM ON SM.StockId=S.StockId  
     WHERE S.Estate>0 AND SM.mStockId=G.StockId AND S.ActionId!='".$APP_CONFIG['PT_ACTIONID']."' 
) ORDER BY M.Date ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
        $POrderId=$myRow["POrderId"];
		$mStockId=$myRow["mStockId"];
		$Qty=$myRow["Qty"];
		$OrderPO=$myRow["OrderPO"];
		$Remark=$myRow["Remark"];
        $Date =$myRow["Date"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
        $UnitName=$myRow["UnitName"];
        $decimals=$myRow["decimals"];
        $Qty=round($Qty,$decimals);
        
        $Price=$myRow["Price"];
        $Amount  = sprintf("%.2f", $Price* $Qty);

        include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性       
        $Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

        $DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
        include "../model/subprogram/deliverydate_toweek.php";

        $SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		
        //是否包含需求BOM
        $checkSemiSql=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS semiSign FROM $DataIn.cg1_semifinished WHERE mStockId='$mStockId'",$link_id));
        $ColbgColor=$checkSemiSql['semiSign']>0?"":" bgcolor='#FF0000' ";
        
        //生产数量
        $checkStockSql=mysql_fetch_array(mysql_query("SELECT StockId FROM $DataIn.yw1_scsheet WHERE mStockId='$mStockId'",$link_id));
        $StockId=$checkStockSql["StockId"]; 
        
        $checkProcessSql    = "SELECT  Id FROM $DataIn.cg1_processsheet WHERE StockId ='$StockId'";
        $checkProcessResult = mysql_fetch_array(mysql_query($checkProcessSql,$link_id));
        if($checkProcessResult){
        	$ProcessRow=mysql_fetch_array(mysql_query("SELECT B.ProcessId AS LastProcessId,PT.Color,PD.ProcessName AS LastProcessName 
				FROM $DataIn.cg1_processsheet B 
			    LEFT JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
			    LEFT JOIN $DataIn.process_type PT ON PT.gxTypeId=PD.gxTypeId
			    WHERE B.StockId='$StockId'  GROUP BY B.ProcessId ORDER BY PT.SortId DESC  LIMIT 1  ",$link_id));
            $LastProcessId  = $ProcessRow["LastProcessId"];
		     //检查已登记数量
			$CheckthisScQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty FROM $DataIn.sc1_gxtj C WHERE   C.StockId='$StockId' AND C.ProcessId='$LastProcessId'",$link_id));
			$thisScQty      = $CheckthisScQty["gxQty"]==""?0:$CheckthisScQty["gxQty"];

        }else{
        	 $scQtyRow  = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.sc1_cjtj  WHERE  StockId='$StockId'",$link_id));
		     $thisScQty = $scQtyRow["Qty"];
        }
        
        $thisScQty=$thisScQty==0?"&nbsp;":$thisScQty;
    
        //入库数量
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId='$mStockId'",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?"&nbsp;":$rkQty;

		//待送货数量
		$checkSql=mysql_query("SELECT SUM(IFNULL(Qty,0)) AS Qty FROM $DataIn.gys_shsheet WHERE 1 AND Estate>0 AND StuffId='$StuffId' AND StockId='$mStockId'",$link_id);  
		$checkQty=mysql_result($checkSql,0,"Qty");
		$checkQty=$checkQty==""?"&nbsp;":$checkQty; 
		
        //显示或隐藏bom
        $ShowBomImageId= "Bom_StuffImage_" . $i;
        $ShowBomTableId= "Bom_StuffTable_" . $i;
        $ShowBomDivId  = "Bom_StuffDiv_" . $i;

	    $showPurchaseorder="<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"pt_order_ajax\",\"$mStockId|$i|1\",\"pt\");' name='$ShowBomImageId' src='../images/showtable.gif' 
			title='显示半成品明细' width='13' height='13' style='CURSOR: pointer'>";	
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='$ShowBomTableId' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='$ShowBomDivId' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

        $ShowPopupDivStr=$myRow["DeliveryDate"]=="0000-00-00"?"":"onclick='ShowPopupDiv($i,13,$mStockId,1)' onmousedown='window.event.cancelBubble=true;' style='CURSOR: pointer'";
        

        
		$ValueArray=array(
			array(0=>$OrderPO,		1=>"align='center'"),
            array(0=>$Date,			    1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,		    1=>"align='center'"),
			array(0=>$Qty,		        1=>"align='right'"),
			array(0=>$Price, 	        1=>"align='right'"),
			array(0=>$Amount, 		    1=>"align='right'"),
			array(0=>$thisScQty,		1=>"align='right'"),
			array(0=>$rkQty, 		    1=>"align='right'"),
			array(0=>$checkQty, 		1=>"align='right'"),
			array(0=>$Remark,			1=>"align='left'"),
			array(0=>$DeliveryDate,		1=>"align='center'",2=>"$ShowPopupDivStr"),
			array(0=>$SendFloor,		1=>"align='center'"),
			array(0=>$mStockId,		1=>"align='center'"),
			array(0=>$Operator,		    1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script  src='../model/IE_FOX_MASK.js' type='text/javascript'></script>
<script language='JavaScript' type='text/JavaScript'>

function ShowPopupDiv(TableId,RowId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	showMaskBack();  
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("PopupDiv");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	
	if(theDiv.style.visibility=="hidden"){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//采购交货期
				InfoSTR="半成品工单:<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='15' class='TM0000' readonly/>采购交货期限&nbsp;<select id='ReduceWeeks' name='ReduceWeeks' style='width:100px;'><option value='' 'selected'>请选择</option>";
				<?PHP 
				    $echoInfo="<option value='0'>同周</option>";
				    $NumberArray=array("零","一","二","三","四","五","六","七","八","九","十");
				    for($m=1;$m<$APP_CONFIG['REDUCE_WEEKS'];$m++){ 
					   $echoInfo.="<option value='-$m'>前".$NumberArray[$m]."周</option>"; 
				    }
				?>
				 InfoSTR=InfoSTR+"<?PHP echo $echoInfo; ?>"+"</select><br>";
				break;
		}
		//if(InfoSTR.length>0){
			buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		//}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
	}
	
}        
function CloseDiv(){
	var theDiv=document.getElementById("PopupDiv");	
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
	}

function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value*1;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	//alert(ObjId);
	switch(ObjId){
		case 1:		//更新交货期:
		    var obj=document.form1.ReduceWeeks;
			var ReduceWeeks=obj.value;
			myurl="pt_order_updated.php?mStockId="+temprunningNum+"&ReduceWeeks="+ReduceWeeks+"&ActionId=DeliveryDate";
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			   if(ajax.readyState==4){// && ajax.status ==200
					 // eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+obj.options[obj.selectedIndex].text+"</NOBR></DIV>";
					  CloseDiv();
				}							
		    }
		    ajax.send(null);
		break;
	}
}

</script>
