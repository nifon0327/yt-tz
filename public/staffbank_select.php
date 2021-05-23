<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//$DataPublic.branchdata/$DataPublic.jobdata 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 员工资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$_SESSION["SelectBanEstate"]=$BanEstate; 
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
                  <TD width="89" align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门
            <input name="Field[]" type="hidden" id="Field[]" value="BranchId"></TD><TD width="92" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="377"><select name=value[] id="value[]" style="width: 274px;">
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
                  <TD width="89" align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位
                  <input name="Field[]" type="hidden" id="Field[]" value="JobId"></TD><TD width="92" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="377"><select name=value[] id="value[]" style="width: 274px;">
                   <option value="" selected>全部</option>
                   <?php 
			$J_Result=mysql_query("SELECT * FROM $DataPublic.jobdata 
								    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
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
                  <TD width="89" align="right">员工 I D
                  <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD><TD width="92" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="377"><INPUT name=value[] class=textfield id="value[]" size=48>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
  </TR>
                <TR>
                  <TD width="89" align="right">分&nbsp;&nbsp;&nbsp;&nbsp;类
                  </TD><TD width="92" align="center"><SELECT name=fun2 id="fun2" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                                   </SELECT>
                  </TD>
                  <TD width="377"><select name='SelectBanEstate' id="SelectBanEstate" style="width: 274px;" disabled>
                   <?php 
                            $BanEstate=$BanEstate==""?1:$BanEstate;
							$TempBanEstate="BanEstateStr".strval($BanEstate); 
							$$TempBanEstate="selected";	
							echo"<option value='1' $BanEstateStr1>工行卡信息</option>";
							echo"<option value='2' $BanEstateStr2>农行卡信息</option>";
							echo"<option value='3' $BanEstateStr0>工行卡(未结付)</option>";	
							echo"<option value='4' $BanEstateStr0>农行卡(未结付)</option>";	
						?>			  
                  </select>
</TD>
                <TR>
                  <TD width="89" align="right">银行帐户
                  <input name="Field[]" type="hidden" id="Field[]" value="Bank"></TD><TD width="92" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="377"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
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