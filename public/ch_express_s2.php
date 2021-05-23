<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
$Parameter.=",Bid,$Bid";
//步骤3：需处理
//$CheckTb="$DataIn.stuffdata";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
		  <TABLE width="600" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="86" align="right">寄件人
                    <input name="Field[]" type="hidden" id="Field[]" value="Name"></TD>
                  <TD width="86" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  </TD>
                  <TD width="414"><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" /></TD>
                </TR>
				<TR>
                  <TD align="right"><p>寄件日期
                      <input name="Field[]" type="hidden" id="Field[]" value="SendDate">
                  </p>
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48 onfocus="WdatePicker()" readonly>
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isDate" />
</TD>
                </TR>
				<TR>
                  <TD align="right"><p>快递公司
                      <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </p></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                                      </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//快递公司
					$PD_Sql = "SELECT E.CompanyId,F.Forshort 
					FROM $DataPublic.my3_express E 
					LEFT JOIN $DataPublic.freightdata F 
					ON F.CompanyId=E.CompanyId 
					WHERE E.Estate=0 GROUP BY E.CompanyId 
					ORDER BY E.CompanyId";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$CompanyId=$PD_Myrow["CompanyId"];
						$Forshort=$PD_Myrow["Forshort"];					
						echo "<option value='$CompanyId'>$Forshort</option>";
						} 
					?>
                  </select>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
				<TR>
                  <TD align="right">快递单号
                    <input name="Field[]" type="hidden" id="Field[]" value="BillNumber"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD>
				  <INPUT name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="E">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
              </TBODY>
      </TABLE>
	</td></tr></table>
<?php 
include "../model/subprogram/s2_model_4.php";
?>
