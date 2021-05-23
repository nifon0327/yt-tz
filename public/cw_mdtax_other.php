<?php 
//电信-zxq 2012-08-01
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width= valign="bottom"><span class="redB">◆已有行政费用明细	<span>    </td>
	</tr>
	
</table>


<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="50" align="center">Id</td>
		<td class="A1101" width="60" align="center">请款人</td>
		<td class="A1101" width="90" align="center">请款日期</td>
		<td class="A1101" width="60" align="center">金额</td>
		<td class="A1101" width="429" align="center">说明</td>
		<td class="A1101" width="112" align="center">分类</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
	<tr>
		<td width="10" class="A0010" height="100">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:881;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='881' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="Listfee">
 		<?php 
		//需求单列表

		$feeResult = mysql_query("SELECT S.Id,S.Mid,S.Content,S.Operator,S.Amount,S.Date,T.Name AS Type,C.Symbol AS Currency,M.TaxNo
                                     FROM $DataIn.cw14_mdtaxfee M
                                     LEFT JOIN $DataIn.hzqksheet S ON S.Id=M.otherfeeNumber 
                                     LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
                                     LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
                                     WHERE M.TaxNo='$TaxNo'",$link_id);
		
		if($feeRows = mysql_fetch_array($feeResult)){
			$i=1;
			do{
			    $Id=$feeRows["Id"];
				$Mid=$feeRows["Mid"];
				$Content=$feeRows["Content"];
				$Operator=$feeRows["Operator"];
				$Amount=$feeRows["Amount"];
				$Date=$feeRows["Date"];
				$Type=$feeRows["Type"];
				$Currency=$feeRows["Currency"];
				$TaxNo=$feeRows["TaxNo"];
				include "../model/subprogram/staffname.php";			
				
			    //echo "$Number.^^.$Forshort.^^.$InvoiceNO.^^.$Amount.^^.$Date.^^.$Wise";
				echo"<tr><td width='40' class='A0101' align='center'>";
				echo"<a href='#' onclick='deleteRowfee(this.parentNode,Listfee)' title='删除此Invoice'>×</a>";
				echo"<td width='40' class='A0101' align='center'>$i</td>";
				echo"</td><td width='50' class='A0101' align='center'>$Id</td>
					<td width='60' class='A0101' align='center'>$Operator</td>
					<td width='90' class='A0101' align='center'>$Date</td>
					<td width='60' class='A0101' align='center'>$Amount</td>
					<td width='429' class='A0101' align='left'>$Content</td>
					<td width='112' class='A0101' align='center'>$Type</td>
					</tr>";
				$i++;
				}while($feeRows = mysql_fetch_array($feeResult));
			}
		?>           
            
            
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	
</table>


<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
		<td width="10" height="15" class="A0010">&nbsp;</td>
		<td   valign="bottom">&nbsp;	    </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="15" class="A0010">&nbsp;</td>
		<td   valign="bottom">◆新增行政费用明细	    </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	
</table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="50" align="center">Id</td>
		<td class="A1101" width="60" align="center">请款人</td>
		<td class="A1101" width="90" align="center">请款日期</td>
		<td class="A1101" width="60" align="center">金额</td>
		<td class="A1101" width="429" align="center">说明</td>
		<td class="A1101" width="112" align="center">分类</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
	<tr>
		<td width="10" class="A0010" height="80">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:881;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='881' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTablefee">
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	
</table>