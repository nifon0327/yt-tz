<?php 
//步骤1 $DataIn.producttest/$DataIn.trade_object 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
//步骤3：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>				<TR>
                <TD width="119" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                </TD>
                <TD width="87" align="center">
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
                <TD width="352"><select name=value[] id="value[]" style="width: 274px;">
                  <option value="" selected>全部</option>
	                <?php 
					$sSql1 = mysql_query("SELECT D.CompanyId,C.Forshort 
					FROM $DataIn.development D 
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE D.Operator=$Login_P_Number
					GROUP BY D.CompanyId ORDER BY D.Id",$link_id);;
					while($sRow1 = mysql_fetch_array($sSql1)){
						$CompanyId=$sRow1["CompanyId"];
						$Forshort=$sRow1["Forshort"];					
						echo "<option value='$CompanyId'>$Forshort</option>";
						} 
					?>
                </select>
                  <input name="table[]" type="hidden" id="table[]" value="D">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD align="right"><p>项目编号
                      <input name="Field[]" type="hidden" id="Field[]" value="ItemId">
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
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>				<TR>
                  <TD width="119" align="right">项目名称
                    <input name="Field[]" type="hidden" id="Field[]" value="ItemName">
                  </TD>
                  <TD width="87" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="352"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="119" align="right">项目内容
                      <input name="Field[]" type="hidden" id="Field[]" value="Content">
                  </TD>
                  <TD width="87" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="352"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD align="right">登记日期
                      <input name="Field[]" type="hidden" id="Field[]" value="Date">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="D">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
           </TBODY>
	    </TABLE>
		</td>
	</tr>
</table>
<?php 
//步骤4：
include "../model/subprogram/s2_model_4.php";
?>