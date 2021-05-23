<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.jobdata
$DataPublic.redeployj
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 职位调动资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataPublic.redeployj";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>				
				<TR>
                  <TD width="119" align="right">在职状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD width="96" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                <TD width="343">
				  <select name=value[] id="value[]" style="width: 274px;">
                  <option value="" selected>全部</option>
                  <option value="1">在职</option>
                  <option value="0">离职</option>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				</TD>
                </TR>
				<TR>
                	<TD width="119" align="right">员工 I D
                	  <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD><TD width="96" align="center">
                        <SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=">">&gt;</OPTION>
                          <OPTION value=">=">&gt;=</OPTION>
                          <OPTION value="<">&lt;</OPTION>
                          <OPTION value="<=">&lt;=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                        </SELECT>
                  </TD>
                <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="J">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD align="right"><p>员工姓名
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
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
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				<TR>
                  <TD width="119" align="right">原 职 位
                      <input name="Field[]" type="hidden" id="Field[]" value="ActionOut">
                  </TD>
                  <TD width="96" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
					<?php 
					$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata WHERE Estate=1 order by Id",$link_id);
					if($outRow = mysql_fetch_array($outResult)) {
						do{
							$outId=$outRow["Id"];
							$outName=$outRow["Name"];
							echo "<option value='$outId'>$outName</option>";
							}while ($outRow = mysql_fetch_array($outResult));
						}
					?>			  
                   </select>
                    <input name="table[]" type="hidden" id="table[]" value="J">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="119" align="right">新 职 位
                      <input name="Field[]" type="hidden" id="Field[]" value="ActionIn">
                  </TD>
                  <TD width="96" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
					  <option value="" selected>全部</option>
						<?php 
						$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata WHERE Estate=1 order by Id",$link_id);
						if($outRow = mysql_fetch_array($outResult)) {
							do{
								$outId=$outRow["Id"];
								$outName=$outRow["Name"];
								echo "<option value='$outId'>$outName</option>";
								}while ($outRow = mysql_fetch_array($outResult));
							}
						?>			  
                      </select>
                    <input name="table[]" type="hidden" id="table[]" value="J">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="119" align="right">起效月份
                      <input name="Field[]" type="hidden" id="Field[]" value="Month">
                  </TD>
                  <TD width="96" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="J">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD align="right">更新日期
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
  <INPUT name=LastDate class=textfield id="LastDate" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="J">
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
                  <input name="table[]" type="hidden" id="table[]" value="J">                  
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
					  <input name="table[]" type="hidden" id="table[]" value="J">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>              </TBODY>
	    </TABLE>
		</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>