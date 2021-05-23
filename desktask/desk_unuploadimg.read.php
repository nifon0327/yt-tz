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
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 已下单未传图片的配件");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script>";
$SearchRows=" AND T.mainType<2";//需采购的配件需求单
//$SearchRows="";
$tableWidth=1010;
$subTableWidth=990;
$i=1;
//AND D.TypeId NOT IN(9074,9082,9093) 
$checkAll=mysql_query("SELECT D.StuffId   
	FROM $DataIn.stuffdata D 
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE 1 AND D.Picture in (0,4,7) AND (D.JobId>0 OR D.JobId='-1') AND D.Estate>0 $SearchRows ",$link_id);
$SumNums=mysql_num_rows($checkAll);

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
		<td height="24" colspan="5">已下单未上传图片的配件统计</td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" height="25">&nbsp;--按传图职责分类--&nbsp;&nbsp;未传合计：(<?php    echo $SumNums?>)</td>
  </tr>
</table>
<?php   
//条件：有下单给供应商 未传图片 配件可用 分类9000以上 采购在职？AND M.Estate>0 
//禁用类：AND D.TypeId NOT IN(9074,9082,9093) 
//SELECT Id,Name FROM $DataPublic.jobdata WHERE Estate=1 AND Id in(3,4,6,35) order by Id,Name
$ShipResult = mysql_query("SELECT J.Id,J.Name,M.Name as LeaderName,count(*) as Nums  
	FROM $DataIn.stuffdata D  
	LEFT JOIN $DataPublic.jobdata J ON J.Id=D.JobId 
	LEFT JOIN $DataIn.jobmanager JM ON JM.JobId=J.Id 
    LEFT JOIN  $DataPublic.staffmain M  ON M.Number = JM.LeaderNumber
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE 1 AND D.Picture in (0,4,7) AND (D.JobId>0 OR D.JobId='-1') AND D.Estate>0 $SearchRows GROUP BY J.Id ORDER BY J.Id",$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Number=$ShipRow["Id"];
		$Name=$ShipRow["Name"];
		$LeaderName=$ShipRow["LeaderName"];
		 if ($Number==""){
			$Number="-1";
			$Name="其它部门按采购人员索引";
	        $str_Title="";
		  }
		else{
		    $str_Title="负责人：";
		}
		$Nums=$ShipRow["Nums"];
		if ($Number=="3" or $Number=="-1"){
		   $DivNum="c".$i;
		   $TempId="$Number|$DivNum";
	      $ajax_File="desk_unuploadimg";
	    }
	  else{
		  $DivNum="u".$i;
		  $TempId="N|$DivNum|$Number";
	      $ajax_File="desk_unuploadimg_a";
	    }	
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"$ajax_File\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏下级资料. ' width='13' height='13' style='CURSOR: pointer'>";
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
			<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $Name?>&nbsp; <?php    echo $str_Title?><?php    echo $LeaderName?>&nbsp;(<?php    echo $Nums?>)</td>
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
