<?php 
include "../model/modelhead.php";
//步骤2：

ChangeWtitle("$SubCompany 新员工评鉴");//需处理
$fromWebPage=$funFrom."_read";      
$nowWebPage =$funFrom."_update";    
$toWebPage  =$funFrom."_updated";   
$_SESSION["nowWebPage"]=$nowWebPage; 

//读取出员工的资料
$ids = implode(',', $checkid);
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
    <input type='hidden' id='staffs' name='staffs' value="<?php echo $ids?>">
    <input type='hidden' id='ActionId' name='ActionId' value="<?php echo $ActionId?>">
    <table width="750" height="120" border="0" cellspacing="5">
        <tr>
            <td align="right" valign="top" scope="col">评鉴员工</td>
            <td valign="middle" scope="col">
                <select name="ListId[]" size="10" id="ListId" multiple style="width: 300px;" readonly>
                    <?php
                        $staffSql = "SELECT staff.Name, branch.Name as BranchName, staff.ComeIn 
                                     FROM $DataPublic.staffmain as staff 
                                     INNER JOIN $DataPublic.branchdata as branch ON branch.Id = staff.BranchId
                                     WHERE staff.Number IN ($ids)";
                        //echo $staffSql;
                        $staffResult = mysql_query($staffSql);
                        $staffContent = array();
                        while($staffRow = mysql_fetch_assoc($staffResult)){
                            $name = $staffRow['Name'];
                            $branch = $staffRow['BranchName'];
                            $ComeIn = $staffRow['ComeIn'];
                            echo "<option>$branch-$name(入职时间:$ComeIn)</option>";
                        }
                    ?>
                </select>
            </td>
        </tr> 
        <tr>
            <td height="25" align="right" scope="col">评鉴主管</td>
            <td valign="middle" scope="col">
                <select name="ManagerId" id="ManagerId" style="width:300px">
                <?php 
                    $inResult=mysql_query("SELECT staff.Number,staff.Name,branch.Name AS branchname FROM $DataIn.branchmanager as manager
                                           INNER JOIN $DataPublic.staffmain AS staff ON staff.Number = manager.Manager
                                           INNER JOIN $DataPublic.branchdata As branch ON manager.BranchId = branch.Id
                                           WHERE staff.Estate=1 order by manager.Id DESC",$link_id);
                    if($inRow = mysql_fetch_array($inResult)) {
                        do{
                            $inId=$inRow["Number"];
                            $inName=$inRow["Name"];
                            $inBranchNae = $inRow['branchname'];
                            echo "<option value='$inId' selected>$inBranchNae-$inName</option>";
                        }while ($inRow = mysql_fetch_array($inResult));
                    }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top" scope="col">评鉴内容</td>
            <td valign="middle" scope="col"><textarea name="Remark" cols="48" rows="4" id="Remark"></textarea></td>
        </tr>
    </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>