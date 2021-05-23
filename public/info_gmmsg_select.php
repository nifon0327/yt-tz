<?php 
//电信-ZX
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 经理留言记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
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
              <TBODY>
<TR>
                  <TD width="119" align="right">留言日期
                      <input name="Field[]" type="hidden" id="Field[]" value="Date">
                  </TD>
                  <TD width="96" align="center">
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
  <input name="table[]" type="hidden" id="table[]" value="I">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
<TR>
  <TD align="right">留言内容
            <input name="Field[]" type="hidden" id="Field[]" value="Remark">
  </TD>
  <TD align="center">
      <SELECT name=fun[] id="fun[]" style="width: 60px;">
          <option value="LIKE" selected>包含</option>
          <OPTION value==>=</OPTION>
          <OPTION 
          value=!=>!=</OPTION>
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="5">
      <input name="table[]" type="hidden" id="table[]" value="I">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
                  <TD align="right">保留天数
                      <input name="Field[]" type="hidden" id="Field[]" value="Days">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="5">
                    <input name="table[]" type="hidden" id="table[]" value="I">
  <input name="types[]" type="hidden" id="types[]" value="isNum">
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