<?php 
//电信-zxq 2012-08-01
//$DataIn.cw5_fbdh/$DataPublic.currencydata 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 货币汇兑记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.cw5_fbdh";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

 

	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
			  <TR>
                <TD align="right">汇兑日期
                        <input name="Field[]" type="hidden" id="Field[]" value="PayDate">
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
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="D">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                </TD>
		      </TR>
              <TBODY>
         <TR>
            <TD align="right">结汇凭证
              <input name="Field[]" type="hidden" id="Field[]" value="BillNumber">
            </TD>
            <TD align="center">
              <SELECT name=fun[] id="fun[]" style="width: 60px;">
                <option value="LIKE" selected>包含</option>
                <OPTION value==>=</OPTION>
                <OPTION 
          value=!=>!=</OPTION>
              </SELECT>            </TD>
            <TD><INPUT name=value[] class=textfield id="value[]" size=48>
              <input name="table[]" type="hidden" id="table[]" value="D">
              <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
            </TD>
            </TR>                
				<TR>
                  <TD width="111" align="right">转出货币
                      <input name="Field[]" type="hidden" id="Field[]" value="OutCurrency">
                  </TD>
                  <TD width="104" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="343">
				  <select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option> 
					<?php 
					$checkCurrency=mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 ORDER BY Id",$link_id);
					if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
						do{
							$cName=$checkCurrencyRow["Name"];
							$cId=$checkCurrencyRow["Id"];
							echo"<option value='$cId'>$cName</option>";
							}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>转出金额
                      <input name="Field[]" type="hidden" id="Field[]" value="OutAmount">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>汇&nbsp;&nbsp;&nbsp;&nbsp;率
                      <input name="Field[]" type="hidden" id="Field[]" value="Rate">
                  </p></TD>
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
                      <input name="table[]" type="hidden" id="table[]" value="D">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>			
<TR>
                  <TD align="right"><p>转入货币
                      <input name="Field[]" type="hidden" id="Field[]" value="InCurrency">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option> 
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
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
  <TD align="right"><p>转入金额
          <input name="Field[]" type="hidden" id="Field[]" value="InAmount">
  </p></TD>
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
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="D">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
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
              </SELECT>            </TD>
            <TD><INPUT name=value[] class=textfield id="value[]" size=48>
              <input name="table[]" type="hidden" id="table[]" value="D">
              <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
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
                  <input name="table[]" type="hidden" id="table[]" value="D">                  
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
			          <option value="=" selected>=</option>
		            </select>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="D">
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