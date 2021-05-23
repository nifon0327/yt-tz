<?php 
//EWEN 2013-02-20 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非BOM配件条码(固定资产)查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataPublic.nonbom4_goodsdata";
$SelectFrom=4;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="95" align="right">非bom配件编号
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
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				  </TD>
                </TR>
                                <TR>
                  <TD width="95" align="right">非bom配件名称
                      <input name="Field[]" type="hidden" id="Field[]" value="GoodsName">
                  </TD>
                  <TD width="81" align="center">
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
                  <TD align="right">非bom配件分类
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD align="center"><select name="fun[]6" id="fun[]6" style="width: 60px;">
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
                  <TD>
					<?php 
					include "../model/subselect/GoodType.php";
					?>
                  <input name="table[]" type="hidden" id="table[]" value="C">                  
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