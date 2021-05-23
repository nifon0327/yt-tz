<?php   
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/style/popupframe.css'>";//弹出窗口css
$From=$From==""?"read":$From;
//需处理参数
ChangeWtitle("$SubCompany  (半成品)未生产");
$funFrom="semifinished_order";
$nowWebPage=$funFrom."_read";
$tableMenuS=600;
$ColsNumber=16;				
$sumCols="8,10";

$Th_Col="操作|55|序号|30|交货日期|70|采购流水号|100|PO|80|下单日期|70|半成品名称|350|单位|40|数量|60|加工单价|60|金额|70|生产数量|70|待检数量|70|入库数量|70|订单备注|150|送货楼层|65|操作员|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,23,131,79";
//步骤3：

include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项

if($From!="slist"){


    $SearchRows=$OrderAction>0?" AND S.ActionId='$OrderAction' ":"";
   
    $blSign  = $blSign == ""?0:$blSign;
    $blSignStr = "blSign".$blSign;
    $$blSignStr = "selected";
    echo"<select name='blSign' id='blSign' onchange='ResetPage(this.name)'>";
    echo"<option value='0' $blSign0>全部</option>";
	echo"<option value='1' $blSign1>未备料</option>";
	echo"<option value='2' $blSign2>已备料</option>";
	echo"</select>&nbsp;";
   
	$WorkShopResult = mysql_query("SELECT W.Id,W.Name 
	FROM $DataIn.yw1_scsheet S 
	LEFT JOIN $DataIn.workshopdata W ON W.Id=S.WorkShopId 
	WHERE 1 $SearchRows  AND S.Estate>0 GROUP BY W.Id ORDER BY Id",$link_id);
	if($WorkShopRow = mysql_fetch_array($WorkShopResult)){
		echo "<select name='WorkShopId' id='WorkShopId' onchange='ResetPage(this.name)'>";
		echo "<option value='' selected>全部</option>";
		do{
			$_Id=$WorkShopRow["Id"];
			$_Name=$WorkShopRow["Name"];
			if($WorkShopId==$_Id){
				echo"<option value='$_Id' selected>$_Name</option>";
				$SearchRows.=" AND S.WorkShopId='$_Id' ";
				}
			 else{
			 	echo"<option value='$_Id'>$_Name</option>";
				}
			}while($WorkShopRow = mysql_fetch_array($WorkShopResult));
			echo "</select>&nbsp;";
	}  
	
	
	 $DeliveryWeekResult = mysql_query("SELECT IFNULL(G.DeliveryWeek,0) AS DeliveryWeek
	 FROM $DataIn.yw1_scsheet S 
	 LEFT JOIN $DataIn.cg1_stocksheet  G  ON G.StockId=S.mStockId 
	 WHERE 1 $SearchRows AND S.Estate>0 GROUP BY IFNULL(G.DeliveryWeek,0) ORDER BY G.DeliveryWeek ASC",$link_id);
	if($DeliveryWeekRow = mysql_fetch_array($DeliveryWeekResult)){
		echo "<select name='DeliveryWeek' id='DeliveryWeek' onchange='ResetPage(this.name)'>";
		echo "<option value='' selected>全部</option>";
		do{
			$DeliveryWeekValue = $DeliveryWeekRow["DeliveryWeek"];
			if($DeliveryWeekValue>0){
			    $week=substr($DeliveryWeekValue, 4,2);
		        $weekName="Week " . $week;
	        }else{
		        $weekName ="未设置";
	        }
			if($DeliveryWeek==$DeliveryWeekValue){
				echo"<option value='$DeliveryWeekValue' selected>$weekName</option>";
				$SearchRows.=" AND G.DeliveryWeek='$DeliveryWeekValue' ";
				}
			 else{
			 	echo"<option value='$DeliveryWeekValue'>$weekName</option>";
				}
			}while($DeliveryWeekRow = mysql_fetch_array($DeliveryWeekResult));
		echo "</select>&nbsp;";
	}  	
	
	
	    //正常单，锁定单
        $LockEstate=$LockEstate==""?0:$LockEstate;
        $LockStr="LockEstate".$LockEstate;
        $$LockStr="selected";
		echo "<select name='LockEstate' id='LockEstate' onchange='document.form1.submit();'>";
		echo "<option value='0' $LockEstate0>全部</option>";
		echo "<option value='1' $LockEstate1>正常单</option>";
		echo "<option value='2' $LockEstate2>锁定单</option>";
		echo "</select>";
	
}
else{
   $SearchRows.=$OrderAction>0?" AND S.ActionId='$OrderAction' ":"";
}

echo "<input type='hidden' id='OrderAction' name='OrderAction' value='$OrderAction'/>";
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
$MaxWeek  = date("Y")."99";
$mySql="SELECT * FROM (
SELECT  G.Id,S.StockId,S.sPOrderId,S.mStockId,G.POrderId, G.StuffId,(G.AddQty + G.FactualQty) AS Qty,SG.Price,
IFNULL(M.PurchaseID,Y.OrderPO) AS OrderPO,IFNULL(M.Date,YM.OrderDate) AS Date,G.Mid,G.StockRemark,D.StuffCname,D.Picture,U.Name AS UnitName,U.decimals,IFNULL(M.BuyerId,G.BuyerId) AS BuyerId,D.SendFloor,G.DeliveryDate,IF(G.DeliveryWeek=0,$MaxWeek,G.DeliveryWeek) AS DeliveryWeek
FROM $DataIn.yw1_scsheet S 
LEFT JOIN $DataIn.cg1_stocksheet  SG  ON SG.StockId=S.StockId
LEFT JOIN $DataIn.cg1_stocksheet  G  ON G.StockId=S.mStockId
LEFT JOIN $DataIn.cg1_stockmain M  ON  M.Id = G.Mid 
LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = G.StuffId 
LEFT JOIN $DataIn.stufftype  T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=Y.OrderNumber 
WHERE  S.Estate>0  AND FIND_IN_SET(G.CompanyId,getSysConfig(106))>0 $SearchRows GROUP BY G.StockId ) A  ORDER BY DeliveryWeek ASC";
	
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);	
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		
		//检查备料情况
       $sPOrderId= $myRow['sPOrderId'];
	   $canStockResult=mysql_fetch_array(mysql_query("SELECT getCanStock('$sPOrderId',0) AS canSign",$link_id));
	   $canStockSign = $canStockResult['canSign'];
	   
	   if ($blSign==1 && $canStockSign==3){
		   continue;
	   }
	   
	   if ($blSign==2 && $canStockSign!=3){
		   continue;
	   }
	   $theDefaultColor = $canStockSign==3?"#D3E9D3":"#FFFFFF";
	    
        $POrderId=$myRow["POrderId"];
		$StockId=$myRow["StockId"];
		$mStockId=$myRow["mStockId"];
		$Qty=$myRow["Qty"];
		$OrderPO=$myRow["OrderPO"];
		$StockRemark=$myRow["StockRemark"];
        $Date =$myRow["Date"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
        $UnitName=$myRow["UnitName"];
        $decimals=$myRow["decimals"];
        $Qty=round($Qty,$decimals);
        
        $Price=$myRow["Price"];
        $Amount  =   sprintf("%.2f", $Price* $Qty);
        include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性       
        $Operator=$myRow["BuyerId"];
        
		include "../model/subprogram/staffname.php";
        
        $DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
        $DeliveryWeek=$myRow["DeliveryWeek"]=="0000-00-00"?"":$myRow["DeliveryWeek"];
        include "../model/subprogram/deliveryweek_toweek.php";

        $SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		
        //生产数量
        //1.有工序的半成品
        $checkProcessSql    = "SELECT  Id FROM $DataIn.cg1_processsheet WHERE StockId = $StockId";
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
        
        $thisScQty = $thisScQty==0?"&nbsp;":$thisScQty;
        
        $ColbgColor=''; $LockRemark='';
        $checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=2 ORDER BY Id DESC LIMIT 1",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
		     $ColbgColor = "bgcolor='#FF0000' ";
			 $LockRemark="业务订单未确认";
		 
		 }
	    else{
	        $CheckSignSql=mysql_query("SELECT Id,Remark FROM $DataIn.cg1_lockstock WHERE StockId ='$mStockId' AND Locks=0 LIMIT 1",$link_id);
			if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			    $LockRemark=$CheckSignRow["Remark"];
			    $ColbgColor = "bgcolor='#FF0000' ";
				$lock="<div style='background-color:#FF0000' title='原因:$lockRemark'> <img src='../images/lock.png' width='15' height='15'></div>";
	
			}
			else{
				if ($OrderAction>$APP_CONFIG['PT_ACTIONID']){
					 $CheckSignSql2=mysql_query("SELECT  L.Remark FROM $DataIn.yw1_scsheet S
									INNER JOIN  $DataIn.cg1_lockstock L ON  L.StockId =S.mStockId AND L.Locks=0 
									WHERE S.POrderId ='$POrderId'  AND ActionId=" . $APP_CONFIG['PT_ACTIONID'],$link_id);
					if($CheckSignRow2=mysql_fetch_array($CheckSignSql2)){
					    $LockRemark=$CheckSignRow2["Remark"];
					    $ColbgColor = "bgcolor='#FF0000' ";
						$lock="<div style='background-color:#FF0000' title='原因:$lockRemark'> <img src='../images/lock.png' width='15' height='15'></div>";
			
					}
				}
			}
		}

        
        if($LockEstate==1 && $LockRemark!="" )continue;
	    if($LockEstate==2 && $LockRemark=="" )continue;		
	    
        //入库数量
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId='$mStockId'",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?"&nbsp;":$rkQty;

		//待送货数量
		$checkSql=mysql_query("SELECT SUM(IFNULL(Qty,0)) AS Qty FROM $DataIn.gys_shsheet WHERE 1 AND Estate>0 AND StuffId='$StuffId' AND StockId='$mStockId'",$link_id);  
		$checkQty=mysql_result($checkSql,0,"Qty");
		$checkQty=$checkQty==""?"&nbsp;":$checkQty; 
		
		if($checkQty>0 ){
		   if($checkQty==$thisScQty && $checkQty==$Qty){
			   $checkQty ="<span class='greenB'>$checkQty</span>";
		   }else{
			   $checkQty ="<span class='yellowB'>$checkQty</span>";
		   }
			
		}
		
        $ShowId=$mStockId;
        $ShowBomImageId= "Bom_StuffImage_" . $ShowId;
        $ShowBomTableId= "Bom_StuffTable_" . $ShowId;
        $ShowBomDivId  = "Bom_StuffDiv_"  . $ShowId;
        
        switch($OrderAction){
           case 104:
               $ajaxFile="slicebom_ajax";
	           $ajaxDir="pt";
              break;       
           case 102:
              $ajaxFile="pt_order_ajax";
              $ajaxDir="pt";
              break;  
           default:
              $ajaxFile="semifinishedbom_ajax";
              $ajaxDir="admin"; 
              break;
	        
        }
        

        $showPurchaseorder = "<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"$ajaxFile\",\"$mStockId|$ShowId|1\",\"$ajaxDir\");'  src='../images/showtable.gif' 
	title='显示或隐藏原材料' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$ShowBomImageId'>";

	    $StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='$ShowBomTableId' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='$ShowBomDivId' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

         $ShowPopupDivStr=$myRow["DeliveryDate"]=="0000-00-00"?"":"onclick='ShowPopupDiv($i,2,$mStockId,1)' onmousedown='window.event.cancelBubble=true;' style='CURSOR: pointer'";
         
         $StockRemarkStr="onclick='ShowPopupDiv($i,14,$mStockId,2)' onmousedown='window.event.cancelBubble=true;' style='CURSOR: pointer'";
         
         if ($Login_P_Number==10019 || in_array($_SESSION["Login_GroupId"], $APP_CONFIG['IT_DEVELOP_GROUPID']))
         {
            $tempStockId=$mStockId . '|' .$StockId;
	        $NewPriceStr="onclick='ShowPopupDiv($i,9,\"$tempStockId\",3)' onmousedown='window.event.cancelBubble=true;' style='CURSOR: pointer'";
         }      
		$ValueArray=array(
		    array(0=>$DeliveryWeek,		1=>"align='center'",2=>"$ShowPopupDivStr"),
		    array(0=>$mStockId,		1=>"align='center'"),
			array(0=>$OrderPO,		1=>"align='center'"),
            array(0=>$Date,			    1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,		    1=>"align='center'"),
			array(0=>$Qty,		        1=>"align='right'"),
			array(0=>$Price, 	        1=>"align='right'",2=>"$NewPriceStr"),
			array(0=>$Amount, 		    1=>"align='right'"),
			array(0=>$thisScQty,		1=>"align='right'"),
			array(0=>$checkQty, 		1=>"align='right'"),
			array(0=>$rkQty, 		    1=>"align='right'"),
			
			array(0=>$StockRemark,		1=>"align='left'",2=>"$StockRemarkStr"),
			array(0=>$SendFloor,		1=>"align='center'"),
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
	if(toObj==1){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	
	if(theDiv.style.visibility=="hidden"){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//采购交货期
				InfoSTR="半成品流水号:<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='15' class='TM0000' readonly/>采购交货期限&nbsp;<select id='ReduceWeeks' name='ReduceWeeks' style='width:100px;'><option value='' 'selected'>请选择</option>";
				<?PHP 
				    $echoInfo="<option value='0'>同周</option>";
				    $NumberArray=array("零","一","二","三","四","五","六","七","八","九","十");
				    for($m=1;$m<$APP_CONFIG['REDUCE_WEEKS'];$m++){ 
					   $echoInfo.="<option value='-$m'>前".$NumberArray[$m]."周</option>"; 
				    }
				?>
				 InfoSTR=InfoSTR+"<?PHP echo $echoInfo; ?>"+"</select><br>";
				break;
			case 2://备注
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='15' class='INPUT0000' readonly>的生管备注<input name='StockRemark' type='text' id='StockRemark' size='50' class='INPUT0100'>";
				break;
		    case 3://更改单价
				InfoSTR="更新流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='14' class='INPUT0000' readonly>的加工单价:<input name='NewPrice' type='text' id='NewPrice' size='50' class='INPUT0100'>";
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
			myurl="semifinished_order_updated.php?mStockId="+temprunningNum+"&ReduceWeeks="+ReduceWeeks+"&ActionId=DeliveryDate";
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
		case 2:		//更新备注:
		    var obj=document.form1.StockRemark;
			var StockRemark=obj.value;
			var tempStockRemark=encodeURIComponent(StockRemark);
			myurl="semifinished_order_updated.php?mStockId="+temprunningNum+"&StockRemark="+tempStockRemark+"&ActionId=StockRemark";
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			   if(ajax.readyState==4){// && ajax.status ==200
					  eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStockRemark+"</NOBR></DIV>";
					  CloseDiv();
				}							
		    }
		    ajax.send(null);
		break;	
		case 3:	//更新加工单价:
		    var obj=document.form1.NewPrice;
			var NewPrice=obj.value;
			
			myurl="semifinished_order_updated.php?StockId="+temprunningNum+"&NewPrice="+NewPrice+"&ActionId=NewPrice";
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			   if(ajax.readyState==4){// && ajax.status ==200
					  eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+NewPrice+"</NOBR></DIV>";
					  CloseDiv();
				}							
		    }
		    ajax.send(null);
		
	}
}



function updateLock(TableCellId,runningNum,Locks){//行即表格序号;列，流水号，更新源
	showMaskBack();  
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("PopupDiv");
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden"){
	
		InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='14' class='TM0000' readonly>&nbsp;<select name='myLock' id='myLock' size='1'> <option value='1'>解锁</option><option value='0'>锁定</option> </select> &nbsp;&nbsp;&nbsp;<br><br>锁定原因:<input name='myLockRemark' type='text' id='myLockRemark' style='width:320px;' class='INPUT0100'/>&nbsp;&nbsp;&nbsp;<br><div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdateLock("+TableCellId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'></div>";

		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function aiaxUpdateLock(TableCellId){
	var temprunningNum=document.form1.runningNum.value;
	var tempmyLock=document.form1.myLock.value;
	var tempLockRemark=document.form1.myLockRemark.value;
	if (tempmyLock=="0" && tempLockRemark==""){
		alert("请输入锁定原因!");
		return;
	}
	var tempLockRemark1=encodeURIComponent(tempLockRemark);//传输中文
	myurl="../admin/yw_order_ajax_updated.php?StockId="+temprunningNum+"&myLock="+tempmyLock+"&LockRemark="+tempLockRemark1 +"&Action=Lock";

	var ajax=InitAjax(); 
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
		if(tempmyLock=="1"){
			eval(TableCellId).innerHTML="<div title='采购未锁定'> <img src='../images/unlock.png' width='15' height='15'> </div>";
			}
		else{
			eval(TableCellId).innerHTML="<div style='background-color:#FF0000' title='采购已锁定,"+tempLockRemark+"' > <img src='../images/lock.png' width='15' height='15'></div>";
			}
	CloseDiv();
			}
		}
	ajax.send(null); 	
}


function  delProcessId(e,StockId,ProcessId){
	
	 if(confirm("确认要删除该记录吗？")) {
        var url="semifinished_order_ajax_updated.php?StockId="+StockId+"&ProcessId="+ProcessId+"&Action=delProcessId"; 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
			     e.innerHTML="<div style='color:#FF0000;'>已删除</div>";
			 
				}
			 else{
			    alert ("数据删除失败！"+ajax.responseText); 
			  }
			}
		 }
	   ajax.send(null); 
       } 
	
}

</script>
