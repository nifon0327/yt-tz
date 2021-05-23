<?php 
//二合一已更新电信---yang 20120801
$fromWebPage=$funFrom."_cw";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT BankId,PayDate,PayAmount,Payee,Receipt,Checksheet,Remark,Locks FROM $upDataMain WHERE Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BankId=$MainRow["BankId"];	//结付银行
	$PayAmount=$MainRow["PayAmount"];	//结付总额
	$PayDate=$MainRow["PayDate"];		//结付日期
	$Payee=$MainRow["Payee"];			//结付凭证
	$Receipt=$MainRow["Receipt"];		//回执
	$Checksheet=$MainRow["Checksheet"];	//对帐单
	$Locks=$MainRow["Locks"];
	$PayDateStr=$Locks==0?$PayDate:"<input name='PayDate' type='text' id='Date' value='$PayDate' size='72' onfocus='new WdatePicker(this,null,false,\"whyGreen\")' title='必选项，结付日期' DataType='Date' format='ymd' Msg='日期不对或没选日期' readonly>";		//状态		决定是否可以修改结付日期
	$Remark=$MainRow["Remark"];			//结付备注
	}
$cashSymbol=$cashSymbol==""?"RMB":$cashSymbol;
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,20,Mid,$Mid,oldPayDate,$PayDate,cashSymbol,$cashSymbol,funFrom,$funFrom,From,$From,Estate,0,Pagination,$Pagination,Page,$Page,fromWebPage,$fromWebPage,PayAmount,$PayAmount";

//步骤4：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td width="100" align="right" scope="col">结付单Id</td>
            <td scope="col"><?php  echo $Mid?></td>
		</tr>
		<tr>
            <td align="right" scope="col">结付总额</td>
            <td scope="col"><?php  echo $PayAmount?> <?php  echo $cashSymbol?></td>
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
			<input name="Payee" type="file" id="Payee" size="75" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="3" Cel="1">
			</td>
		</tr>
            <?php 
			$ReceiptRow=4;
			$ChecksheetRow=5;
			if($Payee!=0){
				echo"<tr><td>&nbsp;</td><td><input name='oldPayee' type='checkbox' id='oldPayee' value='1'><LABEL for='oldPayee'>删除已传凭证</LABEL></td></tr>";
				$ReceiptRow++;
				$ChecksheetRow++;
				}
			?>
          <tr>
            <td align="right">回执图档</td>
            <td>
			<input name="Receipt" type="file" id="Receipt" size="75" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="<?php  echo $ReceiptRow?>" Cel="1">
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
			<input name="Checksheet" type="file" id="Checksheet" size="75" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="<?php  echo $ChecksheetRow?>" Cel="1">
			</td>
          </tr>
            <?php 
			if($Checksheet!=0){
				echo" <tr><td>&nbsp;</td><td><input name='oldChecksheet' type='checkbox' id='oldChecksheet' value='1'><LABEL for='oldChecksheet'>删除已传对帐单</LABEL></td></tr>";
				}
			?>
		<tr>
            <td align="right" valign="top">结付备注</td>
            <td ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php  echo $Remark?></textarea></td>
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