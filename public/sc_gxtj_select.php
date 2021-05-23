<?php 
/*
已更新电信---yang 20120801
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 车间工序登记记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,scType,$TypeId";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.sc1_gxtj";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
			  <TR>
                <TD align="right">工序类型
                  <input name="Field[]" type="hidden" id="Field[]" value="ProcessId">
                </TD>
                <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT>
                </TD>
                <TD><select name=value[] id="value[]" style="width: 274px;">
				<option value="" selected>全部</option>
                   	<?php 
					$typeResult = mysql_query("SELECT ProcessId,ProcessName  FROM $DataIn.process_data WHERE 1 AND Estate=1 ORDER BY Id",$link_id);
					if ($typeRow = mysql_fetch_array($typeResult)){
						do{
							$typeValue=$typeRow["ProcessId"];
							$TypeName=$typeRow["ProcessName"];
							echo"<option value='$typeValue'>$TypeName</option>";
							}while($typeRow = mysql_fetch_array($typeResult));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="T">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
			  <TR>
                <TD align="right">加工日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">
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
                <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="D">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                </TD>
		      </TR>
			  <TR>
                <TD align="right">登记数量
                  <input name="Field[]" type="hidden" id="Field[]" value="Qty">
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
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
              <TBODY>
                 <TR>
                  <TD align="right">工单流水号
                    <input name="Field[]" type="hidden" id="Field[]" value="sPOrderId">
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
                      <input name="table[]" type="hidden" id="table[]" value="D">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">产品名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
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
                      <input name="table[]" type="hidden" id="table[]" value="OD">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                	
                
                
                <TR>
                  <TD align="right">加工状态
                      <input name="Field[]" type="hidden" id="Field[]" value="scFrom">
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                      <option selected  value="">全部</option>
					  <option value="1">未生产</option>
					  <option value="2">生产中</option>
                      <option value="0">已生产</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>				               
                <TR>
                  <TD align="right">订单状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="0">已出货</option>
                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">登 记 人
                    <input name="Field[]" type="hidden" id="Field[]" value="Leader">
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
					$CheckSql = mysql_query("SELECT T.Leader,M.Name FROM $CheckTb T LEFT JOIN 
					$DataPublic.staffmain M ON M.Number=T.Leader WHERE 1 GROUP BY T.Leader ORDER BY T.Leader",$link_id);
					if($CheckRow=mysql_fetch_array($CheckSql)){
						do{
							$Leader=$CheckRow["Leader"];
							$Name=$CheckRow["Name"];
							echo "<option value='$Leader'>$Name</option>";
							}while($CheckRow=mysql_fetch_array($CheckSql));
						}
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="D">                  
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