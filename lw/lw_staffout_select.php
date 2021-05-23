<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 劳务工离职资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
 include "../model/subprogram/read_datain.php";
$CheckTb="$DataPublic.lw_dimissiondata";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
<TR>
                  <TD width="89" align="right">离职类别
            <input name="Field[]" type="hidden" id="Field[]" value="Type"></TD><TD width="91" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                    <TD width="378"><select name=value[] id="value[]" style="width: 380px;">
                      <option value="" selected>全部</option>
                      <?php 
						$dResult=mysql_query("SELECT Id,Name FROM $DataPublic.dimissiontype WHERE Estate=1 order by Id",$link_id);
						if($dRow = mysql_fetch_array($dResult)) {
							do{
								$dId=$dRow["Id"];
								$dName=$dRow["Name"];
								echo "<option value='$dId'>$dName</option>";
								}while ($dRow = mysql_fetch_array($dResult));
							}
						?>
                    </select>
    <input name="table[]" type="hidden" id="table[]" value="D">
    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD></TR>			  
			  <TR>
                  <TD width="89" align="right">离职日期
                <input name="Field[]" type="hidden" id="Field[]" value="outDate"></TD><TD width="91" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="378"><INPUT name=value[] class=textfield id="value[]" size=22 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=23 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="D">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
<TR>
                  <TD width="89" align="right">入职日期
            <input name="Field[]" type="hidden" id="Field[]" value="ComeIn"></TD><TD width="91" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="378"><INPUT name=value[] class=textfield id="value[]" size=22 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=23 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="P">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
<TR>
                  <TD width="89" align="right">员工 I D
            <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD><TD width="91" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="378"><INPUT name=value[] class=textfield id="value[]" size=49>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
			  </TD>
                </TR>
			<TR>
                  <TD align="right"><p>员工姓名
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
                  </p>
              </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
              </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=49>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
			  </TD>
                </TR>
				<TR>
                  <TD width="89" align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门
            <input name="Field[]" type="hidden" id="Field[]" value="BranchId"></TD><TD width="91" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="378"><select name=value[] id="value[]" style="width: 380px;">
                   <option value="" selected>全部</option>
                   <?php 
			$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata WHERE Estate='1' order by 'Id'",$link_id);
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
                  <TD width="89" align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位
                  <input name="Field[]" type="hidden" id="Field[]" value="JobId"></TD><TD width="91" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="378"><select name=value[] id="value[]" style="width: 380px;">
                   <option value="" selected>全部</option>
                   <?php 
			$J_Result=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata WHERE Estate='1' order by 'Id'",$link_id);
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