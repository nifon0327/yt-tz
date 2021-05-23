<?php 
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 设置门禁权限");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_setup";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.PowerType,B.Name FROM $DataPublic.accessguard_user A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number WHERE A.Id='$Id'",$link_id));
$PowerType=$upData["PowerType"];
$Name=$upData["Name"];
//步骤4：
$tableWidth=1000;$tableMenuS=650;
include "../Admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理

?>
	<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="850" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td>用户姓名</td>
            <td><?php  echo $Name?></td>
          </tr>
           <tr>
            <td>权限类型</td>
            <td><select name="chkType" type="text" id="chkType" style="width:380px" maxlength="16" DataType="Require" Msg="没有选择">
            <option value="0" selected>请选择</option>
            <?php 
            $checkSql=mysql_query("SELECT * FROM $DataPublic.accessguard_powertype WHERE Estate=1 ORDER BY Id",$link_id);
			if($checkRow=mysql_fetch_array($checkSql)){
				do{
					if($checkRow[Id]==$PowerType){
						echo"<option value='$checkRow[Id]' selected>$checkRow[TypeName]</option>";
						}
					else{
						echo"<option value='$checkRow[Id]'>$checkRow[TypeName]</option>";
						}
					}while($checkRow=mysql_fetch_array($checkSql));
				}
			?>
            </select>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="right">
                <table width='850' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
                    <tr align='center' bgcolor='#CCCCCC'>
                    <td class='A1111' rowspan='2' width='80'>门禁名称</td>
                     <td class='A1101' colspan='2' height='20'>星期一</td>
                     <td class='A1101' colspan='2'>星期二</td>
                     <td class='A1101' colspan='2'>星期三</td>
                     <td class='A1101' colspan='2'>星期四</td>
                     <td class='A1101' colspan='2'>星期五</td>
                     <td class='A1101' colspan='2'>星期六</td>
                     <td class='A1101' colspan='2'>星期日</td>
                    </tr>
                    <tr align='center' bgcolor='#CCCCCC'>
                    <td class='A0101' height='20'>起始时间</td><td class='A0101'>终止时间</td>
                    <td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
                    <td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
                    <td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
                    <td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
                    <td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
                    <td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
                    </tr>
                </table>
            </td>
          </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>