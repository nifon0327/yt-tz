<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新lotto码记录");//需处理
$nowWebPage =$funFrom."_update";   
$toWebPage  =$funFrom."_updated";  
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
//echo "Id:$Id";
$lottoGetSql = "SELECT * FROM $DataIn.ch_initallotto WHERE Id=$Id";
//echo $lottoGetSql;
$lottoResult = mysql_fetch_assoc(mysql_query($lottoGetSql));
$lotto = $lottoResult['lotto'];
$companyId = $lottoResult['companyId'];

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<input type='hidden' name='Id' id='Id' value='<?php echo $Id;?>'>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
    <table width="600" border="0" align="center" cellspacing="0">
      <tr>
        <td width="104" align="right">客户名称</td>
        <td>
          <select name="CompanyId" id="CompanyId" style="width:275px">
          <?php
            $companySql = "SELECT * FROM trade_object WHERE ObjectSign in (1,2) AND Estate=1 Order By Letter,Id";
            $companyResult = mysql_query($companySql);
            while($companyRow = mysql_fetch_assoc($companyResult)){
              $Letter = $companyRow['Letter'];
              $tmpCompanyId = $companyRow['CompanyId'];
              $tmpForshort = $companyRow['Forshort'];
              $selected = $companyId == $tmpCompanyId ? 'selected':'';
               echo "<option value='$tmpCompanyId' $selected>$Letter-$tmpForshort</option>";
            }
          ?>
          </select>
        </td>
      </tr>
      <tr>
        <td align="right">lotto码</td>
        <td><input name="lotto" type="text" id="lotto" size="20" value='<?php echo $lotto;?>'>
        </td>
      </tr>
    </table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>