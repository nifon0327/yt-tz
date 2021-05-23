<?php
/*
$DataPublic.net_cpdata
$DataPublic.staffmain
$DataPublic.net_cpsfdata
$DataPublic.net_cpcheckdiary
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=24;
$tableMenuS=600;
ChangeWtitle("$SubCompany 公司固定资产清单");
$funFrom="fixed_assets";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|45|序号|30|资产类型|70|使用<br>公司|70|使用<br>部门|60|领用人|60|数量|30|价格|60|设备名称-型号|150|服务编号|80|条码|110|购买日期|80|购买<br>公司|50|购买人|50|保修期|40|
维护人员|60|维护<br>周期|40|下一次<br>维护时间|75|使用<br>年限|50|状态|60|报废日期|75|说明书|40|操作<br>规程|40|备注|150|销售商|100|操作|50";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";

//表示是那个公司的，在Companys_group中，7表示研砼，5表示鼠宝，3表示皮套。*****************************************
//$MCIDtmp=$MCID_XY==""?"7":$MCID_XY;  //可以把$MCID放在登录时就加载,这样就通用
$cSigntmp=$_SESSION["Login_cSign"];
$cSignSTR=" AND D.cSign=$cSigntmp ";
$httpstr="";  //指向所在地，如图片，要指到原来的地方显示才行

if($From!="slist"){
	//echo "Hereewere! $OMTypeId  : $TypeId";
	$SearchRows ="";

	echo"<select name='OMTypeId' id='OMTypeId' onchange='zhtj(this.name)'>";
        echo"<option value='' selected>--主分类--</option>";
	$checkType= mysql_query("SELECT Id,Name FROM $DataPublic.oa1_fixedmaintype WHERE Estate=1 ORDER BY Letter",$link_id);
	if($TypeRow = mysql_fetch_array($checkType)){
		do{
			$thisTypeId=$TypeRow["Id"];
			$thisName=$TypeRow["Name"];
                        //$TypeId=$TypeId==""?$thisTypeId:$TypeId;
                        if ($thisTypeId==$OMTypeId){
			   echo"<option value='$thisTypeId' selected>$thisName</option>";
                           $SearchRows=" AND O.Id='$OMTypeId'";
                        }else{
                           echo"<option value='$thisTypeId'>$thisName</option>";
                        }
			}while ($TypeRow = mysql_fetch_array($checkType));
		}
		echo"</select>&nbsp;&nbsp;";
	/*
	echo "SELECT T.Id,T.Name,T.MainTypeID FROM $DataPublic.oa2_fixedsubtype  T
							 LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
							 WHERE 1 $SearchRows AND T.Estate=1 ORDER BY T.Letter";*/
	echo"<select name='TypeId' id='TypeId' onchange='zhtj(this.name)'>";
        echo"<option value='' selected>--细分类--</option>";
	$checkType= mysql_query("SELECT T.Id,T.Name,T.MainTypeID FROM $DataPublic.oa2_fixedsubtype  T
							 LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
							 WHERE 1 $SearchRows AND T.Estate=1 ORDER BY T.Letter",$link_id);

	if($TypeRow = mysql_fetch_array($checkType)){
		do{
			$SubTypeId=$TypeRow["Id"];
			$SubName=$TypeRow["Name"];
			$thisMainTypeID=$TypeRow["MainTypeID"];
                        //$TypeId=$TypeId==""?$thisTypeId:$TypeId;
                        if ($SubTypeId==$TypeId){
			   echo"<option value='$SubTypeId' selected>$SubName</option>";
                           $SearchRows=" AND D.TypeId='$TypeId'";
                        }else{
                           echo"<option value='$SubTypeId'>$SubName</option>";
                        }
			}while ($TypeRow = mysql_fetch_array($checkType));
		}
		echo"</select>&nbsp;&nbsp;";


	echo"<select name='BranchId' id='BranchId' onchange='zhtj(this.name)'>";
        echo"<option value='' selected>使用部门</option>";
	$checkType= mysql_query("SELECT Id,Name FROM $DataPublic.branchdata WHERE Estate=1 ORDER BY Id",$link_id);

	if($TypeRow = mysql_fetch_array($checkType)){
		do{
			$BranchIdTypeId=$TypeRow["Id"];
			$BranchIdName=$TypeRow["Name"];
                        //$TypeId=$TypeId==""?$thisTypeId:$TypeId;
                        if ($BranchIdTypeId==$BranchId){
			   echo"<option value='$BranchIdTypeId' selected>$BranchIdName</option>";
			   			  //$SearchRows.=" AND B.Id='$BranchId'";
                           $SearchRows.=" AND D.BranchId='$BranchId'";
                        }else{
                           echo"<option value='$BranchIdTypeId'>$BranchIdName</option>";
                        }
			}while ($TypeRow = mysql_fetch_array($checkType));
		}
		echo"</select>&nbsp;&nbsp;";

		  //选择公司名称
     $cSignTB="D";$SelectFrom=1;
     $EstateChoose="9";//给杰藤用显示杰藤公司
      include "../model/subselect/cSign.php";
}

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
include "../model/subprogram/Getstaffname.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT D.Id,D.CpName,D.Qty,D.price,C.CShortName,C.cSign,D.BranchId,K.CShortName as BuyCompany,D.BuycSign,D.Buyer,
D.Model,D.SSNumber,D.BuyDate,D.ServiceLife,D.MTCycle,D.Estate,D.Retiredate,D.Warranty,D.Attached,T.Name AS TypeName,D.Remark,
D.Date,D.Operator,D.Locks,S.Id AS CId,D.CompanyId,S.Forshort,D.Operator 
FROM $DataPublic.fixed_assetsdata D 
LEFT JOIN $DataPublic.companys_group  C ON C.cSign=D.cSign
LEFT JOIN $DataPublic.companys_group  K ON K.cSign=D.BuycSign 
LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
LEFT JOIN $DataPublic.dealerdata S ON S.CompanyId=D.CompanyId
LEFT JOIN (  SELECT D.Mid,M.Name  
                          FROM ( SELECT Max(X.SDate),X.Id,X.User
                                         FROM  $DataPublic.fixed_assetsdata D
								         LEFT JOIN  $DataPublic.fixed_userdata  X ON X.Mid=D.Id 
								         WHERE  X.UserType=1 GROUP BY  X.Mid ) X 
                          LEFT JOIN $DataPublic.fixed_userdata D ON D.Id=X.Id
                          LEFT JOIN $DataPublic.staffmain M ON M.Number=X.User 
                   ) M ON M.Mid=D.Id 
WHERE 1 $SearchRows  ORDER BY C.Id,T.Id ";
//echo "$mySql <Br>";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$CompanyData="$DataPublic.dealerdata";
	do{
		$m=1;
		$Id=$myRow["Id"];
		$BoxCode='8'.str_pad($Id,11,"0",STR_PAD_LEFT);  //条码不够位前边补800000****
		$BoxCode=GetCode($BoxCode,13,"0",1);  //获得条码: include "subprogram/Getstaffname.php";
		$cSign=$myRow["cSign"];
		//使用的公司
		$CShortName=$myRow["CShortName"];
		$CShortName="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"fixed_assets_upMCID\",\"$Id\")' src='../images/edit.gif' alt='更改使用公司' width='13' height='13'>$CShortName";

		//获取使用人员,维修人员表
		$User="&nbsp;";
		$UserDate="";
		$User_Date=GetSName_Date($Id,'fixed_userdata','1',$DataIn,$DataPublic,$link_id);
		if($User_Date!=""){
			$Temp_User=explode('|',$User_Date);
			$User=$Temp_User[0];
			$UserDate=$Temp_User[1];
			//$User="<div  title='$UserDate'>$User</div>";
		}
		$User="<span  title='$UserDate'>$User</span>";
		$User="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"fixed_assets_upUser\",\"$Id\")' src='../images/edit.gif' alt='更新领用人' width='13' height='13'>$User";

		//$User=GetSName_Date($Id,'fixed_userdata','1',$DataIn,$link_id);
		$maintainer="&nbsp;";
		$maintainerDate="";
		$maintainer_Date=GetSName_Date($Id,'fixed_userdata','2',$DataIn,$DataPublic,$link_id);
		if($maintainer_Date!=""){
			$Temp_maintainer=explode('|',$maintainer_Date);
			$maintainer=$Temp_maintainer[0];
			$maintainerDate=$Temp_maintainer[1];
			//$maintainer="<div  title='$maintainerDate'>$maintainer</div>";
		}
		$maintainer="<span  title='$maintainerDate'>$maintainer</span>";
		$maintainer="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"fixed_assets_upMaintainer\",\"$Id\")' src='../images/edit.gif' alt='维护记录操作' width='13' height='13'>$maintainer";

		//$maintainer=GetSName_Date($Id,'fixed_userdata','2',$DataIn,$link_id);

		$CpName=$myRow["CpName"];
		$TypeName=$myRow["TypeName"];
		$Qty=$myRow["Qty"];
		$price=$myRow["price"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"]==""?"&nbsp;":$myRow["Forshort"];
		$Idc=anmaIn($CompanyData,$SinkOrder,$motherSTR);
		$CId=$myRow["CId"];
		$Ids=anmaIn($CId,$SinkOrder,$motherSTR);
		//echo "$CompanyId <br>";
		if ($CompanyId==-1)
		{
			$CResult = mysql_query("SELECT Company FROM $DataPublic.company_assets WHERE Mid=$Id ",$link_id);
			//echo "SELECT Company FROM $DataPublic.company_assets WHERE Mid=$Id <br>";
			if($CRow = mysql_fetch_array($CResult)){
				$Company=$CRow["Company"];
				$KIdstr=anmaIn($Id,$SinkOrder,$motherSTR);
				//$Forshort="<a href='companyinfo_assets.php?Mid=$Id' target='_blank'>$Company</a>";
				$Forshort="<a href='companyinfo_assets.php?d=$KIdstr' target='_blank'>$Company</a>";
				}
		}
		else{
			$Forshort="<a href='../admin/companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";
		}

		$BranchId=$myRow["BranchId"];
		$Branch="&nbsp";
		/*
		if(($cSign==$cSigntmp)){
			$Branch=GetBranchName($BranchId,$DataIn,$DataPublic,$link_id);
		}
		*/
	    $Branch=GetBranchName($BranchId,$DataIn,$DataPublic,$link_id);

		$BuyCompany=$myRow["BuyCompany"];
		$Buyer=$myRow["Buyer"];
		$Model=$myRow["Model"];
		$IDdSTR=anmaIn($Id,$SinkOrder,$motherSTR);
		//$ModelStr="<a href='fixed_assets_view.php?d=$IDdSTR' target='_blank'>$Model</a>";
		$BoxCode="<a href='fixed_assets_view.php?d=$IDdSTR' target='_blank'>$BoxCode</a>";

        $SSNumber=$myRow["SSNumber"]==""?"&nbsp;":$myRow["SSNumber"];
		$BuyDate=$myRow["BuyDate"];
		$tempBuyDate=$BuyDate;
		$ServiceLife=$myRow["ServiceLife"];   //使用年限
		$MTCycle=$myRow["MTCycle"];   //维修周期
		$Warranty=$myRow["Warranty"];    //保修年限
		/*$Attached=$myRow["Attached"];
		if($Attached!=""){
			$CpName="<a href='../download/cpreport/$Attached' target='_blank'>$CpName</a>";
			}*/
		$FixedIDSTR=anmaIn($Id,$SinkOrder,$motherSTR);
    	//$CpName=
		$CpNameStr="<a href='fixed_maintain_view.php?f=$FixedIDSTR' target='_blank' title='点击进入保养详情'>$CpName</a>";

		$Date=$myRow["Date"];
		//$Remark=$myRow["Remark"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//计算保修期是否已过
		$BDate=date("Y",strtotime($BuyDate))+$Warranty."-".date("m",strtotime($BuyDate))."-".date("d",strtotime($BuyDate));
		$BuyDate=$BDate>=date("Y-m-d")?"<span class='greenB'>$BuyDate</span>":"<span class='redB'>$BuyDate</span>";

		$Estate=$myRow["Estate"];
		if($Estate==1){ //在使用需要维护
			$NexMTDate=$maintainerDate==""?$tempBuyDate:$maintainerDate; //如果有最新维修期，否则用采购日期，则加上维修周期
			//echo "NexMTDate:$NexMTDate";
			$NexMTDate=date("Y-m-d",strtotime($NexMTDate."+ $MTCycle day"));
			//echo "NexMTDate:$NexMTDate :MTCycle:$MTCycle";
			if($NexMTDate<date("Y-m-d")){ //如果超过时间了，则显示红色
				$NexMTDate="<b style='color:#F00;'>$NexMTDate</b>";
			}
			else {
				$NexMTDate="<div>$NexMTDate</div>";
			}
		}
		switch($Estate){
			case 0:
				$Estate="<div class='redB' title='报废'>×报废</div>";
				$User="<div class='redB' title='报废'>×报废</div>";
				break;
			case 1:
				$Estate="<div class='greenB' title='使用中'>√使用中</div>";
				break;
			case 3://配件名称审核中
				$Estate="<div class='yellowB' title='闲置'>√闲置</div>";
				break;
			}
		$Retiredate=$myRow["Retiredate"];
		//echo "Estate:$Estate";
                //获得附件
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
                          //$Attached1="<a href='../download/fixedFile/" . $FileName . "' target='_blank'>view</a>"; break;
                       case 3:
                           $Warranty="<span onClick='OpenOrLoad(\"$Dir\",\"$FileName\")' style='CURSOR: pointer;color:#F00;'>$Warranty</span>"; break;
                       case 4:
                          $WorkFile="<a href=\"../admin/openorload.php?d=$Dir&f=$FileName&Type=&Action=6\" target=\"download\">view</a>";break;
                          //$Attached1="<a href='../download/fixedFile/" . $FileName . "' target='_blank'>view</a>"; break;
                     }

                 }
		   if($cSign==9){//给杰藤用的。
	            $Branch="&nbsp;";
                $User="&nbsp;";
	            }
		$ValueArray=array(
			//array(0=>$CpNameStr, 	1=>"align='center'"),
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$CShortName,	1=>"align='center'"),
			array(0=>$Branch,1=>"align='center'"),
			array(0=>$User,1=>"align='left'"),
			array(0=>$Qty,1=>"align='center'"),
			array(0=>$price,1=>"align='right'"),
			array(0=>$Model),
			array(0=>$SSNumber),
			array(0=>$BoxCode,1=>"align='center'"),
			array(0=>$BuyDate,	1=>"align='center'"),
			array(0=>$BuyCompany,	1=>"align='center'"),
			array(0=>$Buyer,	1=>"align='center'"),
			array(0=>$Warranty ." 年",	 1=>"align='center'"),
			array(0=>$maintainer,1=>"align='left'"),
			array(0=>$MTCycle." 天",1=>"align='center'"),
			array(0=>$NexMTDate,1=>"align='center'"),
			array(0=>$ServiceLife." 年",	 1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Retiredate,1=>"align='center'"),
                        array(0=>$Attached1,1=>"align='center'"),
						array(0=>$WorkFile,1=>"align='center'"),
			array(0=>$Remark,1=>"align='Left'"),
			array(0=>$Forshort,1=>"align='Left'"),
			array(0=>$Operator,1=>"align='center'")
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

<script language="JavaScript" type="text/JavaScript">
function toTempValue(thisE){
	document.form1.TempValue.value=thisE.value;
	}
function Indepot(thisE,SumQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue>SumQty) || thisValue==0){
			alert("不在允许值的范围！");
			thisE.value=oldValue;
			return false;
			}
		}
	}

function zhtj(obj){
	switch(obj){
		case "OMTypeId"://改变采购
			//document.forms["form1"].elements["PayMode"].value="";
			var TypeId= document.getElementById("TypeId");
			if(TypeId!=null){
				TypeId.value="";
				}

		break;
		 /*
		case "PayMode":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		case "CompanyId":
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		*/
		}
	document.form1.action="fixed_assets_read.php";
	document.form1.submit();
}
</script>