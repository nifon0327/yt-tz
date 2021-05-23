<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 刀模资料查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,NewSign,$NewSign";
$tableMenuS=500;
$tableWidth=800;
//步骤3：
include "../model/subprogram/select_model_t.php";

$NewSignSTR="NewSign".$NewSign;
$$NewSignSTR="selected";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
              <tr>
                   <td align="right">标识
                      <input name="Field[]" type="hidden" id="Field[]" value="NewSign">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value==>=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><select name="value[]" id="value[]" style="width:350px">
                  <option value="0" <?php echo $NewSign0;?>>旧</option>
				  <option value="1" <?php echo $NewSign1;?>>新</option>
                </select>
                
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </td>
                </tr>
			   <tr>
                   <td align="right">刀模编号
                      <input name="Field[]" type="hidden" id="Field[]" value="CutName">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="50">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>

			   <tr>
                   <td align="right">刀模尺寸
                      <input name="Field[]" type="hidden" id="Field[]" value="CutSize">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" size=48 maxlength="50">
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
			   
			    <TR>
                  <TD align="right">刀模图片
                      <input name="Field[]" type="hidden" id="Field[]" value="Picture">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
                  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name="value[]" id="value[]" style="width:350px">
                  <option value="" selected>请选择</option>
                  <option value="0" >无</option>
				  <option value="1" >有</option>
                </select>
			
                <input name="table[]" type="hidden" id="table[]" value="C">
                <input name="types[]" type="hidden" id="types[]" value="isNum">
	
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