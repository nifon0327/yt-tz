<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw6_orderinmain
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 货款收入记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,cwSign,0";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
			  <TR>
                <TD align="right">收款日期
                        <input name="Field[]" type="hidden" id="Field[]" value="PayDate">
                </TD>
                <TD align="center">
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
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                </TD>
		      </TR>
              <TBODY>
				<TR>
                  <TD width="111" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </TD>
                  <TD width="104" align="center">
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
                  <TD width="343">
				  <select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option> 
					<?php 
					$TypeResult = mysql_query("SELECT M.CompanyId,C.Forshort FROM $DataIn.cw6_orderinmain M,$DataIn.trade_object C WHERE M.CompanyId=C.CompanyId GROUP BY M.CompanyId ORDER BY C.OrderBy DESC,C.CompanyId",$link_id);
					if($TypeRow = mysql_fetch_array($TypeResult)){
						do{
							$CompanyId=$TypeRow["CompanyId"];
							$Forshort=$TypeRow["Forshort"];
							echo"<option value='$CompanyId'>$Forshort</option>";
							}while($TypeRow = mysql_fetch_array($TypeResult));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>收款总额
                      <input name="Field[]" type="hidden" id="Field[]" value="PayAmount">
                  </p>
                  </TD>
                  <TD align="center">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>			
<TR>
                  <TD align="right"><p>手续费
                      <input name="Field[]" type="hidden" id="Field[]" value="Handingfee">
                  </p>
                  </TD>
                  <TD align="center">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
  <TD align="right"><p>货币符号
    <input name="Field[]" type="hidden" id="Field[]" value="Handingfee">
  </p></TD>
  <TD align="center">
      <SELECT name=fun[] id="fun[]" style="width: 60px;">
          <OPTION value== 
          selected>=</OPTION>
          <OPTION value=!=>!=</OPTION>
      </SELECT>  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
      <option value="" selected>全部</option>
      <?php 
					$Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
					if($Currency_Row = mysql_fetch_array($Currency_Result)){
						do{
							$Id=$Currency_Row["Id"];
							$Name=$Currency_Row["Name"];
							echo"<option value='$Id'>$Name</option>";
							}while ($Currency_Row = mysql_fetch_array($Currency_Result));
						}
					?>
    </select>
      <input name="table[]" type="hidden" id="table[]" value="D">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>					               
<TR>
            <TD align="right">TT 备注
                <input name="Field[]" type="hidden" id="Field[]" value="Remark">
            </TD>
            <TD align="center">
              <SELECT name=fun[] id="fun[]" style="width: 60px;">
                <option value="LIKE" selected>包含</option>
                <OPTION value==>=</OPTION>
                <OPTION 
          value=!=>!=</OPTION>
              </SELECT>            </TD>
            <TD><INPUT name=value[] class=textfield id="value[]" size=48>
              <input name="table[]" type="hidden" id="table[]" value="M">
              <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD><div align="right"></div></TD>
                  <TD><div align="center">
                  </div></TD>
                  <TD>&nbsp;
                  </TD>
                </TR>
                <TR>
                  <TD align="right">出货日期
                          <input name="Field[]" type="hidden" id="Field[]" value="Date">                  </TD>
                  <TD align="center">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="C">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">Invoice编号
                    <input name="Field[]" type="hidden" id="Field[]" value="InvoiceNO">                  </TD>
                  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>			        </TD>
                  <TD>
					  <INPUT name=value[] class=textfield id="value[]" size=48>
				    <input name="table[]" type="hidden" id="table[]" value="C">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
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