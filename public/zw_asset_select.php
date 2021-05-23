<?php 
//����-joseph
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/functions.php";
include "../model/modelfunction.php";
//ģ���������:ģ��$Login_WebStyle

WinTitle("��Ʒ��¼��ѯ");
$Login_help="zw_asset_select";
//session_register("Login_help");
$_SESSION["Login_help"] = $Login_help;
$url="zw_asset_read";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<?php 
//CSSģ��
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
?>
<script src="../basic/functions.js" type=text/javascript></script>
<title></title>
</head>
<body>

<form name="form1" method="post" action="">
  <table width="850" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td width="7" <?php  echo $td_bgcolor?>><img name="maintable_r1_c1" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c1.gif" width="7" height="26" /></td>
      <td width="600" <?php  echo $td_bgcolor?> background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c2.gif">����</td>
      <td width="35" <?php  echo $td_bgcolor?>><img name="maintable_r1_c3" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c3.gif" width="35" height="26"/></td>
      <td width="111" align="center" background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c4.gif" >
	  
			<table align="center">
			  <tr>
				<td width="115" class="readlink" ><nobr> 
				<span onClick="javascript:Checkform();" <?php  echo $onClickCSS?>>����</span>  
				<span onClick="javascript:ComeBack('<?php  echo $url?>','');"<?php  echo $onClickCSS?>>����</span> 
				</nobr> </td>
			  </tr>
			</table>
	  
	  </td>
      <td width="34" ><img name="maintable_r1_c5" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c5.gif" width="34" height="26"/></td>
      <td width="150" background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c6.gif" >&nbsp;</td>
      <td width="10"><img name="maintable_r1_c7" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c7.gif" width="7" height="26"/></td>
    </tr>
    <tr>
      <td background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r2_c1.gif"></td>
      <td colspan="5">
	  	
			<p>&nbsp;</p>
			<TABLE width="560" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="114"><div align="right">��&nbsp;&nbsp;&nbsp;&nbsp;��
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
                  </div></TD>
                  <TD width="101"><div align="center">
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
                  <TD width="331"><select name=value[] id="value[]" style="width: 300px;">
	  				<option value="">ȫ��</option>
      <?php 
	  $checkType=mysql_query("SELECT Id,Name FROM $DataIn.zw1_assettypes WHERE Estate=1",$link_id);
	  if($checkTypeRow=mysql_fetch_array($checkType)){
	  	do{
			$Id=$checkTypeRow["Id"];
			$Name=$checkTypeRow["Name"];
			echo"<option value='$Id'>$Name</option>";
			}while($checkTypeRow=mysql_fetch_array($checkType));
		}
	  ?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="R">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD><p align="right">Ʒ&nbsp;&nbsp;&nbsp;&nbsp;��
                    <input name="Field[]" type="hidden" id="Field[]" value="BrandId">
                  </p>
                  </TD>
                  <TD><div align="center">
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
                  <TD><select name=value[] id="value[]" style="width: 300px;">
						 <option value="">ȫ��</option>
					  <?php 
					  $checkBrand=mysql_query("SELECT Id,Name,EName FROM $DataIn.zw1_brandtypes  WHERE Estate=1",$link_id);
					  if($checkBrandRow=mysql_fetch_array($checkBrand)){
						do{
							$Id=$checkBrandRow["Id"];
							$Name=$checkBrandRow["Name"]."/".$checkBrandRow["EName"];
							echo"<option value='$Id'>$Name</option>";
							}while($checkBrandRow=mysql_fetch_array($checkBrand));
						}
					  ?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="R">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">��&nbsp;&nbsp;&nbsp;&nbsp;��
                    <input name="Field[]" type="hidden" id="Field[]" value="Model">
                  </div></TD>
                  <TD>                  <div align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>����</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD>
				    <INPUT name=value[] class=textfield id="value[]" size=41>
				    <input name="table[]" type="hidden" id="table[]" value="R">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
				  </TD>
                </TR>
                <TR>
                  <TD><div align="right">�� �� ID
                      <input name="Field[]" type="hidden" id="Field[]" value="Number">
                  </div></TD>
                  <TD><div align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>����</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=41>
                    <input name="table[]" type="hidden" id="table[]" value="R">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
				<?php 
				/*
                  <TD><div align="right">���ʹ�����
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </div></TD>
                  <TD><div align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>����</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=41>
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">���������
                    <input name="Field[]" type="hidden" id="Field[]" value="User">
                  </div></TD>
                  <TD><div align="center">
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
                  <TD><select name=value[] id="value[]" style="width: 300px;">
                    <?php 
					//Ա�����ϱ�
					$PD_Sql = "SELECT U.User,P.Name FROM zw1_assetuse U LEFT JOIN personneldata P ON P.Number=U.User GROUP BY U.User order by U.User";
					$PD_Result = mysql_query($PD_Sql); 
					echo "<option value='' selected>ȫ��</option>";
					while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
						$User=$PD_Myrow["User"];
						$Name=$PD_Myrow["Name"];					
						echo "<option value='$User'>$Name</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="C">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">�����������
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">
                  </div></TD>
                  <TD><div align="center">
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
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 maxlength="10">
��
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 maxlength="10">
  <input name="table[]" type="hidden" id="table[]" value="C">
  <input name="types[]" type="hidden" id="types[]"
                value="isDate" />
                  </TD>
                </TR>
				*/?>
              </TBODY>
	    </TABLE>
		    <div align="center"></div>
	    <p>&nbsp;</p></td>
      <td background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r2_c7.gif"></td>
    </tr>
    <tr>
      <td <?php  echo $td_bgcolor?>><img name="maintable_r3_c1" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r3_c1.gif" width="7" height="26" border="0"/></td>
      <td <?php  echo $td_bgcolor?> background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r3_c2.gif" ></td>
      <td <?php  echo $td_bgcolor?>><img name="maintable_r3_c3" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r3_c3.gif" width="35" height="26" border="0"/></td>
      <td background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r1_c4.gif">
	  
			<table>
			  <tr>
				<td align="center" class="readlink"><nobr> 
				<span onClick="javascript:Checkform();" <?php  echo $onClickCSS?>>����</span>  
				<span onClick="javascript:ComeBack('<?php  echo $url?>','');"<?php  echo $onClickCSS?>>����</span> 
				</nobr></td>
			  </tr>
			</table>
		
	  </td>
      <td><img name="maintable_r3_c5" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r3_c5.gif" width="34" height="26" border="0"/></td>
      <td background="../model/<?php  echo $Login_WebStyle?>/images/maintable_r3_c6.gif">&nbsp;</td>
      <td><img name="maintable_r3_c7" src="../model/<?php  echo $Login_WebStyle?>/images/maintable_r3_c7.gif" width="7" height="26" border="0"/></td>
    </tr>
  </table>
</form>
</body>
</html>
<script language = "JavaScript"> 
function Checkform(){
	document.form1.action="zw_asset_session.php";
	document.form1.submit();
	}
</script>