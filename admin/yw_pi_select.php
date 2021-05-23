<?php   
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany PI资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
//$_SESSION["nowWebPage"]=$nowWebPage; 
$_SEESION["nowWebPage"] = $nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.yw3_pisheet";
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="115" align="right">Client
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD><TD width="100" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="343">
				  <select name=value[] id="value[]" style="width: 274px;">
                    <?php   
					$jSql = "SELECT P.CompanyId,C.Forshort 
					FROM $DataIn.yw3_pisheet P,$DataIn.trade_object C 
					WHERE P.CompanyId=C.CompanyId AND C.cSign=$Login_cSign GROUP BY P.CompanyId ORDER BY C.OrderBy DESC";
					$jResult = mysql_query($jSql); 
					echo "<option value='' selected>全部</option>";
					while ($jRow = mysql_fetch_array($jResult)){
						$CompanyId=$jRow["CompanyId"];
						$Forshort=$jRow["Forshort"];					
						echo "<option value='$CompanyId'>$Forshort</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>PI NO.
                      <input name="Field[]" type="hidden" id="Field[]" value="PI">
                  </p>
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>			
<TR>
                  <TD align="right"><p>Lead time
                      <input name="Field[]" type="hidden" id="Field[]" value="Leadtime">
                  </p>
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>					               <TR>
                  <TD align="right">Paymentterm
                  <input name="Field[]" type="hidden" id="Field[]" value="Paymentterm"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">Date
                    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="P">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD align="right">Operator
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				  <option value="" selected>全部</option>
					<?php   
					include "../model/subprogram/select_model_stafflist.php";
					?>		 
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="P">                  
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