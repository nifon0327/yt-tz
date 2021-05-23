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
ChangeWtitle("$SubCompany 更新出货方式");
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_upmain";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：
$MainResult = mysql_query("SELECT M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType as Ship
FROM $upDataMain M
WHERE M.Id='$Mid' LIMIT 1",$link_id);

if($MainRow = mysql_fetch_array($MainResult)) {
	$InvoiceNO=$MainRow["InvoiceNO"];
	$Ship=$MainRow["Ship"];
	$CompanyId=$MainRow["CompanyId"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,23,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId";

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
		  <td  align="right"  >出货方式：</td>
		  <td > <select name='Ship'  id='Ship' style='width:234px' dataType='Require' msg='未选' >
           <?php
           		$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Estate=1 ORDER BY Id",$link_id);
		          if($TypeRow = mysql_fetch_array($shipTypeResult)){
				  do{
				          if ($Ship==$TypeRow["Id"]){
					          echo "<option value='$TypeRow[Id]' selected>$TypeRow[Name]</option>";
				          }
				          else{
					           echo "<option value='$TypeRow[Id]'>$TypeRow[Name]</option>";
				          }

					  } while($TypeRow = mysql_fetch_array($shipTypeResult));
			      }
			 /*
		    switch($Ship){  //CEL 报关，走对公账号4, 其它方式走：上海对公账号 5,
				case 0:

				  	echo "<option value='0' >air</option>";
            		echo "<option value='1'  >sea</option>";
            		echo "<option value='7'  >陆运</option>";
            		echo "<option value='8'  >库存</option>";
            		echo "<option value='9'  >UPS</option>";
            		echo "<option value='10'  >DHL</option>";
            		echo "<option value='11'  >SF</option>";
            		echo "<option value='12'  >Fedx</option>";

					break;
				case 1:  //EUR 走上海对公账号5
				  	echo "<option value='0' >air</option>";
            		echo "<option value='1' selected='selected' >sea</option>";
					break;
				default:
				  	echo "<option value='0' >air</option>";
            		echo "<option value='1' selected='selected' >sea</option>";
					break;
			}
			*/
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