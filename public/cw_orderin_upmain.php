<?php 
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.cw6_orderinmain
$DataIn.cw6_advancesreceived
二合一已更新
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.cw6_orderinmain";
ChangeWtitle("$SubCompany 更新收款资料");
//include "subprogram/upmain_model.php";
$fromWebPage=$funFrom."_cw";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT M.BankId,M.PreAmount,M.PayAmount,M.Handingfee,M.Remark,M.PayDate,M.Locks,M.Operator,C.Forshort,M.CompanyId
FROM $upDataMain M,$DataIn.trade_object C 
WHERE M.Id='$Mid' AND C.CompanyId=M.CompanyId LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {	
	$BankId=$MainRow["BankId"];	//结付银行
	$Forshort=$MainRow["Forshort"];
	$PreAmount=$MainRow["PreAmount"];
	$fkAmount=$MainRow["PayAmount"];
	$PayAmount=$PreAmount+$fkAmount;
	$CompanyId=$MainRow["CompanyId"];
	$PayAmount=sprintf("%.2f",$PayAmount);
	$Handingfee=$MainRow["Handingfee"];
	$Remark=$MainRow["Remark"];
	$Locks=$MainRow["Locks"];
	$PayDate=$MainRow["PayDate"];
	$Operator=$MainRow["Operator"];
	$HandingfeeSTR=$Locks==0?$Handingfee:"<input name='Handingfee' type='text' id='Handingfee' value='$Handingfee' size='72' DataType='Currency' Msg='格式不对'>";
	$PayDateStr=$Locks==0?$PayDate:"<input name='PayDate' type='text' id='PayDate' value='$PayDate' size='72' onfocus='new WdatePicker(this,null,false,\"whyGreen\")' title='必选项，结付日期' DataType='Date' format='ymd' Msg='日期不对或没选日期' readonly>";		//状态		决定是否可以修改结付日期
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,200,Mid,$Mid,oldPayDate,$PayDate,cashSymbol,$cashSymbol,funFrom,$funFrom,From,$From,cwSign,0,Pagination,$Pagination,Page,$Page,fromWebPage,$fromWebPage,PayAmount,$PayAmount";

//步骤4：//需处理
?>
<script>
function deleteItem(djId,rowIndex){
	var message=confirm("确定要取消此结付单的预收项目吗?");
	if (message==true){
		myurl="cw_orderin_updated.php?ActionId=916&djId="+djId;
		retCode=openUrl(myurl);
		if (retCode!=-2){
			document.form1.submit();
			}
		else{
			alert("取消失败！");return false;
			}

		}
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td scope="col" align="right" width="60px"><input name="fkAmount" type="hidden" id="fkAmount" value="<?php  echo $fkAmount?>">
            客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
            <td scope="col"><?php  echo $Forshort?></td>
          </tr>
          <tr>
            <td scope="col" align="right">预收金额</td>
            <td scope="col"><?php  echo $PreAmount?></td>
          </tr>
		<tr>
          <td scope="col" align="right">收款总额</td>
          <td scope="col"><?php  echo $PayAmount?></td>
		  </tr>
		<tr>
            <td scope="col" align="right">手 续 费</td>
            <td scope="col">
			<?php  echo $HandingfeeSTR?></td>
		</tr>
		<tr>
            <td scope="col" align="right">收款日期</td>
            <td scope="col"><?php  echo $PayDateStr?></td>
		</tr>
		<tr>
            <td align="right" scope="col">结付银行</td>
            <td scope="col"><?php 
			include "../model/selectbank2.php";
			?></td>
		</tr>
		<tr>
            <td align="right" valign="top">TT 备注</td>
            <td ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php  echo $Remark?></textarea></td>
          </tr>
          <?php 
          $pdf_file="../download/cwjzpz/" . "Z".$Mid.".pdf";
	   if (file_exists($pdf_file)){
              $pdfFlag="<span style='color:blue;'>(已上传)</span>"; 
           }else{
              $pdfFlag="<span style='color:red;'>(未上传)</span>";  
           }
          ?>
           <tr>
               <td align="center">进帐凭证</td>
	      <td><input name="Attached" type="file" id="Attached"  style="width:450px;" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" Row="6" Cel="1"><?php  echo $pdfFlag;?></td>
  	   </tr>
		<?php 
		if($Locks==0){
			echo"<tr><td valign='top'>&nbsp;</td><td >";
				if($Keys & mLOCK){
					echo"<input name='Locks' type='checkbox' id='Locks' value='1'><LABEL for='Locks'>主结付单解锁</LABEL>";
					}
				echo" <span class='redB'>(主收款单处于锁定状态，不允许修改收款日期及手续费)</span>";
			echo"</td></tr>";
			}
		//检查是否有预收款
		$CheckSql=mysql_query("SELECT * FROM $DataIn.cw6_advancesreceived WHERE (Mid=0 OR Mid='$Mid') AND CompanyId=$CompanyId ORDER BY Mid DESC",$link_id);
		if($CheckRow=mysql_fetch_array($CheckSql)){
			echo"<tr><td align='right' valign='top'>预 收 款</td><td>
			<table cellpadding='0' cellspacing='0' width='100%'><tr class=''>
			<td height='20' class='A1111' width='40'>&nbsp;</td>
			<td align='center' class='A1101' width='40'>序号</td>
			<td align='center' class='A1101' width='60'>预收金额</td>
			<td align='center' class='A1101'>预收说明</td>
			<td align='center' class='A1101' width='70'>收款日期</td></tr>
			";
			$i=1;
			do{
				$djId=$CheckRow["Id"];
				$TempMid=$CheckRow["Mid"];
				$TempAmount=$CheckRow["Amount"];
				$TempRemark=$CheckRow["Remark"];
				$TempDate=$CheckRow["PayDate"];
				if($TempMid>0){
					$ActionS="<a href='#' onclick='deleteItem($djId,this.parentNode.parentNode.rowIndex)' title='取消预收金额'>×</a>";
					}
				else{
					$ActionS="<input name='checkdj[]' type='checkbox' id='checkdj$d' value='$djId'>";
					}
				echo"<tr>
				<td align='center' class='A0111' height='20'>$ActionS</td>
				<td align='center' class='A0101'>$i</td>
				<td align='center' class='A0101'>$TempAmount</td>
				<td class='A0101'>$TempRemark</td>
				<td align='center' class='A0101'>$TempDate</td>
				</tr>";
				$i++;
				}while ($CheckRow=mysql_fetch_array($CheckSql));
			echo"</table></td></tr>";
			}
		?>
	</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>