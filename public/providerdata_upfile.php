<style type="text/css">
<!--
.aaaa {
 border-top-width:0px;
 border-right-width: 0px;
 border-bottom-width: 0px;
 border-left-width: 0px;
 border-top-style: none;
 border-right-style: none;
 border-bottom-style: none;
 border-left-style: none;
 border-right-color: #FFFFFF;
 border-bottom-color: #FFFFFF;
 border-left-color: #FFFFFF;
 text-align: center;
}
-->
</style>


<?php 
//步骤1 $DataIn.stuffdata 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 上传图片文件");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upfile";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT CompanyId,Forshort,PackFile,TipsFile FROM $DataIn.trade_object  WHERE Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$CompanyId=$upData["CompanyId"];
	$Forshort=$upData["Forshort"];
	$PackFile=$upData["PackFile"];
	$TipsFile=$upData["TipsFile"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$SelectCode="供应商:" . $Forshort . "($CompanyId)";
//$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,ActionId,$ActionId,CompanyId,$CompanyId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">

	<table width="650" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="35" height="30" align="center" class="A1111">序号</td>
		  <td width="65" align="center" class="A1101">类别</td>
			<td width="550" align="center" class="A1101"><span style="color:red; font-size:14px; font-weight:bold">图片上传</span>(限png图片,背景透明)</td>
		</tr>
	<?php 
	//检查是否有旧文件,如果有则列出

 /*
	$checkImgSql=mysql_query("SELECT Picture FROM $DataPublic.msg1_picture WHERE Mid='$Id' ORDER BY Picture",$link_id);
	if($checkImgRow=mysql_fetch_array($checkImgSql)){
		$i=1;
		do{
			$ImgName=$checkImgRow["Picture"];
			$Item="<div class='redB'>$i</div>";
			echo"
			<tr>
				<td class='A0111' align='center' height='30'><input name='OldImg[]' type='hidden' id='OldImg[]' value='$ImgName'><a href='#' onclick='deleteImg(\"$ImgName\",this.parentNode.parentNode.rowIndex)' title='删除原图片: $ImgName'>×</a></td>
				<td class='A0101' align='center'>$Item</td>
				<td class='A0101'><a href='../download/msgfile/$ImgName' target='_black'>查看图片</a></td>
			</tr>";
			$i++;
			}while ($checkImgRow=mysql_fetch_array($checkImgSql));
		}
	else{
	*/
		$PackFileStr=$PackFile==1?"<a href='../download/providerfile/Pack_$CompanyId.png' target='_black'>查看图片</a>":"";
		$TipsFileStr=$TipsFile==1?"<a href='../download/providerfile/Tips_$CompanyId.png' target='_black'>查看图片</a>":"";

	
	?>
	<tr>
            <td class="A0111" align="center" height="30">1</td>
         	<td class="A0101" align="center">胶框图</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="PackFile" type="file" id="PackFile" size="60" DataType="Filter" Accept="png" Msg="格式不对,请重选" Row="1" Cel="2"><?php echo $PackFileStr;?></td>
    	</tr>
	<tr>
            <td class="A0111" align="center" height="30">2</td>
         	<td class="A0101" align="center">提示图</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="TipsFile" type="file" id="TipsFile" size="60" DataType="Filter" Accept="png" Msg="格式不对,请重选" Row="2" Cel="2"><?php echo $TipsFileStr;?></td>
    	</tr>
	</table>
</td></tr>
</table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
