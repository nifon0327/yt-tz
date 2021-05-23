<?php 
//步骤1 $DataIn.development/$DataIn.trade_object 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：

include "../model/subprogram/s2_model_2.php";

//echo "$Parameter";
//步骤3：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>	
              <!--
			  <TR>
                <TD align="right">采购单号
                    <input name="Field[]" type="hidden" id="Field[]" value="PurchaseID">
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
                <TD><input name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
		      </TR>
              --> 
			  <TR>
                <TD align="right"><p>需求流水号
                        <input name="Field[]" type="hidden" id="Field[]" value="StockId">
                </p></TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
		      </TR>
              
              
 <TR>
  <TD align="right">配件ID
          <input name="Field[]" type="hidden" id="Field[]" value="StuffId">
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
      </SELECT>  </TD>
  <TD>      <input name=value[] class=textfield id="value[]" size=48>
    <input name="table[]" type="hidden" id="table[]" value="A">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>配件名称
      <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
  </p></TD>
  <TD align="center">
    <SELECT name=fun[] id="fun[]" style="width: 60px;">
        <option value="LIKE" selected>包含</option>
        <OPTION value==>=</OPTION>
        <OPTION 
          value=!=>!=</OPTION>
    </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="A">
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
//步骤4：
include "../model/subprogram/s2_model_4.php";
?>