<?php 
//电信-joseph
//代码共享、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0c.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增考勤iPad");//需处理
$nowWebPage =$funFrom."_add";   
$toWebPage  =$funFrom."_save";  
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$checkMaxNum=mysql_fetch_array(mysql_query("SELECT MAX(SortId)+1 AS MaxNum FROM $DataPublic.branchdata",$link_id));
$SortId=$checkMaxNum["MaxNum"];
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr><td class="A0011">
        <table width="760" border="0" align="center" cellspacing="5">
            <tr>
                <td width="150" height="40" align="right" >考勤楼层</td>
                <td >
                <?php 
                echo "<SELECT id='Floor', name='Floor'>";
                $floorSql = "Select Floor From $DataIn.attendance_floor";
                $floorResult = mysql_query($floorSql);
                while($floorRow = mysql_fetch_assoc($floorResult)){
                    $floorName = $floorRow['Floor'];
                    echo "<option value='$floorName'>$floorName</option>";
                }
                echo "</SELECT>";
                ?>
                </td>
            </tr>
            <tr>
                <td width="150" height="40" align="right" scope="col">ipad名称</td>
                <td scope="col"><input name="Name" type="text" id="Name" style="width:380px"></td>
            </tr>
            <tr>
                <td width="150" height="40" align="right" scope="col">公司</td>
                <td>
                <Select id='cSign' name='cSign'>
                    <option value='7'>包裝</option>
                    <option value='3'>皮套</option>
                </Select></td></tr>
            <tr>
                <td width="150" height="40" align="right" scope="col">ipad识别码</td>
                <td scope="col"><input name="Identifier" type="text" id="Identifier" style="width:380px"></td>
            </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>