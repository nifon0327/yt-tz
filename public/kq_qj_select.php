<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 请假记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataPublic.kqqjsheet";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011"><TABLE width="600" border=0 align="center">

		  <TR>
            <TD align="right">起始日期
                    <input name="Field[]" type="hidden" id="Field[]" value="StartDate">
            </TD>
            <TD align="center">
              <SELECT name=fun[] id="fun[]" style="width: 60px;">
                  <option value="LIKE" selected>包含</option>
              </SELECT>            </TD>
            <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="J">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
            </TD>
	      </TR>
          <TBODY>
            <TR>
              <TD align="right">结束日期
                      <input name="Field[]" type="hidden" id="Field[]" value="EndDate">
              </TD>
              <TD align="center">
                <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                </SELECT>              </TD>
              <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
        至
          <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
          <input name="table[]" type="hidden" id="table[]" value="J">
          <input name="types[]" type="hidden" id="types[]" value="isDate">
              </TD>
            </TR>
            <TR>
              <TD width="115" align="right">员工 I D
                      <input name="Field[]" type="hidden" id="Field[]" value="Number">
              </TD>
              <TD width="90" align="center">
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
                  </SELECT>              </TD>
              <TD width="381"><INPUT name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="J">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
              </TD>
            </TR>
            <TR>
              <TD align="right"><p>员工姓名
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
              </p></TD>
              <TD align="center">
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>              </TD>
              <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
              </TD>
            </TR>
            <TR>
              <TD align="right">班&nbsp;&nbsp;&nbsp;&nbsp;次
                <input name="Field[]" type="hidden" id="Field[]" value="bcType">
              </TD>
              <TD align="center">
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option selected  value="">全部</option>
                  <option value="I">上班签到</option>
                  <option value="O">下班签退</option>
                </select>
                  <input name="table[]" type="hidden" id="table[]" value="J">
                  <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
              </TD>
            </TR>
            <TR>
              <TD align="right">请假类别
                      <input name="Field[]" type="hidden" id="Field[]" value="Type">
              </TD>
              <TD align="center">
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                    <option value="&gt;">&gt;</option>
                    <option value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option value="&lt;=">&lt;=</option>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option selected  value="">全部</option>
				<?php 
				$qjtypeSql =  mysql_query("SELECT Id,Name FROM $DataPublic.qjtype WHERE Estate=1 ORDER BY Id",$link_id);
				while( $qjtypeRow = mysql_fetch_array($qjtypeSql)){
					$Id=$qjtypeRow["Id"];
					$Name=$qjtypeRow["Name"];
					echo "<option value='$Id'>$Id - $Name</option>";
					} 
				?>
                </select>
                  <input name="table[]" type="hidden" id="table[]" value="J">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
              </TD>
            </TR>
            <TR>
              <TD align="right">请假原因
                      <input name="Field[]" type="hidden" id="Field[]" value="Reason">
              </TD>
              <TD align="center">
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>              </TD>
              <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="J">
                  <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
              </TD>
            </TR>
            <TR>
              <TD align="right">病历证明
                      <input name="Field[]" type="hidden" id="Field[]" value="Proof">
              </TD>
              <TD align="center">
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                  </SELECT>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option selected  value="">全部</option>
                  <option value="1">有证明档</option>
                  <option value="0">无证明档</option>
                              </select>
                  <input name="table[]" type="hidden" id="table[]" value="J">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
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
                  </select>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
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
                    <option value="=" selected>=</option>
                  </select>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option selected  value="">全部</option>
                  <option value="0">锁定</option>
                  <option value="1">未锁定</option>
                </select>
                  <input name="table[]" type="hidden" id="table[]" value="J">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
              </TD>
            </TR>
            <TR>
              <TD align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门
                <input name="Field[]" type="hidden" id="Field[]" value="BranchId">
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
                  </SELECT>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option value="" selected>全部</option>
                  <?php 
			$bResult=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
								   WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
			if($bRow = mysql_fetch_array($bResult)) {
				do{
					$bId=$bRow["Id"];
					$bName=$bRow["Name"];
					echo "<option value='$bId'>$bName</option>";
					}while ($bRow = mysql_fetch_array($bResult));
				}
			?>
                </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
              </TD>
            </TR>
            <TR>
              <TD align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位
                <input name="Field[]" type="hidden" id="Field[]" value="JobId">
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
                  </SELECT>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option value="" selected>全部</option>
                  <?php 
			$jResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
								  WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
			if($jRow = mysql_fetch_array($jResult)) {
				do{
					$jId=$jRow["Id"];
					$jName=$jRow["Name"];
					echo "<option value='$jId'>$jName</option>";
					}while ($jRow = mysql_fetch_array($jResult));
				}
			?>
                </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
              </TD>
            </TR>
            <TR>
              <TD align="right">考勤状态
                      <input name="Field[]" type="hidden" id="Field[]" value="KqSign">
              </TD>
              <TD align="center">
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>              </TD>
              <TD><select name=value[] id="value[]" style="width: 274px;">
                  <option selected  value="">全部</option>
                  <option value="1">考勤有效</option>
                  <option value="2">考勤参考</option>
                  <option value="3">考勤无效</option>
                </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
              </TD>
            </TR>
          </TBODY>
        </TABLE></td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>