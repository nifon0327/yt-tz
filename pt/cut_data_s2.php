<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
/*
$DataIn.stufftype
$DataIn.staffmain
$DataIn.stuffdata
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
$Parameter.=",Bid,$Bid";
//步骤3：需处理
$CheckTb="$DataIn.pt_cut_data";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
		  <TABLE width="600" border=0 align="center">
              <TBODY>
			
				<TR>
                  <TD width="86" align="right">刀模分类
                  <input name="Field[]" type="hidden" id="Field[]" value="cutSign"></TD>
                  <TD width="86" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
				  </TD>
                  <TD width="414"><select name=value[] id="value[]" style="width: 274px;">
                   <option value="" selected="selected">请选择</option>
				   <option value="1" >刀模</option>
				   <option value="2" >复啤刀模</option>
				   <option value="3" >atom</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				</TD>
                </TR>
			
				
                <TR>
                  <TD align="right"><p>刀模名称
                    <input name="Field[]" type="hidden" id="Field[]" value="CutName">
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
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
				<TR>
                  <TD align="right" valign="top">更新日期
                  <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD align="center" valign="top"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
  <input name="table[]" type="hidden" id="table[]" value="C">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD>
				    <select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
                    </select>
				  <input name="table[]" type="hidden" id="table[]" value="C">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum">
				  </TD>
                </TR>
               
              </TBODY>
      </TABLE>
	</td></tr></table>
<?php 
include "../model/subprogram/s2_model_4.php";
?>
