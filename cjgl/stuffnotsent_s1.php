<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transition al.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR />
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' href='lightgreen/read_line.css'>
<script language="javascript" type="text/javascript"
	src="../model/js/jquery-1.11.1.js"></script>
<link rel="stylesheet" href="../model/keyright.css">
<script src="../model/pagefun.js" type="text/javascript"></script>
</head>
<body onhelp="return false;" oncontextmenu="event.returnValue=false"
	onkeydown="unUseKey()">
<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>
<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//OK
$Th_Col="选项|40|序号|40|配件ID|50|配件名称|250|订单需求数|70|采购总数|70|已送货总数|70|待送货总数|70|未送货总数|70|未补货总数|70|送货楼层|80";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$tableWidth=0;
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$tableWidth+=$wField[$i];
	}
//查询条件

$SearchRows =" and S.CompanyId='$myCompanyId'";
//增加业务单号下拉筛选
$ForshortList = "";
$ForshortResult = mysql_query("SELECT
	O.Forshort
FROM
	$DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON S.POrderId = Y.POrderId
LEFT JOIN $DataIn.yw1_ordermain YO ON Y.OrderPO = YO.OrderPO
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = YO.CompanyId
WHERE 0.Forshort IS NOT NULL
GROUP BY O.Forshort", $link_id);
if ($ForshortRow = mysql_fetch_array($ForshortResult)) {
    $ForshortList .= "<select name='khCompanyId' id='khCompanyId' onchange='ResetPage(1,5)'>";
    $ForshortList .= "<option value='' selected>全部客户</option>";
    do {
        $thisForshort = $ForshortRow["Forshort"];
        //$PurchaseID=$PurchaseID==""?$thisOrderPO:$PurchaseID;
        if ($thisForshort == "") {

        }
        else if ($khCompanyId == $thisForshort) {
            $ForshortList .= "<option value='$thisForshort' selected>$thisForshort</option>";
            $ForshortList .= " and O.Forshort='$thisForshort' ";
        }
        else {
            $ForshortList .= "<option value='$thisForshort'>$thisForshort</option>";
        }
    } while ($ForshortRow = mysql_fetch_array($ForshortResult));
    $ForshortList .= "</select>&nbsp;";
}

// 业务订单
$POrderIdList = "";
$POrderIdResult = mysql_query("SELECT
	CONCAT(M.PurchaseID,'/',M.purchaseOrderNo) AS PurchaseID
FROM
	$DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.cg1_stockmain M ON M.id = S.Mid
LEFT JOIN $DataIn.yw1_ordersheet Y ON S.POrderId = Y.POrderId
LEFT JOIN $DataIn.yw1_ordermain YO ON Y.OrderPO = YO.OrderPO
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = YO.CompanyId
where 1 $SearchRows
group by M.PurchaseID order by CONCAT(M.PurchaseID,'/',M.purchaseOrderNo) desc", $link_id);

if ($POrderIdRow = mysql_fetch_array($POrderIdResult)) {
    $POrderIdList .= "<select name='PurchaseID' id='PurchaseID' onchange='ResetPage(1,5)'>";
    $POrderIdList .= "<option value='all' selected>全部PO</option>";
    do {
        $thisPurchaseID = $POrderIdRow["PurchaseID"];
        if(!$thisPurchaseID) continue;
        $PurchaseID=$PurchaseID==""?$thisPurchaseID:$PurchaseID;
        if ($PurchaseID == $thisPurchaseID) {
            $POrderIdList .= "<option value='$thisPurchaseID' selected>$thisPurchaseID</option>";
            $SearchRows .= " and M.PurchaseID='$thisPurchaseID' ";
        }
        else {
            $POrderIdList .= "<option value='$thisPurchaseID'>$thisPurchaseID</option>";
        }
    } while ($POrderIdRow = mysql_fetch_array($POrderIdResult));
    $POrderIdList .= "</select>&nbsp;";
}

$GysResult= mysql_query("SELECT * FROM (
    SELECT A.SendFloor,B.Name
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
	LEFT JOIN $DataIn.yw1_ordersheet Y ON S.POrderId = Y.POrderId
  LEFT JOIN $DataIn.yw1_ordermain YO ON Y.OrderPO = YO.OrderPO
  LEFT JOIN $DataIn.trade_object O ON O.CompanyId = YO.CompanyId
	LEFT JOIN $DataIn.base_mposition B ON B.Id = A.SendFloor
	WHERE 1  $SearchRows  AND S.rkSign>0 AND S.Mid>0  
    AND NOT EXISTS (SELECT T.Property FROM $DataIn.stuffproperty T WHERE  T.StuffId=S.StuffId AND T.Property=9)
    GROUP BY S.StuffId  
 UNION ALL
    SELECT  A.SendFloor,B.Name
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.yw1_ordersheet Y ON S.POrderId = Y.POrderId
  LEFT JOIN $DataIn.yw1_ordermain YO ON Y.OrderPO = YO.OrderPO
  LEFT JOIN $DataIn.trade_object O ON O.CompanyId = YO.CompanyId
    INNER JOIN $DataIn.cg1_stuffcombox G ON G.mStockId=S.StockId 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
	LEFT JOIN $DataIn.base_mposition B ON B.Id = A.SendFloor
	WHERE 1 AND S.rkSign>0 AND S.Mid>0   $SearchRows  GROUP BY G.StuffId ) A  GROUP BY A.SendFloor ",$link_id);
if ($GysRow = mysql_fetch_array($GysResult)){
	$GysList="<select name='SendFloor' id='SendFloor' onChange='document.form1.submit()'>";//BillNumber重置
	$i=1;
	do{
		$theSendFloor=$GysRow["SendFloor"];
		$theSendFloorName=$GysRow["Name"];
		$SendFloor=$SendFloor==""?$theSendFloor:$SendFloor;
		if($SendFloor==$theSendFloor){
			$GysList.="<option value='$theSendFloor' selected>$theSendFloorName</option>";
			$SearchRows.=" AND A.SendFloor='$theSendFloor'";
			}
		else{
			$GysList.="<option value='$theSendFloor'>$theSendFloorName</option>";
			}
		$i++;
		}while($GysRow = mysql_fetch_array($GysResult));
		$GysList.="</select>";
	}


$nowInfo="当前: 送货配件资料";
echo"<table cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
	<tr bgcolor='#D9D9D9'>
	<td colspan='6' height='40px' class='A1010'>$ForshortList $POrderIdList $GysList</td><td colspan='5' align='right' class='A1001'><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
    echo "<tr>";
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";

$j=1;
/*
$mySql="SELECT '0' AS Sign,S.StuffId,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.Gremark,
	A.GfileDate,A.TypeId ,U.Name AS UnitName
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
	WHERE 1  $SearchRows   AND S.Mid>0
    GROUP BY S.StuffId ";
*/
 $mySql="select * from (SELECT '0' AS Sign,S.StuffId,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,
    A.TypeId ,U.Name AS UnitName,U.decimals,B.Name AS SendFloorName
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
	LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
	LEFT JOIN $DataIn.base_mposition B ON B.Id = A.SendFloor
	LEFT JOIN $DataIn.yw1_ordersheet Y ON S.POrderId = Y.POrderId
  LEFT JOIN $DataIn.yw1_ordermain YO ON Y.OrderPO = YO.OrderPO
  LEFT JOIN $DataIn.trade_object O ON O.CompanyId = YO.CompanyId
	WHERE 1  $SearchRows  AND S.rkSign>0 AND S.Mid>0  
    AND NOT EXISTS (SELECT T.Property FROM $DataIn.stuffproperty T WHERE  T.StuffId=S.StuffId AND T.Property=9)
    GROUP BY S.StuffId  
 UNION ALL
    SELECT  '1' AS Sign,A.StuffId,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,
    A.TypeId,U.Name AS UnitName,U.decimals,B.Name AS SendFloorName
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
  INNER JOIN $DataIn.cg1_stuffcombox G ON G.mStockId=S.StockId 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit 
	LEFT JOIN $DataIn.base_mposition B ON B.Id = A.SendFloor
	LEFT JOIN $DataIn.yw1_ordersheet Y ON S.POrderId = Y.POrderId
  LEFT JOIN $DataIn.yw1_ordermain YO ON Y.OrderPO = YO.OrderPO
  LEFT JOIN $DataIn.trade_object O ON O.CompanyId = YO.CompanyId
	WHERE 1 AND S.rkSign>0 AND S.Mid>0   $SearchRows  GROUP BY G.StuffId) D where TypeId <> '9017' ORDER BY D.StuffCname DESC";

//echo $mySql;
$mainResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($mainResult)){
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$TempStuffCname=$StuffCname;
		$UnitName=$myRow["UnitName"];
		$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
		$Picture=$myRow["Picture"];
        $TypeId=$myRow["TypeId"];
		$Gfile=$myRow["Gfile"];
		$Gremark=$myRow["Gremark"];
		$decimals=$myRow["decimals"];
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		//加密
        $SendFloorName=$myRow["SendFloorName"];
		$Gstate=$myRow["Gstate"];

		include "../model/subprogram/stuffimg_model.php";
         $Sign=  $myRow["Sign"];
		//已购总数
		if ($Sign==1){
			$cgTemp=mysql_query("SELECT SUM(S.OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty   
            FROM $DataIn.cg1_stocksheet G
            INNER JOIN $DataIn.cg1_stuffcombox S ON S.mStockId=G.StockId 
            LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
            WHERE  G.CompanyId='$myCompanyId'  and S.StuffId='$StuffId' and G.Mid>0 ",$link_id);
           $SearchRowsA="";
		}
		else{
			$cgTemp=mysql_query("SELECT SUM(S.OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty   
            FROM $DataIn.cg1_stocksheet S 
            LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
            WHERE 1 and S.CompanyId='$myCompanyId' and S.StuffId='$StuffId' and S.Mid>0 ",$link_id);
            $SearchRowsA=" and S.CompanyId='$myCompanyId'";
		}


		$cgQty=mysql_result($cgTemp,0,"Qty");
		$cgQty=$cgQty==""?0:$cgQty;
		$odQty=mysql_result($cgTemp,0,"odQty");
		$odQty=$odQty==""?0:$odQty;

		//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
		WHERE R.StuffId='$StuffId' $SearchRowsA",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;

		//待送货数量
		$shSql=mysql_query("SELECT SUM(G.Qty) AS Qty FROM $DataIn.gys_shsheet G
		LEFT JOIN $DataIn.gys_shmain S ON S.Id=G.Mid
		WHERE 1 AND G.SendSign=0 AND G.Estate >0 AND G.StuffId=$StuffId $SearchRowsA ",$link_id);

		$shQty=mysql_result($shSql,0,"Qty");
		$shQty=$shQty==""?0:$shQty;
		$noQty=$cgQty-$rkQty-$shQty;


	    //退货的总数量
		$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
		LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
		WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' AND S.Estate = 0  ",$link_id);
		$thQty=mysql_result($thSql,0,"thQty");
		$thQty=$thQty==""?0:$thQty;

	    //补货的数量
		$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
		LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
		WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
		$bcQty=mysql_result($bcSql,0,"bcQty");
		$bcQty=$bcQty==""?0:$bcQty;

		$bcshSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
		LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
		WHERE 1 AND M.CompanyId = '$myCompanyId'  AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1') AND S.Estate>0",$link_id);
		$bcshQty=mysql_result($bcshSql,0,"Qty");
		$bcshQty=$bcshQty==""?0:$bcshQty;

		$webQty=$thQty-$bcQty-$bcshQty; //未补数量


        include"../model/subprogram/stuff_Property.php";//配件属性

		 $ValueStr="$StuffId^^ ".str_replace(","," ",$TempStuffCname)." ^^$odQty^^$cgQty^^$rkQty^^$noQty^^$webQty";
		 $chooseStr="<input name='checkid[$j]' type='checkbox' id='checkid$j' value='$ValueStr'>";
			//输出明细
			$noQty = round($noQty, $decimals);
			if($noQty>0 || $webQty>0){
				echo"<tr>";
				echo"<td class='A0111' height='25'  align='center' width='$Field[$m]'>$chooseStr</td>";
				$m=$m+2;
				echo"<td class='A0101' align='center' width='$Field[$m]'>$j</td>";
				$m=$m+2;
				echo"<td class='A0101' align='center' width='$Field[$m]'>$StuffId</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]'>$StuffCname</td>";
				$m=$m+2;
				echo"<td class='A0101' align='center' width='$Field[$m]' >$odQty</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$cgQty</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$rkQty</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$shQty</td>";
				$m=$m+2;
				echo"<td  class='A0101' width='$Field[$m]' align='center'>$noQty</td>";
				$m=$m+2;
				echo"<td  class='A0101' width='$Field[$m]' align='center'>$webQty</td>";
				$m=$m+2;
				echo"<td  class='A0101' width='$Field[$m]' align='center'>$SendFloorName</td>";
				echo"</tr>";
				}
			    $j++;
		}while($myRow = mysql_fetch_array($mainResult));
	    echo"</table></td></tr>";
	}
if ($j==1){
	echo"<tr><td colspan='11' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
	<br>
	<div id="ie5menu"
		style="border-color: rgb(155, 201, 223); border-style: solid; border-width: 1px; display: none; z-index: 99999; position: absolute; background-color: white; visibility: hidden; left: 193px; top: 71px;"
		class="skin1">
		<div id="ColorSide"
			style="z-index: 866; left: 0px; width: 20px; position: absolute; background-color: lightblue; height: 38px;"></div>
		<div class="menuitems" onmouseover="myover(this);"
			onmouseout="myout(this);"
			onclick="All_elects()" align="right">全选记录&nbsp;&nbsp;</div>
		<div class="menuitems" onmouseover="myover(this);"
			onmouseout="myout(this);"
			onclick="Instead_elects()"
			align="right">反选记录&nbsp;&nbsp;</div>
	</div>

		<script src="../model/pagebottom.js" type="text/javascript"></script>

		<script>
		function showmenuie5(event){

		    if (showIpad==0) showmenuFlag=2;
		        else showmenuFlag=1; //new
			event = event ? event : (window.event ? window.event : null);
			var menu = $("ie5menu");
			var Color = $("ColorSide");
			menu.style.display="block";
			menu.style.visibility ="visible";

		    var rightedge=880-event.clientX;

			var bottomedge=600-event.clientY;
			if(rightedge<menu.offsetWidth){
				menu.style.left=event.clientX-menu.offsetWidth + "px";
				}
			else{
				menu.style.left=event.clientX + "px";
				}
			if(bottomedge<menu.offsetHeight){
				menu.style.top=event.clientY-menu.offsetHeight + "px";
				}
			else{
				menu.style.top=event.clientY + "px";
			    }
			Color.style.height=menu.offsetHeight;
			   return false;
			}

		document.onclick=function(){  //new
		       if(showmenuFlag==1) showmenuFlag=2;
			   else if(showmenuFlag==2){hidemenuie5();}

			}

		function All_elects() {
			jQuery('input[name^="checkid"]:checkbox').prop("checked",true);
		}

		function Instead_elects() {
			jQuery('input[name^="checkid"]:checkbox').prop("checked", false);
		}

		</script>
</body>
</html>