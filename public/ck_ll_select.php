<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 领料记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";
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
                <TD align="right">领料日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Received">
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
                  </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=22 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=22 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="S">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
		      </TR>
			  <TR>
                <TD align="right">领 料 人
                    <input name="Field[]" type="hidden" id="Field[]" value="Receiver">
                </TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <option value="!=">!=</option>
                    </SELECT>                </TD>
                <TD><select name=value[] id="value[]" style="width: 370px;">
                    <?php 
					$result = mysql_query("SELECT L.Receiver,M.Name 
					FROM $DataIn.ck5_llsheet L
					LEFT JOIN $DataPublic.staffmain M  ON L.Receiver=M.Number
					WHERE 1 AND L.Receiver>0 AND L.Receiver<50000 GROUP BY L.Receiver ORDER BY L.Receiver",$link_id);
					echo "<option value='' selected>全部</option>";
					while ($myrow = mysql_fetch_array($result)){
						$Receiver=$myrow["Receiver"];
						$Name=$myrow["Name"];
						echo"<option value='$Receiver'>$Name</option>";
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
		      
			  <TR>
                <TD align="right">生产车间
                    <input name="Field[]" type="hidden" id="Field[]" value="WorkShopId">
                </TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <option value="!=">!=</option>
                    </SELECT>                </TD>
                <TD><select name=value[] id="value[]" style="width: 370px;">
                    <?php 
					$result = mysql_query("SELECT SC.WorkShopId,W.Name 
					FROM $DataIn.ck5_llsheet S
					LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                    LEFT JOIN $DataIn.workshopdata W ON W.Id = SC.WorkShopId
					WHERE 1 AND SC.WorkShopId>0 GROUP BY SC.WorkShopId ",$link_id);
					echo "<option value='' selected>全部</option>";
					while ($myrow = mysql_fetch_array($result)){
						$WorkShopId=$myrow["WorkShopId"];
						$Name=$myrow["Name"];
						echo"<option value='$WorkShopId'>$Name</option>";
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="SC">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>   
		      

              <TBODY>
				<TR>
                  <TD width="104" align="right">配 件 ID 
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffId">
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
                    </SELECT>                  </TD>
                  <TD width="364"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">需求单流水号
                      <input name="Field[]" type="hidden" id="Field[]" value="StockId">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
                </TR>
                <TR>
                  <TD align="right">工单流水号
                      <input name="Field[]" type="hidden" id="Field[]" value="sPOrderId">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
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