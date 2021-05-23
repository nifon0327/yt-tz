<?php 
//电信-zxq 2012-08-01
//步骤1 $DataPublic.msg3_notice 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新加班通知");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$upSql=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.info4_cgmsg WHERE Id='$Id'",$link_id));
$Date=$upSql["Date"];
$Remark=$upSql["Remark"];
$CompanyId=$upSql["CompanyId"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="154" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="120" height="34" align="right" class='A0010'>日&nbsp;&nbsp;&nbsp;&nbsp;期: </td>
    <td class='A0001'><input name="Date" type="text" id="Date" onfocus="WdatePicker()" value="<?php  echo $Date?>" size="78" maxlength="10" readonly  dataType="Date" format="ymd" msg="格式不对或未选择"></td>
  </tr>
  <tr>
            <td align="right" class='A0010'>供应商</td>
            <td class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 420px;">
            <?php 
            echo "<option value='1' selected>全部</option>";
			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign=$Login_cSign or cSign=0) ANDEstate='1' order by Letter";
			$checkResult = mysql_query($checkSql); 
			while ( $checkRow = mysql_fetch_array($checkResult)){
				$theCompanyId=$checkRow["CompanyId"];
				$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
				if($CompanyId==$theCompanyId){
					echo "<option value='$theCompanyId' selected>$Forshort</option>";
					}
				else{
					echo "<option value='$theCompanyId'>$Forshort</option>";
					}
				} 
			?>
            </select>
			</td></tr>
    <tr>
		<td align="right" valign="top" class='A0010'>提示内容:</td>
	  <td class='A0001'><textarea name="Remark" cols="50" rows="8" id="Remark" datatype="Require" msg="未填写"><?php  echo $Remark?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>