<?php 
//电信-ZX  2012-08-01
/*
已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 复制产品标准图");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_copyimg";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：//需处理
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}

if($IdSign==""){
	$tzSTR="AND P.Id IN($Ids)";
	}
else{
	$tzSTR="AND P.ProductId IN($Ids)";
	}
//echo $tzSTR;
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,63,IdSign,1";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
		<table width="820" border="0" id="NoteTable">
          <tr>
            <td width="66" align="right" valign="top">产品列表</td>
            <td width="744" >
       <table width="100%" border="0" align="center" cellspacing="5">
		<tr align="center">
            <td width="44">序号</td>
            <!--<td width="74">客户</td>  -->
            <td width="222">产品中文名</td>
        <td width="179">英文代码</td>
            <td width="181">英文注释</td>
		</tr>
		<?php 
		//取产品资料
		//$myResult =mysql_query("SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Description,C.Forshort FROM $DataIn.productdata P,$DataIn.trade_object C WHERE 1 $tzSTR AND P.CompanyId=C.CompanyId ORDER BY P.Id",$link_id);
		$myResult =mysql_query("SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Description FROM $DataIn.newproductdata P  WHERE 1 $tzSTR  ORDER BY P.Id",$link_id);
		
		if($myRow=mysql_fetch_array($myResult)){
			$i=1;
			do{
				$Id=$myRow["Id"];
				$ProductId=$myRow["ProductId"];
				$cName=$myRow["cName"];
				$eCode=$myRow["eCode"];
				$Description=$myRow["Description"];
				//$Forshort=$myRow["Forshort"];
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$ProductId' checked>";
				//echo"<tr><td align='center'>$Choose$i</td><td>$Forshort</td><td>$cName</td><td>$eCode</td><td>$Description</td></tr>";
				echo"<tr><td align='center'>$Choose$i</td><td>$cName</td><td>$eCode</td><td>$Description</td></tr>";
				$i++;
				}while($myRow=mysql_fetch_array($myResult));
			}
		?>
		</table>
			</td>
          </tr>
          <tr>
            <td align="right">标准图</td>
          <td ><input name="TestStandard" type="file" id="TestStandard" size="79" title="可选项,JPG格式" datatype="Filter" accept="jpg" msg="文件格式不对" row="1" cel="1"></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>