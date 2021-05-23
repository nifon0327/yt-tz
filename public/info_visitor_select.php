<?
/*独立已更新
$DataIn.producttype
$DataIn.staffmain
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 来访登记查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?=$tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
			<TR>
                  <TD width="95">来访日期：
                    <input name="Field[]" type="hidden" id="Field[]" value="ComeDate"></TD>
                  <TD>
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
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="I">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>

            </TR>
                <TR>
                  <TD><p>来访单位：
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
                  </p>
                  </TD>
                  <TD>
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				  <TR>
                  <TD >客人姓名：
                    <input name="Field[]" type="hidden" id="Field[]" value="GuestName"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="agenda_guest">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
                </TR>
                <TR>
                  <TD>来访分类
                      <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
                  </TD>
                  <TD ><select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 235px;">
                      <option value="" selected>全部</option>
					  <?php 
					 $type_Result = mysql_query("SELECT C.Id,C.Name AS TypeName FROM $DataPublic.come_type C WHERE C.Estate=1",$link_id);
						if($typeRow = mysql_fetch_array($type_Result)) {
							do{			
								$TypeId=$typeRow["Id"];
								$TypeName=$typeRow["TypeName"];
								echo"<option value='$TypeId'>$TypeName</option>";				
								}while($typeRow = mysql_fetch_array($type_Result));
							}
					  ?>
                      </select>
                      <input name="table[]" type="hidden" id="table[]" value="I">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD >来访说明：
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
                </TR>
                <TR>
                  <TD>来访状态：
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  <TD>
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 234px;">
                    <option selected  value="">全部</option>
                    <option value="1">未到访</option>
                    <option value="2">来访中</option>
                    <option value="0">已来访</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>操作日期：
                    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD>
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
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="I">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD>操&nbsp;&nbsp;作&nbsp;&nbsp;员：
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD>
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </div></TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 234px;">
				  	<option value="" selected>全部</option>
                    <?
					$CheckTb="$DataPublic.come_data";
					include "subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="I">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
               </TBODY>
	    </TABLE>
		</td>
	</tr>
</table>
<?
//步骤5：
include "../model/subprogram/select_model_b.php";
?>