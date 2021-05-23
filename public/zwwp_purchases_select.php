<?php 
//ewen 2012-12-16
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 总务申购记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.zwwp4_purchase";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
			  <TR>
                <TD align="right">申 购 人
                    <input name="Field[]" type="hidden" id="Field[]" value="Purchaser"></TD>
                <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select>
                </TD>
                <TD><select name=value[] id="value[]" style="width: 380px;">
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
                <TD align="right">申购日期
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
                <TD><INPUT name=value[] class=textfield id="value[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
		      </TR>
			  <TR>
                <TD align="right">物品类型<input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
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
                <TD>
                   <select name=value[] id="value[]" style="width: 380px;">
                    <option selected  value="">全部</option>
                    <?php 
						$TypeSql = mysql_query("SELECT * FROM $DataPublic.zwwp2_subtype WHERE Estate='1' ORDER BY Id",$link_id);
						while ($TypeRow = mysql_fetch_array($TypeSql)){
							$Id=$TypeRow["Id"];
							$TypeName=$TypeRow["TypeName"];
							echo"<option value='$Id'>$TypeName</option>";
							}
						?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
               	<TR>
                <TD align="right">物品名称<input name="Field[]" type="hidden" id="Field[]" value="GoodsName"></TD>
                <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
		      </TR>
			  <TR>
                <TD align="right">申购数量<input name="Field[]" type="hidden" id="Field[]" value="Qty"></TD>
                <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
              <TBODY>
                <TR>
                  <TD width="95" align="right">申购说明<input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD width="81" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">申购状态<input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
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
                  <TD><select name=value[] id="value[]" style="width: 380px;">
                    <option selected  value="">全部</option>
                    <option value="1">待申购</option>
                    <option value="2">待审核</option>
                    <option value="3">待采购</option>
                    <option value="0">已购回</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">&nbsp;</TD>
                  <TD align="center">&nbsp;</TD>
                  <TD>&nbsp;</TD>
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