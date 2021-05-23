<?php   
/*
已更新电信---yang 20120801
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 加工工序资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,scType,$TypeId";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.process_data";
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
                  
		 <!-- <TR>
                <TD align="right">工序类型
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select> 
                  </TD>
                <TD> <select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php   
					$CheckSql = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' AND mainType='3' ORDER BY Letter",$link_id);
					if($CheckRow=mysql_fetch_array($CheckSql)){
						do{
							$TypeId=$CheckRow["TypeId"];
                                                        $Letter=$CheckRow["Letter"];
							$Name=$CheckRow["TypeName"];
							echo "<option value='$TypeId'>$Letter-$Name</option>";
							}while($CheckRow=mysql_fetch_array($CheckSql));
						}
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="S">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
             </TR>-->
              <TBODY>
              <TR>
                  <TD align="right">工序名称
                    <input name="Field[]" type="hidden" id="Field[]" value="ProcessName">
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
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>

	<TR>
                <TD align="right">单    价
                  <input name="Field[]" type="hidden" id="Field[]" value="Price">
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
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
    
                 <TR>
                  <TD align="right">工序说明
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
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
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                    
                  <TD align="right">登 记 人
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
					$CheckSql = mysql_query("SELECT M.Number,M.Name FROM $CheckTb T LEFT JOIN 
					$DataPublic.staffmain M ON M.Number=T.Operator WHERE 1 GROUP BY T.Operator ORDER BY T.Operator",$link_id);
					if($CheckRow=mysql_fetch_array($CheckSql)){
						do{
							$Number=$CheckRow["Number"];
							$Name=$CheckRow["Name"];
							echo "<option value='$Number'>$Name</option>";
							}while($CheckRow=mysql_fetch_array($CheckSql));
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