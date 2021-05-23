<?php 
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
$Parameter.=",Bid,$Bid,Jid,$Jid";
if($Bid==1){
	$OrderSign=" AND S.Estate>0";
	}
else{
	$OrderSign=" AND S.Estate=0";
	}
//步骤3：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
			<TR>
                  <TD width="120" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </TD>
                  <TD width="80" align="center">
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
                  <TD width="358"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <?php 
					$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
						FROM $DataIn.yw1_ordermain M 
						LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
						LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
						LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
						WHERE 1 $OrderSign 
						 AND A.TypeId='$Jid' GROUP BY M.CompanyId order by M.CompanyId ,M.OrderDate desc",$link_id);
					if ($ClientRow = mysql_fetch_array($ClientResult)){
						do{
							$ClientValue=$ClientRow["CompanyId"];
							$Forshort=$ClientRow["Forshort"];
							if($CompanyId==$ClientValue){
								echo"<option value='$ClientValue' selected>$Forshort</option>";
								}
							else{
								echo"<option value='$ClientValue'>$Forshort</option>";
								}
							}while($ClientRow = mysql_fetch_array($ClientResult));
						}
					?>
                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
			</TD>
                </TR>
				<TR>
                  <TD width="120" align="right">订 单 PO
                      <input name="Field[]" type="hidden" id="Field[]" value="OrderPO">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE">包含</option>
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="358"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD align="right"><p>订单流水号
                      <input name="Field[]" type="hidden" id="Field[]" value="POrderId">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE">包含</option>
                        <OPTION value== selected>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD width="120" align="right">产品名称
                    <input name="Field[]" type="hidden" id="Field[]" value="cName">
                  </TD>
                  <TD width="80" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="358"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				<TR>
                  <TD align="right">Product Code
                    <input name="Field[]" type="hidden" id="Field[]" value="eCode">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">下单日期
                    <input name="Field[]" type="hidden" id="Field[]" value="OrderDate">
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
                  <TD align="right">订单数量
                    <input name="Field[]" type="hidden" id="Field[]" value="Qty">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">售&nbsp;&nbsp;&nbsp;&nbsp;价
                    <input name="Field[]" type="hidden" id="Field[]" value="Price">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">包装说明
                    <input name="Field[]" type="hidden" id="Field[]" value="PackRemark">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">出货方式
                    <input name="Field[]" type="hidden" id="Field[]" value="ShipType">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">预出货日期
                      <input name="Field[]" type="hidden" id="Field[]" value="DeliveryDate">
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
  <input name="table[]" type="hidden" id="table[]" value="S">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
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
					WHERE 1 $OrderSign group by M.Operator order by M.Operator desc",$link_id);
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
					  <input name="table[]" type="hidden" id="table[]" value="S">
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
include "../model/subprogram/s2_model_4.php";
?>
