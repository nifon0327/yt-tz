<style type="text/css">
<!--
.aaaa {
border:0px;
border-style: none;
 border-right-color: #FFFFFF;
 border-bottom-color: #FFFFFF;
 border-left-color: #FFFFFF;
 text-align: center;
}
.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:200px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
-->
</style>
<?php 
//步骤1 $DataIn.stuffdata 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非bom配件图片上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_artwork";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);

$upResult = mysql_query("SELECT GoodsId,GoodsName,Attached,AppIcon FROM $DataIn.nonbom4_goodsdata WHERE Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$GoodsId=$upData["GoodsId"];
	$GoodsName=$upData["GoodsName"];
	$Attached=$upData["Attached"];
	$AppIcon=$upData["AppIcon"];
	
	if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$AttachedSTR="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>已上传</span>";
	 }else{
		 $AttachedSTR="";
	 }
	 
	if($AppIcon==1){
			$AppIcon=$GoodsId."_s.png";
			$AppIcon=anmaIn($AppIcon,$SinkOrder,$motherSTR);
			$AppIconSTR="<span onClick='OpenOrLoad(\"$Dir\",\"$AppIcon\")' style='CURSOR: pointer;color:#FF6633'>已上传</span>";
	 }else{
		    $AppIconSTR="";
	 }
}
//步骤4：
$tableWidth=900;$tableMenuS=550;$spaceSide=15;
$SelectCode="($GoodsId) $GoodsName";
//$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,$ActionId,GoodsId,$GoodsId,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="700" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="100" height="30" align="center" class="A1111">类型</td>
			<td width="600" align="center" class="A1101">上传操作</td>
		</tr>
         <tr>
          <td class="A0111" align="center" height="50" >配件图片</td>
	      <td class="A0101"><input name="Attached" type="file" id="Attached" size="60" DataType="Filter" Accept="jpg" Msg="格式不对,请重选" Row="1" Cel="1"><span style="color:red; font-size:14px; font-weight:bold;"></span><?php echo $AttachedSTR?>(限jpg格式)</td>
	      </tr>
         <tr>
          <td class="A0111" align="center" height="50" >App图</td>
	      <td class="A0101"><input name="AppIcon" type="file" id="AppIcon" size="60" DataType="Filter" Accept="png" Msg="格式不对,请重选" Row="2" Cel="1"><span style="color:red; font-size:14px; font-weight:bold;"></span><?php echo $AppIconSTR?>(限png格式,大小200X200像素)</td>
	      </tr>
	</table>
	
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
