<?php   
//电信-yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 配件退换统计明细");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=1070;
$subTableWidth=1050;
$i=1;

$ActioToS="1";
$From=$From==""?"read":$From;
$funFrom="desk_cgth";
$nowWebPage=$funFrom."_read";	
?>
<body>

<form name='form1' id='checkFrom' enctype='multipart/form-data' method='post' action=''>
<?php    
echo "
<input name='funFrom' type='hidden' id='funFrom' value='$funFrom'>
<input name='fromWebPage' type='hidden' id='fromWebPage' value='$nowWebPage'>
<input name='From' type='hidden' id='From' value='$From'> ";
?>

<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">配件退换统计明细</td>
    </tr>
	<tr>
	  <td height="24" colspan="5"><input type="radio" name="Action" value="0" id="Action0" checked><label for="Action0">以供应商为索引进行统计</label>
	  <?php   
	    //<input name="Action" type="radio" value="1" id="Action1" onClick="javascript:document.form1.action='desk_cgsh1.php';document.form1.submit()"><label for="Action1">以退货日期为索引进行统计</label>
if($From=="slist"){
	$Pagination=0;
   	$CencalSstr="<input name='CencalS' type='checkbox' id='CencalS' value='1' checked onclick='javascript:ToReadPage(\"$nowWebPage\",\"$Pagination\")'><LABEL for='CencalS'>查询结果</LABEL>";
	echo $CencalSstr;

  	}
else{
	$SearchRows="";
	}		
?>
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
	<td class="A1101" width="110" align="center">退换数量</td>
	<td class="A1101" width="110" align="center">补仓数量</td>
	<td class="A1101" width="110" align="center">未补数量</td>
  </tr>
</table>
<?php   
//条件：有下单给供应商 未传图片 配件可用 分类9000以上 采购在职？AND M.Estate>0
if($From!="slist"){
	$SearchRows="";
}
/*
$ShipResult = mysql_query("
	
	SELECT M.CompanyId,P.Forshort,P.Letter,I.Tel,I.Fax,C.Symbol,ifnull(B.bcQty,0) AS bcQty,SUM(S.Qty) AS thQty, (SUM(S.Qty)-ifnull(B.bcQty,0)) AS wbQty
	FROM $DataIn.ck2_thsheet S
	LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	LEFT JOIN 
		(SELECT ifnull(SUM(S.Qty),0) AS bcQty,M.CompanyId 
		FROM $DataIn.ck3_bcmain M
		LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid=M.Id
		WHERE 1 GROUP BY M.CompanyId) 
		B ON B.CompanyId=M.CompanyId
	WHERE 1 $SearchRows AND P.Estate=1 AND  I.Type=3 
	GROUP BY M.CompanyId ORDER BY P.Currency,P.Letter",$link_id);
*/
$ShipResult = mysql_query("
	
	SELECT M.CompanyId,P.Forshort,P.Letter,I.Tel,I.Fax,C.Symbol,ifnull(B.bcQty,0) AS bcQty,SUM(S.Qty) AS thQty, (SUM(S.Qty)-ifnull(B.bcQty,0)) AS wbQty
	FROM $DataIn.ck2_thsheet S
	LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	LEFT JOIN 
		(SELECT ifnull(SUM(S.Qty),0) AS bcQty,M.CompanyId 
		FROM $DataIn.ck3_bcmain M
		LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid=M.Id
		WHERE 1 GROUP BY M.CompanyId) 
		B ON B.CompanyId=M.CompanyId
	WHERE 1 $SearchRows AND P.Estate=1 
	GROUP BY M.CompanyId ORDER BY P.Currency,P.Letter",$link_id);


if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Symbol=$ShipRow["Symbol"];
		$Letter=$ShipRow["Letter"];
		$Tel=$ShipRow["Tel"]==""?"&nbsp;":$ShipRow["Tel"];
		$Fax=$ShipRow["Fax"]==""?"&nbsp;":$ShipRow["Fax"];
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
		$bcQty=$ShipRow["bcQty"];
		$thQty=$ShipRow["thQty"];
		$wbQty=$ShipRow["wbQty"];
		//$wbQty=$thQty-$bcQty;
		if($thQty==$wbQty){
			$wbQty="<div align='right' style='color: #FF0000;font-weight: bold;'>".$wbQty."</div>";
			}
		else{
			if($wbQty>0){
				$wbQty="<div align='right' style='color: #0000FF;font-weight: bold;'>".$wbQty."</div>";
				}
			else{
				if($wbQty==0){
					$wbQty="<div align='center' style='color: #009900;font-weight: bold;'>OK</div>";
					}
				else{
					$wbQty="<div align='right' style='color: #FF0000;font-weight: bold;'>".$wbQty."(异常)</div>";
					}
				}
			}
		$bcQty=$bcQty==0?"&nbsp;":$bcQty;
		//传递交货日期
		$DivNum="a".$i;
		$SearchTemp=urlencode($SearchRows);
		//$SearchRows=mb_convert_encoding($SearchRows,"UTF-8", "auto");
		$TempId="$CompanyId|$DivNum|$SearchTemp";	
		//echo "<br> TempId:$TempId";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgth_a1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏下级资料. ' width='13' height='13' style='CURSOR: pointer'>";
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
			<td class="A0101" width="110" align="right"><?php    echo $thQty?></td>
			<td class="A0101" width="110" align="right"><?php    echo $bcQty?></td>
			<td class="A0101" width="110" align="right"><?php    echo $wbQty?></td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}

include "../model/subprogram/read_model_menu.php";	
?>
</form>
</body>
</html>