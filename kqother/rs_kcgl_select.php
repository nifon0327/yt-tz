<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//电信-ZX  2012-08-01
/*
已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 扣工龄记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.staffrandp";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
<TR>
                  <TD width="101" align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门
            <input name="Field[]" type="hidden" id="Field[]" value="BranchId"></TD><TD width="75" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="382"><select name=value[] id="value[]" style="width: 274px;">
                   <option value="" selected>全部</option>
                   <?php 
					$B_Result=mysql_query("SELECT * FROM $DataPublic.branchdata 
										   WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
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
                  <TD width="101" align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位
                  <input name="Field[]" type="hidden" id="Field[]" value="JobId"></TD><TD width="75" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="382"><select name=value[] id="value[]" style="width: 274px;">
                   <option value="" selected>全部</option>
                   <?php 
			$J_Result=mysql_query("SELECT * FROM $DataPublic.jobdata 
								   WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
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
                </TR>				<TR>
                  <TD width="101" align="right">员工 I D
                  <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD><TD width="75" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" size=48>
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
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
            </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                				               <TR>
                                                 <TD align="right">扣工龄原因
                                                 <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD><TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                                                     <option value="LIKE" selected>包含</option>
                                                     <OPTION value==>=</OPTION>
                                                     <OPTION 
          value=!=>!=</OPTION>
                                                   </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                				               <TR>
                                                 <TD align="right">起效月份
                                                   <input name="Field[]" type="hidden" id="Field[]" value="Month"></TD>
                                                 <TD align="center"><select name=fun[] id="fun[]" style="width: 60px;">
                                                   <option value== 
          selected>=</option>
                                                   <option value=">">&gt;</option>
                                                   <option 
          value=">=">&gt;=</option>
                                                   <option value="<">&lt;</option>
                                                   <option 
          value="<=">&lt;=</option>
                                                   <option value=!=>!=</option>
                                                 </select></TD>
                                                 <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                                                     <input name="table[]" type="hidden" id="table[]" value="S">
                                                     <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                                                 </TD>
              				                 </TR>
<TR>
                  <TD width="101" align="right">扣工龄月份数
            <input name="Field[]" type="hidden" id="Field[]" value="Months"></TD><TD width="75" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>                <TR>
                  <TD align="right">更新日期
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
  <input name="table[]" type="hidden" id="table[]" value="S">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
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
                  <input name="table[]" type="hidden" id="table[]" value="S">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">锁定状态
                  <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
                  <TD align="center">
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
					  <input name="table[]" type="hidden" id="table[]" value="S">
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