<?php 
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 车间小组查询");			//需处理
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
                <TD width="105" align="right">生产日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                <TD width="87" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                <TD width="366"><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
		      </TR>
              <TBODY>
                <TR>
                  <TD align="right">生产小组
                    <input name="Field[]" type="hidden" id="Field[]" value="GroupId">
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
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <?php 
					$checkSql=mysql_query("SELECT G.GroupId,G.GroupName,M.Name FROM 
				 	$DataIn.staffgroup G
					LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
					ORDER BY G.GroupId",$link_id);
					if($checkRow=mysql_fetch_array($checkSql)){
						do{
							echo" <option value='$checkRow[GroupId]'>$checkRow[GroupName] - $checkRow[Name]</option>";
							}while($checkRow=mysql_fetch_array($checkSql));
						}
					?>
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