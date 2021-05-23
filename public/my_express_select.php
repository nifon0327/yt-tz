<?php 
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 我的快递单查询");			//需处理
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
				<TR>
                  <TD width="89" align="right">付款方式  
                    <input name="Field[]" type="hidden" id="Field[]" value="PayType"></TD>
                  <TD width="94" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="375">
				  <select name=value[] id="value[]" style="width: 274px;">
				 <option value="" selected>全部</option>
						<option value="1">寄付</option>
						<option value="0">到付</option>
                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>付款帐号
                      <input name="Field[]" type="hidden" id="Field[]" value="PayerNo">
                  </p>
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="E">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>			
<TR>
                  <TD align="right"><p>收 件 人
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
                  </p>
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
  <TD align="right"><p>收件公司
      <input name="Field[]" type="hidden" id="Field[]" value="Company">
  </p></TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <option value="LIKE" selected>包含</option>
    <OPTION value==>=</OPTION>
    <OPTION 
          value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>国家
      <input name="Field[]" type="hidden" id="Field[]" value="Country">
  </p></TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <option value="LIKE" selected>包含</option>
    <OPTION value==>=</OPTION>
    <OPTION 
          value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>邮政编码
      <input name="Field[]" type="hidden" id="Field[]" value="ZIP">
  </p></TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <option value="LIKE" selected>包含</option>
    <OPTION value==>=</OPTION>
    <OPTION 
          value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>地址
    <input name="Field[]" type="hidden" id="Field[]" value="Address">
  </p></TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <option value="LIKE" selected>包含</option>
    <OPTION value==>=</OPTION>
    <OPTION 
          value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>电话传真
      <input name="Field[]" type="hidden" id="Field[]" value="Tel">
  </p></TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <option value="LIKE" selected>包含</option>
    <OPTION value==>=</OPTION>
    <OPTION 
          value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>手机号码
      <input name="Field[]" type="hidden" id="Field[]" value="Mobil">
  </p></TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <option value="LIKE" selected>包含</option>
    <OPTION value==>=</OPTION>
    <OPTION 
          value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>托寄件数
      <input name="Field[]" type="hidden" id="Field[]" value="Pieces">
  </p></TD>
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
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="E">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>托寄内容
    <input name="Field[]" type="hidden" id="Field[]" value="Contents">
  </p></TD>
  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
    <option value="LIKE" selected>包含</option>
    <OPTION value==>=</OPTION>
    <OPTION 
          value=!=>!=</OPTION>
  </SELECT>
  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="E">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>					               
<TR>
            <TD align="right">托寄状态
              <input name="Field[]" type="hidden" id="Field[]" value="Estate">            </TD>
            <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                <OPTION value== 
          selected>=</OPTION>
            </SELECT></TD>
            <TD><select name=value[] id="value[]" style="width: 274px;">
                <option selected  value="">全部</option>
                <option value="1">未寄出</option>
                <option value="0">已寄出</option>
                                </select>
              <input name="table[]" type="hidden" id="table[]" value="E">
              <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">                  </TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="E">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD align="right">锁定状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Locks">                  </TD>
                  <TD align="center">
					  <select name="fun[]" id="fun[]" style="width: 60px;">
						<option value="=">=</option>
					  </select>
				  </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
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