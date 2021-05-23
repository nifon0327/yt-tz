<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 假日资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataPublic.kqholiday";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="89" align="right">假日名称
                  <input name="Field[]" type="hidden" id="Field[]" value="Name"></TD><TD width="93" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="376">
				    <INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="H">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD align="right">假日日期
            <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="H">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>			
<TR>
                  <TD align="right"><p>假日类型
                    <input name="Field[]" type="hidden" id="Field[]" value="Type">
                  </p>
            </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					   <option value="" selected>全部</option>
					  <option value="0">无薪假期</option>
					  <option value="1">有薪假期</option>
					  <option value="2">法定假期</option>
					  </select>
                    <input name="table[]" type="hidden" id="table[]" value="H">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>					               <TR>
                  <TD align="right">是否带薪
                  <input name="Field[]" type="hidden" id="Field[]" value="Sign"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                  </SELECT></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="1">带薪</option>
						<option value="0">不带薪</option>
						</select>
                    <input name="table[]" type="hidden" id="table[]" value="H">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD align="right">加班位率
            <input name="Field[]" type="hidden" id="Field[]" value="jbTimes"></TD>
                  <TD align="center">
					  <select name="fun[]" id="fun[]" style="width: 60px;">
						<option value="=">=</option>
					  </select>
		    </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="1">1倍</option>
						<option value="2">2倍</option>
						<option value="3">3倍</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="H">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>                <TR>
                  <TD align="right">操 作 员
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
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
                  <input name="table[]" type="hidden" id="table[]" value="H">                  
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