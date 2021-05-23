<?php 
//电信-zxq 2012-08-01
//$DataIn.cw4_otherin/$DataPublic.cw4_otherintype 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 预结付取款记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.cw_advanced";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="111" align="right">取款人
                    <input name="Field[]" type="hidden" id="Field[]" value="Name">
                  </TD>
                  <TD width="104" align="center"><select name="fun[]" id="fun[]4" style="width: 60px;">
                    <option value="LIKE" selected="selected">包含</option>
                    <option value="=">=</option>
                    <option 
          value="!=">!=</option>
                  </select></TD>
                  <TD width="343"><input name="value[]" class="textfield" id="value[]4" style="width:380px" />
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<tr>
				  <td align="right">银行
				    <input name="Field[]" type="hidden" id="Field[]3" value="BankId" /></td>
				  <td align="center"><select name="fun[]" id="fun[]3" style="width: 60px;">
				    <option value="=" 
          selected="selected">=</option>
				    <option value="&gt;">&gt;</option>
				    <option 
          value="&gt;=">&gt;=</option>
				    <option value="&lt;">&lt;</option>
				    <option 
          value="&lt;=">&lt;=</option>
				    <option value="!=">!=</option>
			      </select></td>
				  <td><select name="value[]" id="value[]3" style="width: 380px;">
				    <option value="" selected="selected">全部</option>
				    <?php 
					$TypeResult = mysql_query("SELECT A.BankId,B.Title FROM $DataIn.cw_advanced A
											  LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId GROUP BY A.BankId ORDER BY A.BankId",$link_id);
					if($TypeRow = mysql_fetch_array($TypeResult)){
						do{
							$Id=$TypeRow["BankId"];
							$Name=$TypeRow["Title"];
							echo"<option value='$Id'>$Name</option>";
							}while($TypeRow = mysql_fetch_array($TypeResult));
						}
					?>
				    </select>
				    <input name="table[]" type="hidden" id="table[]3" value="A" />
			      <input name="types[]" type="hidden" id="types[]3"
                value="isNum" /></td>
			    </tr>
                <TR>
                  <TD align="right"><p>取款金额
                      <input name="Field[]" type="hidden" id="Field[]" value="Amount">
                  </p>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px" >
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <tr>
                  <td align="right"><p>货币符号
                    <input name="Field[]2" type="hidden" id="Field[]2" value="Currency" />
                  </p></td>
                  <td align="center"><select name="fun[]2" id="fun[]2" style="width: 60px;">
                    <option value="=" 
          selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option 
          value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option 
          value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></td>
                  <td><select name="value[]2" id="value[]2" style="width:380px" >
                    <option value="" selected="selected">全部</option>
                    <?php 
					$Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
					if($Currency_Row = mysql_fetch_array($Currency_Result)){
						do{
							$Id=$Currency_Row["Id"];
							$Name=$Currency_Row["Name"];
							echo"<option value='$Id'>$Name</option>";
							}while ($Currency_Row = mysql_fetch_array($Currency_Result));
						}
					?>
                  </select>
                    <input name="table[]2" type="hidden" id="table[]2" value="A" />
                    <input name="types[]2" type="hidden" id="types[]2"
                value="isNum" /></td>
                </tr>					               <TR>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px" >
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">取款日期
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px"  onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px"  onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="A">
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
				  <select name=value[] id="value[]" style="width:380px" >
                    <option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="A">                  
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
					  <select name=value[] id="value[]" style="width:380px" >
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="A">
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