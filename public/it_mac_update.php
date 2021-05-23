<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新上网设备MAC地址资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT *  FROM $DataPublic.it_mac A 
WHERE A.Id='$Id'",$link_id));
$Name=$upData["Name"];
		$IPad=$upData["IPad"];			
		$IPhone=$upData["IPhone"];			
		$Mac=$upData["Mac"];			
		$PC=$upData["PC"];			
	     $Other=$upData["Other"];	

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
    		<table width="760" border="0" align="center" cellspacing="5">
			<tr>
            	<td scope="col" width="150" height="30" align="right">姓名:</td>
            	<td scope="col">
              	<input name="Name" type="text" id="Name" value="<?PHP echo $Name?>" style="width:380px" maxlength="20" title="可输入2-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="2" Msg="没有填写或字符不在2-20个字节内"> 
				</td>
			</tr>
			<tr>
		  		<td height="40" align="right" scope="col">IPad:</td>
		  		<td scope="col"><input  type="text" name="IPad" style="width:380px"  id="IPad" value="<?PHP echo $IPad?>"></td>
	    	</tr>
			<tr>
		  		<td height="40" align="right" scope="col">IPhone:</td>
		  		<td scope="col"><input  type="text"  name="IPhone" style="width:380px"  id="IPhone"  value="<?PHP echo $IPhone?>"></td>
	    	</tr>
		<tr>
		  		<td height="40" align="right" scope="col">Mac:</td>
		  		<td scope="col"><input  type="text"  name="Mac" style="width:380px"  id="Mac" value="<?PHP echo $Mac?>"></td>
	    	</tr>
		<tr>
		  		<td height="40" align="right" scope="col">PC:</td>
		  		<td scope="col"><input  type="text"  name="PC" style="width:380px"  id="PC" value="<?PHP echo $PC?>"></td>
	    	</tr>
		<tr>
		  		<td height="40" align="right" scope="col">Other:</td>
		  		<td scope="col"><input  type="text"  name="Other" style="width:380px"  id="Other" value="<?PHP echo $Other?>"></td>
	    	</tr>


      	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>