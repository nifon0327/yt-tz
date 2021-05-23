<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 已出订单查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
			<TR>
                  <TD width="120" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">                  </TD><TD width="82" align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== 
          selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD width="356"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <?php   
					$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
						FROM $DataIn.ch1_shipmain M
						LEFT JOIN $DataIn.ch1_shipsheet S ON M.Id=S.Mid 
						LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
						WHERE M.Estate='0' GROUP BY M.CompanyId ORDER BY C.CompanyId,C.OrderBy DESC",$link_id);
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
              <TD align="right">订单日期
                      <input name="Field[]" type="hidden" id="Field[]" value="OrderDate">              </TD>
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
                  </SELECT>              </TD>
              <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="YM">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
              </TD>
			  </TR>
				<TR>
                  <TD width="120" align="right">订 单 PO
                      <input name="Field[]" type="hidden" id="Field[]" value="OrderPO">                  </TD><TD width="82" align="center">
                        <SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <option value="LIKE" selected>包含</option>
                          <OPTION value==>=</OPTION>
                          <OPTION 
          value=!=>!=</OPTION>
                        </SELECT>
                  </TD>
                  <TD width="356"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="YS">
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
  <TD align="right">产品分类
    <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
  </TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
      <OPTION value==>=</OPTION>
      <OPTION 
          value=!=>!=</OPTION>
    </SELECT>
  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
    <option value="" selected>全部</option>
    <?php   
					$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName,T.Letter
						FROM $DataIn.ch1_shipsheet S
						LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
						LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
						WHERE T.Estate='1' GROUP BY T.TypeId ORDER BY T.Letter",$link_id);
					if ($TypeRow = mysql_fetch_array($TypeResult)){
						do{
							$TypeId=$TypeRow["TypeId"];
							$Letter=$TypeRow["Letter"];
							$TypeName=$TypeRow["TypeName"];
							echo "<option value='$TypeId'>$Letter-$TypeName</option>";
							}while($TypeRow = mysql_fetch_array($TypeResult));
						}
					?>
  </select>
      <input name="table[]" type="hidden" id="table[]" value="P">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>

				<TR>
                  <TD align="right">产品属性
                    <input name="Field[]" type="hidden" id="Field[]" value="buySign"></TD>
                  <TD><div align="center">
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
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="1">自购</option>
                    <option value="2">代购</option>
                    <option value="3">客供</option>
                     </select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>

<TR>
                  <TD width="120" align="right">产品名称
                    <input name="Field[]" type="hidden" id="Field[]" value="cName">                  </TD><TD width="82" align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD width="356"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				<TR>
                  <TD align="right">Product Code
                    <input name="Field[]" type="hidden" id="Field[]" value="eCode">                  </TD>
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
                  <TD align="right">订单数量
                    <input name="Field[]" type="hidden" id="Field[]" value="Qty">                  </TD>
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
                    <input name="Field[]" type="hidden" id="Field[]" value="Price">                  </TD>
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
                    <input name="Field[]" type="hidden" id="Field[]" value="PackRemark">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="YS">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">出货方式
                    <input name="Field[]" type="hidden" id="Field[]" value="ShipType">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="YS">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">                  <div align="right"></div></TD><TD><div align="center">                  </div></TD>
                  <TD>&nbsp;                                          </TD>
                </TR>
                <TR>
                  <TD align="right">出货日期
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD align="right">Invoice编号
                      <input name="Field[]" type="hidden" id="Field[]" value="InvoiceNO">                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
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