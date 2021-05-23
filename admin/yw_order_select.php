<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 未出明细查询");			//需处理
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
						WHERE S.Estate>0 GROUP BY M.CompanyId ORDER BY M.CompanyId ,M.OrderDate desc",$link_id);
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
  <TD align="right">产品分类
    <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
  </TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <OPTION value== 
          selected>=</OPTION>
    <OPTION value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><select name="value[]" id="value[]" style="width: 274px;">
    <?php   
					echo"<option value='' selected>全部</option>";
					$result = mysql_query("SELECT T.Letter,T.TypeId,T.TypeName FROM 
					$DataIn.yw1_ordersheet Y,$DataIn.productdata P,$DataIn.producttype T
					WHERE Y.ProductId=P.ProductId AND P.TypeId=T.TypeId AND T.Estate='1' AND Y.Estate>0 GROUP BY P.TypeId ORDER BY T.Letter",$link_id);
					while ($myrow = mysql_fetch_array($result)){
						$Letter=$myrow["Letter"];
						$TypeId=$myrow["TypeId"];
						$TypeName=$myrow["TypeName"];
						echo "<option value='$TypeId'>$Letter-$TypeName</option>";
						} 
					?>
  </select>
      <input name="table[]" type="hidden" id="table[]" value="P">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
                  <TD align="right">产品ID
                    <input name="Field[]" type="hidden" id="Field[]" value="ProductId">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
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
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <OPTION value== 
          selected>=</OPTION>
    <OPTION value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><select name="value[]" id="value[]" style="width: 274px;">
  <option value="" selected>全部</option>
    <?php   
			$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Estate=1 ORDER BY Id",$link_id);
		          if($TypeRow = mysql_fetch_array($shipTypeResult)){
				  do{
				          if ($Ship==$TypeRow["Id"]){
					          echo "<option value='$TypeRow[Id]' selected>$TypeRow[Name]</option>";
				          }
				          else{
					           echo "<option value='$TypeRow[Id]'>$TypeRow[Name]</option>";
				          }
					           
					  } while($TypeRow = mysql_fetch_array($shipTypeResult));
			      }
					?>
  </select>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
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
                  </TR>
                <TR>
                  <TD align="right">PI交期
                      <input name="Field[]" type="hidden" id="Field[]" value="Leadtime">
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
  <input name="table[]" type="hidden" id="table[]" value="PI">
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
					WHERE S.Estate>0 group by M.Operator order by M.Operator desc",$link_id);
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
//步骤5：
include "../model/subprogram/select_model_b.php";
?>