<?php   
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增加工工序资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table  name='NoteTable' id='NoteTable' width="620" border="0" align="center" cellspacing="5">
                       <!-- <tr>
				<td align="right" scope="col">所属加工</td>
				<td width="500" scope="col">
                                    <select name="TypeId" id="TypeId" style="width:400px" dataType="Require"  msg="未选择分类">
				<option value='' selected>请选择</option>
			  	<?php   
				$result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' AND mainType='3' ORDER BY Letter",$link_id);
				while ($StuffType = mysql_fetch_array($result)){
					$Letter=$StuffType["Letter"];
					$TypeId=$StuffType["TypeId"];
					$TypeName=$StuffType["TypeName"];
					echo"<option value='$TypeId'>$Letter-$TypeName</option>";
					}
				?>
				</select>  
                                </td>
			</tr> -->

                        <tr>
				<td align="right" scope="col">工序分类</td>
				<td width="500" scope="col">
                                    <select name="gxTypeId" id="gxTypeId" style="width:400px" dataType="Require"  msg="未选择分类">
				<option value='' selected>请选择</option>
			  	<?php   
					$gxresult = mysql_query("SELECT gxTypeId,gxTypeName  FROM $DataIn.process_type WHERE Estate='1'",$link_id);
				    while ($gxType = mysql_fetch_array($gxresult)){
					  $thegxTypeId=$gxType["gxTypeId"];
					  $gxTypeName=$gxType["gxTypeName"];
                       echo"<option value='$thegxTypeId'>$gxTypeName</option>";
				      }
				?>
				</select>  
                                </td>
			</tr> 
			<tr>
				<td align="right" scope="col">工序名称</td>
				<td width="460" scope="col"><input name="ProcessName" type="text" id="ProcessName" style="width:400px"  dataType="Require" Msg="未填写工序名称"></td>
			</tr> 
             <tr>
				<td align="right" scope="col">基础损耗比率</td>
				<td width="460" scope="col"><input name="BassLoss" type="text" id="BassLoss" style="width:400px"  dataType="Double" msg="错误的损耗"></td>
			</tr> 
             <tr>
				<td align="right" scope="col">单 &nbsp;&nbsp; 价</td>
				<td width="460" scope="col"><input name="Price" type="text" id="Price" style="width:400px"  dataType="Currency" msg="错误的价格"></td>
			</tr> 
			</tr> 
              <tr>
				<td align="right" scope="col">工序说明</td>
				<td width="460" scope="col"><textarea name="Remark" style="width:400px"  rows="6" id="Remark"  Msg="未填写内容"></textarea></td>
			</tr> 
			<tr>
				<td align="right" scope="col">工序图片</td>
				<td width="460" scope="col"><input name="Picture" type="file" id="Picture" style="width: 300px;" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,JPG" Row="4" Cel="1"></td>
			</tr> 
	   </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>