<?php 
//电信-ZX  2012-08-01
/*

二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 电脑资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataPublic.net_cpcheckdiary";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="105" align="right"><p>电脑名称
                      <input name="Field[]" type="hidden" id="Field[]" value="CpName">
                  </p>
                  </TD>
                  <TD width="87" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="366"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">领 用 人
                      <input name="Field[]" type="hidden" id="Field[]" value="User">
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
                      <option selected  value="">全部</option>
                      <?php 
					$CheckSql=mysql_query("SELECT M.Name,D.User FROM $DataPublic.net_cpdata D,$DataPublic.staffmain M WHERE M.Number=D.User GROUP BY D.User ORDER BY M.BranchId,M.JobId,M.Number",$link_id);
					if($CheckRow=mysql_fetch_array($CheckSql)){
						do{
							$Name=$CheckRow["Name"];
							$User=$CheckRow["User"];
							echo"<option value='$User'>$Name</option>";
							}while($CheckRow=mysql_fetch_array($CheckSql));
						}
					?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="D">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">维护内容
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
</TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="C">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">维护评述
                    <input name="Field[]" type="hidden" id="Field[]" value="Opinion">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
</TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="C">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">维护日期
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
  <input name="table[]" type="hidden" id="table[]" value="C">
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
                  <input name="table[]" type="hidden" id="table[]" value="C">                  
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
					  <input name="table[]" type="hidden" id="table[]" value="C">
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