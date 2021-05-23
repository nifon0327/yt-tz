<?php
ChangeWtitle("$SubCompany 客户授权书");
$Th_Col="选项|45|序号|45|客户|120|授权文件名称|400|授权截止|90|授权书档案|80|上传者|60|状态|40";
//步骤3：
include "../model/subprogram/read_model_3.php";
    $TypeResult = mysql_query("SELECT T.Id,T.Name
                            FROM  $DataPublic.zw2_hzdoctype T   WHERE 1 GROUP BY T.Id",$link_id);
	if($TypeRow = mysql_fetch_array($TypeResult)) {
		echo"<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
       echo"<option value='' selected>全部</option>";	
		do{			
              $thisTypeId=$TypeRow["Id"];
              $thisName=$TypeRow["Name"];
              //$TypeId=$TypeId==""?$thisTypeId:$TypeId;
			  if($TypeId==$thisTypeId){
				     echo"<option value='$thisTypeId' selected>$thisName</option>";
				    // $SearchRows.=" and T.Id='$thisTypeId' ";
				     }
			  else{
				      echo"<option value='$thisTypeId'>$thisName</option>";					
				    }
			}while ($TypeRow = mysql_fetch_array($TypeResult));
		echo"</select>&nbsp;";
		}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.Caption,D.TimeLimit,D.Attached,D.Estate,D.Locks,D.Operator,C.Forshort 
FROM $DataIn.yw7_clientproxy D
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId 
WHERE 1 $SearchRows ORDER BY D.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Forshort=$myRow["Forshort"];
		$Caption=$myRow["Caption"];
		$TimeLimit=$myRow["TimeLimit"];
		$TimeLimit=$TimeLimit>date("Y-m-d")?"<div class='greenB'>$TimeLimit</div>":"<div class='redB'>$TimeLimit</div>";
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/clientproxy/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			//$Attached="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		else{
			$Attached="-";
			}
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $proxyResult = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS  proAmount FROM $DataIn.yw7_clientproduct A
                               LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
                               LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
                              WHERE A.cId='$Id'",$link_id));
        $proAmount=$proxyResult ["proAmount"];
//动态读取
		if($proAmount>0){$showPurchaseorder="<img onClick='Showproxy(proxyList$i,showtable$i,proxyList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示此客户的授权产品.' width='13' height='13' style='CURSOR: pointer'>";}
        else $showPurchaseorder="";
		$proxyTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='proxyList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showproxyTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$Caption),
			array(0=>$TimeLimit,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
        echo $proxyTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
?>
