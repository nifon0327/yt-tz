	<?php
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=19;
$tableMenuS=1500;
ChangeWtitle("$SubCompany 客户列表");
$funFrom="clientdata";
$From=$From==""?"read":$From;
$Th_Col="选项|80|序号|40|编号|40|简 称|80|货币|40|电 话|150|传 真|150|联系人|80|移动电话|120|快递帐号|150|图档|50|网 站|50|国家|130|出货数量|80|最后出货日期|100|我公司联系人|80|状态|40|备注|40|Price Term|150|付款方式|200|收款帐号|100";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8,40,24";
$Type=2;//在联系人中的分类代号
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr
	 <input name='Type' type='hidden' id='Type' value='$Type'>
	";
//步骤5：
if($Keys==1){
	$SearchRows.=" and A.Estate=1";
	}
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.CompanyId,A.Forshort,A.ExpNum,A.PayType,A.CompanySignXY,A.Estate,A.Date,A.Operator,A.Locks,K.Title AS BankTitle,
B.Tel,B.Fax,B.Website,B.Area,B.Remark,C.Name,C.Mobile,C.Email,D.Symbol,E.Name AS staff_Name,F.Name AS PayMode,F.eName AS ePayMode ,A.PriceTerm
FROM $DataIn.trade_object A
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId AND B.Type='$Type'
LEFT JOIN $DataIn.linkmandata C ON C.CompanyId=A.CompanyId AND C.Defaults=0 AND C.Type='$Type'
LEFT JOIN $DataPublic.currencydata D ON D.Id=A.Currency
LEFT JOIN $DataPublic.staffmain E ON E.Number=A.Staff_Number
LEFT JOIN $DataPublic.clientpaymode F ON F.Id=A.PayMode
LEFT JOIN $DataPublic.my2_bankinfo K ON K.Id=A.BankId 
WHERE 1 AND A.cSign=$Login_cSign $SearchRows ORDER BY A.Estate DESC,A.OrderBy DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		//加密
		$Idc=anmaIn("clientdata",$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);
		$CompanyId=$myRow["CompanyId"];
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
		$Symbol=$myRow["Symbol"];
		$CompanySignXY=$myRow["CompanySignXY"];
		switch($CompanySignXY){
			case 2: $CompanySignXY="<div class='redB'>&nbsp;&nbsp;鼠宝&nbsp;&nbsp;</div>"; break;
			default:$CompanySignXY="研砼"; break;
			}
		$ExpNum=$myRow["ExpNum"]==""?"&nbsp;":$myRow["ExpNum"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Website=$myRow["Website"]==""?"&nbsp":"<a href='$myRow[Website]' target='_blank'>查看</a>";
		$Area=$myRow["Area"]==""?"&nbsp":$myRow["Area"];
		$Name=$myRow["Name"]==""?"&nbsp":$myRow["Name"];
		$Mobile=$myRow["Mobile"]==""?"&nbsp":$myRow["Mobile"];
		$Linkman=$myRow["Email"]==""?$Name:"<a href='mailto:$myRow[Email]'>$Name</a>";
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$staff_Name=$myRow["staff_Name"]==""?"&nbsp":$myRow["staff_Name"];
		$Locks=$myRow["Locks"];
		$PayMode=$myRow["PayMode"];
		$ePayMode=$myRow["ePayMode"];
       $PriceTerm=$myRow["PriceTerm"];
		$BankTitle=$myRow["Estate"]==1?$myRow["BankTitle"]:"&nbsp;";
		$OrderSignColor=$myRow["PayType"]==1?"bgcolor=\"#F00\"":"";
		$ClientImg="";
		//客户图档
		$imgSql="SELECT I.CompanyId,I.Name,I.Picture,P.Forshort
			FROM $DataIn.clientimg I
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=I.CompanyId
			WHERE 1 AND I.CompanyId='$CompanyId' ORDER BY I.Picture LIMIT 1";
		$imgResult=mysql_query($imgSql,$link_id);
		if($imgRow=mysql_fetch_array($imgResult)){
			$ClientImg=$imgRow["Picture"]==""?"&nbsp":"<a href='clientdata_imgread.php?f=$CompanyId' target='_blank'>查看</a>";
		}
		else $ClientImg="&nbsp;";
		//已出货数量
		$checkShipQty= mysql_query("
		SELECT SUM( S.Qty ) AS ShipQty, MAX( M.Date ) AS DATE,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months
		FROM $DataIn.ch1_shipmain M 
		LEFT JOIN $DataIn.ch1_shipsheet S ON M.Id=S.Mid 
		LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
	    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		WHERE C.CompanyId='$CompanyId' AND S.Type=1 AND O.Estate='0'",$link_id);
		if(mysql_num_rows($checkShipQty)>0){
			$qtyHolder = mysql_result($checkShipQty,0,"ShipQty");
			$ShipQtySum = number_format($qtyHolder)==0?"":number_format($qtyHolder);
			$LastShipDate=mysql_result($checkShipQty,0,"Date");
			$Months=mysql_result($checkShipQty,0,"Months");
			}
		//最后出货日期
		if($Months!=NULL){
			if($Months<6){//6个月内绿色
				$LastShipDate="<div class='greenB'>".$LastShipDate."</div>";
				}
			else{
				if($Months<12){//6－12个月：橙色
					$LastShipDate="<div class='yellowB'>".$LastShipDate."</div>";
					}
				else{//红色
					$LastShipDate="<div class='redB'>".$LastShipDate."</div>";
					}
				}
			}
		else{//没有出过货
			$LastShipDate="&nbsp;";
			}
			$ShipQtySum="<div class='redB'>$ShipQtySum</div>";
			$showPurchaseorder="<img onClick='showdetail(StuffList$i,showtable$i,StuffList$i,\"$CompanyId\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$ShipQtySum="<div onClick='showdetail2(StuffList$i,StuffList$i,\"$CompanyId\",$i);'  title='点击查看明细' width='13' height='13' style='CURSOR: pointer'>$ShipQtySum</div>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$CompanyId,1=>"align='center'"),
			array(0=>$Forshort,2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Tel),
			array(0=>$Fax),
			array(0=>$Linkman),
			array(0=>$Mobile,1=>"align='center'"),
			array(0=>$ExpNum),
			array(0=>$ClientImg,1=>"align='center'"),
			array(0=>$Website,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Area),
			array(0=>$ShipQtySum,2=>"align='right'"),
			array(0=>$LastShipDate,2=>"align='right'"),
			array(0=>$staff_Name),
			//array(0=>$CompanySignXY),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$PriceTerm,1=>"align='center'"),
			array(0=>$PayMode,1=>"title='$ePayMode' "),
			array(0=>$BankTitle)
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
<script language="JavaScript">
function showdetail(e,f,Order_Rows,ShipId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(ShipId!=""){
			var url="../public/clientdata_ajax.php?ShipId="+ShipId+"&RowId="+RowId;
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null);
			}
		}
	}


function showdetail2(e,Order_Rows,ShipId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(ShipId!=""){
			var url="../admin/clientdata_ajax.php?ShipId="+ShipId+"&RowId="+RowId;
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null);
			}
	}
</script>