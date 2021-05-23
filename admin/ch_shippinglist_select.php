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
ChangeWtitle("$SubCompany 出货资料查询");			//需处理
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
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId GROUP BY M.CompanyId ORDER BY C.Estate DESC,C.OrderBy DESC",$link_id);
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
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                	<TR>
                  <TD align="right">客户ID
                      <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
   
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
			    </TR>
			    
				<TR>
                  <TD align="right">出货日期
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
			    </TR>
                <TR>
                  <TD align="right"><p>出货单号
                    <input name="Field[]" type="hidden" id="Field[]" value="Number">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                
  <TR>
                  <TD align="right">报关
                    <input name="Field[]" type="hidden" id="Field[]" value="Type">
                  </TD>
                  <TD align="center">
				    <select name="fun[]" id="fun[]" style="width: 60px;">
			          <option value="=">=</option>
		            </select>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">未报关</option>
						<option value="1">已报关</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="T">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
               
                <TR>
                  <TD align="right"><p>InvoiceNO
                      <input name="Field[]" type="hidden" id="Field[]" value="InvoiceNO">
                  </p></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>			
<TR>
                  <TD align="right"><p>货运信息
                    <input name="Field[]" type="hidden" id="Field[]" value="Wise">
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
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
  <TD align="right">收款标记
          <input name="Field[]" type="hidden" id="Field[]" value="Sign">
  </TD>
  <TD align="center">
    <SELECT name=fun[] id="fun[]" style="width: 60px;">
        <OPTION value== selected>=</OPTION>
        <OPTION 
          value=!=>!=</OPTION>
    </SELECT>  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
      <option selected  value="">全部</option>
      <option value="1">需收款</option>
      <option value="0">不收款</option>
      <option value="-1">退款</option>
    </select>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right">文档模板
          <input name="Field[]" type="hidden" id="Field[]" value="ModelId">
  </TD>
  <TD align="center">
    <SELECT name=fun[] id="fun[]" style="width: 60px;">
        <OPTION value== selected>=</OPTION>
        <OPTION 
          value=!=>!=</OPTION>
    </SELECT>  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
      <option selected  value="">全部</option>	  
		<?php   
		$modelSql = mysql_query("SELECT M.ModelId,D.Title 
		FROM $DataIn.ch1_shipmain M 
		LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId GROUP BY M.ModelId ORDER BY M.ModelId",$link_id);
		echo "<option value='' selected>全部</option>";
		if($modelRow = mysql_fetch_array($modelSql)){
			do{
				$ModelId=$modelRow["ModelId"];
				$Title=$modelRow["Title"];
				echo"<option value='$ModelId'>$Title</option>";
				}while($modelRow = mysql_fetch_array($modelSql));
			}
		?>
    </select>
      <input name="table[]" type="hidden" id="table[]" value="M">
      <input name="types[]" type="hidden" id="types[]" value="isNum" />
  </TD>
</TR>					               
<TR>
            <TD align="right">收款帐号
              <input name="Field[]" type="hidden" id="Field[]" value="BankId">
            </TD>
            <TD align="center">
              <SELECT name=fun[] id="fun[]" style="width: 60px;">
                <OPTION value== selected>=</OPTION>
                <OPTION 
          value=!=>!=</OPTION>
              </SELECT>            </TD>
            <TD><select name=value[] id="value[]" style="width: 274px;">
                <?php   
				$bankSql = mysql_query("SELECT M.BankId,B.Title 
				FROM $DataIn.ch1_shipmain M 
				LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId GROUP BY M.BankId ORDER BY M.BankId",$link_id);
				echo "<option value='' selected>全部</option>";
				if($bankRow = mysql_fetch_array($bankSql)){
					do{
						$BankId=$bankRow["BankId"];
						$Title=$bankRow["Title"];
						echo"<option value='$BankId'>$Title</option>";
						}while($bankRow = mysql_fetch_array($bankSql));
					}
				?>			
                </select>
              <input name="table[]" type="hidden" id="table[]" value="M">
              <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
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
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="M">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">锁定状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Locks">
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
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="M">
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