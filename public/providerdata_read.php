<?php 
//电信---yang 20120801
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=20;
$tableMenuS=600;
ChangeWtitle("$SubCompany 供应商列表");
$funFrom="providerdata";
$From=$From==""?"read":$From;
if ($ProviderType==1 || $ProviderType==2){
	$ClientSTR=$ProviderType==1?"代购客户名称|150|":"客供客户名称|150|";
}
else{
	$ClientSTR="";
}
$Th_Col="选项|40|序号|40|类型|40|编号|50|供应商简称|140|货币|40|结付<br>方式|40|限交<br>货期|40|电话|150|传真|100|网址|40|联系人|60|手机|100|".$ClientSTR."胶框图|40|提示图|40|评审|40|营业<br>执照|40|税务<br>登记证|40|生产<br>许可证|40|备注|40|图片职责|120|状态|40|更新日期|80|操作员|50";
$Type=3;//供应商
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8,24,13,109,87";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){//排序字母
	$SearchRows=" AND (A.cSign=$Login_cSign OR A.cSign=0)";
	$Orderby=$Orderby==""?"Letter":$Orderby;
	if($Orderby=="Id"){//以供应商ID排序
		$Orderby0="selected";
		$OrderbySTR=",A.CompanyId DESC";
		}
	else{//以供应商首字母排序
		$Orderby1="selected";
		$OrderbySTR=",A.Letter";
		}
	$ProviderType=$ProviderType==""?0:$ProviderType;
    $ProTypeSTR="ProviderType" . $ProviderType;
    $$ProTypeSTR="selected";
	echo"<select name='Orderby' id='Orderby' onchange='ResetPage(this.name)'><option value='Letter' $Orderby1>排序字母</option><option value='Id' $Orderby0>供应商id</option></select>&nbsp;";
    echo"<select name='ProviderType' id='ProviderType' onchange='ResetPage(this.name)'>
    	<option value='0' $ProviderType0>自购</option>
        <option value='1' $ProviderType1>代购</option>
        <option value='2' $ProviderType2>客供</option>
        <option value='99' $ProviderType99>禁用</option>
        <option value='97' $ProviderType97>不合格</option> 
        </select>&nbsp;";
	$ProviderTypeSign=0;
    switch($ProviderType){
    	case 97:
			$SearchRows.="";
			$ProviderTypeSign=97;
			break;
		case 99://禁用
			$SearchRows.=" AND A.Estate='0'";
			break;
		default:
			$SearchRows.=" AND A.Estate='1' AND A.ProviderType='$ProviderType' ";
			break;
        }
  }
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr<input name='Type' type='hidden' id='Type' value='3'>";//联系人中公司的分类 2客户，3供应商...

//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
A.Id,A.CompanyId,A.Letter,A.Forshort,A.ProviderType,A.GysPayMode,A.Estate,A.Date,A.Operator,A.Locks,B.Tel,B.Fax,B.Website,B.Remark,C.Symbol,A.PackFile,A.TipsFile,A.Prepayment,A.LimitTime    
FROM $DataIn.trade_object A
LEFT JOIN $DataIn.providersheet S ON A.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
WHERE 1 AND (B.Type='$Type' OR B.Type IS NULL) $SearchRows ORDER BY A.Estate DESC,A.ProviderType DESC $OrderbySTR";
//echo $mySql;
//B.Type 1客户 	3:供应商		3:forward公司		4:快递公司
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
       $d=anmaIn("download/providerfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];        
		//最后一次评审是否合格
        $ColbgColor="";
		$Results="";
        $viewRowSign=1;
        $ReviewResult=mysql_query("SELECT Results FROM $DataIn.providerreview WHERE CompanyId=$CompanyId ORDER BY Year DESC LIMIT 1",$link_id);
        if($Review_Row = mysql_fetch_array($ReviewResult)){
        	$Results=$Review_Row["Results"];
            }
		else{
        	$checkResults=mysql_fetch_array(mysql_query("SELECT Results FROM $DataIn.providersheet WHERE CompanyId='$CompanyId'  LIMIT 1",$link_id));
            $Results=$checkResults["Results"];
            }
		if($Results==4) $ColbgColor=" bgcolor='#FF0000' ";        
		if($ProviderTypeSign==97) {
			if ($Results==4) $viewRowSign=1; else $viewRowSign=0;
            }
		//加密
		if ($viewRowSign==1){
			$Idc=anmaIn("providerdata",$SinkOrder,$motherSTR);
			$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
			$Letter=$myRow["Letter"]==""?"":$myRow["Letter"]."-";
			//加密
			$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
			$Symbol=$myRow["Symbol"];
            $EstateColor="";
			$checkEstate=mysql_query("SELECT Id FROM $DataIn.providerreview WHERE CompanyId='$CompanyId' AND Estate=2",$link_id);
			if($checkRow = mysql_fetch_array($checkEstate)){
				$EstateColor=" style='background:#EFE769' ";
                } 
			$Judge="<a href='providerdata_review_read.php?CompanyId=$CompanyId' target='_blank'>查看</a>";
			switch($myRow["ProviderType"]){
		    	case 0:$ProviderType="自购";break;
			  	case 1:$ProviderType="<span class='redB'>代购";break;
			  	case 2:$ProviderType="<span style='color:#FF33FF'>客供";break;
				}
			
			$ClientTdArray=array();$ClientTd_STR="";
			if ($ClientSTR!=""){
			    $checkClient=mysql_query("SELECT C.CompanyId,C.Forshort 
			    FROM  $DataIn.cg1_stocksheet  S  
			    LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
                LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
                LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber = Y.OrderNumber  
			    LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=YM.CompanyId 
			    WHERE M.CompanyId='$CompanyId' AND C.cSign='7' GROUP BY C.CompanyId",$link_id);
			    while($ClientRow = mysql_fetch_array($checkClient)){
			        $ClientTd_STR.=$ClientTd_STR==""?$ClientRow['Forshort']:"," . $ClientRow['Forshort'];
                } 
                $ClientTdArray=$ClientTd_STR==""?array(0=>"&nbsp;"):array(0=>"$ClientTd_STR");
  			}
			/*
			$GysPayMode=$myRow["GysPayMode"]==1?"现金":"月结";
			if ($myRow["GysPayMode"]==1){
				$GysPayMode=$myRow["Prepayment"]==1?"<div title='先付款' class='redB'>$GysPayMode</div>":$GysPayMode;
			}
			*/
			$GysPayMode=$myRow["GysPayMode"];
			//echo "GysPayMode:$GysPayMode";
            switch($GysPayMode){
				case 0:
				    $GysPayMode="30天";
					break;
				case 1:
				    $GysPayMode=$myRow["Prepayment"]==1?"<div title='先付款' class='redB'>现金</div>":"现金";
					break;
				case 2:
				    $GysPayMode="60天";
					break;
				case 3:
				    $GysPayMode="45天";
					break;					
				default:
				break;
				
				
			}
			$LimitTime=$myRow["LimitTime"]==2?$myRow["LimitTime"]. "周":"<div style='color:#FF0000'>".$myRow["LimitTime"]. "周</div>";
			$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
			$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
			$Website=$myRow["Website"]==""?"&nbsp":"<a href='http://$myRow[Website]' target='_blank'>查看</a>";
			//联系人:L.Name,L.Mobile,L.Email,
			$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' AND Type='$Type' and Defaults=0 LIMIT 1",$link_id));
			$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
			$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
			$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";
			
			$PackFile=$myRow["PackFile"];
			if ($PackFile==1){
			   $PackFileName="Pack_$CompanyId.png";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$PackFile="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$PackFile="&nbsp;";
			}
			
			$TipsFile=$myRow["TipsFile"];
			if ($TipsFile==1){
			   $PackFileName="Tips_$CompanyId.png";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$TipsFile="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$TipsFile="&nbsp;";
			}
			
			
			$BusinessLicence=$myRow["BusinessLicence"];
			if ($BusinessLicence==1){
			   $PackFileName="B".$CompanyId.".jpg";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
			   $BusinessLicence="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$BusinessLicence="&nbsp;";
			}
			
			
			$TaxCertificate=$myRow["TaxCertificate"];
			if ($TaxCertificate==1){
			   $PackFileName="T".$CompanyId.".jpg";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$TaxCertificate="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$TaxCertificate="&nbsp;";
			}
			
			
			$ProductionCertificate=$myRow["ProductionCertificate"];
			if ($ProductionCertificate==1){
			   $PackFileName="P".$CompanyId.".jpg";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$ProductionCertificate="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$ProductionCertificate="&nbsp;";
			}			
			
			$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
			
			//add by zx 2014-03-27 抓图片职责，找到最多人的那个作为职责
			$PJobname="&nbsp;";
			$checkPNumber=mysql_query("SELECT A.PicNumber,A.MoreCS FROM (
										SELECT S.PicNumber,SUM(1) as MoreCS 
										FROM $DataIn.bps  B
										LEFT JOIN $DataIn.stuffdata S ON S.StuffId=B.StuffId 
										WHERE B.CompanyId='$CompanyId' AND S.Estate>0 AND S.Pjobid!=-1  group by S.PicNumber ) A order by A.MoreCS desc
								   ",$link_id);

			
			if($PNumberRow=mysql_fetch_array($checkPNumber)){
				$PicNumber=$PNumberRow["PicNumber"];
				if ($PicNumber!=0 ){  //说明指定的人
					 $mySql="SELECT j.Id,j.Name,m.Number,m.Name as staffname FROM $DataPublic.jobdata  j
						  LEFT JOIN $DataPublic.staffmain M on J.Id=M.JobId
						  WHERE  M.Number='$PicNumber' ";
				   $result = mysql_query($mySql,$link_id);
				   if($myrow = mysql_fetch_array($result)){
					$jId=$myrow["Id"];
					$jobName=$myrow["Name"];
					$Number=$myrow["Number"];
					$staffname=$myrow["staffname"];
					$PJobname=$jobName."-$staffname";
				   }
					
				}
				
			}
			
			
			$PJobname="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"providerdata_uppicNumber\",\"$Id\")' src='../images/edit.gif' title='更新上传图片职责人' width='13' height='13'>
			<span class='yellowB'>$PJobname</span>";
		
			
			
			$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
			$Date=$myRow["Date"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
			$Locks=$myRow["Locks"];
			$ValueArray=array(
				array(0=>$ProviderType,1=>"align='center'"),
				array(0=>$CompanyId,1=>"align='center'"),
				array(0=>$Letter.$Forshort,2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Symbol,1=>"align='center'"),
				array(0=>$GysPayMode,1=>"align='center'"),
				array(0=>$LimitTime,1=>"align='center'"),
				array(0=>$Tel),
				array(0=>$Fax),
				array(0=>$Website,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Linkman),
				array(0=>$Mobile,1=>"align='left'"),
				array(0=>$PackFile,1=>"align='center' "),
				array(0=>$TipsFile,1=>"align='center' "),
				array(0=>$Judge,1=>"align='center' $EstateColor "),
				array(0=>$BusinessLicence,1=>"align='center'"),
				array(0=>$TaxCertificate,1=>"align='center'"),
				array(0=>$ProductionCertificate,1=>"align='center'"),
				array(0=>$Remark,1=>"align='center'"),
				array(0=>$PJobname),
				array(0=>$Estate,1=>"align='center'"),
				array(0=>$Date,1=>"align='center'"),
				array(0=>$Operator,1=>"align='center'")
				);
			if (count($ClientTdArray)==1) {
			        array_splice($ValueArray,11,0,array($ClientTdArray)); 
			}
			
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
            }//if ($viewRowSign==1)
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