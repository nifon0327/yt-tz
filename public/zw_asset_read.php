<?php 
/*
$DataIn.zw1_assetrecord
$DataIn.zw1_brandtypes
$DataIn.zw1_assetuse
$DataIn.zw1_assetuse
$DataPublic.staffmain
二合一已更新
电信-joseph
*/
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 手机领用记录");
$funFrom="zw_asset";
$nowWebPage=$funFrom;
$Th_Col="操作|60|序号|40|品牌|120|型号|130|图片|50|机身ID|120|现使用情况|300|现领用人|60|交接日期|80|原领用人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="37,2,3,7,8,41,38";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
   if($From!="slist"){
		echo"<select name='TypeId' id='TypeId' onchange='myAction()'>";
		$checkType= mysql_query("SELECT Id,Name FROM $DataIn.zw1_assettypes WHERE Estate=1 AND Type=5 ORDER BY Id",$link_id);
		if($TypeRow = mysql_fetch_array($checkType)){			
			do{
				$thisTypeId=$TypeRow["Id"];
				$thisName=$TypeRow["Name"];			
				if($TypeId==$thisTypeId){
					echo"<option value='$thisTypeId' selected>$thisName</option>";
					$SearchRows=" and R.TypeId='$TypeId'";
					}
				else{
					echo"<option value='$thisTypeId'>$thisName</option>";
					}
				}while ($TypeRow = mysql_fetch_array($checkType));
			}	
			echo"<option value='0'>需求</option>";
			echo"</select>&nbsp;";
		
		//品牌
		echo"<select name='BrandId' id='BrandId' onchange='myAction()'>";
		echo"<option value='' selected>全部品牌</option>";
		$checkType= mysql_query("SELECT R.BrandId,B.Name FROM $DataIn.zw1_assetrecord R 
		LEFT JOIN $DataIn.zw1_brandtypes B ON B.Id=R.BrandId
		WHERE R.Estate=1 $SearchRows GROUP BY R.BrandId ORDER BY B.Name",$link_id);
		if($TypeRow = mysql_fetch_array($checkType)){			
			do{
				$thisId=$TypeRow["BrandId"];
				$thisName=$TypeRow["Name"];						
				if($BrandId==$thisId){
					echo"<option value='$thisId' selected>$thisName</option>";
					$SearchRows.=" and R.BrandId='$BrandId'";
					}
				else{
					echo"<option value='$thisId'>$thisName</option>";
					}
				}while ($TypeRow = mysql_fetch_array($checkType));
			}	
			echo"</select>&nbsp;";
		//领用人
		echo"<select name='User' id='User' onchange='myAction()'>";
		echo"<option value='' selected>全部领用人</option>";
		$checkType= mysql_query("
			SELECT P.Name AS UserName,U.User FROM $DataIn.zw1_assetrecord R 
			LEFT JOIN $DataIn.zw1_brandtypes B ON B.Id=R.BrandId	
			INNER JOIN(
					SELECT U1.AssetId,U1.User,U1.Remark,U1.Date,U1.Estate,U1.Operator FROM $DataIn.zw1_assetuse U1 
						INNER JOIN(SELECT AssetId,MAX(Id) AS Id FROM $DataIn.zw1_assetuse group by AssetId) U2
						ON U1.AssetId=U2.AssetId and U1.Id=U2.Id 
				) U ON U.AssetId=R.Id
			LEFT JOIN $DataPublic.staffmain P ON P.Number=U.User
			WHERE R.Estate=1 $SearchRows AND U.User>0 GROUP BY U.User",$link_id);
		if($TypeRow = mysql_fetch_array($checkType)){			
			do{
				$Number=$TypeRow["User"];
				$thisName=$TypeRow["UserName"];
					if($User==$Number){
						echo"<option value='$Number' selected>$thisName</option>";
						$SearchRows.=" and U.User='$Number'";
						}
					else{
						echo"<option value='$Number'>$thisName</option>";
						}
				}while ($TypeRow = mysql_fetch_array($checkType));
			}	
			echo"</select>";
		}
	else{
		echo"<input name='CencalS' type='checkbox' id='CencalS' value='1' checked onclick='javascript:ToReadPage(\"$Login_help\",\"$Pagination\")'><LABEL for='CencalS'>查询结果</LABEL>";
		}
echo"$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$DefaultBgColor=$theDefaultColor;
$mySql="SELECT R.Id,R.Model,R.Photo,R.delSign,R.Number,B.Name AS Brand,U.User,P.Name AS UserName,U.Remark,U.Date,U.Estate,U.Operator
	FROM $DataIn.zw1_assetrecord R 
	LEFT JOIN $DataIn.zw1_brandtypes B ON B.Id=R.BrandId	
	INNER JOIN(
			SELECT U1.AssetId,U1.User,U1.Remark,U1.Date,U1.Estate,U1.Operator FROM $DataIn.zw1_assetuse U1 
				INNER JOIN(SELECT AssetId,MAX(Id) AS Id FROM $DataIn.zw1_assetuse group by AssetId) U2
				ON U1.AssetId=U2.AssetId and U1.Id=U2.Id 
		) U ON U.AssetId=R.Id
	LEFT JOIN $DataPublic.staffmain P ON P.Number=U.User
	WHERE R.Estate=1 $SearchRows ORDER BY R.delSign,R.BrandId,R.Model";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Model=$myRow["Model"];
		$Photo=$myRow["Photo"];
		$delSign=$myRow["delSign"];
		$Number=$myRow["Number"]==""?"&nbsp;":$myRow["Number"];
		$Brand=$myRow["Brand"];
		$Remark=$myRow["Remark"];
		$Date=$myRow["Date"];
		$UserName=$myRow["UserName"];
		$showSign="1";
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		$theDefaultColor=$DefaultBgColor;
		$User=$myRow["User"];
		if($User==$Login_P_Number){
			$Locks=1;}
		if($Estate==0){
			$Operator="初始记录";
			}
		else{
			include "../model/subprogram/staffname.php";
			}
			//加密
			if($Photo==1){
				$d=anmaIn("download/mobile/",$SinkOrder,$motherSTR);		
				$f=anmaIn("Mobile".$Id.".jpg",$SinkOrder,$motherSTR);
				$Photo="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>查看</span>";
				}
			else{
				$Photo="&nbsp;";
				}
		
		if($delSign==1){
			$theDefaultColor="#FFA6D2";
			$UserName="&nbsp;";
			}
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'><tr bgcolor='#B7B7B7'><td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$ValueArray=array(
			array(0=>$Brand),
			array(0=>$Model),
			array(0=>$Photo,1=>"align='center'"),
			array(0=>$Number,
				1=>"align='center'"),
			array(0=>$Remark,
				3=>"..."),
			array(0=>$UserName,
				1=>"align='center'"),
			array(0=>$Date,
				1=>"align='center'"),
			array(0=>$Operator,
				1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
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
<!--
function InitAjax(){ 
	var ajax=false;
	try{   
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){   
　　	try{   
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){   
　　　		ajax=false;
			}   
　		} 
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}   
　	return ajax;
	}

//16	显示或隐藏配件采购单列表
function ShowOrHide(e,f,Order_Rows,Id,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(Id!=""){			
			var url="../admin/zw_asset_ajax.php?Id="+Id+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}
function ToReadPage(nowWebPage,Sign){
	if(document.all("Page")!=null){
		document.form1.Page.value=1;
		}
	if(document.all("From")!=null){
		document.form1.From.value="";
		}
	if(document.all("Pagination")!=null){
		if(Sign!=0){
			document.forms["form1"].elements["Pagination"].value="1";
			}
		else{
			document.forms["form1"].elements["Pagination"].value="0";
			}
		//document.forms["form1"].elements["Pagination"].selectedIndex=1; 
		}
	document.form1.action=nowWebPage+".php";
	document.form1.submit();
	}
function myAction(){
	document.form1.action="zw_asset.php";
	document.form1.submit();
	}
//-->
</script>
