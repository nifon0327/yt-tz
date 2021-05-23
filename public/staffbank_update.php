<?php 
//ewen 2014-06-11 加入结付货币和帐户一起操作
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$mainRow=mysql_fetch_array(mysql_query("SELECT S.Number,S.Bank AS Bank_B,S.Bank2 AS Bank_A,S.Bank3 AS Bank_C,M.Name,M.Currency FROM $DataPublic.staffsheet S LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number WHERE S.Id='$Id' LIMIT 1",$link_id));
$Number=$mainRow["Number"];
$Currency=$mainRow["Currency"];
$CurrencyOther=" AND A.Id IN(1,4)";//只显示RMB/TWD
$Name=$mainRow["Name"];
$Bank_A=$mainRow["Bank_A"];
$Bank_B=$mainRow["Bank_B"];
$Bank_C=$mainRow["Bank_C"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,BanEstate,$BanEstate";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td width="112" align="right" scope="col">员工姓名</td>
            <td scope="col"><?php  echo $Name?>
            </td>
          </tr>
           <tr>
		     <td align="right">薪资货币</td>
		     <td><?php 
             include "../model/subselect/Currency.php";
			 ?></td>
        </tr>
          <tr>
            <td align="right"><?php  echo $BankStr?>农行帐户</td>
            <td><input name="Bank_A" type="text" id="Bank_A" value="<?php  echo $Bank_A?>" style="width:380px"></td>
          </tr>
          <tr>
            <td align="right"><?php  echo $BankStr?>工行帐户</td>
            <td><input name="Bank_B" type="text" id="Bank_B" value="<?php  echo $Bank_B?>" style="width:380px"></td>
          </tr>
          <tr>
            <td align="right"><?php  echo $BankStr?>其他帐户</td>
            <td><input name="Bank_C" type="text" id="Bank_C" value="<?php  echo $Bank_C?>" style="width:380px"></td>
          </tr>
   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>