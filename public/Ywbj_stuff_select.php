<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 报价配件查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,StuffType,$StuffType,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.ywbj_stuffdata";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
			  <TABLE width="600" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="89">配件ID 
                    <input name="Field[]" type="hidden" id="Field[]" value="Id"></TD>
                  <TD width="126" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="316"><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="Name">
                  </p>
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD valign="top">参考价格
                    <input name="Field[]" type="hidden" id="Field[]" value="Price"></TD>
                  <TD align="center" valign="top"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD valign="top">更新日期
                  <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD align="center" valign="top"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isDate" />
</TD>
                </TR>
                <TR>
                  <TD>操作员
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
				  <input name="table[]" type="hidden" id="table[]" value="S">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				  </TD>
                </TR>
              </TBODY>
	    </TABLE>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>