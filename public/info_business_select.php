<?php 
//电信-ZX
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 外出记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
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
                  <TD width="119" align="right"><p>外出登记
                    <input name="Field[]" type="hidden" id="Field[]" value="Businesser">
                  </p>
                  </TD>
                  <TD width="96" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
</TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" style="width:380px" maxlength="5">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD align="right">外出日期
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="I">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>				<TR>
                  <TD align="right">起始时间
                      <input name="Field[]" type="hidden" id="Field[]" value="StartTime">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px" maxlength="7">
                    <input name="table[]" type="hidden" id="table[]" value="I">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>          
                <TR>
                  <TD align="right">结束时间
                      <input name="Field[]" type="hidden" id="Field[]" value="EndTime">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px" maxlength="7">
                    <input name="table[]" type="hidden" id="table[]" value="I">
  <input name="types[]" type="hidden" id="types[]" value="isStr">
</TD>
                </TR>
<TR>
                  <TD align="right">用车情况
                      <input name="Field[]" type="hidden" id="Field[]" value="CarId">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name="value[]" id="value[]" style="width:380px">
          <option value="" selected>请选择</option>
            <?php 
           $CarSql=mysql_query("SELECT Id,CarNo FROM $DataPublic.cardata ORDER BY cSign,TypeId,CarNo,Id",$link_id);
		 if($CarRow=mysql_fetch_array($CarSql)){
			do{
				$Id=$CarRow["Id"];
				$CarNo=$CarRow["CarNo"];
				echo"<option value='$Id'>$CarNo</option>";
				}while($CarRow=mysql_fetch_array($CarSql));
			}
		  ?>
          </select>
                    <input name="table[]" type="hidden" id="table[]" value="I">
  <input name="types[]" type="hidden" id="types[]" value="isNum">
</TD>
                </TR>
<TR>
  <TD align="right">司&nbsp;&nbsp;&nbsp;&nbsp;机
      <input name="Field[]" type="hidden" id="Field[]" value="Drivers">
  </TD>
  <TD align="center">
      <SELECT name=fun[] id="fun[]" style="width: 60px;">
          <OPTION value== 
          selected>=</OPTION>
          <OPTION value=!=>!=</OPTION>
      </SELECT>  </TD>
  <TD><select name="value[]" id="value[]" style="width:380px">
    <option value="" selected>请选择</option>
    <option value="0">自驾</option>
    <?php 
	 $CheckSql=mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 AND JobId='10'",$link_id);
		 if($CheckRow=mysql_fetch_array($CheckSql)){
			do{
				$Number=$CheckRow["Number"];
				$Name=$CheckRow["Name"];
				echo"<option value='$Number'>$Name</option>";
				}while($CheckRow=mysql_fetch_array($CheckSql));
			}
	?>
  </select>
      <input name="table[]" type="hidden" id="table[]" value="I">
      <input name="types[]" type="hidden" id="types[]" value="isNum">
  </TD>
</TR>
                <TR>
                  <TD align="right">外出说明
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </TD>
                  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>			        </TD>
                  <TD>
					  <INPUT name=value[] class=textfield id="value[]" style="width:380px" maxlength="5">
				    <input name="table[]" type="hidden" id="table[]" value="I">
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