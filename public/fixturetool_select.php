<?php   
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 治工具资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,scType,$TypeId";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.fixturetool";
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
                  
		   <TBODY>
		   	<TR>
                <TD align="right">治工具ID
                  <input name="Field[]" type="hidden" id="Field[]" value="ToolsId">
                </TD>
                <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT>
                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum" />
                </TD>
		      </TR>
		      
              <TR>
                  <TD align="right">治工具名称
                    <input name="Field[]" type="hidden" id="Field[]" value="ToolsName">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                 </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isStr" />
                  </TD>
                </TR>
                
              <TR>
                  <TD align="right">治工具编码
                    <input name="Field[]" type="hidden" id="Field[]" value="ToolsCode">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
             </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                

	         <TR>
                <TD align="right">使用周期
                  <input name="Field[]" type="hidden" id="Field[]" value="UseTimes">
                </TD>
                <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value="=" selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT>
                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
    
                 <TR>
                  <TD align="right">说明
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
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