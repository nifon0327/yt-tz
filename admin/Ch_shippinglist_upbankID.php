<?php
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rkmain
$DataSharing.providerdata
$DataSharing.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.ch1_shipmain";
ChangeWtitle("$SubCompany 更新付款帐号");
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_upbankID";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：
$MainResult = mysql_query("SELECT M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark 
FROM $upDataMain M
WHERE M.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$InvoiceNO=$MainRow["InvoiceNO"];
	$BankId=$MainRow["BankId"];
	$CompanyId=$MainRow["CompanyId"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,21,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId";

 $check_Id=$Mid;
 include "subprogram/ch_mycompany_check.php";


//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right" scope="col">Invoice名称：</td>
            <td scope="col"><?php    echo $InvoiceNO?></td>
		</tr>

         <tr>
		  <td  align="right"  >付款账号：</td>
		  <td > <select name='BankId'  id='BankId' style='width:234px' dataType='Require' msg='未选' >
           <?php
				switch($CompanyId){  //CEL 报关，走对公账号4, 其它方式走：上海对公账号 5,
					case 1003:  //Laz
					case 1018:  //EUR
					case 1024:  //Kon
					case 1031:  //Elite
						echo "<option value='4' selected='selected'>研砼国内对公账号</option>";
						break;
                                        case 1013: //VOG
                                            if ($BankId==4){
                                                echo "<option value='4' selected='selected'>研砼国内对公账号</option>";
                                                echo "<option value='5' >研砼上海对公账号</option>";
                                            }else{
                                                echo "<option value='4'>研砼国内对公账号</option>";
                                                echo "<option value='5' selected='selected' >研砼上海对公账号</option>";
                                            }
						break;
					default:
						echo "<option value='5' selected='selected' >研砼上海对公账号</option>";
						break;
				}
		   ?>
            </select>
          </td>
  		</tr>

</table>
	</td></tr></table>
<?php
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>