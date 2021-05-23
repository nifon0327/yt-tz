<?php   
/*电信---yang 20120801
$DataIn.ck1_rkmain
$DataSharing.providerdata
$DataSharing.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.ch1_shipmain";
ChangeWtitle("$SubCompany 删除生产登记产量");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage;
//Mid=201012110502|7020
$AField=explode("|",$Mid);
$POrderId=$AField[0];
$TypeId=$AField[1];
$scFrom=$AField[2];   //用于是否还要更新状态，如果是0的，还要更新回2,要重新审核

//步骤3：

$tableWidth=520;$tableMenuS=400;
//$CustomFun="<span onClick='ViewId(2)' $onClickCSS>加入下级模块</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="10" height="10" class="A0010">&nbsp;</td>
		<td colspan="2" class="A0100">
   			<input name='AddIds' type='hidden' id="AddIds">
            <input name='scFrom' type='hidden' id="scFrom" value="<?php    echo $scFrom?>">
            <input name='POrderId' type='hidden' id="POrderId" value="<?php    echo $POrderId?>">
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<table border="1" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A0111" width="80" align="center">操作</td>
		<td class="A0101" width="80" align="center">序号</td>
		<td class="A0101" width="80" align="center">主分类</td>
		<td class="A0101" width="60" align="center">数量</td>
		<td class="A0101" width="200" align="center">日期</td>
		   
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" >&nbsp;</td>
		<td colspan="5" align="center" class="A0111">
		<div style="width:500;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='500' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php   
			//入库明细列表
			$Result2 = mysql_query("SELECT C.Id,X.TypeName,C.Qty,C.Date FROM $DataIn.sc1_cjtj C
						   Left join $DataIn.stufftype X ON X.TypeId=C.TypeId
						   WHERE C.POrderId='$POrderId' AND C.TypeId='$TypeId' order by C.Date desc",$link_id);
			/*
			echo "SELECT C.Id,X.TypeName,C.Qty,C.Date FROM $DataIn.sc1_cjtj C
						   Left join $DataIn.stufftype X ON X.TypeId=C.TypeId
						   WHERE C.POrderId='$POrderId' AND C.TypeId='$TypeId'";
			*/			   
			if($Row2 = mysql_fetch_array($Result2)){
				$i=1;
				do{
					$Id=$Row2["Id"];
					$TypeName=$Row2["TypeName"];
					$Qty=$Row2["Qty"];
					$Date=$Row2["Date"];
					$eName="&nbsp;";
					echo"<tr><td align='center' class='A0101' width='80' height='20'><input name='checkid[]' type='checkbox' id='checkid[]' value='$Id'>删除</td>";
					echo"<td align='center' class='A0101' width='80'>$i</td>";
					//echo"<td align='center' class='A0101' width='220'>$dModuleId<input name='checkid[]' type='hidden' id='checkid[]' value='$dModuleId'></td>";
					echo"<td align='center' class='A0101' width='80'>$TypeName</td>";
					echo"<td align='center' class='A0101' width='60'>$Qty</td>";
					echo"<td align='center' class='A0101' width='200'>$Date</td>";
					

					$i++;
					}while ($Row2 = mysql_fetch_array($Result2));
				}
			?>
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>