<?php   
/*
配件分类页面
已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$SearchRows=" AND T.mainType<2";
$TempArray=explode("|",$TempId);
$JobId=$TempArray[0];	//采购
$predivNum=$TempArray[1];	//a
if ($JobId=="-1"){
   $SearchRows.=" AND M.JobId!='1' ";//去除经理采购数据
  }else{
	$SearchRows.=" AND M.JobId='$JobId' ";
 }
$mySql="SELECT M.Number,M.Name,count(*) as Nums 
	FROM $DataIn.stuffdata D 
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
	LEFT JOIN  $DataPublic.staffmain M ON M.Number=B.BuyerId 
	WHERE 1 AND D.Picture in (0,4,7) AND D.JobId='$JobId' AND D.Estate>0 $SearchRows GROUP BY M.Number ORDER BY M.Number
	";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=950;
$subTableWidth=930;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Number=$myRow["Number"];
		if ($Number!=0)
	 {
		$Name=$myRow["Name"];
		$Nums=$myRow["Nums"];
		$DivNum=$predivNum."a".$i;
		$TempId="$Number|$DivNum|$JobId";			
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_unuploadimg_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏下级资料. ' width='13' height='13' style='CURSOR: pointer'>";
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
			<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $Name?> <?php    echo $Nums?></td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
	 }
		}while ($myRow = mysql_fetch_array($myResult));
	}
//	添加其它部门让采购上传的数据
if ($JobId=="3"){
$mySql="SELECT D.StuffId 
	FROM $DataIn.stuffdata D 
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
	LEFT JOIN  $DataPublic.staffmain M ON M.Number=B.BuyerId 
	WHERE 1 AND D.Picture in (0,4,7) AND D.JobId='$JobId' AND D.Estate>0 AND M.JobId!='$JobId' AND T.mainType<2";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$SumNums=mysql_num_rows($myResult);
if ($SumNums>0)
   {
	$Number="-2"; //不是自己部门采购
	$Name="指定【采购】上传图片";
	$DivNum=$predivNum."a".$i;
	$TempId="$Number|$DivNum|$JobId";			
	$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_unuploadimg_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏下级资料. ' width='13' height='13' style='CURSOR: pointer'>";
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
			<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $Name?> <?php    echo $SumNums?></td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
	 }
}
?>