<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-20
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 员工资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
$SelectFrom=4;
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
           <TR>
                  <TD width="89" align="right">入职日期
            <input name="Field[]" type="hidden" id="Field[]" value="ComeIn"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="M">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR><TR>
                  <TD width="89" align="right">介 绍 人
                  <input name="Field[]" type="hidden" id="Field[]" value="Introducer"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
    <TR>
                  <TD width="89" align="right">工作地点
                    <input name="Field[]" type="hidden" id="Field[]" value="WorkAdd"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="374">
                   <?php 
				   
					include "../model/subselect/WorkAdd.php";
					?>			  
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
     <TR>
                  <TD width="89" align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门
            <input name="Field[]" type="hidden" id="Field[]" value="BranchId"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
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
                </TR>               
                <TR>
                  <TD width="89" align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位
                  <input name="Field[]" type="hidden" id="Field[]" value="JobId"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
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
                </TR>				<TR>
                  <TD width="89" align="right">员工 I D
                  <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>				<TR>
                  <TD width="89" align="right">性别
                  <input name="Field[]" type="hidden" id="Field[]" value="Sex"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
                    <option value="" selected>全部</option>
                    <option value="1">男</option>
                    <option value="0">女</option>
                                                                        </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR><TR>
                  <TD width="89" align="right">民族
                  <input name="Field[]" type="hidden" id="Field[]" value="Nation"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
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
                </TR>
<TR>
                  <TD width="89" align="right">血型
                  <input name="Field[]" type="hidden" id="Field[]" value="Id"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
					  <option value="" selected>全部</option>
					  <?php 
					 $Result5 = mysql_query("SELECT Id,Name FROM $DataPublic.bloodgroup_type WHERE Estate=1 order by Id",$link_id);
					 if($myRow5 = mysql_fetch_array($Result5)){
						do{
							echo" <option value='$myRow5[Id]'>$myRow5[Name]</option>";
							}while($myRow5 = mysql_fetch_array($Result5));
						}
					 ?>
                                                                        </select>
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD width="89" align="right">户口原地
                  <input name="Field[]" type="hidden" id="Field[]" value="Rpr"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
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
                  <TD width="89" align="right">教育程度
                  <input name="Field[]" type="hidden" id="Field[]" value="Education"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
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
                  <TD width="89" align="right">婚姻状况
                  <input name="Field[]" type="hidden" id="Field[]" value="Married"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
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
                  <TD width="89" align="right">出生日期
                  <input name="Field[]" type="hidden" id="Field[]" value="Birthday"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="S">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR><TR>
                  <TD width="89" align="right">照 &nbsp;&nbsp;&nbsp;片
                  <input name="Field[]" type="hidden" id="Field[]" value="Photo"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
                    <option value="" selected>全部</option>
                    <option value="1">有</option>
                    <option value="0">没有</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isSign" />
</TD>
                </TR><TR>
                  <TD width="89" align="right">身 份 证
                  <input name="Field[]" type="hidden" id="Field[]" value="IdcardPhoto"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
                    <option value="" selected>全部</option>
                    <option value="1">有</option>
                    <option value="0">没有</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isSign" />
</TD>
                </TR><TR>
                  <TD width="89" align="right">健 康 证
                  <input name="Field[]" type="hidden" id="Field[]" value="HealthPhoto"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><select name=value[] id="value[]" style="width: 380px;">
                    <option value="" selected>全部</option>
                    <option value="1">有</option>
                    <option value="0">没有</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isSign" />
</TD>
                </TR>				<TR>
                  <TD width="89" align="right">身份证号
                  <input name="Field[]" type="hidden" id="Field[]" value="Idcard"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                				               <TR>
                                                 <TD align="right">家庭地址
                                                 <input name="Field[]" type="hidden" id="Field[]" value="Address"></TD><TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                                                     <option value="LIKE" selected>包含</option>
                                                     <OPTION value==>=</OPTION>
                                                     <OPTION 
          value=!=>!=</OPTION>
                                                   </SELECT></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
                  <TD width="89" align="right">邮政编码
            <input name="Field[]" type="hidden" id="Field[]" value="Postalcode"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89" align="right">家庭电话
                  <input name="Field[]" type="hidden" id="Field[]" value="Tel"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89" align="right">移动电话
                  <input name="Field[]" type="hidden" id="Field[]" value="Mobile"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89" align="right">短&nbsp;&nbsp;&nbsp;&nbsp;号
                  <input name="Field[]" type="hidden" id="Field[]" value="Dh"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR><TR>
                  <TD width="89" align="right">电子邮件
                  <input name="Field[]" type="hidden" id="Field[]" value="Mail"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				<?php 
				/*
				<TR>
                  <TD width="89">银行帐户
                    <input name="Field[]" type="hidden" id="Field[]" value="Bank"></TD><TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				*/
				?>
				<TR>
                  <TD width="89" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注
                  <input name="Field[]" type="hidden" id="Field[]" value="Note"></TD><TD width="95" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="374"><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 380px;">
		                  <input name="table[]" type="hidden" id="table[]" value="M">                  
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
					  <select name=value[] id="value[]" style="width: 380px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
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
include "../admin/subprogram/select_model_b.php";
?>