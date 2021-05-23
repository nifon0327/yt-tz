<?php 
/*电信---yang 20120801
$DataIn.pands 
$DataIn.stuffdata
$DataIn.productdata
二合一已更新
*/
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新图档");//需处理
$nowWebPage =$funFrom."_artwork";	
$toWebPage  =$funFrom."_artsave";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,ProductType,$ProductType,Id,$Id";
//读取产品资料
$P_Row = mysql_fetch_array(mysql_query("SELECT cName FROM $DataIn.productdata WHERE ProductId='$Id' LIMIT 1",$link_id));
$cName=$P_Row["cName"];
$eCode=$P_Row["eCode"];
//步骤3：
$tableWidth=900;$tableMenuS=550;$ColsNumber=8;
include "../model/subprogram/add_model_t.php";

?>
<script>
function ChooseSet(Row){
	if(eval("document.form1.SFcheck"+Row).checked==true){
		eval("document.form1.Gremark"+Row).disabled=false;
		eval("document.form1.Picture"+Row).disabled=false;
		}
	else{
		eval("document.form1.Gremark"+Row).disabled=true;
		eval("document.form1.Picture"+Row).disabled=true;
		}
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
&nbsp;&nbsp;产品(<?php  echo $Id?>)中文名：<?php  echo $cName?> &nbsp;&nbsp;&nbsp;&nbsp;Product Code：<?php  echo $eCode?>
<table width="98%" border="0" align="center" cellspacing="0" id="NoteTable">
	<tr>
		<td height="22" width="250" scope="col" class="A1111" align="center">配件名称</td>
		<td class="A1101" align="center">图档说明</td>
	    <td width="350" class="A1101" align="center">配件图档</td>
	</tr>
<?php 
//插入配件列表
$StuffResult = mysql_query("SELECT 
D.StuffId,D.StuffCname,D.Gfile,D.Gremark 
FROM $DataIn.pands A,$DataIn.stuffdata D
WHERE A.ProductId='$Id' AND D.StuffId=A.StuffId ORDER BY A.Id",$link_id);
//输出配件列表
$i=1;
if($StuffMyrow=mysql_fetch_array($StuffResult)) {
	do{	
		$StuffId=$StuffMyrow["StuffId"];
		$StuffCname=$StuffMyrow["StuffCname"];
		$Gfile=$StuffMyrow["Gfile"];
		$Gremark=$StuffMyrow["Gremark"];
		$Gfile_Sign=$Gfile?"<span class='redB'>*</span>":"";
  		//配件名称/对应数量
		echo"<tr><td height='22' class='A0111'>
					<input name='SFcheck[]' type='checkbox' id='SFcheck$i' value='$StuffId' onclick='ChooseSet($i)';><LABEL for='SFcheck$i'>$StuffCname$Gfile_Sign<input name='delFile[]' type='hidden' id='delFile$i' value='$Gfile'></LABEL>
				</td>
				<td class='A0101'>
					<input name='Gremark[]' id='Gremark$i' type='text' size='35' value='$Gremark' disabled>
				</td>
				<td class='A0101'>
					<input name='Picture[]' id='Picture$i' type='file' size='35' disabled dataType='Filter' msg='错误' accept='zip,rar,7z,ai,eps,pdf,psd,jpg,titf,png,plt,cdr,bmp,qdf,Qdf' Row='$i' Cel='2'>
				</td>
			</tr>";
			$i++;
		} while ($StuffMyrow = mysql_fetch_array($StuffResult));
	}				
?>
		<input type="hidden" name="hfield" value="<?php  echo $i;?>">  
		<tr>
			<td colspan="3"><div class="redB">注意:1、图档格式ZIP、RAR、7Z;AI、PDF、PSD;JPG、TITF、PNG
			<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、有*号表示该图档已存在于数据表中。本操作只对选定的项目进行处理：如果有新上传文件，则做更新，如果没有上传文件且说明文字为空时则清除原图档。</div></td>
		</tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>