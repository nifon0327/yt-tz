<?php 
//电信-ZX  2012-08-01
/*
$DataIn.trade_object
$DataIn.trade_object
$DataPublic.freightdata
$DataPublic.freightdata
$DataIn.linkmandata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
//步骤3：需处理
switch($uType){
	case 2:
		$result = mysql_query("SELECT CompanyId AS cId,Forshort AS cName FROM $DataIn.trade_object WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0) ORDER BY Id",$link_id);
	break;
	case 3:
		$result = mysql_query("SELECT CompanyId AS cId,Forshort AS cName,Letter FROM $DataIn.trade_object WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0) ORDER BY Letter,Id",$link_id);
	break;
	case 4:
		$result = mysql_query("SELECT CompanyId AS cId,Forshort AS cName FROM $DataPublic.freightdata WHERE Estate=1 order by Id",$link_id);
	break;
	case 5:
		$result = mysql_query("SELECT CompanyId AS cId,Forshort AS cName FROM $DataPublic.freightdata WHERE Estate=1 order by Id",$link_id);
	break;
	}
?>
<input name="uType" type="hidden" id="uType" value="<?php echo $uType?>">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="89">公司名称
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
					<?php 
					if($myrow = mysql_fetch_array($result)){
						do{
							echo"<option value='$myrow[cId]'>$myrow[Letter]  $myrow[cName]</option>";
							} while ($myrow = mysql_fetch_array($result));
						}
						?>					
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD><p>联 系 人
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD><p>职&nbsp;&nbsp;&nbsp;&nbsp;务
                      <input name="Field[]" type="hidden" id="Field[]" value="Headship">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD><p>昵&nbsp;&nbsp;&nbsp;&nbsp;称
                      <input name="Field[]" type="hidden" id="Field[]" value="Nickname">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				
<TR>
                  <TD><p>移动电话
                      <input name="Field[]" type="hidden" id="Field[]" value="Mobile">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD><p>固定电话
                      <input name="Field[]" type="hidden" id="Field[]" value="Tel">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD><p>SKYPE
                      <input name="Field[]" type="hidden" id="Field[]" value="SKYPE">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD><p>MSN
                      <input name="Field[]" type="hidden" id="Field[]" value="MSN">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>								
<TR>
                  <TD><p>备注
                      <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				
				<TR>
                  <TD>可用状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION 
          value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION 
          value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="1">可用</option>
                    <option value="0">不可用</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="L">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
  <input name="table[]" type="hidden" id="table[]" value="L">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD>操 作 员
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php 
					$CheckTb="$DataIn.linkmandata";
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="L">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>锁定状态
                  <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
                  <TD>
					  <select name="fun[]" id="fun[]" style="width: 60px;">
						<option value="=">=</option>
					  </select>
				  </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="L">
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
//步骤4：
include "../model/subprogram/s2_model_4.php";
?>