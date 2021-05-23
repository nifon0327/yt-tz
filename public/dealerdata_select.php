<?php 
//步骤1电信-yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 经销商或其它公司");			//需处理
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
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="89" align="right">公 司 ID<input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD>
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
                      </SELECT>                    </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="F">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">公司全称<input name="Field[]" type="hidden" id="Field[]" value="Company"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">公司简称<input name="Field[]" type="hidden" id="Field[]" value="Forshort"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="F">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<TR>
                  <TD align="right">所在地区<input name="Field[]" type="hidden" id="Field[]" value="Area"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<TR>
                  <TD align="right">公司电话<input name="Field[]" type="hidden" id="Field[]" value="Tel"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<TR>
                  <TD align="right">公司传真<input name="Field[]" type="hidden" id="Field[]" value="Fax"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<TR>
                  <TD align="right">通信地址<input name="Field[]" type="hidden" id="Field[]" value="Address"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<TR>
                  <TD align="right">结付货币<input name="Field[]" type="hidden" id="Field[]" value="Currency"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<?php 
					$cSql = "SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id";
					$cResult = mysql_query($cSql); 
					echo "<option value='' selected>全部</option>";
					while ( $cMyrow = mysql_fetch_array($cResult)){						
						$Name=$cMyrow["Name"];
						$Id=$cMyrow["Id"];				
						echo "<option value='$Id'>$Name</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="F">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<TR>
                  <TD align="right">银行帐号<input name="Field[]" type="hidden" id="Field[]" value="Bank"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<TR>
                  <TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注<input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="49">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">状&nbsp;&nbsp;&nbsp;&nbsp;态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="1">可用</option>
                    <option value="0">禁用</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="F">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">                  </TD>
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
                  <TD>
				  <INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="F">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
				</TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator">                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
					<?php 
					$PD_Sql = "SELECT F.Operator,S.Name 
					FROM $DataPublic.dealerdata F 
					LEFT JOIN $DataPublic.staffmain S ON S.Number=F.Operator 
					GROUP BY F.Operator ORDER BY F.Operator";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$Operator=$PD_Myrow["Operator"];
						$Name=$PD_Myrow["Name"];					
						echo "<option value='$Operator'>$Name</option>";
						} 
					?>		 
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="F">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">操作状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Locks">                  </TD>
                  <TD align="center">
				    <select name="fun[]" id="fun[]" style="width: 60px;">
			          <option value="=" selected>=</option>
		            </select>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="F">
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