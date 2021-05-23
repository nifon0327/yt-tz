<?php 
//EWEN 2013-03-08 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非BOM申购记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,singel,$singel";
$tableMenuS=500;
$tableWidth=850;
echo $singel;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.nonbom6_cgsheet";
$SelectFrom=4;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="800" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="154"  align="right">非bom配件编号
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsId">
                  </TD>
                  <TD width="72" align="center"><select name="fun[]2" id="fun[]2" style="width: 60px;">
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
                  <TD width="560"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				  </TD>
                </TR>
                                <TR>
                  <TD  align="right">非bom配件名称
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsName">
                  </TD>
                  <TD width="72" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="560"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>
                                <tr>
                                  <td align="right">申购时间
                                    <input name="Field[]2" type="hidden" id="Field[]8" value="Date" /></td>
                                  <td align="center"><select name="fun[]8" id="fun[]14" style="width: 60px;">
                                    <option value="=" 
          selected="selected">=</option>
                                    <option value="&gt;">&gt;</option>
                                    <option 
          value="&gt;=">&gt;=</option>
                                    <option value="&lt;">&lt;</option>
                                    <option 
          value="&lt;=">&lt;=</option>
                                    <option value="!=">!=</option>
                                  </select></td>
                                  <td><input name="value[]2" class="textfield" id="value[]8" style="width:180px;" onfocus="WdatePicker()" readonly="readonly" />
                                    至
                                    <input name="DateArray[]2" class="textfield" id="DateArray[]2" style="width:180px;" onfocus="WdatePicker()" readonly="readonly" />
                                    <input name="table[]2" type="hidden" id="table[]8" value="A" />
                                    <input name="types[]2" type="hidden" id="types[]8" value="isDate" /></td>
                                </tr>
                                <tr>
                                  <td align="right">申购人
                                    <input name="Field[]2" type="hidden" id="Field[]7" value="Operator" /></td>
                                  <td align="center"><select name="fun[]8" id="fun[]13" style="width: 60px;">
                                    <option value="=" selected="selected">=</option>
                                    <option value="!=">!=</option>
                                  </select></td>
                                  <td><select name="value[]2" id="value[]7" style="width:380px;">
                                    <option value="" selected="selected">全部</option>
                                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
                                  </select>
                                    <input name="table[]2" type="hidden" id="table[]7" value="A" />
                                    <input name="types[]2" type="hidden" id="types[]7"
                value="isNum" /></td>
                                </tr>
                                <tr>
                                  <td align="right">申购状态
                                    <input name="Field[]2" type="hidden" id="Field[]6" value="Estate" /></td>
                                  <td align="center"><select name="fun[]8" id="fun[]12" style="width: 60px;">
                                    <option value="=">=</option>
                                  </select></td>
                                  <td><select name="value[]2" id="value[]6" style="width:380px;">
                                    <option selected="selected"  value="">全部</option>
                                    <option value="1">已审核</option>
                                    <option value="2">初审</option>
                                    <option value="3">终审</option>
                                    <option value="4">审核退回</option>
                                  </select>
                                    <input name="table[]2" type="hidden" id="table[]6" value="A" />
                                    <input name="types[]2" type="hidden" id="types[]6"
                value="isNum" /></td>
                                </tr>
                                <tr>
                                  <td  align="right">申购数量
                                    <input name="Field[]2" type="hidden" id="Field[]5" value="Qty" /></td>
                                  <td align="center"><select name="fun[]8" id="fun[]11" style="width: 60px;">
                                    <option value="=" 
          selected="selected">=</option>
                                    <option value="&gt;">&gt;</option>
                                    <option 
          value="&gt;=">&gt;=</option>
                                    <option value="&lt;">&lt;</option>
                                    <option 
          value="&lt;=">&lt;=</option>
                                    <option value="!=">!=</option>
                                  </select></td>
                                  <td><input name="value[]2" class="textfield" id="value[]5" style="width:380px;" />
                                    <input name="table[]2" type="hidden" id="table[]5" value="A" />
                                    <input name="types[]2" type="hidden" id="types[]5"
                value="isNum" /></td>
                                </tr>
                                <tr>
                                  <td  align="right">单价
                                    <input name="Field[]2" type="hidden" id="Field[]4" value="Price" /></td>
                                  <td align="center"><select name="fun[]8" id="fun[]10" style="width: 60px;">
                                    <option value="=" 
          selected="selected">=</option>
                                    <option value="&gt;">&gt;</option>
                                    <option 
          value="&gt;=">&gt;=</option>
                                    <option value="&lt;">&lt;</option>
                                    <option 
          value="&lt;=">&lt;=</option>
                                    <option value="!=">!=</option>
                                  </select></td>
                                  <td><input name="value[]2" class="textfield" id="value[]4" style="width:380px;" />
                                    <input name="table[]2" type="hidden" id="table[]4" value="A" />
                                    <input name="types[]2" type="hidden" id="types[]4"
                value="isNum" /></td>
                                </tr>
                                <TR>
                  <TD  align="right">非bom配件条码
                      <input name="Field[]" type="hidden" id="Field[]" value="BarCode">
                  </TD>
                  <TD width="72" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="560"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>
                                <TR>
                  <TD  align="right">非bom配件单位
                      <input name="Field[]" type="hidden" id="Field[]" value="Unit">
                  </TD>
                  <TD width="72" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="560"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>
                                <TR>
                  <TD  align="right">非bom配件在库
                      <input name="Field[]" type="hidden" id="Field[]" value="wStockQty">
                  </TD>
                  <TD width="72" align="center"><select name="fun[]4" id="fun[]4" style="width: 60px;">
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
                  <TD width="560"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
					</TD>
                </TR>
                             <TR>
                  <TD  align="right">非bom配件采购库存
                      <input name="Field[]" type="hidden" id="Field[]" value="oStockQty">
                  </TD>
                  <TD width="72" align="center"><select name="fun[]5" id="fun[]5" style="width: 60px;">
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
                  <TD width="560"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
					</TD>
                </TR>
                             <TR>
                  <TD  align="right">非bom配件最低库存
                      <input name="Field[]" type="hidden" id="Field[]" value="mStockQty">
                  </TD>
                  <TD width="72" align="center"><select name="fun[]3" id="fun[]3" style="width: 60px;">
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
                  <TD width="560"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
					</TD>
                </TR>
                <TR>
                  <TD align="right">非bom配件分类
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD align="center"><select name="fun[]6" id="fun[]6" style="width: 60px;">
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
                  <TD>
					<?php 
					include "../model/subselect/GoodType.php";
					?>
                  <input name="table[]" type="hidden" id="table[]" value="E">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
               </TD>
                </TR>
                 <TR>
                  <TD align="right">采购
                  <input name="Field[]" type="hidden" id="Field[]" value="BuyerId"></TD>
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
					$checkResult = mysql_query("SELECT A.BuyerId,B.Name FROM $DataPublic.nonbom3_buyer A 
						LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId
						ORDER BY B.Name",$link_id);
					while($checkRow = mysql_fetch_array($checkResult)){
						echo"<option value='$checkRow[BuyerId]'>$checkRow[Name]</option>";
						}
					?>
                     </select>
                  <input name="table[]" type="hidden" id="table[]" value="B">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
               </TD>
                </TR>
                <TR>
                  <TD align="right">供应商
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
					$checkResult = mysql_query("SELECT A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Forshort",$link_id);
					while($checkRow = mysql_fetch_array($checkResult)){
						$TempCompanyId=$checkRow["CompanyId"];
						$TempForshort=$checkRow["Forshort"];
						echo"<option value='$TempCompanyId'>$TempForshort</option>";
						}
					?>
                     </select>
                  <input name="table[]" type="hidden" id="table[]" value="A">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
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