<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 非BOM个人退回记录查询");			//需处理
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
			<TABLE width="612" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="135" align="right">非bom配件编号
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsId">
                  </TD>
                  <TD width="81" align="center"><select name="fun[]2" id="fun[]2" style="width: 60px;">
                    <option value="=" 
          selected="selected">=</option>
                    <option value="&gt;">&gt;</option>
                    <option 
          value="&gt;=">&gt;=</option>
                    <option value="&lt;">&lt;</option>
                    <option 
          value="&lt;=">&lt;=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				  </TD>
                </TR>
             <TR>
                  <TD  align="right">非bom配件名称
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsName">
                  </TD>
                  <TD  align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]" value="isStr" />
					</TD>
                </TR>
   <TR>
                  <TD align="right">退回时间
                      <input name="Field[]" type="hidden" id="Field[]" value="Date">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width: 180px;" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>

<TR>
                  <TD align="right">退回人员
                    <input name="Field[]" type="hidden" id="Field[]" value="BackNumber">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 380px;">
				  	<option value="" selected>全部</option>
                    <?php 
                        $CheckOutResult=mysql_query("SELECT M.Number,M.Name FROM $DataIn.nonbom8_reback  B
                         LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BackNumber WHERE M.Estate=1",$link_id);
                       while($CheckOutRow=mysql_fetch_array($CheckOutResult)){
                              $thisOutNumber=$CheckOutRow["Number"];
                              $thisOutName=$CheckOutRow["Name"];
                               echo "<option value='$thisOutNumber'>$thisOutName</option>";
                           }
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="A">                  
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
include "../model/subprogram/select_model_b.php";
?>