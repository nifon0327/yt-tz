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
//电信-EWEN
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/Totalsharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<link rel='stylesheet' href='../model/tableborder.css'>
<script src='../model/pagefun_yw.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
include "../model/subprogram/sys_parameters.php";
include "../model/subprogram/business_authority.php";//看客户权限
$From=$From==""?"read":$From;
//
//需处理参数
$tableMenuS=800;
$funFrom="lock_order";
$helpFile='';//有帮助文件
$nowWebPage=$funFrom."_read";

	$unColorCol=11;//不着色列
	$Th_Col="操作|55|序号|30|PO|80|中文名|210|&nbsp;|30|Product Code|150|成品重<br>(g)|50|Unit|40|Price|55|Qty|50|Amount|70|锁单备注|250|锁单人|60|锁单时间|70|审核状态|50|退回原因|180|Air/Sea|70|交期|80|操作员|55|期限|40";
	$ColsNumber=17;

//更新
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
 $Temptoday=date("Y-m-d");
if($From!="slist"){
	$SearchRows ="";	
	$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort,SUM(S.Qty*S.Price*R.Rate) AS Amount
	FROM $DataIn.yw2_orderexpress  E
    LEFT JOIN $DataIn.yw1_ordersheet S  ON S.POrderId=E.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
	INNER JOIN $DataPublic.currencydata R ON R.Id=C.Currency
	WHERE S.Estate>0   AND E.Type=2 GROUP BY M.CompanyId ORDER by Amount DESC,M.CompanyId ASC",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
				echo"<option value='' selected>全部</option>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];$theForshort=$ClientRow["Forshort"];
			//$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows="and M.CompanyId='$theCompanyId'";$DefaultClient=$theForshort;
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	}


echo"$CencalSstr";
$searchtable="productdata|P|cName|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";
//步骤5：
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";

$sumQty=0;
$sumSaleAmount=0;
$sumTOrmb=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
	
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,E.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.MainWeight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,E.Remark AS LockRemark,E.Operator AS LockOperator,E.Date AS LockDate,E.Estate AS LockEstate,E.ReturnReasons
	FROM $DataIn.yw2_orderexpress  E 
    LEFT JOIN $DataIn.yw1_ordersheet S  ON S.POrderId=E.POrderId
INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
WHERE 1 and S.Estate>0 $SearchRows  AND E.Type=2 ORDER BY M.CompanyId,M.OrderDate ASC,M.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$thisTOrmbOUTsum=0;
	do{
		$OrderCgRemark="";
		$OrderRemark="";		
		$m=1;$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		//加密参数
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];				
        $OrderDate=$myRow["OrderDate"];
		$PI=$myRow["PI"];
		if($PI!=""){
			$f1=anmaIn($PI.".pdf",$SinkOrder,$motherSTR);
			$d1=anmaIn("download/pipdf/",$SinkOrder,$motherSTR);		
			$PI="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>查看</a>";
			$PIoId=$POrderId . "|" .  $Id;//弹出DIV传值用
			}
		else{
			$PI="&nbsp;";
			$PIoId="$POrderId|N";
			}
		$ClientOrder=$myRow["ClientOrder"];
		if($ClientOrder!=""){//原单在序号列显示
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
			$ClientOrder="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$i</a>";
			}
		else{
			$ClientOrder=$i;
			}
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);

		$Weight=$myRow["Weight"];$MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		
		$PIRemark=$myRow["PIRemark"];
		$Leadtime=$myRow["Leadtime"];
		$LeadbgColor="";
		if ($Leadtime==""){
			 $checkTimeResult=mysql_fetch_array(mysql_query("SELECT Leadtime FROM $DataIn.yw3_pileadtime WHERE POrderId='$POrderId'",$link_id));
			 $Leadtime=$checkTimeResult["Leadtime"]==""?"&nbsp;":$checkTimeResult["Leadtime"];
			 $LeadbgColor=$checkTimeResult["Leadtime"]==""?$LeadbgColor:" bgColor='#F7E200' ";
		}
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";
		
		$OrderDate=CountDays($OrderDate,0);
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
		//$Leadtime=$myRow["Leadtime"]==""?"&nbsp;":$myRow["Leadtime"];
       $LockEstate=$myRow["LockEstate"];
       switch($LockEstate){
           case 2:
              $LockEstate="<span class='greenB'>通过</span>";
              break;
           case 3:
				$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
              $LockEstate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
              break;
             case 1:
         
				$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":$myRow["ReturnReasons"];
              $LockEstate="<span class='redB'>未审核</span>";
               break;
		 }
		include "../model/subprogram/PI_Leadtime.php";
	    $Leadtime=$PIRemark==""?$Leadtime:"<div title='$PIRemark' style='color:#FF0000' >$Leadtime</div>";	
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
        $cgRemark=$myRow["cgRemark"]==""?"&nbsp;":$myRow["cgRemark"];
		$bjRemark=$myRow["bjRemark"]==""?"&nbsp;":$myRow["bjRemark"];
        $dcRemark=$myRow["dcRemark"]==""?"&nbsp;":$myRow["dcRemark"];
	   include "../model/subprogram/order_shiptype.php";
		//读取操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);//本订单卖出金额
		$sumSaleAmount=sprintf("%.2f",$sumSaleAmount+$thisSaleAmount);
		$sumQty=$sumQty+$Qty;
		//交货期
		$CompanyId=$myRow["CompanyId"];


			//订单状态色：有未下采购单，则为白色
			$checkColor=mysql_query("SELECT G.Id,G.StockId FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId'",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
				$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
				}
			else{//已全部下单	
				$OrderSignColor="bgColor='#339900'";	//设默认绿色
				//生产数量与工序数量不等时，黄色
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//工序总数
				$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
				$gxQty=$CheckgxQty["gxQty"];
				//已完成的工序数量
				$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
				$scQty=$CheckscQty["scQty"];
	
				if($gxQty!=$scQty){
					$OrderSignColor="bgColor='#FFCC00'";
					}
				}
			       
		    $tempRemark   =$myRow["LockRemark"]==""?"&nbsp;":$myRow["LockRemark"];
		    $LockOperator   =$myRow["LockOperator"];
if ($LockOperator>0){
		$pResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$LockOperator ORDER BY Number LIMIT 1",$link_id);
		if($pRow = mysql_fetch_array($pResult)){
			   $LockOperator=$pRow["Name"];
		}
		else
		{
		   //外部人员资料
		   $otResult = mysql_query("SELECT Name FROM $DataIn.ot_staff WHERE Number=$LockOperator ORDER BY Number LIMIT 1",$link_id);
		   if($otRow = mysql_fetch_array($otResult)){
			     $LockOperator=$otRow["Name"];
		     } 
	    }
}
		    $LockDate   =$myRow["LockDate"];
		     //采购单锁定
		   $OrderCgRemark="";$cgRemarked=0;
			$TmpCgRemark="";
			 $CheckStockSql=mysql_query("SELECT * FROM $DataIn.cg1_lockstock K WHERE K.StockId LIKE '$POrderId%' AND K.Locks=0 AND exists (SELECT StockId FROM $DataIn.cg1_stocksheet WHERE StockId=K.StockId AND POrderId='$POrderId')",$link_id);
			 while($CheckStockRow=mysql_fetch_array($CheckStockSql)) 
				{      
				     if ($CheckStockRow["Remark"]!=""){
				       $OrderCgRemark.=$OrderCgRemark==""?"原因:".$CheckStockRow["Remark"]:"," .$CheckStockRow["Remark"];
					   $TmpCgRemark.=$OrderCgRemark==""?"".$CheckStockRow["Remark"]:"," .$CheckStockRow["Remark"];
				       }
				       $cgRemarked=1;
					   $OrderSignColor="bgColor='#0099FF'";	 //break; //找到一个跳出当前循环  
				}
		       if ($cgRemarked==1 && $OrderCgRemark==""){
			          $OrderCgRemark="未填写原因";
					  $TmpCgRemark=$OrderCgRemark;
		       }

             $ColbgColor="";$UrgentColor="";
			//加急订单
			$checkExpress=mysql_query("SELECT Type,Remark,Estate FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
			if($checkExpressRow = mysql_fetch_array($checkExpress)){
				do{
					$Type=$checkExpressRow["Type"];
					$UPRemark=$checkExpressRow["Remark"];
					$UPEstate=$checkExpressRow["Estate"];
					//echo $UPRemark;
					switch($Type){
						case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
						case 2:
                              if($UPEstate==1){
                                    $ColbgColor="bgcolor='#FF0000'"; 
                                 }
                                else if($UPEstate==2){
                                    $ColbgColor="bgcolor='#CD2990'"; 
                                      }
                                else $ColbgColor="bgcolor='#20B2AA'"; 
                               $OrderRemark=$UPRemark ;
						       //$LockRemark=$OrderRemark="未确定产品 ".$UPRemark ; 
								break;		//未确定产品
						}
					}while ($checkExpressRow = mysql_fetch_array($checkExpress));
				}
			   	
		
        


				//动态读取 $thisTOrmbINo
			if($OrderRemark!=""){
				$TempStrtitle=$OrderRemark;
			}
			else {
				$TempStrtitle=$TmpCgRemark;
			}
			
			 $TempStrtitle=$TempStrtitle==""?"显示或隐藏配件采购明细资料":$TempStrtitle;
			$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='$TempStrtitle' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			$Weight=zerotospace($Weight);

         $ShipQtyStr="";$tempStr="";
		 $titleShipQty= mysql_query("SELECT S.Qty AS ShipQty ,M.Date
         FROM $DataIn.ch1_shipsheet  S
         LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
       WHERE S.POrderId='$POrderId'",$link_id);
         while($titleRow=mysql_fetch_array($titleShipQty)){
              $ShipDate=$titleRow["Date"];
              $titleQty=$titleRow["ShipQty"];
                 if($tempStr=="")$tempStr="出货时间:$ShipDate,出货数量:$titleQty";
                 else $tempStr=$tempStr."<br>"."出货时间:$ShipDate,出货数量:$titleQty";
                  }
             if($tempStr!="")$ShipQtyStr="title='$tempStr'";

				$ValueArray=array(
					array(0=>$OrderPO),
					array(0=>$TestStandard,3=>"line"),
					array(0=>$CaseReport,1=>"align='center'"),
					array(0=>$eCode,	3=>"..."),
					array(0=>$Weight,1=>"align='right'"),
					array(0=>$Unit,				1=>"align='center'"),
					array(0=>$Price, 			1=>"align='right'"),
					array(0=>$Qty,				1=>"align='right' $ShipQtyStr"),
					array(0=>$thisSaleAmount,	1=>"align='right'"),
					array(0=>$tempRemark,2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,11,$POrderId,1)' style='CURSOR: pointer'"),
					array(0=>$LockOperator,			1=>"align='center'"),
					array(0=>$LockDate,			1=>"align='center'"),
					array(0=>$LockEstate,			1=>"align='center'"),
					array(0=>$ReturnReasons),
					array(0=>$ShipType,			1=>"align='center'"),
					 array(0=>$Leadtime,			1=>"align='center' "),
					array(0=>$Operator,			1=>"align='center'"),
					array(0=>$OrderDate,		    1=>"align='center' $BackImg")
					);
			$checkidValue=$Id;
			include "subprogram/read_model_6_yw.php";
			echo $StuffListTB;
		
		}while ($myRow = mysql_fetch_array($myResult));	
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
//include "../model/subprogram/ColorInfo.php";
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany.$DefaultClient."未出订单锁定列表");
$ActioToS="1";
include "../model/subprogram/read_model_menu.php";
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script src='../model/weekdate.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
function updateJq(TableId,RowId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==25){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	//theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
              case 1:
		           InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='14' class='TM0000' readonly>&nbsp;<select name='myLock' id='myLock' size='1'> <option value='0'>锁定</option> <option value='1'>解锁</option></select> &nbsp;&nbsp;&nbsp;<br><br>锁定原因:<input name='myLockRemark' type='text' id='myLockRemark' style='width:320px;' class='INPUT0100'/>&nbsp;&nbsp;&nbsp;<br><div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdateLock("+RowId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'></div>";
		         break;
			}

		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   加入庶影   
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
	if (isIe()) {  //只有IE才能用 
		theDiv.filters.revealTrans.apply();
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //关闭庶影   
	}

function aiaxUpdateLock(RowId){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var tempmyLock=document.form1.myLock.value;
	var tempLockRemark=document.form1.myLockRemark.value;
	if (tempmyLock=="0" && tempLockRemark==""){
	      	alert("请输入锁定原因!");
	     	return false;
	      }

	switch(ObjId){
             case "1":
	             var tempLockRemark1=encodeURIComponent(tempLockRemark);//传输中文
             	myurl="lock_order_ajax.php?POrderId="+temprunningNum+"&myLock="+tempmyLock+"&LockRemark="+tempLockRemark1 +"&Action=Lock";
	             //alert (myurl);
	             var ajax=InitAjax(); 
	             ajax.open("GET",myurl,true);
	             ajax.onreadystatechange =function(){
	             	if(ajax.readyState==4){// && ajax.status ==200
	             	if(tempmyLock=="1"){
		             	eval("ListTable"+tempTableId).rows[0].cells[11].innerHTML="<div style='background-color:#FF0000'> 解锁</div>";
	             		}
	             	else{
		             	eval("ListTable"+tempTableId).rows[0].cells[11].innerHTML=tempLockRemark;
	             		}
	             CloseDiv();
			}
		}
	ajax.send(null); 	

	          break;
		}
	}
</script>

