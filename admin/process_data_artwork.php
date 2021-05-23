<?php   
//步骤1 $DataIn.process_data 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 加工工序图档上传");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_artwork";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT ProcessId,ProcessName FROM $DataIn.process_data WHERE Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$ProcessId=$upData["ProcessId"];
	$ProcessName=$upData["ProcessName"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$SelectCode="($ProcessId) $ProcessName";

include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,40,ProcessId,$ProcessId";
//步骤5：//需处理
?>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table  name='NoteTable' id='NoteTable' width="620" border="0" align="center" cellspacing="5">
                     
                        <tr>
				<td align="right" scope="col" height='50'>图档文件:</td>
				<td width="520" scope="col"><input name="Gfile" type="file" id="Gfile" style="width: 300px;" title="可选项,JPG、PNG、AI、PDF、ZIP、RAR格式" DataType="Filter" Accept="jpg,JPG,PNG,AI,PDF,ZIP,RAR" Msg="文件格式不对" Row="0" Cel="1"></td>
			</tr> 
                        <tr>
				<td align="right" scope="col">格式要求:</td>
				<td width="520" scope="col"><FONT color="#F00">JPG,PNG,AI,PDF,ZIP,RAR</FONT></td>
			</tr> 
	   </table>
</td></tr></table>
<?php   
include "../model/subprogram/add_model_b.php";
?>
