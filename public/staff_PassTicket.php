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
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 个人通行证上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_PassPort";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT M.Id,M.Name,S.PassTicket FROM $DataPublic.staffmain  M 
						 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
						WHERE M.Number='$Mid' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
    $Id=$upData["Id"];
	$Name=$upData["Name"];
	$PassTicket=$upData["PassTicket"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;

//$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Mid,$Mid,funFrom,$funFrom,From,$From,ActionId,99,Number,$Mid";
//步骤5：//需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
	      </tr>
            <td align="right">个人通行证上传</td>
            <td><input name="PassTicket" type="file" id="PassTicket" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="0" Cel="1">
            </td>
           </tr>
			<?php 
			if($PassTicket==1){
				echo"<tr>
				<td>&nbsp;</td>
				<td>
					<input type='checkbox' name='oldPassTicket' id='oldPassTicket' value='1'><LABEL for='oldPassTicket'>删除已传个人护照</LABEL></a>
				</td>
				</tr>";
				}
			?>
	</table>	
</td></tr>
</table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
