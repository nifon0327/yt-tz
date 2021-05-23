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
<?php   //鼠宝的为 $ActioToS="65";
//电信-zxq 2012-08-01
session_start();
$Login_WebStyle="default";
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script> 
<script>
function CnameChanged(e){
	var StuffCname=e.value;
	if (StuffCname.length>=1){
	   e.style.color='#000';
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  e.style.color='#DDD';
	  document.getElementById("stuffQuery").disabled=true;
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
 }
function checkSendWay()
{
    if (document.getElementById("SendWay").value==""){
	    ShowSendWay();
    }
}
function ShowSendWay(){
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		   InfoSTR="送货信息：<select id='wayName' name='wayName' style='width:280px;'><option value='' 'selected'>请选择</option>";
				<?PHP 
					$shipTypeResult = mysql_query("SELECT DISTINCT Name FROM $DataPublic.come_data WHERE  CompanyId='$myCompanyId' ORDER BY Id ",$link_id);
		          if($TypeRow = mysql_fetch_array($shipTypeResult)){
				  do{
					           $echoInfo.="<option value='$TypeRow[Name]' selected>$TypeRow[Name]</option>";
					  } while($TypeRow = mysql_fetch_array($shipTypeResult));
			      }
			      $DateSTR=date("Y-m-d");
				?>
				 InfoSTR=InfoSTR+"<?PHP echo $echoInfo; ?>"+"</select><br>";
				 InfoSTR= InfoSTR+"<br>新增信息：<input name='newWay' type='text' id='newWay' size='50' maxlength='30' class='INPUT0100' > <br><br>送货日期：<input name='newDate' type='text' id='newDate' size='12' maxlength='12' class='INPUT0100' onFocus='WdatePicker()' value='<?PHP echo $DateSTR ?>' readonly>&nbsp;<br><br>注：送货信息请填写 <车辆牌号> 或 <快递公司><br><br>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()' style=' float:right;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确定' onclick='upSendWay()' style='margin-right:20px; float:right;'>";
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

function upSendWay()
{
   var newWay= document.getElementById("newWay").value;
   
   if (newWay.length>0){
	    document.getElementById("SendWay").value=newWay;
   }
   else{
	   newWay=document.getElementById("wayName").value;
	   if (newWay.length>0){
	       document.getElementById("SendWay").value=newWay;
       }
   }
   
   var newDate=document.getElementById("newDate").value;
    if (newDate.length>0){
	    document.getElementById("SendDate").value=newDate;
   }
   CloseDiv();
}	
</script>
<?php 
$ColsNumber=17;
$tableMenuS=800;
ChangeWtitle("$SubCompany 客供未出");
$funFrom="Gys_client";
$From=$From==""?"read":$From;
$sumCols="9,10,11,13";			//求和列,需处理
$Th_Col="选项|60|序号|30|配件ID|45|配件名称|200|配件<br>图档|30|图档日期|70|QC图|40|品检<br>报告|40|单位|30|采购总数|60|已送货总数|60|未送货总数|60|可送货数|60|本次送货|60|未补货总数|60|本次补货|60|本次备品|60|送货</br>楼层|40|期限|60";//历史<br>订单|30|
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="0,11";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (A.StuffCname LIKE '%$StuffCname%' OR A.StuffId LIKE '%$StuffCname%')";
	$List1="&nbsp;&nbsp;<input type='button' id='cancelQuery' value='取消查询'  class='ButtonH_25' onclick='document.form1.submit();'/>";
    }
else{
     $List1="<input name='StuffCname' type='text' id='StuffCname' size='20' value='配件名称或ID'  onchange='CnameChanged(this)'  oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件名称或ID'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件名称或ID' : this.value;\" style='color:#DDD;'><input type='button' id='stuffQuery' value='查询'  class='ButtonH_25' onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;document.form1.submit();\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
     
     $List1.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;送货信息:<input name='SendWay' type='text' id='SendWay' size='30' onClick='ShowSendWay()' readonly value='' />";
     $List1.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;送货时间:<input name='SendDate' type='text' id='SendDate' size='20' onClick='ShowSendWay()' readonly value='' />";
}
echo $List1;
$SearchRowsA=" and S.CompanyId='$myCompanyId'";
echo $CencalSstr ;

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

$WeekSearch=$IsWeeks==""?"":" AND YEARWEEK(S.DeliveryDate,1)='$IsWeeks' ";
$WeekSearch=$IsWeeks=="NODATE"?" AND  S.DeliveryDate='0000-00-00'":$WeekSearch;

$MDate=date('Y-m-d',strtotime('-1 year'));
$mySql="SELECT S.StuffId,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,A.TypeId ,U.Name AS UnitName,F.Remark as SendFloor,P.LimitTime
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
LEFT JOIN $DataIn.base_mposition F ON  F.Id=A.SendFloor 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=A.StuffId AND OP.Property=2
WHERE 1 and S.Mid=0   AND S.CompanyId='$myCompanyId'  AND IFNULL(OP.Property,0)=2  $SearchRows  $WeekSearch  GROUP BY S.StuffId   ORDER BY A.StuffCname";  
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$tempStuffId="";
$Locks=1;
$Keys=4;
$DefaultBgColor=$theDefaultColor;
$SUM_cgQty=0;$SUM_rkQty=0;$SUM_noQty=0;$SUM_webQty=0;
$StuffIdArray=array();
if($myRow = mysql_fetch_array($myResult)){
		//本周数
		$curDate=date("Y-m-d");
		$nextDate=$curDate<'2015-02-09'?date("Y-m-d",strtotime("$curDate  +7 day")):$curDate;
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextDate',1) AS curWeek",$link_id));
		$nextWeek=$dateResult["curWeek"];
		echo "<input name='nextWeek' type='hidden' id='nextWeek'  value='$nextWeek' >";
	do{
		$m=1;
        $DisabledSTR=""; 
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"];
		$SendFloor=$myRow["SendFloor"];
		$StockId=$myRow["StockId"];
		$PorderId=$myRow["PorderId"];
		$DeliveryDate=$myRow["DeliveryDate"];		
       $OnclickStr="";$ColbgColor="";
	   				
		$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
		$Picture=$myRow["Picture"];
         $TypeId=$myRow["TypeId"];
		$Gfile=$myRow["Gfile"];
		$Gremark=$myRow["Gremark"];
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		//加密
		$Gstate=$myRow["Gstate"];

		$ComeFrom="Supplier"; //说明来自供应商，则只已审核的图片.
		include "../model/subprogram/stuffimg_model.php";			
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
        include "../model/subprogram/stuffimg_qcfile.php";    //配件QC检验标准图
        include "../model/subprogram/stuff_get_qualityreport.php";    //配件品检报告
        include"../model/subprogram/stuff_Property.php";//配件属性
			//已购总数
		$cgTemp=mysql_query("SELECT SUM(OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty FROM $DataIn.cg1_stocksheet S 
       WHERE 1 and S.Mid=0    and S.StuffId='$StuffId'  $SearchRowsA $WeekSearch",$link_id);
		$cgQty=mysql_result($cgTemp,0,"Qty");
		$cgQty=$cgQty==""?0:$cgQty;
		$odQty=mysql_result($cgTemp,0,"odQty");
		$odQty=$odQty==""?0:$odQty;
		//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
		WHERE  1   AND  R.StuffId='$StuffId' and S.Mid=0  $SearchRowsA $WeekSearch",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		//待送货数量
		$shSql=mysql_query("SELECT SUM(G.Qty) AS Qty FROM $DataIn.gys_shsheet G
						   LEFT JOIN $DataIn.gys_shmain M ON M.Id=G.Mid
						   LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.StockId 
						   WHERE 1 AND G.SendSign=0 AND G.Estate>0 and S.Mid=0  
						   AND G.StuffId=$StuffId $SearchRowsA $WeekSearch",$link_id);  		
		
		$notSendResult=mysql_query("SELECT SUM(S.FactualQty+S.AddQty) AS Qty FROM $DataIn.cg1_stocksheet S WHERE 1 $SearchRowsA and S.Mid>0 and S.rkSign>0 and S.StuffId='$StuffId' AND YEARWEEK(S.DeliveryDate,1)>'$nextWeek'",$link_id);
		$notSendQty=mysql_result($notSendResult,0,"Qty");
		$notSendQty=$notSendQty>0?$notSendQty:0;
		
		$notDateResult=mysql_query("SELECT SUM(S.FactualQty+S.AddQty) AS Qty FROM $DataIn.cg1_stocksheet S WHERE 1 $SearchRowsA and S.Mid>0 and S.rkSign>0 and S.StuffId='$StuffId' and S.DeliveryDate='0000-00-00'",$link_id);
		$notDateQty=mysql_result($notDateResult,0,"Qty");
		$notSendQty+=$notDateQty>0?$notDateQty:0;
			   
		$shQty=mysql_result($shSql,0,"Qty");
		$shQty=$shQty==""?0:$shQty;
		
		$noQty=$cgQty-$rkQty-$shQty;
		$okSendQty=$IsWeeks==""?$noQty-$notSendQty:$noQty;
		$okSendQty=$IsWeeks>$nextWeek?0:$okSendQty;
		$okSendQty=$okSendQty<=0?0:$okSendQty;
      	$DisabledSTR=$okSendQty<=0?" readonly":"";

	    //退货的总数量 
		$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
									   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
									   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
		$thQty=mysql_result($thSql,0,"thQty");
		$thQty=$thQty==""?0:$thQty;
		$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
									   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
									   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
		$bcQty=mysql_result($bcSql,0,"bcQty");
		$bcQty=$bcQty==""?0:$bcQty;
		
		$bcshSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
							LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
							WHERE 1 AND M.CompanyId = '$myCompanyId' AND S.Estate>0 AND S.StuffId='$StuffId' AND (S.StockId='-1' or S.SendSign='1')",$link_id);  
						
		$bcshQty=mysql_result($bcshSql,0,"Qty");
		$bcshQty=$bcshQty==""?0:$bcshQty;			
		

		$webQty=$thQty-$bcQty-$bcshQty; //未补数量
		if($noQty>0  ||  $webQty>0 ){	//未送货的，未补货
			//清0
				$SUM_cgQty+=$cgQty;
		        $SUM_rkQty+=$rkQty;
		        $SUM_noQty+=$noQty;
		        $SUM_webQty+=$webQty;
				$StuffIdArray["$StuffId"]=$StuffId;
			//echo "$noQty=$cgQty-$rkQty-$shQty";
			$odQty=zerotospace($odQty);
			$StockQty=zerotospace($StockQty);
			$FactualQty=zerotospace($FactualQty);
			$AddQty=zerotospace($AddQty);
			$oStockQty=zerotospace($oStockQty);
			$rkQty=zerotospace($rkQty);		
			$webQty=zerotospace($webQty);  
			
			$DivNum="a";
			//如果已限制采购或供应商，则需传递
			$TempId="$StuffId|$Number|$myCompanyId";			
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"Gys_client\",\"supplier\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='left' style='margin-left:180px;'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
			$toReport="<a href='stuffreport_result.php?Idtemp=$StuffId' target='_blank'>查看</a>";
			//$DisabledSTR=" disabled";//暂停送货
			$Locks=1;
			$SendQty="<input name='sendQty[]' type='text' id='sendQty$i' style='width:50px' value='' onclick='checkSendWay()' class='I0000C' $DisabledSTR>";
			$BS_Qty="<input name='BSQty[]' type='text' id='BSQty$i' style='width:50px' value='' onclick='checkSendWay()' class='I0000C'>"; 
			$BP_Qty="<input name='BPQty[]' type='text' id='BPQty$i' style='width:50px'  value='' onclick='checkSendWay()' class='I0000C'>";  
			$checkDeliveryDate=mysql_query("SELECT Id FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' AND Mid>0 AND  rkSign>0 AND DeliveryDate ='0000-00-00' ",$link_id);
			if($checkDeliveryDateRow = mysql_fetch_array($checkDeliveryDate)){
					$ColbgColor=" bgcolor='#F8E700' ";
			}
			else{
				$ColbgColor="";
			}
		
			$ValueArray=array(
				array(0=>$StuffId,		1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$Gfile, 		1=>"align='center'"),
				array(0=>$GfileDate, 	1=>"align='center'"),
                                array(0=>$QCImage, 	1=>"align='center'"),
				array(0=>$qualityReport, 1=>"align='center'"),
				array(0=>$UnitName,		1=>"align='center'"),
				array(0=>$cgQty, 		1=>"align='right'"),
				array(0=>$rkQty, 		1=>"align='right'"),
				array(0=>"<div class='redB'>".$noQty."</div>", 1=>"align='right'"),
				array(0=>"<div class='greenB'>".$okSendQty."</div>", 1=>"align='right'"),
				array(0=>$SendQty, 	1=>"align='center'"),
				array(0=>"<div class='redB'>".$webQty."</div>", 1=>"align='right'"),
				array(0=>$BS_Qty, 	1=>"align='center'"),
				array(0=>$BP_Qty, 	1=>"align='center'"),
				array(0=>$SendFloor),
				array(0=>$cgDate,1=>"align='center'")
				);
			$checkidValue=$i."-".$StuffId;
			include "../model/subprogram/read_model_6.php";
			echo $HideTableHTML;		
			}
		}while ($myRow = mysql_fetch_array($myResult));
 
		$m=1;
		$SUM_cgQty=$SUM_cgQty==0?"&nbsp;":number_format($SUM_cgQty);
		$SUM_rkQty=$SUM_rkQty==0?"&nbsp;":number_format($SUM_rkQty);
		$SUM_noQty=$SUM_noQty==0?"&nbsp;":number_format($SUM_noQty);
		$SUM_okQty=$SUM_okQty==0?"&nbsp;":number_format($SUM_okQty);
		$SUM_webQty=$SUM_webQty==0?"&nbsp;":number_format($SUM_webQty);
		$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
			//	array(0=>"&nbsp;"	),
			//	array(0=>"&nbsp;"	),
				array(0=>$SUM_cgQty,1=>"align='right'"),
				array(0=>$SUM_rkQty,	1=>"align='right'"),
				array(0=>"$SUM_noQty",	1=>"align='right'" ),
				array(0=>"$SUM_okQty",	1=>"align='right'" ),
			    array(0=>"  ",	1=>"align='right'"	),
			    array(0=>"$SUM_webQty",	1=>"align='right'"	),
				array(0=>"  "	,1=>"align='right'"),
                array(0=>"&nbsp;"	),
                array(0=>"&nbsp;"	),
			//	array(0=>"&nbsp;"	),
                array(0=>"&nbsp;"	)
				);
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";			
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
//$ActioToS="65";
$ActioToS="63";
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
<!--
   document.body.style.backgroundColor="#fff";

function updateJq(TableId,RowId,runningNum){//行即表格序号;列，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;//表格名称
		InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='18' class='TM0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='12' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+RowId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
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

function aiaxUpdate(RowId){
	var tempTableId="ListTable"+document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var tempDeliveryDate=document.form1.DeliveryDate.value;
	myurl="../public/purchaseorder_updated.php?StockId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=jq";
	var ajax=InitAjax(); 
	ajax.open("GET",myurl,true);
	var CellId=4;
	ajax.onreadystatechange =function(){
	　		 	if(ajax.readyState==4){// && ajax.status ==200
				  //更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
	         	if(tempDeliveryDate==""){
			          eval(tempTableId).rows[RowId].cells[CellId].innerHTML="<div class='yellowN'>未设置</div>";
			        }
		       else{
		          	var ColorDate=Number(DateDiff(tempDeliveryDate));
						if(ColorDate<2){
							eval(tempTableId).rows[RowId].cells[CellId].innerHTML="<div class='redB'>"+tempDeliveryDate+"</div>";
							}
						else{
								if(ColorDate<5){
									eval(tempTableId).rows[RowId].cells[CellId].innerHTML="<div class='yellowB'>"+tempDeliveryDate+"</div>";
									}
								else{
									eval(tempTableId).rows[RowId].cells[CellId].innerHTML="<div class='greenB'>"+tempDeliveryDate+"</div>";
									}
							}
					}
			CloseDiv();
		}
	}
	ajax.send(null); 
	}
</script>