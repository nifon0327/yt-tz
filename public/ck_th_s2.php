<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
$Parameter.=",Jid,$Jid";
//步骤3：需处理
$CheckTb="$DataIn.stuffdata";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
		  <TABLE width="600" border=0 align="center">
              <TBODY>
			  	 <?php 
				 if($uType==""){
				 ?>
				<TR>
                  <TD width="86" align="right">配件分类
                  <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD width="86" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
				  </TD>
                  <TD width="414"><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//员工资料表
					$typeSql = "SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype order by Letter";
					$typeResult = mysql_query($typeSql); 
					echo "<option value='' selected>全部</option>";
					while ( $typeMyrow = mysql_fetch_array($typeResult)){
						$typeId=$typeMyrow["TypeId"];
						$typrName=$typeMyrow["TypeName"];
						$typrLetter=$typeMyrow["Letter"];
						echo "<option value='$typeId'>$typrLetter-$typrName</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
              <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				</TD>
                </TR>
				<?php 
				}
				?>
				<TR>
                  <TD width="86" align="right">配&nbsp;件 ID 
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffId"></TD>
                  <TD width="86" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="414"><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
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
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">规&nbsp;&nbsp;&nbsp;&nbsp;格
                  <input name="Field[]" type="hidden" id="Field[]" value="Spec"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right" valign="top">参考买价
                  <input name="Field[]" type="hidden" id="Field[]" value="Price"></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注
                  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
              </TBODY>
      </TABLE>
	</td></tr></table>
<?php 
include "../model/subprogram/s2_model_4.php";
?>
