<?php 
include "../model/modelhead.php";
$upDataMain="$DataIn.cw1_fkoutmain";
ChangeWtitle("$SubCompany 更新供应商货款结付资料");
//include "subprogram/upmain_model.php";
$fromWebPage=$funFrom."_cw";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT BankId,PayDate,PayAmount,djAmount,Payee,Receipt,Checksheet,Remark,Locks FROM $upDataMain WHERE Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BankId=$MainRow["BankId"];	//结付银行
	$PayAmount=$MainRow["PayAmount"];	//结付总额
	$djAmount=$MainRow["djAmount"];		//结付总额
	$sumAmount=$PayAmount+$djAmount;
	$PayDate=$MainRow["PayDate"];		//结付日期
	$Payee=$MainRow["Payee"];			//结付凭证
	$Receipt=$MainRow["Receipt"];		//回执
	$Checksheet=$MainRow["Checksheet"];	//对帐单
	$Locks=$MainRow["Locks"];
	$PayDateStr=$Locks==0?$PayDate:"<input name='PayDate' type='text' id='Date' value='$PayDate' size='72' onfocus='new WdatePicker(this,null,false,\"whyGreen\")' title='必选项，结付日期' DataType='Date' format='ymd' Msg='日期不对或没选日期' readonly>";		//状态		决定是否可以修改结付日期
	$mainRemark=$MainRow["Remark"];			//结付备注
	//供应商和货币
	$checkSymbol=mysql_query("SELECT P.Forshort,C.Symbol 
	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE S.Mid='$Mid' LIMIT 1",$link_id);
	if($checkRow = mysql_fetch_array($checkSymbol)) {
		$Forshort=$checkRow["Forshort"];
		$cashSymbol=$checkRow["Symbol"];
		}
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,20,Mid,$Mid,oldPayDate,$PayDate,cashSymbol,$cashSymbol,funFrom,$funFrom,From,$From,Estate,0,Pagination,$Pagination,Page,$Page,fromWebPage,$fromWebPage,PayAmount,$PayAmount";
//步骤4：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td width="100" align="right" scope="col">供 应 商</td>
            <td scope="col"><?php  echo $Forshort?></td>
          </tr>
		<tr>
          <td align="right" scope="col">结付总额</td>
          <td scope="col"><?php  echo $sumAmount?> <?php  echo $cashSymbol?></td>
		  </tr>
		<tr>
            <td align="right" scope="col">已付订金</td>
            <td scope="col"><?php  echo $djAmount?></td>
		</tr>
		<?php 
		//订金检查
		$djResult = mysql_query("SELECT Id,TypeId,CompanyId,Amount,Remark,Date,Estate,Locks,Operator FROM $DataIn.cw2_fkdjsheet WHERE Did='$Mid'",$link_id);
		if($djRow = mysql_fetch_array($djResult)){
			echo"<tr><td scope='col'></td><td scope='col'>
				 <table width='100%' border='0' cellpadding='0' cellspacing='0' id='djList'>
				    <tr bgcolor='$Title_bgcolor'>
						<td class='A1111' align='center' width='30' height='20'>操作</td>
						<td class='A1101' align='center' width='30'>序号</td>
						<td class='A1101' align='center' width='60'>订金金额</td>
						<td class='A1101' align='center'>订金说明</td>
						<td class='A1101' align='center' width='100'>所属分类</td>
						<td class='A1101' align='center' width='50'>请款人</td>
						<td class='A1101' align='center' width='70'>请款日期</td>
					</tr>";
			$i=1;
			do{
				$Id=$djRow["Id"];
				$Amount=$djRow["Amount"];
				$Remark=$djRow["Remark"];
				$TypeId=$djRow["TypeId"];
				$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
				$Operator=$djRow["Operator"];
				$Date=$djRow["Date"];
				echo"<tr><td class='A0111' align='center'><a href='#' onclick='deleteRow(this.parentNode,djList,$Id)' title='取消此订金金额'>×</a></td>              
					<td class='A0101' align='center' height='20'>$i</td>
              		<td class='A0101' align='right'>$Amount</td>
              		<td class='A0101'>&nbsp;$Remark</td>
              		<td class='A0101'>&nbsp;$Type</td>
              		<td class='A0101' align='center'>$Operator</td>
					<td class='A0101' align='center'>$Date</td>
					</tr>";
				$i++;
				}while($djRow = mysql_fetch_array($djResult));
			 echo"</table></td></tr>";
			}
			
				//供应商扣款数
	   $KKReuslt=mysql_query("SELECT SUM(Amount) AS KKAmount FROM $DataIn.cw15_gyskksheet WHERE Kid='$Mid'",$link_id);
	   $KKAmount=mysql_result($KKReuslt,0,"KKAmount");
	   $PayAmount=$PayAmount-$KKAmount;
	   $KKAmount=$KKAmount==0?"0.00":"<a href='cw_cgkk_read.php?Kid=$Mid' target='_bank'>$KKAmount</a>";

	   $ReturnReuslt=mysql_query("SELECT SUM(Amount) AS ReturnAmount FROM $DataIn.cw2_hksheet WHERE Did='$Mid'",$link_id);
	   $ReturnAmount=mysql_result($ReturnReuslt,0,"ReturnAmount");
	   $PayAmount=$PayAmount-$ReturnAmount;
	   $ReturnAmount=$ReturnAmount==0?"0.00":"<a href='cw_fkhk_read.php?Did=$Mid' target='_bank'>$ReturnAmount</a>";
          ?>
          <tr>
            <td align="right" scope="col">供应商扣款</td>
            <td scope="col"><?php  echo $KKAmount?></td>
		</tr>
        <tr>
            <td align="right" scope="col">货款返利</td>
            <td scope="col"><?php  echo $ReturnAmount?></td>
		</tr>
		<tr>
          <td align="right" scope="col">实付金额</td>
          <td scope="col"><?php  echo $PayAmount?></td>
		  </tr>
		 
		<tr>
            <td align="right" scope="col">结付日期</td>
            <td scope="col"><?php  echo $PayDateStr?></td>
		</tr>
		<tr>
            <td align="right" scope="col">结付银行</td>
            <td scope="col"><?php 
			include "../model/selectbank2.php";
			?></td>
		</tr>
		<tr>
            <td align="right">汇单凭证</td>
            <td>
			<input name="Payee" type="file" id="Payee" size="74" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1">
			</td>
		</tr>
            <?php 
			$ReceiptRow=6;
			$ChecksheetRow=7;
			if($Payee!=0){
				echo"<tr><td>&nbsp;</td><td><input name='oldPayee' type='checkbox' id='oldPayee' value='1'><LABEL for='oldPayee'>删除已传凭证</LABEL></td></tr>";
				$ReceiptRow++;
				$ChecksheetRow++;
				}
			?>
          <tr>
            <td align="right">回执图档</td>
            <td>
			<input name="Receipt" type="file" id="Receipt" size="74" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="<?php  echo $ReceiptRow?>" Cel="1">
			</td>
          </tr>
            <?php 
			if($Receipt!=0){
				echo" <tr><td>&nbsp;</td><td><input name='oldReceipt' type='checkbox' id='oldReceipt' value='1'><LABEL for='oldReceipt'>删除已传回执</LABEL></td></tr>";
				$ChecksheetRow++;
				}
			?>
          <tr>
            <td align="right">对 帐 单</td>
            <td>
			<input name="Checksheet" type="file" id="Checksheet" size="74" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="<?php  echo $ChecksheetRow?>" Cel="1">
			</td>
          </tr>
            <?php 
			if($Checksheet!=0){
				echo" <tr><td>&nbsp;</td><td><input name='oldChecksheet' type='checkbox' id='oldChecksheet' value='1'><LABEL for='oldChecksheet'>删除已传对帐单</LABEL></td></tr>";
				}
			?>
		<tr>
            <td align="right" valign="top">结付备注</td>
            <td ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php  echo $mainRemark?></textarea></td>
          </tr>
		<?php 
		if($Locks==0){
			echo"<tr><td valign='top'>&nbsp;</td><td >";
				if($Keys & mLOCK){
					echo"<input name='Locks' type='checkbox' id='Locks' value='1'><LABEL for='Locks'>主结付单解锁</LABEL>";
					}
				echo" <span class='redB'>(主结付单处于锁定状态，不允许修改结付日期及相关明细项目)</span>";
			echo"</td></tr>";
			}
		?>
</table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>
function deleteRow (RowTemp,TableTemp,delId){
	var rowIndex=RowTemp.parentElement.rowIndex;
	var message=confirm("确定要删除这笔订金吗？");
	if (message==true){
		myurl="cw_fkout_updated.php?Id="+delId+"&ActionId=919";
		retCode=openUrl(myurl);
		if (retCode!=-2){
			TableTemp.deleteRow(rowIndex);
			ReOpen("cw_fkout_upmain");
			}
		else{
			alert("删除失败！");return false;
			}
		}	
	}
</script>
