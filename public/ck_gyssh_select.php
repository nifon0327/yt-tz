<?php 
//电信-zxq 2012-08-01
/*
$DataIn.stufftype
$DataIn.stuffdata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 查询条件");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,StuffType,$StuffType,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.stuffdata";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td  class="A0011">
			  <TABLE width="600" border=0 align="center">
              <TBODY>
                <tr>

                  <TD>供 应 商
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				    <option value="">全部</option>
                    <?php 
					$checkSql = "SELECT P.CompanyId,P.Forshort,P.Letter FROM $DataIn.trade_object P,$DataIn.bps B WHERE B.CompanyId=P.CompanyId GROUP BY B.CompanyId ORDER BY P.Letter";
					$checkResult = mysql_query($checkSql); 
					while ( $checkRow = mysql_fetch_array($checkResult)){
						$CompanyId=$checkRow["CompanyId"];
						$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
						echo "<option value='$CompanyId'>$Forshort</option>";
						} 
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                
				<TR>
                  <TD><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>                
                 
              </TBODY>
	    </TABLE>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>