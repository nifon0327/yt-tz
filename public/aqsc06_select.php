<?php 
//
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 纸质试卷查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataPublic.aqsc06";
$SelectFrom=4;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="600" border=0 align="center">
			  <TR>
                <TD align="right">教程分类
                  <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                <TD align="center">
                  <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                </TD>
                <TD>
                  <?php 
					//分类表
					include "../model/subselect/aqsc06type.php";
					?>
                </select>
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
              <TBODY>
                <TR>
                  <TD width="108" align="right">教程名称
                      <input name="Field[]" type="hidden" id="Field[]" value="Caption">
                  
                  </TD>
                  <TD width="78" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="400"><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">附件名称
                      <input name="Field[]" type="hidden" id="Field[]" value="Attached">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
				<TR>
                  <TD align="right">更新日期
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
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="A">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 380px;">
                    <option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="A">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">锁定状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Locks">
                  </TD>
                  <TD align="center">
					  
					    <select name="fun[]" id="fun[]" style="width: 60px;">
					      <option value="=">=</option>
				        </select>
			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 380px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="A">
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