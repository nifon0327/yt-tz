<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplemail
$DataPublic.freightdata
$DataIn.ch10_mailaddress
$DataIn.trade_object
$DataPublic.staffmain
$DataIn.ch10_samplepicture
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=18;
$tableMenuS=600;
$sumCols="10,11,13";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 客户样品寄送费用审核列表");
$funFrom="ch_samplemailing";
$Th_Col="选项|40|序号|30|寄件日期|70|快递公司|60|客户|70|目的地|120|提单号码|100|发票|40|样品<br>照片|40|寄送<br>进度|40|件数|40|重量<br>(KG)|40|单价|40|金额|50|经手人|50|签收日期|70|状态|40|备注|180";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$pResult = mysql_query("SELECT P.CompanyId,P.Forshort  
		FROM $DataIn.ch10_samplemail F  
		LEFT JOIN $DataPublic.freightdata P ON P.CompanyId=F.CompanyId WHERE 1 AND F.Estate='2' GROUP BY P.CompanyId ORDER BY P.CompanyId",$link_id);
		if($pRow = mysql_fetch_array($pResult)){
			echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
			echo"<option value='' selected>全部</option>";
			do{
				$Forshort=$pRow["Forshort"];
				$thisCompanyId=$pRow["CompanyId"];
				if($CompanyId==$thisCompanyId){
					echo"<option value='$thisCompanyId' selected>$Forshort </option>";
					$SearchRows.=" and S.CompanyId='$thisCompanyId'";
					}
				else{
					echo"<option value='$thisCompanyId'>$Forshort</option>";
					}
				}while($pRow = mysql_fetch_array($pResult));
			echo"</select>&nbsp;";
			}
		}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
$d=anmaIn("download/samplemail",$SinkOrder,$motherSTR);
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
S.Id,S.Mid,S.DataType,S.CompanyId,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,
S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,S.Locks,S.Operator
,P.Name AS HandledBy,C.Forshort AS Client,D.Forshort AS Freight,
M.Termini 
FROM $DataIn.ch10_samplemail S
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.ch10_mailaddress M ON M.Id=S.LinkMan
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.HandledBy
WHERE 1 $SearchRows AND S.Estate='2'
ORDER BY S.SendDate DESC,S.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/samplemail/",$SinkOrder,$motherSTR);//寄样图片、进度图片目录
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$SendDate=$myRow["SendDate"];
		$Freight=$myRow["Freight"];
		$Client=$myRow["Client"];
		$Termini=$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		//提单
		$Lading="../download/expressbill/".$ExpressNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO="<span onClick='OpenOrLoad(\"$d2\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
			}
		//发票
		$Invoice="<a href='ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
		//照片
		$SamplePicture="&nbsp;";
		$checkPicture=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
		if($checkPicture["Id"]!=""){			
			$f2=anmaIn($Id,$SinkOrder,$motherSTR);
			$t=anmaIn("ch10_samplepicture",$SinkOrder,$motherSTR);
			$SamplePicture="<span onClick='OpenPhotos(\"$d\",\"$f2\",\"$t\",\"public\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		//进度
		$Schedule=$myRow["Schedule"]==0?"&nbsp;":$myRow["Schedule"];
		if($Schedule==1){
			$f3=anmaIn("Schedule".$Id.".jpg",$SinkOrder,$motherSTR);
			$Schedule="<span onClick='OpenOrLoad(\"$d\",\"$f3\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		$Pieces=$myRow["Pieces"];
		$Weight=$myRow["Weight"];
		$Price=$myRow["Price"];
		$Amount=$myRow["Amount"];
		$HandledBy=$myRow["HandledBy"];		
		$ReceiveDate=$myRow["ReceiveDate"]==""?"&nbsp;":$myRow["ReceiveDate"];
		//$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myrow[Remark]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Locks=1;
		$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$SendDate,
					 1=>"align='center'"),
			array(0=>$Freight),
			array(0=>$Client),
			array(0=>$Termini),
			array(0=>$ExpressNO,					
					 1=>"align='center'"),
			array(0=>$Invoice,
					 1=>"align='center'"),
			array(0=>$SamplePicture,
					 1=>"align='center'"),
			array(0=>$Schedule,
					 1=>"align='center'"),
			array(0=>$Pieces,
					 1=>"align='center'"),
			array(0=>$Weight,
					 1=>"align='right'"),
			array(0=>$Price,
					 1=>"align='right'"),
			array(0=>$Amount,
					 1=>"align='center'"),
			array(0=>$HandledBy,
					 1=>"align='center'"),
			array(0=>$ReceiveDate,
					 1=>"align='right'"),
			array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$Remark)					 
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