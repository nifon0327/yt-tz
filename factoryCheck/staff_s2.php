<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
/*电信---yang 20120801
$DataPublic.sbdata
$DataPublic.staffmain
$DataPublic.staffsheet
$DataPublic.jobdata
$DataPublic.branchdata
$DataPublic.gradedata
$DataPublic.rprdata

$DataIn.usertable
$DataIn.sbpaysheet
二合一已更新
*/

//步骤1
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
//步骤3：需处理
$Parameter.=",Bid,$Bid,Jid,$Jid,Kid,$Kid,Month,$Month";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
<TR>
                  <TD width="89">入职日期
                    <input name="Field[]" type="hidden" id="Field[]" value="ComeIn"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value="=" selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value="!=">!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="M">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR><TR>
                  <TD width="89">介 绍 人
                    <input name="Field[]" type="hidden" id="Field[]" value="Introducer"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
			 <?php 
			 $Result1 = mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 order by Id",$link_id);
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
				<?php 
				if($Bid==""){
				?>
			<TR>
                  <TD width="89">部&nbsp;&nbsp;&nbsp;&nbsp;门
                    <input name="Field[]" type="hidden" id="Field[]" value="BranchId"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value="!=">!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
				  <option value="" selected>全部</option>
                   <?php 
					$B_Result=mysql_query("SELECT * FROM $DataPublic.branchdata 
										   WHERE  Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);					
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
                </TR>
				
		<?php 
				}
		if($Jid==""){
				?>
				<TR>
                  <TD width="89">职&nbsp;&nbsp;&nbsp;&nbsp;位
                    <input name="Field[]" type="hidden" id="Field[]" value="JobId"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                </TR>
				<?php 
				}?>
				
				<TR>
                  <TD width="89">员工 I D
                    <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD><p>员工姓名
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				<TR>
                  <TD width="89">性别
                    <input name="Field[]" type="hidden" id="Field[]" value="Sex"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <option value="1">男</option>
                    <option value="0">女</option>
                                                                        </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="89">民族
                    <input name="Field[]" type="hidden" id="Field[]" value="Nation"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
					  <option value="" selected>全部</option>
					  <?php 
					 $Result2 = mysql_query("SELECT Id,Name FROM $DataPublic.nationdata WHERE Estate=1 order by Id",$link_id);
					 if($myRow2 = mysql_fetch_array($Result2)){
						do{
							echo" <option value='$myRow2[Id]'>$myRow2[Name]</option>";
							}while($myRow2 = mysql_fetch_array($Result2));
						}
					 ?>
                                                                        </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="89">户口原地
                    <input name="Field[]" type="hidden" id="Field[]" value="Rpr"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
					  <option value="" selected>全部</option>
					  <?php 
					 $Result3 = mysql_query("SELECT Id,Name FROM $DataPublic.rprdata WHERE Estate=1 order by Id",$link_id);
					 if($myRow3 = mysql_fetch_array($Result3)){
						do{
							echo" <option value='$myRow3[Id]'>$myRow3[Name]</option>";
							}while($myRow3 = mysql_fetch_array($Result3));
						}
					 ?>
                                                                        </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="89">教育程度
                    <input name="Field[]" type="hidden" id="Field[]" value="Education"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
					  <option value="" selected>全部</option>
					  <?php 
					 $Result4 = mysql_query("SELECT Id,Name FROM $DataPublic.education WHERE Estate=1 order by Id",$link_id);;
					 if($myRow4 = mysql_fetch_array($Result4)){
						do{
							echo" <option value='$myRow4[Id]'>$myRow4[Name]</option>";
							}while($myRow4 = mysql_fetch_array($Result4));
						}
					 ?>
                                                      </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="89">婚姻状况
                    <input name="Field[]" type="hidden" id="Field[]" value="Married"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <option value="1">未婚</option>
                    <option value="0">已婚</option>
                    <option value="2">离异</option>
                    <option value="3">再婚</option>
                                      </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="89">出生日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Birthday"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="S">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR><TR>
                  <TD width="89">照 &nbsp;&nbsp;&nbsp;片
                    <input name="Field[]" type="hidden" id="Field[]" value="Photo"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <option value="1">有</option>
                    <option value="0">没有</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isSign" />
</TD>
                </TR><TR>
                  <TD width="89">身 份 证
                    <input name="Field[]" type="hidden" id="Field[]" value="IdcardPhoto"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <option value="1">有</option>
                    <option value="0">没有</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isSign" />
</TD>
                </TR>				<TR>
                  <TD width="89">身份证号
                    <input name="Field[]" type="hidden" id="Field[]" value="Idcard"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                				               <TR>
                                                 <TD>家庭地址
                                                   <input name="Field[]" type="hidden" id="Field[]" value="Address"></TD><TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                                                     <option value="LIKE" selected>包含</option>
                                                     <OPTION value==>=</OPTION>
                                                     <OPTION 
          value=!=>!=</OPTION>
                                                   </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD width="89">邮政编码
                    <input name="Field[]" type="hidden" id="Field[]" value="Postalcode"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89">家庭电话
                    <input name="Field[]" type="hidden" id="Field[]" value="Tel"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89">移动电话
                    <input name="Field[]" type="hidden" id="Field[]" value="Mobile"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89">短&nbsp;&nbsp;&nbsp;&nbsp;号
                    <input name="Field[]" type="hidden" id="Field[]" value="Dh"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89">电子邮件
                    <input name="Field[]" type="hidden" id="Field[]" value="Mail"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89">银行帐户
                    <input name="Field[]" type="hidden" id="Field[]" value="Bank"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89">备&nbsp;&nbsp;&nbsp;&nbsp;注
                    <input name="Field[]" type="hidden" id="Field[]" value="Note"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>                <TR>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="M">
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
					//员工资料表
					$PD_Sql = "SELECT Number,Name FROM $DataPublic.staffmain WHERE Number IN (SELECT Operator FROM $DataPublic.staffmain group by Operator ORDER BY Id)order by Id";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$Number=$PD_Myrow["Number"];
						$Name=$PD_Myrow["Name"];					
						echo "<option value='$Number'>$Name</option>";
						} 
					?>		 
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">                  
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
					  <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				<?php 
				if($Action!=0){
				?>
				
                <TR>
                  <TD>社保
                  <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="1">已加入</option>
                    <option value="0">未加入</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isSb" /></TD>
                </TR>
                <TR>
                  <TD>加入月份
                  <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=15 maxlength="6">
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=15 maxlength="6">
  <input name="table[]" type="hidden" id="table[]" value="P">  
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
				<?php 
				}
				?>
              </TBODY>
	    </TABLE>
		</td>
	</tr>
</table>
<?php 
//步骤4：
include "../model/subprogram/s2_model_4.php";
?>