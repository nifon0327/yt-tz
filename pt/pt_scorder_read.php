<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0;} 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 生产工单");
$funFrom="pt_scorder";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|55|序号|30|工单流水号|100|生产车间|100|半成品名|320|单位|40|工单数量|70|已生产数|70|待检数量|70|入库数量|70|工单备注|250|期限|70";	
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
   
   	$SearchRows="";
    $ScFrom = $ScFrom ==""?"1":$ScFrom;
	$scStr="ScFrom".$ScFrom;
	$$scStr="selected";			  
    echo"<select name='ScFrom' id='ScFrom' onchange='ResetPage(this.name)'>";
	echo"<option value='1' $ScFrom1>未生产</option>";
	echo"<option value='2' $ScFrom2>生产中</option>";
	echo"<option value='0' $ScFrom0>已生产</option>";
	echo"</select>&nbsp;";
	if($ScFrom !=''){
		$SearchRows.=" AND SC.ScFrom = $ScFrom";
	}
    
	  
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT SC.Id,SC.POrderId,SC.sPOrderId,SC.Qty,SC.ScFrom,SC.ActionId,SC.StockId,SC.Remark,
D.StuffId,D.StuffCname,D.Picture,G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,W.Name AS WorkShopName,U.Name AS UnitName,M.mStockId
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
INNER  JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
INNER JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
WHERE 1  $SearchRows  ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	    $m=1;
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$sPOrderId=$myRow["sPOrderId"];
		$WorkShopName=$myRow["WorkShopName"];
		$mStockId=$myRow["mStockId"];  //半成品StockId
		$StuffId=$myRow["StuffId"];
		$Picture=$myRow["Picture"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"];
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
		
		$StockId = $myRow["StockId"]; //半成品生产类配件StockId
		$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE  C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId ",$link_id));
		$scQty=$CheckscQty["scQty"];
		
		
       //入库数量
		$rkTemp=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet    WHERE StockId='$mStockId' AND StuffId='$StuffId' AND sPOrderId = '$sPOrderId'",$link_id));
		$rkQty=$rkTemp["Qty"];	//收货总数
        //待检数量
		$gysTemp=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.gys_shsheet    WHERE StockId='$mStockId' AND StuffId='$StuffId' AND sPOrderId = '$sPOrderId' AND Estate >0 ",$link_id));
		$gysQty=$gysTemp["Qty"];	//收货总数 
		
		
	    $showPurchaseorder="<img onClick='ShowOrHideSemi(StuffList$i,ShowStuffListTable$i,StuffListDiv$i,\"$sPOrderId\",$i,\"fromscorder\");' name='ShowStuffListTable$i' src='../images/showtable.gif' 
			title='显示半成品明细' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='StuffListDiv$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		
		$thisScFrom = $myRow["ScFrom"];
		if($thisScFrom == 1){
			$updateWorkShop = "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,3,$sPOrderId,1)' style='CURSOR: pointer'";
		}else{
			$updateWorkShop = "";
		}
		$ValueArray=array(
		array(0=>$sPOrderId,		1=>"align='center'"),
        array(0=>$WorkShopName,		1=>"align='center'",2=>$updateWorkShop),
		array(0=>$StuffCname),
		array(0=>$UnitName,		    1=>"align='center'"),
		array(0=>$Qty, 	            1=>"align='right'"),
		array(0=>$scQty,		    1=>"align='right'"),
		array(0=>$gysQty,		    1=>"align='right'"),
		array(0=>$rkQty, 		    1=>"align='right'"),
		array(0=>$Remark,			2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,10,$sPOrderId,2)' style='CURSOR: pointer'"),
		array(0=>$DeliveryWeek,		1=>"align='center'")
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
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
function updateJq(TableId,RowId,sPOrderId,toObj){//行即表格序号;列，流水号，更新源
	showMaskBack();  
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==1){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 2:	//工单备注
				InfoSTR="工单流水号为<input name='sPOrderId' type='text' id='sPOrderId' value='"+sPOrderId+"' size='14' class='TM0000' readonly>的工单备注<textarea name='Remark' cols = '50' rows='4' id='Remark'></textarea><br>";
				break;
			case 1://变更生产车间
				InfoSTR="<input name='sPOrderId' type='text' id='sPOrderId' value='"+sPOrderId+"' size='12' class='TM0000' readonly/>生产车间<select id='WorkShopId' name='WorkShopId' style='width:150px;'><option value='' 'selected'>请选择</option>";
				
				<?PHP 
				$WorkShopResult = mysql_query("SELECT Id,Name FROM $DataIn.workshopdata WHERE  Estate=1 ORDER BY Id",$link_id);
		        if($WorkShopRow = mysql_fetch_array($WorkShopResult)){
				  do{
					  $echoInfo.="<option value='$WorkShopRow[Id]'>$WorkShopRow[Name]</option>";
					 } while($WorkShopRow = mysql_fetch_array($WorkShopResult));
			      }
				?>
				 InfoSTR=InfoSTR+"<?PHP echo $echoInfo; ?>"+"</select><br>";
				break;
			
			}
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  
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
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) { 
		theDiv.filters.revealTrans.apply();
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	closeMaskBack();   
	}
	
	
 function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var tempsPOrderId=document.form1.sPOrderId.value;
	switch(ObjId){

		case "2":		//订单说明 PackRemark
			var tempRemark0=document.form1.Remark.value;
			var tempRemark1=encodeURIComponent(tempRemark0);
			myurl="pt_scorder_updated.php?sPOrderId="+tempsPOrderId+"&tempRemark="+tempRemark1+"&ActionId=Remark";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempRemark0+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
		case "1":		//出货方式
			var tempWorkShopId0=document.form1.WorkShopId.value;
			var index = document.form1.WorkShopId.selectedIndex;
			var tempWorkShopText = document.form1.WorkShopId.options[index].text;
			var tempWorkShopId1=encodeURIComponent(tempWorkShopId0);
			myurl="pt_scorder_updated.php?sPOrderId="+tempsPOrderId+"&tempWorkShopId="+tempWorkShopId1+"&ActionId=WorkShopId";
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempWorkShopText+"</NOBR></DIV>";

					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
			
		}
	}	
	
</script>