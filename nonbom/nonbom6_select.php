<?php 
//ewen 2013-03-22 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非BOM采购单查询");			//需处理
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
				if($fromWebPage==$funFrom."_cw"){
					if($Estate==0){
				?>
                  <TD align="right">结付编号
                          <input name="Field[]" type="hidden" id="Field[]" value="Id">
                  </TD>
                  <TD width="86" align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value==  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                <TD width="467"><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付日期
                          <input name="Field[]" type="hidden" id="Field[]" value="PayDate">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD width="467"><INPUT name=value[] class=textfield id="value[]" style="width:180px" onfocus="WdatePicker()" readonly>
                    至
                      <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px" onfocus="WdatePicker()" readonly>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付金额
                          <input name="Field[]" type="hidden" id="Field[]" value="PayAmount">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                <TD width="467"><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">预付金额
                    <input name="Field[]" type="hidden" id="Field[]8" value="djAmount"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]8" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]8" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]8" value="A">
                  <input name="types[]2" type="hidden" id="types[]8" value="isNum"></TD>
                </TR>
                <TR>
                  <TD align="right">结付凭证
                          <input name="Field[]" type="hidden" id="Field[]" value="Payee">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                <TD width="467"><select name=value[] id="value[]" style="width:380px">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付回执
                          <input name="Field[]" type="hidden" id="Field[]" value="Receipt">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                <TD width="467"><select name=value[] id="value[]" style="width:380px">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">对 帐 单
                          <input name="Field[]" type="hidden" id="Field[]" value="Checksheet">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                <TD width="467"><select name=value[] id="value[]" style="width:380px">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付备注
                          <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                <TD width="467"><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                <TR>
                  <TD colspan="3">&nbsp;</TD>
                </TR>
                <?php 
				}
				//财务查询的其他条件
			?>
            	<TR>
                  <TD align="right">请款日期
                          <input name="Field[]" type="hidden" id="Field[]" value="Date">                  </TD>
                  <TD align="center" valign="top">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="B">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">请款金额
                          <input name="Field[]" type="hidden" id="Field[]" value="Amount">                  </TD>
                  <TD align="center" valign="top">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD><input name="value[]" class="textfield" id="value[]" style="width:380px" />
                    <input name="table[]" type="hidden" id="table[]" value="B">
      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
               <?php
				}
				?>
				<TR>
                  <TD align="right">下单日期
                          <input name="Field[]" type="hidden" id="Field[]" value="Date">                  </TD>
                  <TD align="center" valign="top">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="E">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                			    <TR>
                			      <TD align="right">采购单号
                			        <input name="Field[]" type="hidden" id="Field[]9" value="PurchaseID"></TD>
                			      <TD align="center"><SELECT name=fun[] id="fun[]9" style="width: 60px;">
                			        <OPTION value== selected>=</OPTION>
                			        <OPTION value=">">&gt;</OPTION>
                			        <OPTION value=">=">&gt;=</OPTION>
                			        <OPTION value="<">&lt;</OPTION>
                			        <OPTION value="<=">&lt;=</OPTION>
                			        <OPTION value=!=>!=</OPTION>
              			        </SELECT></TD>
                			      <TD><input name="value[]" class="textfield" id="value[]" style="width:380px" />
                			        <input name="table[]" type="hidden" id="table[]9" value="E">
               			          <input name="types[]2" type="hidden" id="types[]9" value="isNum"></TD>
              			      </TR>
                <TR>
                  <TD align="right">采购
                    <input name="Field[]" type="hidden" id="Field[]" value="BuyerId">                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width:380px">
                      <option value="" selected>全部</option>
						<?php 
						$checkSql = "SELECT A.BuyerId,B.Name FROM $DataIn.nonbom6_cgmain A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId GROUP BY A.BuyerId ORDER BY B.Name";
						$checkResult = mysql_query($checkSql); 
						while($checkRow = mysql_fetch_array($checkResult)){
							$BuyerId=$checkRow["BuyerId"];
							$Name=$checkRow["Name"];
							echo "<option value='$checkRow[BuyerId]'>$checkRow[Name]</option>";
							}
						?>		 
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                
              <TR>
                  <TD align="right">申购人
                          <input name="Field[]" type="hidden" id="Field[]" value="Operator">                  </TD>
                  <TD align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width:380px">
				  	<option value="" selected>全部</option>
                    <?php 
					$CheckTb='nonbom6_cgsheet';
					include "../model/subprogram/select_model_stafflist.php";
					?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                               
                <TR>
                  <TD align="right">供应商
                      <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                      </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  
                  <TD><select name=value[] id="value[]" style="width:380px">
                      <option value="" selected>全部</option>
						<?php 
						$checkSql = "SELECT A.CompanyId,B.Forshort,B.Letter,C.Symbol 
						FROM $DataIn.nonbom11_qksheet A 
						LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=A.CompanyId	
						LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency 
						GROUP BY A.CompanyId ORDER BY B.Letter,B.CompanyId";
						$checkResult = mysql_query($checkSql); 
						while($checkRow = mysql_fetch_array($checkResult)){
							$CompanyId=$checkRow["CompanyId"];
							$Forshort=$checkRow["Forshort"];
							$Symbol=$checkRow["Symbol"];
							$Letter=$checkRow["Letter"];
							$Forshort="$Letter - ".$Forshort."";
							echo "<option value='$CompanyId'>$Forshort</option>";
							}
						?>		 
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                                <TR>
                  <TD width="125" align="right">采购备注
                          <input name="Field[]" type="hidden" id="Field[]" value="Remark">                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD width="467"><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                
  <?PHP
  if($fromWebPage!=$funFrom."_cw"){
  ?>              
                <TR>
                  <TD width="125" align="right">非BOM配件ID
                    <input name="Field[]" type="hidden" id="Field[]" value="GoodsId">                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD width="467"><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">配件名称
                    <input name="Field[]2" type="hidden" id="Field[]2" value="GoodsName"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                  <input name="types[]2" type="hidden" id="types[]2" value="isStr"></TD>
                </TR>
                <TR>
                  <TD align="right">配件条码
                    <input name="Field[]2" type="hidden" id="Field[]3" value="BarCode"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]3" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]3" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]3" value="D">
                  <input name="types[]2" type="hidden" id="types[]3" value="isStr"></TD>
                </TR>
                <TR>
                  <TD align="right">申购备注
                    <input name="Field[]2" type="hidden" id="Field[]4" value="Remark"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]4" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]4" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]4" value="F">
                  <input name="types[]2" type="hidden" id="types[]4" value="isStr"></TD>
                </TR>
                <TR>
                  <TD align="right">申购数量
                    <input name="Field[]" type="hidden" id="Field[]5" value="Qty"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]5" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]5" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]5" value="F">
                  <input name="types[]2" type="hidden" id="types[]5" value="isNum"></TD>
                </TR>
                <TR>
                  <TD align="right">单价
                    <input name="Field[]" type="hidden" id="Field[]6" value="Price"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]6" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]6" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]6" value="F">
                  <input name="types[]2" type="hidden" id="types[]6" value="isNum"></TD>
                </TR>

                <TR>
                  <TD align="right">记录状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width:380px">
						<option selected  value="">全部</option>
							<option value="1">未处理</option>
							<option value="2">请款中</option>
							<option value="3">请款通过</option>
							<option value="0">已结付</option>							
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">收货状态
                    <input name="Field[]" type="hidden" id="Field[]7" value="rkSign"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]7" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><select name=value[] id="value[]7" style="width:380px">
                    <option selected  value="">全部</option>
                    <option value="1">未收货</option>
                    <option value="2">部分收货</option>
                    <option value="0">已收货</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]7" value="F">
                  <input name="types[]2" type="hidden" id="types[]7" value="isNum"></TD>
                </TR>
                <TR>
                  <TD align="right">申购人
                          <input name="Field[]" type="hidden" id="Field[]" value="Operator">                  </TD>
                  <TD align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width:380px">
				  	<option value="" selected>全部</option>
                    <?php
					$CheckTb='nonbom6_cgsheet';
					include "../model/subprogram/select_model_stafflist.php";
					?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
               <?php
				}
			   ?>
              </TBODY>
      </TABLE></td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>