<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 未出明细查询");			//需处理
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
                      <TD align="right">报关日期
                                    <input name="Field[]" type="hidden" id="Field[]" value="DeclarationDate">
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
                  <TD width="120" align="right">报关单号
                      <input name="Field[]" type="hidden" id="Field[]" value="DeclarationNo">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE">包含</option>
                      <OPTION value== selected>=</OPTION>
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
                  <TD width="120" align="right">核销单号
                      <input name="Field[]" type="hidden" id="Field[]" value="CertificateNo">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE">包含</option>
                      <OPTION value== selected>=</OPTION>
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
                  <TD align="right">核实状态
                    <input name="Field[]" type="hidden" id="Field[]" value="CertificateEstate">
                  </TD>
                  <TD align="center">
				    <select name="fun[]" id="fun[]" style="width: 60px;">
			          <option value="=">=</option>
		            </select>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">已核实</option>
						<option value="1">未核实</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
					</TD>
                </TR>                  
                

				 <TR>
                  <TD width="120" align="right">出口发票
                      <input name="Field[]" type="hidden" id="Field[]" value="exportinvoiceNo">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE">包含</option>
                      <OPTION value== selected>=</OPTION>
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
                      <TD align="right">发票日期
                                    <input name="Field[]" type="hidden" id="Field[]" value="exportinvoiceDate">
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
                  <TD width="120" align="right">结汇凭证
                      <input name="Field[]" type="hidden" id="Field[]" value="BillNumber">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE">包含</option>
                      <OPTION value== selected>=</OPTION>
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
                  <TD align="right">操 作 员
                      <input name="Field[]" type="hidden" id="Field[]" value="Operator">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                      <?php 
					//员工资料表
					$StaffResult= mysql_query("SELECT M.Operator,C.Name 
					FROM $DataIn.yw1_ordermain M 
					LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
					LEFT JOIN $DataPublic.staffmain C ON M.Operator=C.Number 
					WHERE S.Estate>0 group by M.Operator order by M.Operator desc",$link_id);
					if($StaffRow = mysql_fetch_array($StaffResult)){
						do{
							$Operator=$StaffRow["Operator"];
							$Name=$StaffRow["Name"];
							echo"<option value='$Operator'>$Name</option>";
							}while($StaffRow = mysql_fetch_array($StaffResult));
						}
					?>
                        </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]"  value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">操作状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Locks">
                  </TD>
                  <TD align="center">
				    <select name="fun[]" id="fun[]" style="width: 60px;">
			          <option value="=">=</option>
		            </select>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="M">
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