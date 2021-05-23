<?php
//电信-zxq 2012-08-01
/*
$DataPublic.freightdata
$DataIn.ch3_forward
$DataIn.ch1_shipmain
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany Forward杂费查询");			//需处理
$nowWebPage =$funFrom."_select";
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,TypeId,$TypeId";
$tableMenuS=500;
$tableWidth=850;
if($TypeId==1)$TempNumber="InvoiceNO";
else $TempNumber="DeliveryNumber";
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
                  <?php
				//如果是来自于财务查询
				if($fromWebPage==$funFrom."_cw" && $Estate==0){
				?>
                  <TD><div align="right">结付编号
                          <input name="Field[]" type="hidden" id="Field[]" value="Id">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value==  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付日期
                          <input name="Field[]" type="hidden" id="Field[]" value="PayDate">
                  </div></TD>
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
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
                    至
                      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付总额
                          <input name="Field[]" type="hidden" id="Field[]" value="PayAmount">
                  </div></TD>
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
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付凭证
                          <input name="Field[]" type="hidden" id="Field[]" value="Payee">
                  </div></TD>
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
                  <TD><div align="right">结付回执
                          <input name="Field[]" type="hidden" id="Field[]" value="Receipt">
                  </div></TD>
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
                  <TD><div align="right">对 帐 单
                          <input name="Field[]" type="hidden" id="Field[]" value="Checksheet">
                  </div></TD>
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
                  <TD><div align="right">结付备注
                          <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=48>
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
                  <TD width="102" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </TD>
                  <TD width="87" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value==
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="369">
				  <select name=value[] id="value[]" style="width: 274px;">
                    <?php
					$cSql = mysql_query("SELECT 
					M.CompanyId,C.Forshort 
					FROM $DataIn.ch3_forward F
					LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=F.chId
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 					
					GROUP BY M.CompanyId ORDER BY C.Estate DESC,C.OrderBy DESC",$link_id);
					echo "<option value='' selected>全部</option>";
					if($cRow = mysql_fetch_array($cSql)){
						do{
							$CompanyId=$cRow["CompanyId"];
							$Forshort=$cRow["Forshort"];
							echo "<option value='$CompanyId'>$Forshort</option>";
							}while ($cRow = mysql_fetch_array($cSql));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<TR>
                  <TD align="right">出货日期
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
			    </TR>
                <TR>
                  <TD align="right"><p>出货单号
                    <input name="Field[]" type="hidden" id="Field[]" value="Number">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>Invoice编号
                      <input name="Field[]" type="hidden" id="Field[]" value="<?php  echo $TempNumber?>">
                  </p></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD><p align="right">&nbsp;                  </p></TD>
                  <TD align="center">                  <div align="center"></div></TD>
                  <TD>&nbsp;                                          </TD>
                </TR>
<TR>
                  <TD align="right"><p>Forward公司
                      <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php
					$cSql = mysql_query("SELECT F.CompanyId,D.Forshort 
					FROM $DataIn.ch3_forward F					
					LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 					
					GROUP BY F.CompanyId ORDER BY D.Estate DESC",$link_id);
					echo "<option value='' selected>全部</option>";
					if($cRow = mysql_fetch_array($cSql)){
						do{
							$CompanyId=$cRow["CompanyId"];
							$Forshort=$cRow["Forshort"];
							echo "<option value='$CompanyId'>$Forshort</option>";
							}while ($cRow = mysql_fetch_array($cSql));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="F">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
  <TD align="right">入仓号
      <input name="Field[]" type="hidden" id="Field[]" value="HoldNO">  </TD>
  <TD align="center">
      <SELECT name=fun[] id="fun[]" style="width: 60px;">
            <OPTION value== selected>=</OPTION>
            <OPTION
          value=!=>!=</OPTION>
      </SELECT>    </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="F">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right">Forward Invoice
      <input name="Field[]" type="hidden" id="Field[]" value="ForwardNO">  </TD>
  <TD align="center">
      <SELECT name=fun[] id="fun[]" style="width: 60px;">
            <OPTION value== selected>=</OPTION>
            <OPTION
          value=!=>!=</OPTION>
      </SELECT>    </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="F">
      <input name="types[]" type="hidden" id="types[]" value="isStr" />
  </TD>
</TR>
<TR>
            <TD align="right">件数
              <input name="Field[]" type="hidden" id="Field[]" value="BoxQty">            </TD>
            <TD align="center">
              <SELECT name=fun[] id="fun[]" style="width: 60px;">
                <OPTION value== selected>=</OPTION>
                <OPTION
          value=!=>!=</OPTION>
              </SELECT>            </TD>
            <TD><INPUT name=value[] class=textfield id="value[]" size="48">
              <input name="table[]" type="hidden" id="table[]" value="F">
              <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">研砼称重
                      <input name="Field[]" type="hidden" id="Field[]" value="mcWG">                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <INPUT name=value[] class=textfield id="value[]" size="48">
                  <input name="table[]" type="hidden" id="table[]" value="F">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">上海称重
                      <input name="Field[]" type="hidden" id="Field[]" value="forwardWG">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">金额
                      <input name="Field[]" type="hidden" id="Field[]" value="Amount">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">发票日期
                      <input name="Field[]" type="hidden" id="Field[]" value="InvoiceDate">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><input name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
                    至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="M">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD align="right">ETD/ETA
                      <input name="Field[]" type="hidden" id="Field[]" value="ETD">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">备注
                      <input name="Field[]" type="hidden" id="Field[]" value="Remark">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付状态
                      <input name="Field[]" type="hidden" id="Field[]" value="Estate">                  </TD>
                  <TD align="center">
				        <SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION
          value=!=>!=</OPTION>
                    </SELECT>		            </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="1">未请款</option>
						<option value="2">请款中</option>
						<option value="3">准备结付</option>
						<option value="0">已结付</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">费用来源
                      <input name="Field[]" type="hidden" id="Field[]" value="PayType">                  </TD>
                  <TD align="center">
				        <SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION
          value=!=>!=</OPTION>
                    </SELECT>		            </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">自付</option>
						<option value="1">代付</option>
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