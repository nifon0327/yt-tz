<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 出货文档附图查询");			//需处理
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
			  <TR>
                <TD><div align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                </div></TD>
                <TD><div align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                </div></TD>
                <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option selected  value="">全部</option>
                  <?php 
					 $checkC=mysql_query("SELECT M.CompanyId,C.Forshort FROM $DataIn.ch7_shippicture T 
					 LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=T.Mid
					 LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
					 GROUP BY M.CompanyId ORDER BY C.OrderBy",$link_id);
					 if($cRow=mysql_fetch_array($checkC)){
					 	do{
							$CompanyId=$cRow["CompanyId"];
							$Forshort=$cRow["Forshort"];
							echo"<option value='$CompanyId'>$Forshort</option>";
							}while ($cRow=mysql_fetch_array($checkC));
						}
					 ?>
                </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
			  <TR>
                <TD><div align="right">出货日期
                        <input name="Field[]" type="hidden" id="Field[]" value="Date">
                </div></TD>
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
                <TD><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                </TD>
		      </TR>
              <TBODY>
				<TR>
                  <TD width="115"><div align="right">出货流水号
                      <input name="Field[]" type="hidden" id="Field[]" value="Number">
                  </div></TD><TD><div align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>
                    </div></TD>
                  <TD width="343">
				    <INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<TR>
                  <TD><p align="right">Invoice编号
                      <input name="Field[]" type="hidden" id="Field[]" value="InvoiceNO">
                  </p></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
			    </TR>
                <TR>
                  <TD><p align="right">文档说明
                      <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </p>
                  </TD>
                  <TD><div align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>			
<TR>
                  <TD><p align="right">文档名称
                      <input name="Field[]" type="hidden" id="Field[]" value="Picture">
                  </p>
                  </TD>
                  <TD><div align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD><div align="right">更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">
                  </div></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="T">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD><div align="right">操 作 员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator">
                  </div></TD>
                  <TD><div align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </div></TD>
                  <TD>
				<select name=value[] id="value[]" style="width: 274px;">
				  <?php 
					//员工资料表
					$PD_Sql = "SELECT M.Operator,p.Name FROM $DataIn.ch7_shippicture M LEFT JOIN $DataPublic.staffmain P ON P.Number=M.Operator group by M.Operator order by M.Operator";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$Operator=$PD_Myrow["Operator"];
						$Name=$PD_Myrow["Name"];					
						echo "<option value='$Operator'>$Name</option>";
						} 
					?>
				</select>                  
				<input name="table[]" type="hidden" id="table[]" value="T">                  
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