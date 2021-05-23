<?php 
//电信-zxq 2012-08-01
//步骤1 $DataPublic.msg3_notice 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 供应商提示查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.info4_cgmsg";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="101" align="right">日&nbsp;&nbsp;&nbsp;&nbsp;期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">                  </TD>
                  <TD width="114" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION 
          value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION 
          value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="N">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD align="right">提示内容
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
</TD>
                  <TD>
				  <INPUT name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="N">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<TR>
                  <TD align="right">供 应 商
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD>
                  <TD align="center"><select name=fun[] id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				    <option value="1">全部</option>
                    <?php 
					$checkSql = "SELECT P.CompanyId,P.Forshort,P.Letter FROM $DataIn.trade_object P,$DataIn.bps B WHERE B.CompanyId=P.CompanyId GROUP BY B.CompanyId ORDER BY P.Letter";
					$checkResult = mysql_query($checkSql); 
					while ( $checkRow = mysql_fetch_array($checkResult)){
						$CompanyId=$checkRow["CompanyId"];
						$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
						echo "<option value='$CompanyId'>$Forshort</option>";
						} 
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="N">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD><div align="right">操 作 员
                          <input name="Field[]" type="hidden" id="Field[]" value="Operator">
                  </div></TD>
                  <TD><div align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="N">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD>&nbsp;</TD>
                  <TD>&nbsp;</TD>
                  <TD>&nbsp;</TD>
                </TR>
              </TBODY>
	    </TABLE>
		</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>