<?php
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;
$tableMenuS=550;
ChangeWtitle("$SubCompany 车辆信息列表");
$funFrom="car_info";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|车辆类型|60|车辆品牌|60|车牌号|70|车主|60|使用人|60|购车时间|80|保险期限|80|物流公司|200|车主电话|80|状态|60|维修记录|60|违规记录|60|粤通卡|60|加油卡|60|行车里程|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$cSignTB="A";
	$SelectFrom=1;
	require "../model/subselect/cSign.php";
  	require "../model/subselect/CarType.php";
	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.cardata A WHERE 1 $SearchRows  AND UserSign>0 ORDER BY A.Estate DESC,A.cSign,A.TypeId,A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	  do{
	    $m=1;
		$Id=$myRow["Id"];
		$cSignFrom=$myRow["cSign"];
		require"../model/subselect/cSign.php";
		$TypeFrom=$myRow["TypeId"];
		require "../model/subselect/CarType.php";
		$BrandId=$myRow["BrandId"];
		$BrandName=$BrandId>0?"<image src='../images/carbrand_" . $BrandId . ".png' / width='42px'>":"&nbsp;";
		//require "../model/subselect/CarBrand.php";
		$Estate=$myRow["Estate"];
		if($Estate==1) $Estate="<div class='greenB'>√</div>";
		else $Estate="<div class='redB'>×</div>";
		$CarName=$myRow["CarName"];
		$CarNo=$myRow["CarNo"]==""?"&nbsp":$myRow["CarNo"];
		$BuyDate=$myRow["BuyDate"]=="0000-00-00"?"&nbsp":$myRow["BuyDate"];
		$BuyAddress=$myRow["BuyAddress"]==""?"&nbsp":$myRow["BuyAddress"];
		$BuyContact=$myRow["BuyContact"]==""?"&nbsp":$myRow["BuyContact"];
		$Maintainer=$myRow["Maintainer"]==""?"&nbsp":$myRow["Maintainer"];
		$Checktime=$myRow["Checktime"];
		if($Checktime=="0000-00-00"or $Checktime=="")$Checktime="&nbsp;";
		$InsuranceDate=$myRow["InsuranceDate"]=="0000-00-00"?"&nbsp":$myRow["InsuranceDate"];
		$TmpDate=$InsuranceDate;
		$Insurance=$myRow["Insurance"];
		$Enrollment=$myRow["Enrollment"];
		$DriveLic=$myRow["DriveLic"];
		$YueTong=$myRow["YueTong"]==""?"&nbsp;":$myRow["YueTong"];
		$OilCard=$myRow["OilCard"]==""?"&nbsp;":$myRow["OilCard"];
		$BuyStore=$myRow["BuyStore"]==""?"&nbsp;":$myRow["BuyStore"];
		$StoreNum=$myRow["StoreNum"]==""?"&nbsp;":$myRow["StoreNum"];
		$User=$myRow["User"]==""?"&nbsp":$myRow["User"];

		$Dir=anmaIn("download/cardata/",$SinkOrder,$motherSTR);
		if($DriveLic!=""){
			$DriveLic=anmaIn($DriveLic,$SinkOrder,$motherSTR);
			$CarNo="<span onClick='OpenOrLoad(\"$Dir\",\"$DriveLic\")' style='CURSOR: pointer;color:#FF6633'>$CarNo</span>";
		}
		if($Enrollment!=""){
			$Enrollment=anmaIn($Enrollment,$SinkOrder,$motherSTR);
			$Maintainer="<span onClick='OpenOrLoad(\"$Dir\",\"$Enrollment\")' style='CURSOR: pointer;color:#FF6633'>$Maintainer</span>";
		}
		$InsuranceColor="";
		if($Insurance!=""){
			$Insurance=anmaIn($Insurance,$SinkOrder,$motherSTR);
			$InsuranceDate="<span onClick='OpenOrLoad(\"$Dir\",\"$Insurance\")' style='CURSOR: pointer;color:#FF6633'>$InsuranceDate</span>";
			$Deadline=round((strtotime($TmpDate) - strtotime(date("Y-m-d"))) /3600/24);
			if($Deadline<=30 && $Deadline>=0)
				$InsuranceColor=" bgcolor='#FFE1FF'";	//橙色
			else if($Deadline<0)
				$InsuranceColor=" bgcolor='#EEB4B4'";	//红色
			else
				$InsuranceColor="";
		}
		if($YueTong!="&nbsp;"){
			$YueTong=anmaIn($YueTong,$SinkOrder,$motherSTR);
			$YueTong="<span onClick='OpenOrLoad(\"$Dir\",\"$YueTong\")' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		}
		if($OilCard!="&nbsp;"){
			$OilCard=anmaIn($OilCard,$SinkOrder,$motherSTR);
			$OilCard="<span onClick='OpenOrLoad(\"$Dir\",\"$OilCard\")' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		}
		$checkSql =mysql_fetch_array(mysql_query("SELECT  max( sCourses ) AS courses FROM  $DataPublic.info1_business WHERE CarId =$Id",$link_id));
		if($checkSql)
		     {
		      $Courses=$checkSql["courses"]==""?"0":$checkSql["courses"];
		     }

		$ViolationSql=mysql_query("select sum(CarId) from  $DataPublic.car_violation where CarId=$Id",$link_id);
		 if(mysql_fetch_array($ViolationSql))
		 {
			$ViewNumber=anmaIn($Id,$SinkOrder,$motherSTR);
			$Violation="<a href='car_violation_read.php?f=$ViewNumber' target='_blank'>查看</a>";
		 }
		 else
		 {
		  $Violation="&nbsp;";
		 }
		$Repair="<a href='car_repair_read.php?f=$ViewNumber' target='_blank'>查看</a>";
		$Locks=1;
		$ValueArray=array(
			//array(0=>$cSign,1=>"align='center'"),
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$BrandName,1=>"align='center'"),
			array(0=>$CarNo,1=>"align='center'"),
			array(0=>$Maintainer,1=>"align='center'"),
			array(0=>$User,1=>"align='center'"),
			array(0=>$BuyDate,1=>"align='center'"),
			array(0=>$InsuranceDate,1=>"align='center' $InsuranceColor"),
			array(0=>$BuyStore,1=>"align='center'"),
			//array(0=>$BuyAddress),
			array(0=>$StoreNum,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Repair,1=>"align='center'"),
			array(0=>$Violation,1=>"align='center'"),
			array(0=>$YueTong,1=>"align='center'"),
			array(0=>$OilCard,1=>"align='center'"),
			array(0=>$Courses,1=>"align='center'"),
			);

		   $checkidValue=$Id;
	      include "../model/subprogram/read_model_6.php";
	 	}while ($myRow = mysql_fetch_array($myResult));

	 }
else{
	  noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' class='div-mcmain'>
<tr align='center'>
<td class='A0010' width='140' heigth='30'>保险期限底色：</td>
<td width='60' bgcolor='#FFE1FF'>剩1个月</td>
<td width='70' bgcolor='#EEB4B4'>已过期</td>
<td class='A0001'>&nbsp;</td>
</tr></table>";
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);


include "../model/subprogram/read_model_menu.php";
?>