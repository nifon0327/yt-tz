<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 员工离职补助费用查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="692" border=0 align="center">
              <TBODY>
                <TR>
                  <?php 
				//如果是来自于财务查询
				if($fromWebPage==$funFrom."_cw" && $Estate==0){
				?>
                  <TD align="right">结付编号
                          <input name="Field[]" type="hidden" id="Field[]" value="Id">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value==  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付日期
                          <input name="Field[]" type="hidden" id="Field[]" value="PayDate">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
                    至
                      <INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付总额
                          <input name="Field[]" type="hidden" id="Field[]" value="PayAmount">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付凭证
                          <input name="Field[]" type="hidden" id="Field[]" value="Payee">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付回执
                          <input name="Field[]" type="hidden" id="Field[]" value="Receipt">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">对 帐 单
                          <input name="Field[]" type="hidden" id="Field[]" value="Checksheet">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付备注
                          <input name="Field[]" type="hidden" id="Field[]" value="Remark">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                <TR>
                  <TD colspan="3">&nbsp;</TD>
                </TR>
                <?php 
			}
			?>
                <TR>
                  <TD align="right">离职人员
                          <input name="Field[]" type="hidden" id="Field[]" value="Operator">
                  </TD>
                  <TD><div align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				  		<option value="" selected>全部</option>
                     	<?php 
					$outResult = mysql_query("SELECT M.Name,M.Number FROM $DataIn.staff_outsubsidysheet   S 
                    LEFT JOIN $DataPublic.staffmain  M   ON M.Number=S.Number
                     WHERE 1 GROUP BY S.Number order by S.Id",$link_id);
					if($outRow = mysql_fetch_array($outResult)){
						do{
							echo"<option value='$outRow[Number]'>$outRow[Name]</option>";
							}while ($outRow = mysql_fetch_array($outResult));
						}
						?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right" valign="top">请款日期
                          <input name="Field[]" type="hidden" id="Field[]" value="Date">
                  </TD>
                  <TD valign="top"><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD width="125" align="right">补助金额
                      <input name="Field[]" type="hidden" id="Field[]" value="Amount">
                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="40">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">货币符号
                      <input name="Field[]" type="hidden" id="Field[]" value="Currency">
                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option>
					<?php 
					$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
					if($cRow = mysql_fetch_array($cResult)){
						do{
							echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
							}while ($cRow = mysql_fetch_array($cResult));
						}
					?>
                  </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD width="125" align="right">请款说明
                          <input name="Field[]" type="hidden" id="Field[]" value="Content">
                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="40">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">单&nbsp;&nbsp;&nbsp;&nbsp;据
                    <input name="Field[]" type="hidden" id="Field[]" value="Bill">
                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <option value="1">有单据</option>
                    <option value="0">无单据</option>
                     </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">状&nbsp;&nbsp;&nbsp;&nbsp;态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                      <?php 
						if($fromWebPage==$funFrom."_cw"){
							if($Estate==0){
								echo"<option value='0'>已结付</option>";
								$EstatePass=" WHERE H.Estate=0";
								}
							else{
								echo"<option value='3'>未结付</option>";
								$EstatePass=" WHERE H.Estate=3";
								}
							}
						else{
							$EstatePass="";
							echo"<option selected  value=''>全部</option>
							<option value='1'>未处理</option>
							<option value='2'>请款中</option>
							<option value='3'>请款通过</option>
							<option value='0'>已结付</option>";
							}
						?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
				<?php 
				if($fromWebPage==$funFrom."_cw" || $fromWebPage==$funFrom."_m"){				
				?>
                <TR>
                  <TD align="right">请 款 人
                          <input name="Field[]" type="hidden" id="Field[]" value="Operator">
                  </TD>
                  <TD><div align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				  		<option value="" selected>全部</option>
                     	<?php 
					 	 $CheckTb="$DataIn.staff_outsubsidysheet";
						include "../model/subprogram/select_model_stafflist.php";
						?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
				<?php 
				}?>
              </TBODY>
            </TABLE></td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>