<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.staffmain
$DataPublic.my3_exadd
$DataPublic.freightdata
$DataPublic.my3_expresstype
$DataPublic.my3_express
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 快递单查询");			//需处理
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
              <TBODY>
				<TR>
                  <TD width="89" align="right">寄 件 人
                    <input name="Field[]" type="hidden" id="Field[]" value="Shipper"></TD><TD width="93" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="376">
				  <select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//员工资料表
					$jSql = "SELECT Name,Shipper FROM $DataPublic.my3_express E LEFT JOIN $DataPublic.staffmain M ON M.Number=E.Shipper GROUP BY E.Shipper ORDER BY M.BranchId,M.JobId,M.Number";
					$jResult = mysql_query($jSql); 
					echo "<option value='' selected>全部</option>";
					while ($jRow = mysql_fetch_array($jResult)){
						$Shipper=$jRow["Shipper"];
						$Name=$jRow["Name"];					
						echo "<option value='$Shipper'>$Name</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isDate" />
</TD>
                </TR>			
<TR>
                  <TD align="right"><p>经 手 人
                      <input name="Field[]" type="hidden" id="Field[]" value="HandledBy">
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
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//员工资料表
					$hSql = "SELECT Name,HandledBy FROM $DataPublic.my3_express E 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=E.HandledBy 
					WHERE E.Estate=0
					GROUP BY E.HandledBy ORDER BY M.BranchId,M.JobId,M.Number";
					$hResult = mysql_query($hSql); 
					echo "<option value='' selected>全部</option>";
					while ($hRow = mysql_fetch_array($hResult)){
						$HandledBy=$hRow["HandledBy"];
						$Name=$hRow["Name"];					
						echo "<option value='$HandledBy'>$Name</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>					               <TR>
                  <TD align="right">&nbsp;</TD>
                  <TD align="center">&nbsp;</TD>
                  <TD>&nbsp;</TD>
                </TR>
                <TR>
                  <TD align="right">收 件 人
                      <input name="Field[]" type="hidden" id="Field[]" value="Receiver"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                                      </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//员工资料表
					$PD_Sql = "SELECT Id,Name FROM $DataPublic.my3_exadd WHERE Estate=1 ORDER BY Company,Name";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$Operator=$PD_Myrow["Operator"];
						$Name=$PD_Myrow["Name"];					
						echo "<option value='$Operator'>$Name</option>";
						} 
					?>
                  </select>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>收件公司
                      <input name="Field[]" type="hidden" id="Field[]" value="Company">
                  </p></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>收件地址
                      <input name="Field[]" type="hidden" id="Field[]" value="Address">
                  </p></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">收件电话
                    <input name="Field[]" type="hidden" id="Field[]" value="Tel"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">&nbsp;</TD>
                  <TD align="center">&nbsp;</TD>
                  <TD>&nbsp;                      </TD>
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
                  <TD align="right">快件类型
                    <input name="Field[]" type="hidden" id="Field[]" value="ShipType"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//快件类型
					$PD_Sql = "SELECT T.Id,T.Name 
					FROM $DataPublic.my3_expresstype T 
					LEFT JOIN $DataPublic.my3_express E ON E.ShipType=T.Id 
					GROUP BY E.ShipType ORDER BY T.Id";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$Id=$PD_Myrow["Id"];
						$Name=$PD_Myrow["Name"];					
						echo "<option value='$Operator'>$Name</option>";
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="E"><input name="types[]" type="hidden" id="types[]" value="isNum">
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
                </TR>
                <TR>
                  <TD align="right">托寄数量
                  <input name="Field[]" type="hidden" id="Field[]" value="Pieces"></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>托寄内容
                      <input name="Field[]" type="hidden" id="Field[]" value="Contents">
                  </p></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>计费重量
                      <input name="Field[]" type="hidden" id="Field[]" value="cWeight">
                  </p></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注
                  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>
				  </TD>
                  <TD>
					  <INPUT name=value[] class=textfield id="value[]" size=48>
				    <input name="table[]" type="hidden" id="table[]" value="E">
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