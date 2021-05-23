<?php 
//步骤1 $DataIn.stuffdata 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 条码文件上传");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_codefile";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT D.cName,D.ProductId FROM $DataIn.productdata D WHERE D.Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$ProductId=$upData["ProductId"];
	$cName=$upData["cName"];
	}
//步骤4：
$tableWidth=950;$tableMenuS=650;$spaceSide=15;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,ActionId,80,ProductId,$ProductId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="900" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr>
			<td height="38" colspan="2" class="A1111">&nbsp;&nbsp;产品ID：<?php  echo $ProductId?> &nbsp;&nbsp;产品名称：<?php  echo $cName?></td>
	    </tr>
        <tr bgcolor="#CCCCCC">
          <td width="226" height="31" align="center" class="A0111">文件类型</td>
        <td width="670" align="center" class="A0101">上传的文件</td>
        </tr>
		<tr>
            <td height="35" class="A0111"><select name="CodeType" id="CodeType" dataType="Require"  style="width:380px;" msg="未选择文件" onchange="selFileType()">
         	  <option value="" selected>请选择条码类型</option>
             <?php 
			 	for($j=1;$j<5;$j++){
					switch($j){
					case 1:$TypeName="背卡条码";break;
					case 2:$TypeName="PE袋标签";break;
					case 3:$TypeName="外箱条码";break;
					case 4:$TypeName="白盒坑盒";break;
					}
			  		$checkFileSql=mysql_query("SELECT Estate FROM $DataIn.file_codeandlable WHERE ProductId='$ProductId' AND CodeType='$j' LIMIT 1",$link_id);
			  		$Info="-- 未上传";
			  		if($checkFileRow=mysql_fetch_array($checkFileSql)){
			  			$tempEstate=$checkFileRow["Estate"];
						switch($tempEstate){
							case 1:$Info="-- 审核未通过";break;
							case 2:$Info="-- 审核中...";break;
							default:$Info="-- 审核通过";break;
							}
						}
					echo" <option value='$j'>$TypeName $Info</option>";
					}
		  //增加条码JPG图片上传功能 2011-3-3
		            $checkFileSql=mysql_query("SELECT Estate FROM $DataIn.file_codeandlable WHERE ProductId='$ProductId' AND CodeType='7' LIMIT 1",$link_id);
				    if($checkFileRow=mysql_fetch_array($checkFileSql)){	
					    $Info="-- 已上传";
					 }
					 else{
                        $Info="-- 未上传";	
					 }
				   echo" <option value='7'>JPG图片 $Info</option>";
			  ?>
            </select></td>
         	<td class="A0101" ><div id="selList">
             <input name="CodeFile" type="file" id="CodeFile" style="width:380px;" DataType="Filter" Accept="qdf,QDF,PDF,pdf" Msg="格式不对" Row="2" Cel="1"></div>
             </td>
    	</tr>
		
		<tr>
		  <td height="38" colspan="2" class="A0111">说明：上传车间打印的条码文件(限QDF,PDF文件，供内部打印)，如果没有选择上传的文件而保存，视为删除原文件。(文件需为审核不通过的状态才执行删除操作)</td>
	    </tr>
	</table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
<script type=text/javascript>
function selFileType(){
	var strAccept;
	var typeValue=document.getElementById('CodeType').value;
	var selList=document.getElementById('selList');
	//alert (selList);
	if(typeValue=="7"){
		strAccept="jpg,JPG";
		}
	else{
         strAccept="qdf,QDF,PDF,pdf";
		}
	//alert(selList);
	selList.innerHTML="<input name='CodeFile' type='file' id='CodeFile' size='100' DataType='Filter'
	Accept='"+strAccept+"' Msg='格式不对'";
   //selList.innerHTML="<input name='CodeFile' type='file' id='CodeFile' size='60' DataType='Filter' Accept='"+strAccept+"' Msg='格式不对' Row='2' Cel='1'>";
}
</script>
