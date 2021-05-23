<?php 
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 任务分配");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_assign";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"] = $nowWebPage;
//步骤3：
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
 $Lens=count($checkid);
            for($i=0;$i<$Lens;$i++){
	        $Id=$checkid[$i];
	           if ($Id!=""){
		        $Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		        }
	       }
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ItemId,$ItemId,ActionId,93";
//步骤4：需处理
$upResult = mysql_query("SELECT * FROM $DataIn.development WHERE Id IN ($Ids)",$link_id);
if($upRow = mysql_fetch_array($upResult)){
$TempItemId="";
$TempItemName="";
    do{
    $CompanyId=$upRow["CompanyId"];
	$ItemId=$upRow["ItemId"];
	$ItemName=$upRow["ItemName"];
	if($TempItemId=="")$TempItemId=$ItemId;
	else $TempItemId=$TempItemId."/".$ItemId;
	if($TempItemName=="")$TempItemName=$ItemName;
	else $TempItemName=$TempItemName."/".$ItemName;
    }while($upRow = mysql_fetch_array($upResult));
	}

?>
<form name="form" enctype="multipart/form-data" action="" method="post" >
<input name="ItemId" type="hidden" id="ItemId">
<input  name="Ids" type="hidden" id="Ids" value="<?php  echo $Ids?>">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
   	  <td width="260" height="26" align="right" class="A0010" scope="col">项目编号:</td>
    	<td scope="col" class="A0001"><?php  echo $TempItemId?></td>
	</tr>
	<tr>
	  <td width="260" height="26" align="right" class="A0010" scope="col">项目名称:</td>
	  <td class="A0001" scope="col"><?php  echo $TempItemName?></td>
  </tr>

	<tr>
		<td width="260" height="26" align="right" class="A0010" scope="col">开发人员:</td>
    	<td scope="col" class="A0001">
		<select name="Developer" id="Developer" size="1" style="width: 250px;" dataType="Require"  msg="未选择">
		<option value='' selected>请选择</option>
		<?php  
		$result = mysql_query("SELECT * FROM $DataPublic.staffmain WHERE BranchId=5 AND Estate=1 ORDER BY Id",$link_id);
		if($myrow = mysql_fetch_array($result)){
			do{	
				if($myrow[Number]==$Developer){
					echo"<option value='$myrow[Number]' selected>$myrow[Name]</option>";
					}
				else{
					echo"<option value='$myrow[Number]'>$myrow[Name]</option>";
					}
				} while ($myrow = mysql_fetch_array($result));
			}
		?>
        </select>
		</td>
	</tr>
</table>		
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>    </select>
		</td>
	</tr>
</table>		