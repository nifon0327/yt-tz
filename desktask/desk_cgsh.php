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
/*电信-yang 20120801
已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 采购收货统计明细");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=1070;
$subTableWidth=1050;
$i=1;
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
		<td height="24" colspan="5">采购收货统计明细</td>
    </tr>
	<tr>
	  <td height="24" colspan="5"><input type="radio" name="Action" value="0" id="Action0" checked><label for="Action0">以供应商为索引进行统计</label>
	    <input name="Action" type="radio" value="1" id="Action1" onClick="javascript:document.form1.action='desk_cgsh1.php';document.form1.submit()"><label for="Action1">以收货日期为索引进行统计</label>
		 </td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" height="25" width="60" align="center">货币</td>
  <td class="A1101" width="40" align="center">排序符</td>
    <td class="A1101" width="40" align="center">序号</td>
    <td class="A1101" width="180" align="center">供应商简称</td>
    <td class="A1101" width="150" align="center">电话</td>
    <td class="A1101" width="150" align="center">传真</td>
    <td class="A1101" width="60" align="center">报表</td>
    <td class="A1101" width="60" align="center">图例</td>
    <td class="A1101" width="330" align="right">&nbsp;</td>
  </tr>
</table>
<?php   
//条件：有下单给供应商 未传图片 配件可用 分类9000以上 采购在职？AND M.Estate>0
$ShipResult = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter,I.Tel,I.Fax,C.Symbol 
	FROM $DataIn.ck1_rkmain M
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
	WHERE 1 AND P.Estate=1 AND  I.Type=3 GROUP BY M.CompanyId ORDER BY P.Currency,P.Letter",$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Symbol=$ShipRow["Symbol"];
		$Letter=$ShipRow["Letter"];
		$Tel=$ShipRow["Tel"]==""?"&nbsp;":$ShipRow["Tel"];
		$Fax=$ShipRow["Fax"]==""?"&nbsp;":$ShipRow["Fax"];
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
		//传递交货日期
		$DivNum="a".$i;
		$TempId="$CompanyId|$DivNum";			
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgsh_a1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏下级资料. ' width='13' height='13' style='CURSOR: pointer'>";
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
			<td class="A0111" height="25" width="60" align="center">&nbsp;<?php    echo $showPurchaseorder."&nbsp;$Symbol"?></td>
			<td class="A0101" width="40" align="center"><?php    echo $Letter?></td>
			<td class="A0101" width="40" align="center"><?php    echo $i?></td>
			<td class="A0101" width="180"><?php    echo $CompanyId."-".$Forshort;?></td>
			<td class="A0101" width="150"><?php    echo $Tel?></td>
			<td class="A0101" width="150"><?php    echo $Fax?></td>
			<td class="A0101" width="60" align="center">&nbsp;</td>
			<td class="A0101" width="60" align="center">&nbsp;</td>
			<td class="A0100" width="90" align="right">&nbsp;</td><td class="A0100" width="85" align="right">&nbsp;</td><td class="A0100" width="80" align="right">&nbsp;</td><td class="A0101" width="75" align="right">&nbsp;</td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
</form>
</body>
</html>