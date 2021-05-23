<?php   
//步骤1 电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 加工工序资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.process_data WHERE Id='$Id'",$link_id));
$ProcessId=$upData["ProcessId"];
$ProcessName=$upData["ProcessName"];
$TypeId=$upData["TypeId"];
$gxTypeId=$upData["gxTypeId"];
$SortId=$upData["SortId"];
$Picture=$upData["Picture"]==1?"已上传":"未上传";
$Price=$upData["Price"];
$Remark=$upData["Remark"];
$BassLoss=$upData["BassLoss"];
$Color=$upData["Color"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
                       <!-- <tr>
				<td align="right" scope="col">所属加工</td>
				<td width="460" scope="col">
                                    <select name="TypeId" id="TypeId" style="width:400px" dataType="Require"  msg="未选择分类">
			  	<?php   
				$result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' AND mainType='3' ORDER BY Letter",$link_id);
				while ($StuffType = mysql_fetch_array($result)){
					$Letter=$StuffType["Letter"];
					$theTypeId=$StuffType["TypeId"];
					$TypeName=$StuffType["TypeName"];
                                        if ($theTypeId==$TypeId){
					     echo"<option value='$theTypeId' selected>$Letter-$TypeName</option>";
                                        }else{
                                             echo"<option value='$theTypeId'>$Letter-$TypeName</option>";
                                        }
				      }
				?>
				</select>  
                                </td>
			</tr> -->
  <tr>
				<td align="right" scope="col">工序类型</td>
				<td width="460" scope="col">
                                    <select name="gxTypeId" id="gxTypeId" style="width:400px" dataType="Require"  msg="未选择分类">
			  	<?php   
				$gxresult = mysql_query("SELECT gxTypeId,gxTypeName  FROM $DataIn.process_type WHERE Estate='1'",$link_id);
				while ($gxType = mysql_fetch_array($gxresult)){
					$thegxTypeId=$gxType["gxTypeId"];
					$gxTypeName=$gxType["gxTypeName"];
                                        if ($thegxTypeId==$gxTypeId){
					     echo"<option value='$thegxTypeId' selected>$gxTypeName</option>";
                                        }else{
                                             echo"<option value='$thegxTypeId'>$gxTypeName</option>";
                                        }
				      }
				?>
				</select>  
                                </td>
			</tr> 

			<tr>
				<td align="right" scope="col">工序名称</td>
				<td width="460" scope="col"><input name="ProcessName" type="text" value="<?php    echo $ProcessName?>" id="ProcessName" style="width:400px"  dataType="Require" Msg="未填写加工工序名称">
                                    <input name="ProcessId" id="ProcessId"  type="hidden" value="<?php    echo $ProcessId?>" ></td>
			</tr> 
           <tr>
				<td align="right" scope="col">基础损耗比率</td>
				<td width="460" scope="col"><input name="BassLoss" type="text" id="BassLoss" value="<?php    echo $BassLoss?>" style="width:400px"  dataType="Double" msg="错误的损耗"></td>
			</tr> 
           <tr>
				<td align="right" scope="col">单 &nbsp;&nbsp; 价</td>
				<td width="460" scope="col"><input name="Price" type="text" id="Price" value="<?php    echo $Price?>" style="width:400px"  dataType="Currency" msg="错误的价格"></td>
			</tr> 
                        <tr>
				<td align="right" scope="col">工序说明</td>
				<td width="460" scope="col"><textarea name="Remark" style="width:400px"  rows="6" id="Remark"  Msg="未填写内容"><?php    echo $Remark?></textarea></td>
			</tr> 
			<tr>
				<td align="right" scope="col">工序图片</td>
				<td width="460" scope="col"><input name="Picture" type="file" id="Picture" style="width: 300px;" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1"><span style='color:#F00;'><?php    echo $Picture?></span></td>
			</tr> 
	   </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>