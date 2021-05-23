<?php 
//电信-ZX  2012-08-01
//步骤1  $DataIn.trade_object / $DataIn.yw3_pimodel 二合一已理锌板 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 生成报价单");//需处理
$nowWebPage =$funFrom."_toQuotation";	
$toWebPage  =$funFrom."_Quotationtopdf";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$Parameter="Id,$Id,funFrom,$funFrom,fromWebPage,$fromWebPage,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
//读取产品数据
/*
$Ids="";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
*/	
//检查PI文件名
//echo "Id is is :$Id";
	
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'><input name="Id" type="hidden" id="Id" value="<?php  echo $Id?>"></td>
	</tr>
    <!--
    <tr>
    	<td width="200" height="35" valign="middle" class='A0010' align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户：
      </td>
	    <td valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 337px;" dataType="Require"  msg="未选择或该客户无需PI">
			<
			$checkSql = "SELECT P.CompanyId,C.Forshort FROM $DataIn.trade_object C,$DataIn.yw3_pimodel P
			WHERE C.CompanyId=P.COmpanyId AND C.cSign=$Login_cSign AND C.Estate=1";
			$checkResult = mysql_query($checkSql);
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<option value='' selected>-Select-</option>";
				do{
					$CompanyId=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];
					echo "<option value='$CompanyId'>$Forshort</option>";
					}while($checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		  </select>
		</td>
    </tr>
    -->
    <tr>
    	<td height="43" valign="middle" class='A0010' align="right">To：</td>
	    <td valign="middle" class='A0001'><input name="To" type="text" id="To" size="60" dataType="Require" msg="没有填写To文件名"></td>
    </tr>
    <tr>
      <td height="47" valign="middle" class='A0010' align="right">Attn： </td>
      <td valign="middle" class='A0001'><input name="Attn" type="text" id="Attn" size="60" dataType="Require" msg="没有填写Attn"></td>
    </tr>
    <tr>
      <td height="47" valign="middle" class='A0010' align="right">PI No： </td>
      <td valign="middle" class='A0001'><input name="PINo" type="text" id="PINo" size="60" ></td>
    </tr>

    <tr>
      <td height="47" valign="middle" class='A0010' align="right">Address： </td>
      <td valign="middle" class='A0001'><input name="Address" type="text" id="Address" size="60" dataType="Require" msg="没有填写Incoterm"></td>
    </tr>

    <tr>
      <td height="47" valign="middle" class='A0010' align="right">Co.： </td>
      <td valign="middle" class='A0001'><input name="Co" type="text" id="Co" size="60" dataType="Require" msg="没有填写Co"></td>
    </tr>    
    
<tr align="center">
      <td height="47" colspan="2" valign="middle" class='A0010'>&nbsp;</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>