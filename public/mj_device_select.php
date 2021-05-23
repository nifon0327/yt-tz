<?php 
//代码、数据共享-EWEN 2012-09-17
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 门禁设备查询");			//需处理
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
                  <TD width="89">门禁名称
                      <input name="Field[]" type="hidden" id="Field[]" value="DoorName">
                  </TD>
                  <TD width="126" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="343"><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
			<TR>
                  <TD>门禁地址
                      <input name="Field[]" type="hidden" id="Field[]" value="DoorAdd">
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
				</TD>
                </TR>
				 <TR>
                  <TD>控制器IP
                      <input name="Field[]" type="hidden" id="Field[]" value="IP">
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:380px;">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
				</TD>
                </TR>
				 <tr>
				   <td>控制开关
				     <input name="Field[]2" type="hidden" id="Field[]2" value="OrderId" /></td>
				   <td align="center"><select name="fun[]2" id="fun[]2" style="width: 60px;">
				     <option value="=" 
          selected="selected">=</option>
				     <option value="&gt;">&gt;</option>
				     <option 
          value="&gt;=">&gt;=</option>
				     <option value="&lt;">&lt;</option>
				     <option 
          value="&lt;=">&lt;=</option>
				     <option value="!=">!=</option>
				     </select></td>
				   <td><select name="value[]2" id="value[]2" style="width:380px;">
				     <option selected="selected"  value="">全部</option>
				    <?php 
                    $checkKGSql=mysql_query("SELECT Id,Name FROM $DataPublic.accessguard_order WHERE Estate='1' ORDER BY Id",$link_id);
					if($checkKGRow=mysql_fetch_array($checkKGSql)){
						do{
							echo"<option value='$checkKGRow[Id]'>$checkKGRow[Name]</option>";
							}while($checkKGRow=mysql_fetch_array($checkKGSql));
						}
					?>
				     </select>
				     <input name="table[]2" type="hidden" id="table[]2" value="A" />
			       <input name="types[]2" type="hidden" id="types[]2"
                value="isNum" /></td>
			    </tr>               
                <TR>
                  <TD>可用状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
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
                  </SELECT></TD>
                  <TD><select name=value[] id="value[]" style="width:380px;">
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
                  <TD>更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
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
                  <TD><INPUT name=value[] class=textfield id="value[]" style="width:180px;" onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" style="width:180px;" onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="A">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD>操 作 员
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD>
				  <select name=value[] id="value[]" style="width:380px;">
				  		<option value="" selected>全部</option>
                     	<?php 
					 	 $CheckTb="$DataPublic.accessguard_door";
						include "../model/subprogram/select_model_stafflist.php";
						?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="A">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD>锁定状态
                  <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
                  <TD align="center">
					  <select name="fun[]" id="fun[]" style="width: 60px;">
						<option value="=">=</option>
					  </select>
				  </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width:380px;">
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