<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 标签打印机信息查询");			
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=800;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
			   <tr>
                   <td align="right">楼层
                      <input name="Field[]" type="hidden" id="Field[]" value="Floor">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="10">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
			   
			    <tr>
                   <td align="right">公司地点
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="10">
                    <input name="table[]" type="hidden" id="table[]" value="W">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
	            <tr>
                   <td align="right">设备编号
                      <input name="Field[]" type="hidden" id="Field[]" value="Identifier">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="10">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
				<tr>
                   <td align="right">打印机名称
                      <input name="Field[]" type="hidden" id="Field[]" value="Name">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="10">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>

			   <tr>
               <td align="right">IP
                   <input name="Field[]" type="hidden" id="Field[]" value="IP">
                 </td>
                 <td align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			        
				</td>
                 <td>
					<INPUT name=value[] class=textfield id="value[]" size=48 maxlength="5">
				    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isStr" />
                </td>
                </tr>
				   <tr>
               <td align="right">端口号
                   <input name="Field[]" type="hidden" id="Field[]" value="Port">
                 </td>
                 <td align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			        
				</td>
                 <td>
					<INPUT name=value[] class=textfield id="value[]" size=48 maxlength="5">
				    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isStr" />
                </td>
                </tr>
				
			  </TBODY>
	    </TABLE>
	  </td>
	</tr>
</table>

<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>