<?php 
//EWEN 2013-02-28 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非bom配件报废记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.nonbom10_outsheet";
$SelectFrom=4;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="95" align="right">非bom配件编号
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsId">
                  </TD>
                  <TD width="81" align="center"><select name="fun[]2" id="fun[]2" style="width: 60px;">
                    <option value="=" selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]" value="isNum" />
				  </TD>
                </TR>
                                <TR>
                  <TD width="95" align="right">非bom配件名称
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsName">
                  </TD>
                  <TD width="81" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>
                                <TR>
                  <TD width="95" align="right">非bom配件条码
                      <input name="Field[]" type="hidden" id="Field[]" value="BarCode">
                  </TD>
                  <TD width="81" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]" value="isStr" />
					</TD>
                </TR>
                                <TR>
                  <TD width="95" align="right">非bom配件单位
                      <input name="Field[]" type="hidden" id="Field[]" value="Unit">
                  </TD>
                  <TD width="81" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]" value="isStr" />
					</TD>
                </TR>
                                <TR>
                  <TD width="95" align="right">非bom配件在库
                      <input name="Field[]" type="hidden" id="Field[]" value="wStockQty">
                  </TD>
                  <TD width="81" align="center"><select name="fun[]4" id="fun[]4" style="width: 60px;">
                    <option value="=" selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
					</TD>
                </TR>
                             <TR>
                  <TD width="95" align="right">非bom配件采购库存
                      <input name="Field[]" type="hidden" id="Field[]" value="oStockQty">
                  </TD>
                  <TD width="81" align="center"><select name="fun[]5" id="fun[]5" style="width: 60px;">
                    <option value="=" selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]" value="isNum" />
					</TD>
                </TR>
				<TR>
                  <TD width="95" align="right">非bom配件最低库存
                      <input name="Field[]" type="hidden" id="Field[]" value="mStockQty">
                  </TD>
                  <TD width="81" align="center"><select name="fun[]3" id="fun[]3" style="width: 60px;">
                    <option value="=" selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]" value="isNum" />
					</TD>
                </TR>
                <TR>
                  <TD align="right">非bom配件分类
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD align="center"><select name="fun[]6" id="fun[]6" style="width: 60px;">
                    <option value="=" selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD>
					<?php 
					include "../model/subselect/GoodType.php";
					?>
                  <input name="table[]" type="hidden" id="table[]" value="C">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
               </TD>
                </TR>
                <TR>
                  <TD align="right">默认供应商
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD>
                  <TD align="center"><select name="fun[]7" id="fun[]7" style="width: 60px;">
                    <option value="=" 
          selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option 
          value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option 
          value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width:380px;">
                  <option selected  value="">全部</option>
					<?php 
					$checkResult = mysql_query("SELECT A.CompanyId,A.Company FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Company",$link_id);
					while($checkRow = mysql_fetch_array($checkResult)){
						$TempCompanyId=$checkRow["CompanyId"];
						$TempCompany=$checkRow["Company"];
						echo"<option value='$TempCompanyId'>$TempCompany</option>";
						}
					?>
                     </select>
                  <input name="table[]" type="hidden" id="table[]" value="D">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
               </TD>
                </TR>
                                <TR>
                  <TD align="right">报废数量
                    <input name="Field[]" type="hidden" id="Field[]" value="Qty"></TD>
                  <TD align="center"><select name="fun[]7" id="fun[]7" style="width: 60px;">
                    <option value="=" 
          selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option 
          value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option 
          value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><input name="value[]" class="textfield" id="value[]" style="width:380px;" />
                    <input name="table[]" type="hidden" id="table[]" value="A">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
               </TD>
                </TR>
                                                <TR>
                  <TD align="right">报废备注
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD align="center"><select name="fun[]8" id="fun[]8" style="width: 60px;">
                    <option value="LIKE" selected="selected">包含</option>
                    <option value="=">=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><input name="value[]" class="textfield" id="value[]" style="width:380px;" />
                    <input name="table[]" type="hidden" id="table[]" value="A">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
               </TD>
                </TR>
                <TR>
                  <TD align="right">可用状态
                  <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
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
                  <TD><select name=value[] id="value[]" style="width:380px;">
                    <option selected  value="">全部</option>
                    <option value="1">可用</option>
                    <option value="2">未审核</option>
                    <option value="0">不可用</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">更新日期
                  <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px;" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px;" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="A">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width:380px;">
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
                  <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
                  <TD align="center">
				    <select name="fun[]" id="fun[]" style="width: 60px;">
			          <option value="=">=</option>
		            </select>		          </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width:380px;">
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