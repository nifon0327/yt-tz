<?php 
//步骤1 $DataPublic.net_facilitytype 二合一已更新
//电信-joseph
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 固定资产分类查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataPublic.net_facilitytype";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
  	
			<p>&nbsp;</p>
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="89">分类I D
                    <input name="Field[]" type="hidden" id="Field[]" value="Id"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD><p>分类名称
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				               <TR>
                  <TD>可用状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION 
          value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION 
          value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="1">可用</option>
                    <option value="0">不可用</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="T">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD>操 作 员
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="T">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>锁定状态
                  <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
                  <TD>
					  <select name="fun[]" id="fun[]" style="width: 60px;">
						<option value="=">=</option>
					  </select>
				  </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="T">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
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