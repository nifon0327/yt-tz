<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的IP
if ($donwloadFileIP=="") {
	$donwloadFileIP="..";    //无IP，则用原来的方式
	$donwloadFileaddress="$donwloadFileIP/admin/openorload.php";
}

//步骤2：需处理
$ColsNumber=9;
$tableMenuS=500;
$sumCols="";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 标准图存档");
$funFrom="graphicsfile";

switch ($FileType)
{
	case 2:
		$Th_Col="选项|40|序号|40|产品类型|80|客户|80|分类|80|图档说明|220|下载|80|状态|40|更新日期|70|操作|60";
		break;
	case 5:
		$Th_Col="选项|40|序号|40|类别|80|客户|80|分类|80|检讨主题|220|下载|80|状态|40|更新日期|70|操作|60";
		break;
	default:
		$Th_Col="选项|40|序号|40|产品ID|80|客户|80|分类|80|图档说明|220|下载|80|状态|40|更新日期|70|操作|60";
	break;
}


//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Estate=$Estate==""?1:$Estate;
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
	
if($From!="slist"){
	$SearchRows="AND S.FileType=1";
	$result =  mysql_query("SELECT S.FileType,T.Name 
			FROM $DataIn.doc_standarddrawing S 
			LEFT JOIN $DataPublic.doc_type T ON T.Id=S.FileType
			WHERE 1 GROUP BY S.FileType ORDER BY T.Id ",$link_id);  //WHERE 1 AND T.Id IN (1,2) GROUP BY S.FileType ORDER BY T.Id 
	if($myrow = mysql_fetch_array($result)){
		echo "<select name='FileType' id='FileType' onchange='ResetPage(this.name)'>";
		//echo "<option value='' selected>全部</option>";
		do{
			$FileId=$myrow["FileType"];
			if ($FileType==$FileId){
				echo "<option value='$FileId' selected>$myrow[Name]</option>";
				$SearchRows=" AND S.FileType='$FileId'";
				}
			else{
				echo "<option value='$FileId'>$myrow[Name]</option>";
				}
		}while($myrow = mysql_fetch_array($result));
		echo"</select>&nbsp;";
		}
		
		switch ($FileType)
		{
			case 5:
			$result = mysql_query("SELECT * FROM $DataPublic.errorcasetype order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
			echo"<select name='Type' id='Type' onchange='ResetPage(this.name)'>";
			echo "<option value='' selected>全部</option>";
				do{
					$theTypeId=$myrow["Id"];
					$TypeName=$myrow["Name"];
					//$Type=$Type==""?$theTypeId:$Type;
					if ($Type==$theTypeId){
						echo "<option value='$theTypeId' selected>$TypeName</option>";
						$SearchRows.=" AND P.Id='$theTypeId'";
						}
					else{
						echo "<option value='$theTypeId'>$TypeName</option>";
						}
					}while ($myrow = mysql_fetch_array($result));
					echo "</select>&nbsp;";
				}
				break;
			default:
				//添加客户检索选项
				$result =  mysql_query("
						SELECT S.CompanyId,C.Forshort FROM $DataIn.doc_standarddrawing S 
						LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
						WHERE 1 GROUP BY S.CompanyId ORDER BY C.Forshort",$link_id);
				if($myrow = mysql_fetch_array($result)){
						echo "<select name='Company' id='Company' onchange='ResetPage(this.name)'>";
						echo "<option value='' selected>全部</option>";
						do{
							$ComId=$myrow["CompanyId"];
							if ($Company==$ComId){
								echo "<option value='$ComId' selected>$myrow[Forshort]</option>";
								$ClientTRS=" AND S.CompanyId='$ComId'";
								}
							else{
								echo "<option value='$ComId'>$myrow[Forshort]</option>";
								}
							}while  ($myrow = mysql_fetch_array($result));
							echo"</select>&nbsp;";
						}		

			break;
		}
    		
		
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);


switch ($FileType)
{
	case 5:
		$mySql="SELECT S.Id,S.FileRemark,S.FileName,S.CompanyId,S.Estate,S.Locks,S.Date,S.Operator,S.FileType AS FileId,
		T.Name AS FileType,'--'  AS Company,P.Name as TypeName,S.ProductType
		FROM $DataIn.doc_standarddrawing S 
		LEFT JOIN $DataPublic.doc_type T ON T.Id=S.FileType 
		LEFT JOIN $DataPublic.errorcasetype P ON P.Id=S.ProductType
		WHERE 1 $SearchRows  ORDER BY S.Date desc";
		break;
	default:
		$mySql="SELECT S.Id,S.FileRemark,S.FileName,S.CompanyId,S.Estate,S.Locks,S.Date,S.Operator,S.FileType AS FileId,
		T.Name AS FileType,C.Forshort AS Company,C.Id AS CID,P.TypeName,S.ProductType
		FROM $DataIn.doc_standarddrawing S 
		LEFT JOIN $DataPublic.doc_type T ON T.Id=S.FileType 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
		LEFT JOIN $DataIn.producttype P ON P.TypeId=S.ProductType
		WHERE 1 $SearchRows $ClientTRS ORDER BY S.Date desc";
	
	break;
}
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/standarddrawing/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$FileId=$myRow["FileId"];
		$ProductType=$myRow["ProductType"];
		$FileType=$myRow["FileType"];
		$FileRemark=$myRow["FileRemark"];
		$FileName=$myRow["FileName"];
		$Company=$myRow["Company"];
		$TypeName=$FileId==1?$ProductType:$myRow["TypeName"];
		$f=anmaIn($FileName,$SinkOrder,$motherSTR);

        $CompanyId = $myRow["CompanyId"];
        $Forshort = $myRow["Forshort"];
        $CID = $myRow["CID"];

		/*
		$FileName="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='$style' width='18' height='18'></a>";
		*/
		//$FileName="<a href=\"$donwloadFileaddress?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='$style' width='18' height='18'></a>";
		/* by.lwh 20180416 */
        $FileName="<a href=\"../design/dwgFiles/$CID/Pord/$FileName\" target=\"download\"><img src='../images/down.gif' style='$style' width='18' height='18'></a>";


        $Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Estate=$myRow["Estate"]==0?"<div class='redB'>×</div>":"<div class='greenB'>√</div>";
		$Locks=$myRow["Locks"];
		$ValueArray=array(	
		    array(0=>$TypeName,	1=>"align='center'"),	 
			array(0=>$Company,	1=>"align='center'"),
			array(0=>$FileType),
			array(0=>$FileRemark,1=>"align='left'"),
			array(0=>$FileName,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
