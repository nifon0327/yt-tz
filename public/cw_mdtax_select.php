<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 免抵退税明细查询");			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId";
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
                      <TD align="right">国税时间
                                    <input name="Field[]" type="hidden" id="Field[]" value="TaxDate">
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
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]" value="isDate">
                </TD>
                </TR>    
                
				 <TR>
                  <TD width="120" align="right">免抵退税发票号
                      <input name="Field[]" type="hidden" id="Field[]" value="TaxNo">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value== >=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="358"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>   
                
				 <TR>
                  <TD width="120" align="right">免抵退税金额
                      <input name="Field[]" type="hidden" id="Field[]" value="Taxamount">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE">包含</option>
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  
					</TD>
                  <TD width="358"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>    
                
                   
                 <TR>
                      <TD align="right">收到发票日期
                                    <input name="Field[]" type="hidden" id="Field[]" value="Taxgetdate">
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
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]" value="isDate">
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