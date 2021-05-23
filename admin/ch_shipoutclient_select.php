<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch1_shipmain
$DataIn.trade_object
$DataIn.ch8_shipmodel
$DataPublic.my2_bankinfo
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 客户库存查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.ch1_shipmain";
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<TR>
                  <TD width="102" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                    <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </TD>
                  <TD width="89" align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>                    </TD>
                  <TD width="367">
				  <select name=value[] id="value[]" style="width: 274px;">
                    <?php   
					$cSql = mysql_query("SELECT M.CompanyId,C.Forshort 
					FROM $DataIn.ch1_shipmain M 
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
					WHERE  M.Id IN (SELECT ShipId FROM $DataIn.ch1_shipout) 
					GROUP BY M.CompanyId ORDER BY C.Estate DESC,C.OrderBy DESC",$link_id);
					echo "<option value='' selected>全部</option>";
					if($cRow = mysql_fetch_array($cSql)){
						do{
							$CompanyId=$cRow["CompanyId"];
							$Forshort=$cRow["Forshort"];					
							echo "<option value='$CompanyId'>$Forshort</option>";
							}while ($cRow = mysql_fetch_array($cSql));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
				 <TR>
                  <TD align="right" >产品名称
                    <input name="Field[]" type="hidden" id="Field[]" value="cName">                  </TD>
                  <TD align="center" >
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right" >Product Code 
                    <input name="Field[]" type="hidden" id="Field[]" value="eCode">                  </TD>
                  <TD align="center" >
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="P">
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