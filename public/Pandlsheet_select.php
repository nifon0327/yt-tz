<?php 
//电信---yang 20120801
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 损益表项目查询");			//需处理
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
			  <TR>
                <TD>主项目类型： 
                   <input name="Field[]" type="hidden" id="Field[]" value="Mid"></td>
                  <td>
                   <SELECT name=fun[] id="fun[]" style="width: 60px;">
				      
                       <option value== selected>=</option>
                       <option value=!=>!=</option>
                    </SELECT>                  
				   </td>
                 <td><select name=value[] id="value[]" style="width:380px">
                     <option value="" selected>请选择</option>
           <?php 
           $checkSql=mysql_query("SELECT Id,ItemName FROM $DataPublic.sys8_pandlmain WHERE 1 GROUP BY SortId",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					do{
						$Mid=$checkRow["Id"];
						$BigName=$checkRow["ItemName"];
						echo"<option value='$Mid'>$BigName</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}
		    ?>
            </select>
               <input name="table[]" type="hidden" id="table[]" value="A">
               <input name="types[]" type="hidden" id="types[]" value="isNum">
			   
               </td>
              </TR>
              <TBODY>
			<TR>
                  <TD width="95">项目名称：
                    <input name="Field[]" type="hidden" id="Field[]" value="ItemName"></TD>
                    <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
            </TR>
                

                <TR>
                  <TD><p>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：
                      <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </p>
                  </TD>
                  <TD>
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
				  <TR>
                  <TD>对&nbsp;&nbsp;应&nbsp;&nbsp;表：
                    <input name="Field[]" type="hidden" id="Field[]" value="TableName"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
                </TR>
                <TR>
                  <TD>参&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数：
                    <input name="Field[]" type="hidden" id="Field[]" value="Parameters"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
                </TR>
                <TR>
                  <TD>明&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;细：
                    <input name="Field[]" type="hidden" id="Field[]" value="AjaxView"></TD>
                  <TD>
                  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 380px;">
                    <option selected  value="">全部</option>
                    <option value="1">显示</option>
                    <option value="0">不显示</option>
                  </select>
                  </TD>
                  <TD>
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
                </TR>
                <TR>
                  <TD>Ajax&nbsp;&nbsp;No：
                    <input name="Field[]" type="hidden" id="Field[]" value="AjaxNo"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
                </TR>
                
                <TR>
                  <TD>可用状态：
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  <TD>
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 380px;">
                    <option selected  value="">全部</option>
                    <option value="1">可用</option>
                    <option value="0">不可用</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                          <TR>
                  <TD>行政项目：
                    <input name="Field[]" type="hidden" id="Field[]" value="Sign"></TD>
                  <TD>
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><select name=value[] id="value[]" style="width: 380px;">
                    <option selected  value="">全部</option>
                    <option value="1">是</option>
                    <option value="0">否</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>更新日期：
                    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD>
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
                    </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="A">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD>操&nbsp;&nbsp;作&nbsp;&nbsp;员：
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD>
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </div></TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 380px;">
				  	<option value="" selected>全部</option>
                    <?php 
					$CheckTb="$DataPublic.sys8_pandlsheet";
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="A">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>锁定状态：
                  <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
                  <TD>
					  
					    <select name="fun[]" id="fun[]" style="width: 60px;">
					      <option value="=">=</option>
				        </select>
				        </div></TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 380px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="A">
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