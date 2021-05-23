<?php 
//ewen 2013-03-22 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非bom配件入库记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,chooseDate,$chooseDate";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.nonbom7_insheet";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="104" align="right">供 应 商
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </TD>
                  <TD width="88" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <option value="!=">!=</option>
                    </SELECT>                  </TD>
                  <TD width="366">
				  <select name=value[] id="value[]" style="width: 380px;">
                    <?php 
					$result = mysql_query("SELECT A.CompanyId,B.Forshort FROM $DataIn.nonbom7_inmain A LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=A.CompanyId GROUP BY A.CompanyId ORDER BY B.Forshort",$link_id);
					echo "<option value='' selected>全部</option>";
					while ($myrow = mysql_fetch_array($result)){
						echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
						}
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="B">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<TR>
                  <TD width="104" align="right">采&nbsp;&nbsp;&nbsp;&nbsp;购
                    <input name="Field[]" type="hidden" id="Field[]" value="BuyerId">
                  </TD>
                  <TD width="88" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="366">
				  <select name=value[] id="value[]" style="width: 380px;">
                  <?php 
					$result = mysql_query("SELECT A.BuyerId,B.Name FROM $DataIn.nonbom7_inmain A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId GROUP BY A.BuyerId ORDER BY B.Name",$link_id);
					echo "<option value='' selected>全部</option>";
					while ($myrow = mysql_fetch_array($result)){
						echo"<option value='$myrow[BuyerId]'>$myrow[Name]</option>";
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<TR>
                  <TD width="104" align="right">配 件 ID 
                    <input name="Field[]" type="hidden" id="Field[]" value="GoodsId">
                  </TD>
                  <TD width="88" align="center">
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
                  <TD width="366"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="GoodsName">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">需求单流水号
                      <input name="Field[]" type="hidden" id="Field[]" value="cgId">
                  </TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD align="right">送货单号
                    <input name="Field[]" type="hidden" id="Field[]" value="BillNumber">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <tr>
                  <td align="right">入库数量
                    <input name="Field[]" type="hidden" id="Field[]" value="Qty" /></td>
                  <td align="center"><select name="fun[]" id="fun[]3" style="width: 60px;">
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
                  <td><input name="value[]" class="textfield" id="value[]" style="width: 380px;" />
                    <input name="table[]" type="hidden" id="table[]" value="A" />
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" /></td>
                </tr>
                <TR>
                  <TD align="right">入库日期
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
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="B">
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
                    </select>                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 380px;">
				  	<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="B">                  
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
		            </select>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 380px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="B">
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