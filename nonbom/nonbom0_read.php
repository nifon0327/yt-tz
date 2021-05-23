<?php 
//EWEN 2013-03-29 OK
include "../model/modelhead.php";
$tableMenuS=600;
ChangeWtitle("$SubCompany 非Bom供应商");
$funFrom="nonbom0";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|编号|60|供应商简称|140|货币|40|结付<br>方式|40|电话|150|传真|100|网址|40|联系人|60|手机|100|默认购买分类|150|评审|40|增值税率|80|备注|200|状态|40|更新日期|80|操作员|50";
$Type=3;//供应商
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";//24

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
	echo"<select name='Orderby' id='Orderby' onchange='ResetPage(this.name)'><option value='Letter' $Orderby1>排序字母</option><option value='Id' $Orderby0>供应商id</option></select>&nbsp;";
  }
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";//联系人中公司的分类 2客户，3供应商...

//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
A.Id,A.CompanyId,A.Letter,A.Forshort,A.PayMode,A.Estate,A.Date,A.Operator,A.Locks,A.PackFile,
B.Tel,B.Fax,B.Website,B.Remark,C.Symbol,T.Name AS AddTaxValueName
FROM $DataPublic.nonbom3_retailermain A
LEFT JOIN $DataIn.nonbom3_retailersheet B ON B.CompanyId=A.CompanyId
LEFT JOIN $DataIn.provider_addtax T ON T.Id = A.AddTaxValue 
LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
WHERE 1 $SearchRows ORDER BY A.Estate DESC $OrderbySTR";
//echo $mySql."<br>";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
       $d=anmaIn("download/providerfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"]; 
		$Forshort=$myRow["Forshort"]; 
		$AddTaxValueName=$myRow["AddTaxValueName"]==""?"&nbsp;":$myRow["AddTaxValueName"];
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
        $TypeStr="";
          $CheckTypeResult=mysql_query("SELECT  T.TypeName,T.Id  FROM $DataPublic.nonbom3_link  L  LEFT JOIN $DataPublic.nonbom2_subtype  T  ON T.Id=L.TypeId  WHERE L.CompanyId=$CompanyId ",$link_id);
        while($CheckTypeRow=mysql_fetch_array($CheckTypeResult)){
              $TypeName=$CheckTypeRow["TypeName"];
              $TypeId=$CheckTypeRow["Id"];
              if($TypeStr=="")$TypeStr=$TypeId."----".$TypeName;
              else $TypeStr=$TypeStr."|".$TypeId."----".$TypeName;
              }
             $TypeStr="<DIV STYLE='width:150px;overflow: hidden; text-overflow:ellipsis' title='$TypeStr'><NOBR>$TypeStr</NOBR></DIV>";


		//加密
		if ($viewRowSign==1){
			$Letter=$myRow["Letter"]==""?"":$myRow["Letter"]."-";
			$CompanyIdTemp=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
			$Forshort="<a href='nonbom3_view.php?d=$CompanyIdTemp' target='_blank'>$Forshort</a>";
		
			$Symbol=$myRow["Symbol"];
            $EstateColor="";
			$checkEstate=mysql_query("SELECT Id FROM $DataIn.providerreview WHERE CompanyId='$CompanyId' AND Estate=2",$link_id);
			if($checkRow = mysql_fetch_array($checkEstate)){
				$EstateColor=" style='background:#EFE769' ";
                } 
			$Judge="<a href='providerdata_review_read.php?CompanyId=$CompanyId' target='_blank'>查看</a>";
			$PayMode=$myRow["PayMode"]==1?"现金":"月结";
			$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
			$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
			$Website=$myRow["Website"]==""?"&nbsp":"<a href='http://$myRow[Website]' target='_blank'>查看</a>";
			//联系人:L.Name,L.Mobile,L.Email,
			$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataPublic.nonbom3_retailerlink WHERE CompanyId='$CompanyId' AND Defaults=0 LIMIT 1",$link_id));
			$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
			$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
			$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";
			$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
			$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
			$Date=$myRow["Date"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
			$Locks=$myRow["Locks"];
			$ValueArray=array(
				array(0=>$CompanyId,1=>"align='center'"),
				array(0=>$Letter.$Forshort,2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Symbol,1=>"align='center'"),
				array(0=>$PayMode,1=>"align='center'"),
				array(0=>$Tel),
				array(0=>$Fax),
				array(0=>$Website,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Linkman),
				array(0=>$Mobile,1=>"align='center'"),
				array(0=>$TypeStr,1=>"align='left' "),
				array(0=>"&nbsp;",1=>"align='center'"),
				array(0=>$AddTaxValueName,1=>"align='center'"),
				
				array(0=>$Remark),
				array(0=>$Estate,1=>"align='center'"),
				array(0=>$Date,1=>"align='center'"),
				array(0=>$Operator,1=>"align='center'")
				);
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