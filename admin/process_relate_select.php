<?php 
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 半成品配件加工工序关联表查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ProductType,$ProductType,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<TABLE width="590" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="127" align="right">半成品ID号
                    <input name="Field[]" type="hidden" id="Field[]" value="mStuffId">
                  </TD>
                  <TD width="72" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="377" ><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="B">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right" >半成品名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </TD>
                  <TD align="center" ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
               
                <TR>
                  <TD align="right" >配件分类
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
                  </TD>
                  <TD align="center" ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD ><select name="value[]" id="value[]" style="width: 274px;">
                    <?php 
					echo"<option value='' selected>全部</option>";
					$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName
	FROM $DataIn.semifinished_bom B 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId = B.mStuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
	WHERE 1 GROUP BY T.TypeId ",$link_id);
					while ($myrow = mysql_fetch_array($result)){
						$Letter=$myrow["Letter"];
						$TypeId=$myrow["TypeId"];
						$TypeName=$myrow["TypeName"];
						echo "<option value='$TypeId'>$Letter-$TypeName</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
              
              </TBODY>
	    </TABLE>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>