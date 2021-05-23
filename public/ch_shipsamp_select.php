<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch5_sampsheet
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 随货样品查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.ch5_sampsheet";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			  <input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
			<TABLE width="572" border=0 align="center">
			  <TR>
                <TD width="89" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                </TD>
                <TD width="100" align="center">
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
                <TD width="369"><select name=value[] id="value[]" style="width: 274px;">
                  <?php 
					$ppResult =mysql_query("SELECT S.CompanyId,C.Forshort FROM $DataIn.ch5_sampsheet S LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId WHERE 1 GROUP BY S.CompanyId ORDER BY S.CompanyId",$link_id);
					echo "<option value='' selected>全部</option>";
					while($ppMyrow = mysql_fetch_array($ppResult)){
						$CompanyId=$ppMyrow["CompanyId"];
						$Forshort=$ppMyrow["Forshort"];					
						echo "<option value='$CompanyId'>$Forshort</option>";
						} 
					?>
                </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
			  <TR>
                <TD align="right">样 品 ID
                    <input name="Field[]" type="hidden" id="Field[]" value="SampId">
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
                <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
			  <TR>
                <TD align="right">中文名称
                  <input name="Field[]" type="hidden" id="Field[]" value="SampName">
                </TD>
                <TD align="center">
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
		      </TR>
              <TBODY>
                <TR>
                  <TD align="right"><p>英文注释
                      <input name="Field[]" type="hidden" id="Field[]" value="Description">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">数&nbsp;&nbsp;&nbsp;&nbsp;量
                    <input name="Field[]" type="hidden" id="Field[]" value="Qty">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>				  
                </TR>
                <TR>
                  <TD align="right">单&nbsp;&nbsp;&nbsp;&nbsp;价
                      <input name="Field[]" type="hidden" id="Field[]" value="Price">
                  </TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
<TR>
                  <TD align="right">单品重量
                    <input name="Field[]" type="hidden" id="Field[]" value="Weight">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <INPUT name=value[] class=textfield id="value[]" size="48">
                  <input name="table[]" type="hidden" id="table[]" value="S">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
  <TD align="right" valign="top">更新日期
          <input name="Field[]" type="hidden" id="Field[]" value="Date">
  </TD>
  <TD align="center" valign="top">
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
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
    至
      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]" value="isDate">
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
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="S">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum">
			</TD>
                </TR>
<TR>
  <TD align="right">装箱设置
        <input name="Field[]" type="hidden" id="Field[]" value="Type">
  </TD>
  <TD align="center">
      <select name="fun[]" id="fun[]" style="width: 60px;">
          <option value="=" selected>=</option>
          <option value="!=">!=</option>
      </select>  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
      <option selected>全部</option>
      <option value="1">需要装箱</option>
      <option value="0">无需装箱</option>
    </select>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>				
				<TR>
                  <TD align="right">记录状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				    <option selected>全部</option>
				    <option value="0">已审核</option>
				    <option value="1">未审核</option>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="S">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
  <TD align="right">操作状态
          <input name="Field[]" type="hidden" id="Field[]" value="Locks">
  </TD>
  <TD align="center">
    <select name="fun[]" id="fun[]" style="width: 60px;">
          <option value="=" selected>=</option>
          <option value="!=">!=</option>
    </select>  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
    <option selected>全部</option>
    <option value="0">锁定</option>
    <option value="1">未锁定</option>
  </select>
    <input name="table[]" type="hidden" id="table[]" value="S">
    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
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