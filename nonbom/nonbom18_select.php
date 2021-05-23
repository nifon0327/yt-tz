<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 非BOM资产保养记录查询");			//需处理
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
                    <input name="table[]" type="hidden" id="table[]" value="A">
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
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>

                            <TR>
                  <TD  align="right">资产条码
                      <input name="Field[]" type="hidden" id="Field[]" value="BarCode">
                  </TD>
                  <TD  align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>

                            <TR>
                  <TD align="right">资产编号
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsNum">
                  </TD>
                  <TD  align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
					</TD>
                </TR>
                                <TR>
                  <TD align="right">保养日期
                          <input name="Field[]" type="hidden" id="Field[]" value="ByDate">                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="382"><INPUT name=value[] class=textfield id="value[]" size="23" onfocus="WdatePicker()" readonly>
                    至
                      <INPUT name=DateArray[] class=textfield id="DateArray[]" size="22" onfocus="WdatePicker()" readonly>
                      <input name="table[]" type="hidden" id="table[]" value="D">
                      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>


            <TR>
                  <TD align="right">内部保养人:
                          <input name="Field[]" type="hidden" id="Field[]" value="ByNumber">
                  </TD>
                  <TD><div align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				  		<option value="" selected>全部</option>
                     	<?php 
                         $ByNumResult=mysql_query("SELECT  M.Name,M.Number FROM $DataIn.nonbom7_care  D  LEFT JOIN $DataPublic.staffmain M  ON M.Number=D.ByNumber WHERE 1 AND ByNumber>0  GROUP BY D.ByNumber",$link_id);
                       while($ByNumRow=mysql_fetch_array($ByNumResult)){
                                      $thisNumber=$ByNumRow["Number"];
                                      $thisName=$ByNumRow["Name"];
                                      echo "<option value='$thisNumber'>$thisName</option>";
                                 }
						?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="D">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>


            <TR>
                  <TD align="right">外部保养公司:
                          <input name="Field[]" type="hidden" id="Field[]" value="ByCompanyId">
                  </TD>
                  <TD><div align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				  		<option value="" selected>全部</option>
                     	<?php 
                                             $ByCompanyResult=mysql_query("SELECT  M.Forshort,M.CompanyId FROM $DataIn.nonbom7_care  D  LEFT JOIN $DataPublic.nonbom3_retailermain M  ON M.CompanyId=D.ByCompanyId WHERE 1 AND ByCompanyId>0  GROUP BY D.ByCompanyId",$link_id);
                       while($ByCompanyRow=mysql_fetch_array($ByCompanyResult)){
                                      $thisCompanyId=$ByCompanyRow["CompanyId"];
                                      $thisForshort=$ByCompanyRow["Forshort"];
                                      echo "<option value='$thisNumber'>$thisForshort</option>";
                                 }
						?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="D">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
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