<?php 
include "../model/modelhead.php";
$ColsNumber=14;				
$tableMenuS=900;
ChangeWtitle("$SubCompany 公司设备领用");
$funFrom="staff";
$fromWebPage=$funFrom."_read";
$Number= $_GET['f'];
$Th_Col="选项|50|序号|40|资产类型|100|设备名称-型号|220|序列号|140|购买日期|100|购买人|60|保修期|40|维护人员|80|维护周期|60|下一次维护时间|100|使用年限|60|说明书|60|操作规程|60|销售商|100";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
include "../model/subprogram/read_model_3.php";
$cSigntmp=$cSign_XY==""?"7":$cSign_XY;  //可以把$cSign放在登录时就加载,这样就通用
$cSignSTR=" AND D.cSign=$cSigntmp ";
$httpstr="";
if($From!="slist"){
	$SearchRows=" ";
	if($Sign==""){
		   $Sign=0;
		   $TempSignSTR="SignSTR".strval($Sign);
	    }
	else $TempSignSTR="SignSTR".strval($Sign);
	$$TempSignSTR="selected";	

$searchSql="SELECT DISTINCT D.Sign,D.Id, D.Buyer, D.Model, D.BuyDate, D.ServiceLife, D.MTCycle, C.cSign, D.Warranty, T.Name AS TypeName, D.CompanyId, S.Forshort, D.Estate
FROM $DataPublic.fixed_assetsdata D 
LEFT JOIN $DataPublic.fixed_userdata U ON U.MID=D.Id
LEFT JOIN $DataPublic.staffmain M ON U.User=M.Number
LEFT JOIN $DataPublic.companys_group  C ON C.cSign=D.cSign
LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
LEFT JOIN $DataPublic.dealerdata S ON S.CompanyId=D.CompanyId
LEFT JOIN (
     SELECT A.User ,A.MID,A.SDate
     FROM $DataPublic.fixed_userdata A
     LEFT JOIN (SELECT N.MID, MAX(N.SDate)  AS SDate FROM  $DataPublic.fixed_userdata N WHERE N.UserType=1  GROUP BY N.MID
)B ON B.MID = A.MID  WHERE 1 AND B.SDate=A.SDate AND A.UserType=1 ) Z ON Z.MID=D.Id 
WHERE 1 AND Z.User=$Number and C.cSign='7' and D.Sign=1"; 
//echo $searchSql;
$searchResult = mysql_query($searchSql." $PageSTR",$link_id);
if(mysql_fetch_array($searchResult)){
	echo "<select name='Sign' id='Sign' onchange='ResetPage(this.name)'>";
	echo "<option value=0 $SignSTR0>个人</option>";
	echo "<option value=1 $SignSTR1>公用</option>";
	echo "</select>&nbsp";
	$SearchRows=" and D.Sign=$Sign";
}
else{
	echo "<select name='Sign' id='Sign' onchange='ResetPage(this.name)'>";
	echo "<option value=0 $SignSTR0>个人</option>";
	echo "</select>&nbsp";
	$SearchRows=" and D.Sign=$Sign";
}
}
$SearchRows.=" AND D.Estate=1 ";
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
include "../admin/subprogram/Getstaffname.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

$mySql="SELECT DISTINCT D.Id, D.Buyer, D.Model, D.BuyDate, D.ServiceLife, D.MTCycle, C.cSign, D.Warranty, T.Name AS TypeName, D.CompanyId, S.Forshort, D.Estate,D.SSNumber
FROM $DataPublic.fixed_assetsdata D 
LEFT JOIN $DataPublic.fixed_userdata U ON U.MID=D.Id
LEFT JOIN $DataPublic.staffmain M ON U.User=M.Number
LEFT JOIN $DataPublic.companys_group  C ON C.cSign=D.cSign
LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
LEFT JOIN $DataPublic.dealerdata S ON S.CompanyId=D.CompanyId
LEFT JOIN (
    SELECT A.User ,A.MID,A.SDate
    FROM $DataPublic.fixed_userdata A
    LEFT JOIN (SELECT N.MID, MAX(N.SDate)  AS SDate  FROM  $DataPublic.fixed_userdata N  WHERE N.UserType=1  group by N.MID
)B ON B.MID = A.MID  WHERE 1 AND B.SDate=A.SDate AND A.UserType=1 ) Z ON Z.MID=D.Id 
WHERE 1 AND Z.User=$Number and C.cSign='$Login_cSign' $SearchRows "; 
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$CompanyData="$DataPublic.dealerdata";
	do{
		$m=1;
		$Id=$myRow["Id"];
        $Buyer=$myRow["Buyer"];
        $Model=$myRow["Model"];
        $cSign=$myRow["cSign"];
        $BuyDate=$myRow["BuyDate"];
        $CompanyId=$myRow["CompanyId"];
        $ServiceLife=$myRow["ServiceLife"];
        $Warranty=$myRow["Warranty"]; 
        $TypeName=$myRow["TypeName"];
        $Forshort=$myRow["Forshort"]==""?"&nbsp;":$myRow["Forshort"];
        $MTCycle=$myRow["MTCycle"];
        $SSNumber=$myRow["SSNumber"];
        $tempBuyDate=$BuyDate;
        $BDate1=date("Y",strtotime($BuyDate))+"1"."-".date("m",strtotime($BuyDate))."-".date("d",strtotime($BuyDate));
        $BDate2=date("Y",strtotime($BuyDate))+"3"."-".date("m",strtotime($BuyDate))."-".date("d",strtotime($BuyDate));
        if($BDate1>=date("Y-m-d"))$BuyDate="<span class='greenB'>$BuyDate</span>";
        else if($BDate1<date("Y-m-d") && date("Y-m-d")<$BDate2)$BuyDate="<span class='orangeB'>$BuyDate</span>";
        else if($BDate2<=date("Y-m-d"))$BuyDate="<span class='redB'>$BuyDate</span>";
        
        $maintainer="&nbsp;";
		$maintainerDate="";
		//$maintainer_Date=GetSName_Date($Id,'fixed_userdata','2',$cSigntmp,$DataIn,$DataPublic,$link_id);
		if(($cSign==$cSigntmp) && ($maintainer_Date!="")){
			$Temp_maintainer=explode('|',$maintainer_Date);	
			$maintainer=$Temp_maintainer[0];
			$maintainerDate=$Temp_maintainer[1];	
		}
		$maintainer="<span  title='$maintainerDate'>$maintainer</span>";
		
        if ($CompanyId==-1)
		{
			$CResult = mysql_query("SELECT Company FROM $DataPublic.company_assets WHERE Mid=$Id ",$link_id);
			
			if($CRow = mysql_fetch_array($CResult)){
				$Company=$CRow["Company"];
				$KIdstr=anmaIn($Id,$SinkOrder,$motherSTR);
				$Forshort="<a href='companyinfo_assets.php?d=$KIdstr' target='_blank'>$Company</a>";
				}
		      }
		else{
			$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";
		    }
        
        
        $Estate=$myRow["Estate"];
		if($Estate==1){ //在使用需要维护
			$NexMTDate=$maintainerDate==""?$tempBuyDate:$maintainerDate; //如果有最新维修期，否则用采购日期，则加上维修周期
			$NexMTDate=date("Y-m-d",strtotime($NexMTDate."+ $MTCycle day")); 
			if($NexMTDate<date("Y-m-d")){ //如果超过时间了，则显示红色
				$NexMTDate="<b style='color:#F00;'>$NexMTDate</b>";
			}
			else {
				$NexMTDate="<div>$NexMTDate</div>";
			}
		}	
		
		
		$Attached1="&nbsp;";
				 $WorkFile="&nbsp;";
                 $Dir="download/fixedFile/";
                 $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);	
                 $AttachedResult = mysql_query("SELECT Id,FileName,Type  FROM $DataPublic.fixed_file WHERE Mid=$Id order by Type",$link_id); 
                 while ($AttachedRow=mysql_fetch_array($AttachedResult)){
                     $FileName=$AttachedRow["FileName"];
                     $FileName=anmaIn($FileName,$SinkOrder,$motherSTR);	
                     switch($AttachedRow["Type"]){
                       case 1:
                           $Model="<span onClick='OpenOrLoad(\"$Dir\",\"$FileName\")' style='CURSOR: pointer;color:#F00;'>$Model</span>"; break;
                       case 2: 
                          $Attached1="<a href=\"../admin/openorload.php?d=$Dir&f=$FileName&Type=&Action=6\" target=\"download\">view</a>";break;
                       case 3:
                           $Warranty="<span onClick='OpenOrLoad(\"$Dir\",\"$FileName\")' style='CURSOR: pointer;color:#F00;'>$Warranty</span>"; break;
                       case 4: 
                          $WorkFile="<a href=\"../admin/openorload.php?d=$Dir&f=$FileName&Type=&Action=6\" target=\"download\">view</a>";break;			   
                     }   
                 }	
        
        $ValueArray=array(
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$Model,1=>"align='center'"),
			array(0=>$SSNumber,1=>"align='center'"),
			array(0=>$BuyDate,	1=>"align='center'"),
			array(0=>$Buyer,	1=>"align='center'"),
			array(0=>$Warranty ." 年",	 1=>"align='center'"),
			array(0=>$maintainer,1=>"align='center'"),
			array(0=>$MTCycle." 天",1=>"align='center'"),
			array(0=>$NexMTDate,1=>"align='center'"),
			array(0=>$ServiceLife." 年",	 1=>"align='center'"),
            array(0=>$Attached1,1=>"align='center'"), 
            array(0=>$WorkFile,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
  	
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>  
        