
<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.staffmain
$DataPublic.branchdata
$DataPublic.jobdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 公积金资料查询");			//需处理
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
                <TD align="right">公积金分类
                  <input name="Field[]" type="hidden" id="Field[]" value="Type">
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
                    <option value="" selected>全部</option>
                    <?php 
		$tResult=mysql_query("SELECT Id,Name FROM $DataPublic.rs_sbtype WHERE Id>=4 ORDER BY Id",$link_id);
		if($tRow = mysql_fetch_array($tResult)) {
			do{
				$tId=$tRow["Id"];
				$tName=$tRow["Name"];
				echo "<option value='$tId'>$tName</option>";
				}while($tRow = mysql_fetch_array($tResult));
			}
		?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
				<TR>
                  <TD align="right">缴费起始月份
                      <input name="Field[]" type="hidden" id="Field[]" value="sMonth">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="18" maxlength="7">至
				  <INPUT name=MonthArray[] class=textfield id="MonthArray[]" size="18" maxlength="7">
				  <input name="table[]" type="hidden" id="table[]" value="S">  
				  <input name="types[]" type="hidden" id="types[]" value="isMonth"></TD>
                </TR>
<TR>
                  <TD align="right">缴费结束月份
                      <input name="Field[]" type="hidden" id="Field[]" value="eMonth">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="18" maxlength="7">至
				  <INPUT name=MonthArray[] class=textfield id="MonthArray[]" size="18" maxlength="7">
				  <input name="table[]" type="hidden" id="table[]" value="S">  
				  <input name="types[]" type="hidden" id="types[]" value="isMonth"></TD>
                </TR>
<TR>
                  <TD width="119" align="right">在职状态
                      <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD width="96" align="center">
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
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <option value="1">在职</option>
                    <option value="0">离职</option>
                     </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="119" align="right">员工 I D
                    <input name="Field[]" type="hidden" id="Field[]" value="Number">
                  </TD>
                  <TD width="96" align="center">
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
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				
				<TR>
                  <TD width="119" align="right">入职日期
                    <input name="Field[]" type="hidden" id="Field[]" value="ComeIn">
                  </TD>
                  <TD width="96" align="center">
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
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="M">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR><TR>
                  <TD width="119" align="right">介 绍 人
                    <input name="Field[]" type="hidden" id="Field[]" value="Introducer">
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
			 $Result1 = mysql_query("SELECT M.Number,M.Name FROM $DataPublic.staffmain M,$DataPublic.staffmain I WHERE I.Introducer=M.Number AND M.Estate='1' GROUP BY I.Introducer ORDER BY M.BranchId,M.JobId,M.Number",$link_id);
			 if($myRow1 = mysql_fetch_array($Result1)){
				do{
					echo" <option value='$myRow1[Number]'>$myRow1[Name]</option>";
					}while($myRow1 = mysql_fetch_array($Result1));
				}
			 ?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD width="119" align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门
                    <input name="Field[]" type="hidden" id="Field[]" value="BranchId">
                  </TD>
                  <TD width="96" align="center">
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
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                   <option value="" selected>全部</option>
                   <?php 
					$B_Result=mysql_query("SELECT * FROM $DataPublic.branchdata WHERE Estate=1 order by Id",$link_id);
					if($B_Row = mysql_fetch_array($B_Result)) {
						do{
							$B_Id=$B_Row["Id"];
							$B_Name=$B_Row["Name"];
							echo "<option value='$B_Id'>$B_Name</option>";
							}while ($B_Row = mysql_fetch_array($B_Result));
						}
					?>			  
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="119" align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位
                    <input name="Field[]" type="hidden" id="Field[]" value="JobId">
                  </TD>
                  <TD width="96" align="center">
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
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                   <option value="" selected>全部</option>
                   <?php 
					$J_Result=mysql_query("SELECT * FROM $DataPublic.jobdata WHERE Estate=1 order by Id",$link_id);
					if($J_Row = mysql_fetch_array($J_Result)) {
						do{
							$J_Id=$J_Row["Id"];
							$J_Name=$J_Row["Name"];
							echo "<option value='$J_Id'>$J_Name</option>";
							}while ($J_Row = mysql_fetch_array($J_Result));
						}
					?>			  
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="M">
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