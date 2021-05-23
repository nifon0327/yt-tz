<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.currencydata
$DataPublic.staffmain
$DataPublic.adminitype
$DataIn.hzqksheet
二合一已更新
*/

include "../model/modelhead.php";
//步骤2：
/*ChangeWtitle("$SubCompany 行政费用查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;*/
//步骤3：
include "../model/subprogram/s2_model_2.php";
$Parameter.=",Bid,$Bid";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="692" border=0 align="center">
              <TBODY>
                  
                <TR>
                  <TD align="right" valign="top">请款日期
                          <input name="Field[]" type="hidden" id="Field[]" value="Date">
                  </TD>
                  <TD valign="top"><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD width="125" align="right">请款金额
                      <input name="Field[]" type="hidden" id="Field[]" value="Amount">
                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">货币符号
                      <input name="Field[]" type="hidden" id="Field[]" value="Currency">
                  </TD>
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
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option>
					<?php 
					$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
					if($cRow = mysql_fetch_array($cResult)){
						do{
							echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
							}while ($cRow = mysql_fetch_array($cResult));
						}
					?>
                  </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD width="125" align="right">请款说明
                          <input name="Field[]" type="hidden" id="Field[]" value="Content">
                  </TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                <TR>
                  <TD align="right">费用分类
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
                  </TD>
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
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option>
					<?php 
					$result = mysql_query("SELECT * FROM $DataPublic.adminitype T WHERE T.Estate=1 and (T.TypeId='610' or T.TypeId='624' or T.TypeId='621') order by Id",$link_id);
					if($myrow = mysql_fetch_array($result)){
						do{
							echo"<option value='$myrow[TypeId]'>$myrow[Name]</option>";//
							} while ($myrow = mysql_fetch_array($result));
						}
						?>
                  </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
              

                <TR>
                  <TD align="right">请 款 人
                          <input name="Field[]" type="hidden" id="Field[]" value="Operator">
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
					 	 $CheckTb="$DataIn.hzqksheet";
						include "../model/subprogram/select_model_stafflist.php";
						?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
		
              </TBODY>
            </TABLE></td>
	</tr>
</table>
<?php 
//步骤5：
//include "../model/subprogram/select_model_b.php";
include "../model/subprogram/s2_model_4.php";
?>