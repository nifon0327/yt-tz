<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rksheet
$DataIn.ck1_rkmain
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 入库记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,chooseDate,$chooseDate";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.ck1_rksheet";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<p>&nbsp;</p>
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
				  <select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					$result = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter FROM $DataIn.ck1_rkmain M,$DataIn.trade_object P WHERE M.CompanyId=P.CompanyId GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
					echo "<option value='' selected>全部</option>";
					while ($myrow = mysql_fetch_array($result)){
						$Provider=$myrow["CompanyId"];
						$Forshort=$myrow["Forshort"];
						$Letter=$myrow["Letter"];
						$Forshort=$Letter.'-'.$Forshort;	
						echo"<option value='$Provider'>$Forshort</option>";
						}
					
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">
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
				  <select name=value[] id="value[]" style="width: 274px;">
                  <?php 
				$PD_Sql = "SELECT M.BuyerId,A.Name FROM $DataIn.ck1_rkmain M LEFT JOIN $DataPublic.staffmain A ON M.BuyerId=A.Number GROUP BY M.BuyerId ORDER BY M.BuyerId";
				$PD_Result = mysql_query($PD_Sql); 
				echo "<option value='' selected>全部</option>";
				while($PD_Myrow = mysql_fetch_array($PD_Result)){
					$PD_BuyerId=$PD_Myrow["BuyerId"];
					$PD_StuffCname=$PD_Myrow["Name"];					
						echo "<option value='$PD_BuyerId'>$PD_StuffCname</option>";
					} 
				
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<TR>
                  <TD width="104" align="right">配 件 ID 
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffId">
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
                  <TD width="366"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">需求单流水号
                      <input name="Field[]" type="hidden" id="Field[]" value="StockId">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="M">
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
				  <select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">                  
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