<style type="text/css">
<!--
.list{position:relative;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:200px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
-->
</style>

<?php 
//Create 2011-02-14
//$DataPublic.otdata /$DataPublic.otdatatype 
//电信-joseph
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=9;
$tableMenuS=500;
$sumCols="";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 加工文档列表");
$funFrom="otdata";
$Th_Col="选项|40|序号|40|文档说明|320|文档类别|80|客户|80|文档下载|80|图片展示|80|更新日期|70|操作|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Estate=$Estate==""?1:$Estate;
$Page_Size = 50;							//每页默认记录数量	
$ActioToS="1,2,3,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
	
if($From!="slist"){
	$SearchRows="";
	echo "<select name='ProType' id='ProType' onchange='ResetPage(this.name)'>";
	$result =  mysql_query("SELECT Id,Letter,Name FROM $DataPublic.ottypedata WHERE Estate=1 order by Letter",$link_id);
	echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$ProId=$myrow["Id"];
			if ($ProType==$ProId){
				echo "<option value='$ProId' selected>$myrow[Letter]-$myrow[Name]</option>";
				}
			else{
				echo "<option value='$ProId'>$myrow[Letter]-$myrow[Name]</option>";
				}
			} 
		echo"</select>&nbsp;";
	$ProIdSTR=$ProType==""?"":" AND E.TypeId=".$ProType;
	$SearchRows.=$ProIdSTR;
	//添加客户检索选项
	echo "<select name='ComType' id='ComType' onchange='ResetPage(this.name)'>";
	$result =  mysql_query("SELECT Id,ListName FROM $DataPublic.otdata_kfinfo WHERE 1 order by ListName",$link_id);
	echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$ComId=$myrow["Id"];
			if ($ComType==$ComId){
				echo "<option value='$ComId' selected>$myrow[ListName]</option>";
				}
			else{
				echo "<option value='$ComId'>$myrow[ListName]</option>";
				}
			} 
		echo"</select>&nbsp;";
	$ComIdSTR=$ComType==""?"":" AND P.Id=".$ComType;
	$SearchRows.=$ComIdSTR;
	}
echo"<select name='Pagination' id='Pagi
nation' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT E.Id,E.Name,T.Name as TypeId,E.FileName,E.ImageFlag,E.ImageName,P.ListName,P.Company,P.Name as pName,P.Tel,P.Fax,E.Date,E.Operator,E.Locks 
FROM $DataPublic.otdata E  
LEFT JOIN $DataPublic.ottypedata T ON T.Id=E.TypeId 
LEFT JOIN $DataPublic.otdata_kfinfo P ON P.Id=E.CompanyId 
WHERE 1 $SearchRows 
ORDER BY E.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/otfile/doc/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$TypeId=$myRow["TypeId"];
		$FileName=$myRow["FileName"];
		$ImageFlag=$myRow["ImageFlag"];
		$pCompany=$myRow["Company"];
		$pName=$myRow["pName"];
		$pTel=$myRow["Tel"];
		$pFax=$myRow["Fax"];
		$ImageFile_d=$myRow['ImageName'];
		$ImageFile="../download/otfile/Image/" . $myRow['ImageName'];
		$ListName=$myRow["ListName"];
		if ($ListName==""){
			$ListName="&nbsp;";
		    }
		  else{
		    $ListName="<font title='$pCompany &#10;联系人: $pName &#10;TEL: $pTel &#10;FAX: $pFax'>$ListName</font>";
		  }
		$Tel=$myRow["Tel"];
		switch($ImageFlag){
			case 0:
				$ImageName="请上传图片";
				break;
			case 1:
				 $ImageName=$myRow["ImageName"];
				 $d1=anmaIn("../download/otfile/Image/",$SinkOrder,$motherSTR);
				 $f1=anmaIn($ImageName,$SinkOrder,$motherSTR);
				 $noStatue="onMouseOver=\"window.status='none';return true\"";
                 $ImageName="<div><a class='list' href='../admin/openorload.php?d=$d1&f=$f1&Type=ottype' target='_blank'><img src='$ImageFile'  $noStatue width='28' height='28' border='0'/><span><img src='$ImageFile' $noStatue/></span></a></div>";
				break;
			case 2:
				$ImageName="图片未审核";
				break;
		}
		$f=anmaIn($FileName,$SinkOrder,$motherSTR);
		//$FileName="<img src='../images/down.gif' onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;' width='18' height='18'>";
		$FileName="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='$style' width='18' height='18'></a>";
		
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(		  
			array(0=>$Name),
			array(0=>$TypeId,	1=>"align='center'"),	
			array(0=>$ListName,	1=>"align='center'"),
			array(0=>$FileName,	1=>"align='center'"),
			array(0=>$ImageName,	1=>"align='center'"),
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
