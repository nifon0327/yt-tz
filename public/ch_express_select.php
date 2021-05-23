<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch9_expsheet			
$DataPublic.freightdata
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 快递资料查询");			//需处理
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
              <TBODY>
                  <?php 
				//如果是来自于财务查询
				if($fromWebPage==$funFrom."_cw" && $Estate==0){
				?>
                  <TD><div align="right">结付编号
                          <input name="Field[]" type="hidden" id="Field[]" value="Id">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value==  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付日期
                          <input name="Field[]" type="hidden" id="Field[]" value="PayDate">
                  </div></TD>
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
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
                    至
                      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付总额
                          <input name="Field[]" type="hidden" id="Field[]" value="PayAmount">
                  </div></TD>
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
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付凭证
                          <input name="Field[]" type="hidden" id="Field[]" value="Payee">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付回执
                          <input name="Field[]" type="hidden" id="Field[]" value="Receipt">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">对 帐 单
                          <input name="Field[]" type="hidden" id="Field[]" value="Checksheet">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付备注
                          <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                <TR>
                  <TD colspan="3">&nbsp;</TD>
                </TR>
                <?php 
			}
			?>
				<TR>
                  <TD width="102" align="right"><p>快递公司
                      <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </p>
                  </TD>
                  <TD width="94" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="362"><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					$cSql = mysql_query("SELECT F.CompanyId,D.Forshort 
					FROM $DataIn.ch9_expsheet F					
					LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 					
					GROUP BY F.CompanyId ORDER BY D.Estate DESC",$link_id);
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
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
  <TD align="right">寄件日期
            <input name="Field[]" type="hidden" id="Field[]" value="Date">
  </TD>
  <TD align="center">
      <SELECT name=fun[] id="fun[]" style="width: 60px;">
          <OPTION value== selected>=</OPTION>
          <OPTION 
          value=!=>!=</OPTION>
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="E">
      <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
</TR>
<TR>
  <TD align="right">提单号码
        <input name="Field[]" type="hidden" id="Field[]" value="ExpressNO">
  </TD>
  <TD align="center">
    <SELECT name=fun[] id="fun[]" style="width: 60px;">
        <OPTION value== selected>=</OPTION>
        <OPTION 
          value=!=>!=</OPTION>
    </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="E">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>					               
<TR>
            <TD align="right">件&nbsp;&nbsp;&nbsp;&nbsp;数
              <input name="Field[]" type="hidden" id="Field[]" value="BoxQty">
            </TD>
            <TD align="center">
              <SELECT name=fun[] id="fun[]" style="width: 60px;">
                <OPTION value== selected>=</OPTION>
                <OPTION 
          value=!=>!=</OPTION>
              </SELECT>            </TD>
            <TD><INPUT name=value[] class=textfield id="value[]" size=48>
              <input name="table[]" type="hidden" id="table[]" value="E">
              <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">重&nbsp;&nbsp;&nbsp;&nbsp;量
                    <input name="Field[]" type="hidden" id="Field[]" value="Weight">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <INPUT name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="E">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">金&nbsp;&nbsp;&nbsp;&nbsp;额
                    <input name="Field[]" type="hidden" id="Field[]" value="Amount">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">类&nbsp;&nbsp;&nbsp;&nbsp;别
                    <input name="Field[]" type="hidden" id="Field[]" value="Type">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>经 手 人
                      <input name="Field[]" type="hidden" id="Field[]" value="HandledBy">
                  </p></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
				$PD_Sql = "SELECT E.HandledBy,A.Name FROM 
				$DataIn.ch9_expsheet E LEFT JOIN $DataPublic.staffmain A ON E.HandledBy=A.Number GROUP BY E.HandledBy ORDER BY E.HandledBy";
				$PD_Result = mysql_query($PD_Sql); 
				echo "<option value='' selected>全部</option>";
				while($PD_Myrow = mysql_fetch_array($PD_Result)){
					$HandledBy=$PD_Myrow["HandledBy"];
					$PD_StuffCname=$PD_Myrow["Name"];					
						echo "<option value='$HandledBy'>$PD_StuffCname</option>";
					} 				
					?>
                  </select>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="E">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">结付状态
                      <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="1">未请款</option>
						<option value="2">请款中</option>
						<option value="3">准备结付</option>
						<option value="0">已结付</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="E">
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