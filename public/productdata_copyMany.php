<?php 
/*电信---yang 20120801
$DataIn.trade_object 
$DataIn.producttype
$DataPublic.productunit
$DataPublic.packingunit
$DataIn.productdata

*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 批量复制产品资料");//需处理
$fromWebPage=$funFrom."_read";      
$nowWebPage =$funFrom."_copyMany";    
$toWebPage  =$funFrom."_copyupdated";   
$_SESSION["nowWebPage"]=$nowWebPage; 

$oldCompanySql = "SELECT M.CompanyId,M.Forshort FROM $DataIn.trade_object M 
                  WHERE 1 AND M.Estate=1 AND M.ObjectSign IN (1,2) and M.CompanyId='$CompanyId'  ORDER BY M.Id";
$oldCompanyResult = mysql_query($oldCompanySql);
$oldCompanyRow = mysql_fetch_assoc($oldCompanyResult);
$Forshort = $oldCompanyRow['Forshort'];
$oldCompanyId = $CompanyId;

$targetid = implode(',', $checkid);
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$checkid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId,ActionId,58,TestStandard,$TestStandard";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
    <input type='hidden' name='targetid' id='targetid' value="<?php echo $targetid; ?>"'">
       <table width="820" border="0" align="center" cellspacing="5" id="NoteTable">
        <tr>
            <td align="right">隶属客户</td>
            <td><?php  echo $Forshort?></td>
        </tr>
          <tr>
            <td align="right">复制至</td>
            <td>              <input name="CopyTo[]" type="checkbox" id="CopyTo1" value="7">
            包装 
              <select name="CompanyId7" id="CompanyId7" size="1" style="width: 435px;">
            <?php  
            $result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign in (0,7) AND Estate=1 AND CompanyId!=$oldCompanyId ORDER BY Id",$link_id);
            if($myrow = mysql_fetch_array($result)){
                do{
                    echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
                    } while ($myrow = mysql_fetch_array($result));
                }
              ?>              
            </select></td>
          </tr>
         <!--  <tr>
            <td align="right">&nbsp;</td>
            <td><input name="CopyTo[]" type="checkbox" id="CopyTo2" value="5" dataType="Group" min="1" msg="必须选择1个"> 
              五楼
                <select name="CompanyId5" id="CompanyId5" size="1" style="width: 435px;">
            <?php  
            $result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=5 AND Estate=1 AND CompanyId!=$oldCompanyId ORDER BY Id",$link_id);
            if($myrow = mysql_fetch_array($result)){
                do{
                    echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
                    } while ($myrow = mysql_fetch_array($result));
                }
              ?>              
            </select></td>
          </tr> -->
          <tr>
            <td align="right">&nbsp;</td>
            <td><div class="redB">注意：此复制动作，将新建一产品ID，产品资料为本页内容，同时复制BOM表和标准图，<br>但因中文名唯一，所以中文名需做更改才能成功保存。不同的地方都要进行修改。</div></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>