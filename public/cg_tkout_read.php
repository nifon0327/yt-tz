<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=24;
$tableMenuS=600;
ChangeWtitle("$SubCompany 客户退款配件待请款列表");
$funFrom="cg_tkout";
$From=$From==""?"read":$From;
$Estate=$Estate==""?1:$Estate;
$sumCols="6,7,8,9,12";			//求和列,需处理
$Th_Col="选项|60|序号|30|配件ID|40|配件名称|200|图档|30|历史订单|60|订单数|45|需求数|45|增购数|45|实购数|45|单价|45|单位|40|金额|60|结付状态|50|采购流水号|100|供应商|80|预付金额|60|Invoice|80|出货日期|70|货款|30";
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$ActioToS="1";
if($Estate==1 && $From!="slist"){
	$otherAction="<span onclick='javascript:showMaskDiv()' $onClickCSS>请款</span>";
	}
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
$SearchRows.="  AND  A.TypeId='9104'";//客户退款配件
$SearchRowA="";
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$TempEstateSTR="EstateSTR".strval($Estate);
	$$TempEstateSTR="selected";
//结付状态
	echo"<select name='Estate' id='Estate' onchange='zhtj(this.name)'>";
	echo"<option value='1' $EstateSTR1>未请款</option>";
	echo"<option value='2' $EstateSTR2>请款中</option>";
	echo"<option value='3' $EstateSTR3>请款通过</option>";
	echo"<option value='0' $EstateSTR0>已结付</option>";
	echo"</select>&nbsp;";
	if($Estate==1){
		$SearchRows.=" and F.Estate IS NULL";
		$SearchRowA=" and (H.Date>='2012-01-01' OR H.Date IS NULL)";
		}
	else{
		$SearchRows.=" and F.Estate='$Estate'";
		}

    //客户

	$clientSql= mysql_query("SELECT 
	M.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId = S.StuffId
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.cw1_tkoutsheet F ON F.StockId=S.StockId
	WHERE 1  $SearchRows   GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);

	if($clientRow = mysql_fetch_array($clientSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		do{
			$Letter=$clientRow["Letter"];
			$Forshort=$clientRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$clientRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($clientRow = mysql_fetch_array($clientSql));
		echo"</select>&nbsp;";
		}
	}
//检查进入者是否采购
$checkResult = mysql_query("SELECT JobId FROM $DataPublic.staffmain where Number=$Login_P_Number order by Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	$JobId=$checkRow["JobId"];//3为采购
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,U.Name AS UnitName,SUM(E.Qty) AS ShipQty,Count(*) AS ShipCount,E.Mid,
A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.TypeId,C.Forshort AS Client,P.cName,P.TestStandard,P.ProductId,H.Date as OutDate,H.InvoiceNO,H.InvoiceFile
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.cw1_tkoutsheet F ON F.StockId=S.StockId
LEFT JOIN $DataIn.ch1_shipsheet E ON E.PorderId=S.PorderId
LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid
WHERE 1 $SearchRows  $SearchRowA GROUP BY S.StockId order by H.Date  desc";
//if ($Login_P_Number==10868) echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($mainRows = mysql_fetch_array($mainResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{

		    $m=1;$ShipSign=0;
		    $Id=$mainRows["Id"];
		    $theDefaultColor=$DefaultBgColor;
		    $StuffId=$mainRows["StuffId"];
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$OrderQty=$mainRows["OrderQty"];
			$FactualQty=$mainRows["FactualQty"];
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$Locks=$mainRows["Locks"];
			$BuyerId=$mainRows["BuyerId"];
			$CompanyId=$mainRows["CompanyId"];
			$OrderPO=$mainRows["OrderPO"];
			$POrderId=$mainRows["POrderId"];
			$tdBGCOLOR=$POrderId==""?"bgcolor='#FFCC99'":"";
			$PQty=$mainRows["PQty"];
			$PackRemark=$mainRows["PackRemark"];
			$sgRemark=$mainRows["sgRemark"];
			$ShipType=$mainRows["ShipType"];
			$Leadtime=$mainRows["Leadtime"];
			$TypeId=$mainRows["TypeId"];
            $Forshort=$mainRows["Forshort"];
			$Gremark=$mainRows["Gremark"];
			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];

			$ShipQty=$mainRows["ShipQty"];
			$ShipCount=$mainRows["ShipCount"];
            $ShipId="";
            $LockRemark="";
			if ($ShipCount>1){
				//分批出货
				$InvoiceNOSTR="";
				$chResult=mysql_query("SELECT H.InvoiceNO,H.InvoiceFile,H.cwSign FROM $DataIn.ch1_shipsheet E 
			                               LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid  
			                               WHERE E.PorderId='$POrderId' order by H.Date",$link_id);
			  while($chRow = mysql_fetch_array($chResult)){
				        $InvoiceNO=$chRow["InvoiceNO"];
	                    $InvoiceFile=$chRow["InvoiceFile"];
	                    $cwSign=$chRow["cwSign"];
                       if($cwSign!=0)$ShipSign=1;
			            $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
			            $InvoiceNOSTR.=$InvoiceFile==0?"":"<div><a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a></div>";
			    	}
				$InvoiceNO=$InvoiceNOSTR==""?"&nbsp;":$InvoiceNOSTR;
			}
			else{
                $ShipId=$mainRows["Mid"];
				$chRow=mysql_fetch_array(mysql_query("SELECT H.cwSign FROM $DataIn.ch1_shipsheet E 
			                               LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid  
			                               WHERE H.Id='$ShipId'",$link_id));
	            $cwSign=$chRow["cwSign"];
                if($cwSign!=0)$ShipSign=1;
	            $InvoiceNO=$mainRows["InvoiceNO"];
	            $InvoiceFile=$mainRows["InvoiceFile"];
			    $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
			    $InvoiceNO=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
		    }
           $ShipSign=$InvoiceNO=="&nbsp;"?1:$ShipSign;
           if($ShipSign==0){//全部货款已结付，可以请款
                    $ShipStr="<div class='greenB' >√</div>";
               }
           else{
                    $ShipStr="<div class='redB'>×</div>";
				  //  $LockRemark="对应的INVOICE货款未结付完，不能请款!";
                }
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
			$Picture=$mainRows["Picture"];
			include "../model/subprogram/stuffimg_model.php";
			$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";

			//供应商结付货币的汇率
			$Rate=1;
			$currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C
			                              LEFT JOIN $DataIn.trade_object P  ON P.Currency=C.Id 
			                              WHERE P.CompanyId='$CompanyId' ORDER BY C.Id LIMIT 1",$link_id);
			if($RowTemp = mysql_fetch_array($currency_Temp)){
				$Rate=$RowTemp["Rate"];//汇率
				}
			$rmbAmount=sprintf("%.2f",$Amount*$Rate);

			//结付情况/**/
			$checkPay=mysql_query("SELECT Estate,Month FROM $DataIn.cw1_tkoutsheet WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPayRow=mysql_fetch_array($checkPay)){
				$cwEstate=$checkPayRow["Estate"];
				$AskMonth=$checkPayRow["Month"];
				switch($cwEstate){
					case 0://已结付
						$cwEstate="<div class='greenB' title='已结付...货款月份:$AskMonth'>√</div>";
						$LockRemark="已结付，锁定操作";
					break;
					case 2:	//请款中
						$cwEstate="<div class='yellowB' title='请款中...货款月份:$AskMonth'>×.</div>";
						$LockRemark="已请款，锁定操作";
					break;
					case 3://请款通过
						$cwEstate="<div class='yellowB' title='等候结付...货款月份:$AskMonth'>√.</div>";
						$LockRemark="已请款通过，锁定操作";
					break;
					}
				}
			else{
				$cwEstate="<div class='redB'>×</div>";
				}

			$PreAmount="&nbsp;";
			$checkPrePay=mysql_query("SELECT Amount   FROM $DataIn.cw2_fkdjsheet WHERE PurchaseID='$PurchaseID' ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPrePayRow=mysql_fetch_array($checkPrePay)){
			   //$LockRemark="已付订金，锁定操作";
			   $PreAmount=$checkPrePayRow["Amount"];

			}
         if($mainRows["OutDate"]=="")  $LockRemark="未出货,不能请款";
			///权限///////////////////////////////////////////

			/*if(($BuyerId==$Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK){//有权限
				if($LockRemark!=""){
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png'  title='$LockRemark' width='15' height='15'>";
					}
				else{
					$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
					}
				}
			else{//无权限
				$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png'  title='锁定操作!' width='15' height='15'>";
				}*/



			$cName=$mainRows["cName"];
			$Client=$mainRows["Client"];
			//加急订单标色
			include "../model/subprogram/cg_cgd_jj.php";
			$OutDate=$mainRows["OutDate"]==""?"&nbsp":$mainRows["OutDate"];
			$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
			$XtableWidth=$tableWidth-160;
			$XtableWidth=0;
			//$XsubTableWidth=$subTableWidth-160;
			$ProductId=$mainRows["ProductId"];
			$TestStandard=$mainRows["TestStandard"];
			include "../admin/Productimage/getProductImage.php";
			$StuffListTB="
				<table width='$XtableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $TestStandard)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
				</tr>
				
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";

					$ValueArray=array(
			                       array(0=>$StuffId,			1=>"align='center'"),
			                       array(0=>$StuffCname),
			                       array(0=>$Gfile,		1=>"align='center'"),
			                       array(0=>$OrderQtyInfo, 	1=>"align='center'"),
			                       array(0=>$OrderQty, 	1=>"align='center'"),
			                       array(0=>$FactualQty,	1=>"align='right'"),
			                       array(0=>$AddQty,	1=>"align='right'"),
			                      array(0=>$Qty,	1=>"align='right'"),
			                      array(0=>$Price,	1=>"align='right'"),
			                      array(0=>$UnitName,		1=>"align='center'"),
			                      array(0=>$Amount,	1=>"align='right'"),
			                      array(0=>$cwEstate,	1=>"align='center'"),
			                      array(0=>$StockId,	1=>"align='center'"),
			                      array(0=>$Forshort,	1=>"align='center'"),
			                      array(0=>$PreAmount,	1=>"align='right'"),
			                      array(0=>$InvoiceNO,1=>"align='center'"),
			                      array(0=>$OutDate,	1=>"align='center'"),
			                      array(0=>$ShipStr,	1=>"align='center'")
			                 );
		    $checkidValue=$Id;
		    include "../model/subprogram/read_model_6.php";
		     echo $StuffListTB;
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
?>
<div id='divShadow' class="divShadow" style="display:none;">
	<div class='divInfo' id='divInfo'>
	<table width="300">
		<tr><td align="left">请输入请款月份</td></tr>
		<tr><td align="center"><input name="Month" type="text" id="Month" value="" maxlength="7"></td></tr>
		<tr><td align="right"><a href="javascript:ckeckForm()">确定</a> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
	</table>
	</div>
</div>
<div id="divPageMask" class="divPageMask" style="display:none;">
	<iframe scrolling="no" height="100%" width="100%" marginwidth="0" marginheight="0" src="MaskBgColor.htm"></iframe>
</div>
<?php
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function showMaskDiv(){	//显示遮罩对话框
	//检查是否有选取记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					break;
					}
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		alert("没有选取记录!");
		}
	else{
		document.form1.Month.value="";
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		}
	}

function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}
function ckeckForm(){
	//检查月份
	var checkMonth=yyyymmCheck(document.form1.Month.value);
	if(checkMonth){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=false;
				}
			}
		document.form1.action="cg_tkout_updated.php?ActionId=14";
		document.form1.submit();
		}
	else{
		alert("格式不对(YYYY-MM)");
		}
	}
function zhtj(obj){
	switch(obj){
		case "BuyerId"://改变采购
			document.forms["form1"].elements["GysPayMode"].value="";
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		case "GysPayMode":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		case "CompanyId":
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		}
	document.form1.action="cg_tkout_read.php";
	document.form1.submit();
	}
</script>