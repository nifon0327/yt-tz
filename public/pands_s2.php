<?php 
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

ChangeWtitle("$SubCompany 产品查询");
$url="productdata_s1";
$tableMenuS=500;
$tableWidth=850;
?>
<html>
<head>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<?php 
include "../model/characterset.php";
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
?>
<script language="javascript" type="text/javascript" src="../DatePicker/WdatePicker.js"></script>
<script language="javascript"> 
window.name="win_test";
function CheckForm(){
	document.form1.action="pands_s3.php";
	document.form1.submit();
	}
</script> 
<BASE target=_self> 
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" method="post" action="">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td <?php  echo $td_bgcolor?> class="A0100" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class="A1100" <?php  echo $Fun_bgcolor?>>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
				<nobr>
				<span onClick="javascript:CheckForm();" <?php  echo $onClickCSS?>>搜索</span>
				<span onClick="javascript:document.form1.submit();" <?php  echo $onClickCSS?>>重置</span>
				</nobr> 
				</td>
			</tr>
	 </table>
   </td>
  </tr>
  <tr><td height="5" colspan="6" class="A0011"><input name="Tid" type="hidden" id="Tid" value="<?php  echo $Tid?>">
    <input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>"></td></tr>
</table>
	  	
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<TABLE width="600" border=0 align="center">
         <TBODY>
                <TR>
                  <TD width="127">产品ID号
                  <input name="Field[]" type="hidden" id="Field[]" value="ProductId"></TD>
                  <TD width="101"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="348" ><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD >产品名称
                  <input name="Field[]" type="hidden" id="Field[]" value="cName"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD >Product Code 
                  <input name="Field[]" type="hidden" id="Field[]" value="eCode"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD >Description
                  <input name="Field[]" type="hidden" id="Field[]" value="Description"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" /></TD>
                </TR>
               <?php 
			   if($Tid==""){?>
			    <TR>
                  <TD >产品分类
                  <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                                    </SELECT></TD>
                  <TD ><select name="value[]" id="value[]" style="width: 274px;">
                    <?php 
					echo"<option value='' selected>全部</option>";
					$result = mysql_query("SELECT * FROM $DataIn.TypeId WHERE Estate=1 order by Letter",$link_id);
					while ($myrow = mysql_fetch_array($result)){
						$Letter=$myrow["Letter"];
						$TypeId=$myrow["TypeId"];
						$TypeName=$myrow["TypeName"];
						echo "<option value='$TypeId'>$Letter-$TypeName</option>";
						} 
					?>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
				<?php 
				}
				?>
                <TR>
                  <TD height="23" >产品售价
                  <input name="Field[]" type="hidden" id="Field[]" value="Price"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD >客户
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD>
                  <TD ><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select>
			 </TD>
                  <TD ><select name="value[]" id="value[]" size="1" style="width: 274px;">
                    <?php  
					echo"<option value='' selected>全部</option>";
					$result = mysql_query("SELECT * FROM $DataIn.trade_object where Estate=1 order by Id",$link_id);
					if($myrow = mysql_fetch_array($result)){
						do{
							echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
							} while ($myrow = mysql_fetch_array($result));
						}
				  ?>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD >备注
                  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD >检验标准图
                  <input name="Field[]" type="hidden" id="Field[]" value="TestStandard"></TD>
                  <TD ><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                                    </select></TD>
                  <TD ><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>全部</option>
                    <option value="1">有标准图</option>
                    <option value="0">没有标准图</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isYandN" /></TD>
                </TR>
                <TR>
                  <TD >日期
                  <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=15 onfocus="WdatePicker()" readonly>
				  至
				  <INPUT name=LastDate class=textfield id="LastDate" size=15 onfocus="WdatePicker()" readonly>
                  <input name="table[]" type="hidden" id="table[]" value="productdata">
                  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD >外箱条码
                  <input name="Field[]" type="hidden" id="Field[]" value="Code"></TD>
                  <TD ><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=40>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" /></TD>
                </TR>
                <TR>
                  <TD >可用标记
                  <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  <TD ><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD >                    <select name=value[] id="value[]" style="width: 274px;">
                      <option selected value="">全部</option>
                      <option value="1">可用</option>
                      <option value="0">禁用</option>
                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD >操作员
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD ><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD ><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//员工资料表
					$PD_Sql = "SELECT M.Number,M.Name FROM $DataPublic.staffmain M ,$DataIn.stuffdata S
					WHERE M.Number=S.Operator group by  S.Operator
					order by M.Number";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$Number=$PD_Myrow["Number"];
						$Name=$PD_Myrow["Name"];					
						echo "<option value='$Number'>$Name</option>";
						} 
					?>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD >锁定状态
                  <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
                  <TD ><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD ><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="0">锁定</option>
                    <option value="1">未锁定</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="productdata">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
              </TBODY>
	    </TABLE>
</td></tr></table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011"><input name="countField" type="hidden" id="countField" value="14"></td></tr>
  <tr>
   <td <?php  echo $td_bgcolor?> class="A1000" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="100" id="menuT2" align="center" class="A1100" <?php  echo $Fun_bgcolor?>>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<span onClick="javascript:CheckForm();" <?php  echo $onClickCSS?>>搜索</span> 
					<span onClick="javascript:document.form1.submit();" <?php  echo $onClickCSS?>>重置</span> 
					</nobr>					
				</td>
			</tr>
	 </table>
   </td>
   <td class="A0100">&nbsp;</td>
   <td width="5"><img src="../model/<?php  echo $Login_WebStyle?>/Corner.gif" style="FILTER:flipV" width="5" height="23"></td>
  </tr>
  </table>
</form>
</body>
</html>