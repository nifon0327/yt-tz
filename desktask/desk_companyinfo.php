<?php   
/*电信---yang 20120801
$DataIn.companyinfo
$DataIn.linkmandata
$DataPublic.currencydata
$DataIn.trade_object
$DataIn.trade_object
$DataPublic.freightdata
$DataPublic.freightdata
$DataPublic.dealerdata
二合一已更新
*/
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=600;
ChangeWtitle("$SubCompany 通讯录资料列表");
$funFrom="desk_companyinfo";
$From=$From==""?"read":$From;
$Type=$Type==""?"2":$Type;
if($Type==2){
	$Th_Col="选项|40|序号|40|编号|40|简 称|80|货币|40|电 话|150|传 真|150|联系人|80|移动电话|100|网 站|50|国家或地区|130|状态|40|备注|40|地图|40";
	}
else{
	$Th_Col="选项|40|序号|40|编号|40|简 称|80|货币|40|电 话|150|传 真|150|联系人|80|移动电话|100|网 站|50|国家或地区|130|状态|40|备注|40";
	}
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//
switch($Type){
	case 2:$Pagination2="selected";$CompanyData="$DataIn.trade_object";$OrderBy="P.Estate DESC,P.OrderBy DESC,P.CompanyId";$SearchRows=" AND P.cSign='$Login_cSign' ";break;
	case 3:$Pagination3="selected";$CompanyData="$DataIn.trade_object";$OrderBy="P.Estate DESC,P.CompanyId";$SearchRows=" AND (P.cSign='$Login_cSign' OR P.cSign='0')";break;
	case 4:$Pagination4="selected";$CompanyData="$DataPublic.freightdata";$OrderBy="P.Estate DESC,P.CompanyId";break;
	case 5:$Pagination5="selected";$CompanyData="$DataPublic.freightdata";$OrderBy="P.Estate DESC,P.CompanyId";break;
	case 6:$Pagination6="selected";$CompanyData="$DataPublic.dealerdata";$OrderBy="P.Estate DESC,P.CompanyId";break;
	}
echo"<select name='Type' id='Type' onchange='document.form1.submit();'>
<option value='2' >客户</option>
<option value='3' $Pagination3>供应商</option>
<option value='4' $Pagination4>Forward公司</option>
<option value='5' $Pagination5>货运公司</option>
<option value='6' $Pagination6>经销商及其它</option>
</select>
  	$CencalSstr
	";
//步骤5：
if($Keys==1){
	$SearchRows.=" and P.Estate=1";
	}
$otherAction="<span onClick='javascript:showMaskDiv(\"$funFrom\")' $onClickCSS>导出通讯录</span>&nbsp;";
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
P.Id,P.CompanyId,P.Forshort,P.Estate,P.Date,P.Operator,P.Locks,
F.Tel,F.Fax,F.Website,F.Area,F.Remark,
L.Name,L.Mobile,L.Email,C.Symbol
FROM $CompanyData P
LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId and L.Defaults=0
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1  and F.Type='$Type' $SearchRows ORDER BY $OrderBy";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		//加密
		$Idc=anmaIn($CompanyData,$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
		$CompanyId=$myRow["CompanyId"];
		$Forshort="<a href='../public/companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
		$Symbol=$myRow["Symbol"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Website=$myRow["Website"]==""?"&nbsp":"<a href='$myRow[Website]' target='_blank'>查看</a>";
		$Area=$myRow["Area"]==""?"&nbsp":$myRow["Area"];
		$Name=$myRow["Name"]==""?"&nbsp":$myRow["Name"];		
		$Mobile=$myRow["Mobile"]==""?"&nbsp":$myRow["Mobile"];
		$Linkman=$myRow["Email"]==""?$Name:"<a href='mailto:$myRow[Email]'>$Name</a>";
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='16' height='16'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		if($Type==2){
			//地图搜索
			//$CheckMapRow=mysql_fetch_array(mysql_query("SELECT MapPath FROM $DataIn.MapPath WHERE CompanyId='$CompanyId' LIMIT 1",$link_id));
			//$MapPath=$CheckMapRow["MapPath"]==""?"&nbsp;":"<a href='".$CheckMapRow["MapPath"]."' target='_blank'>地图</a>";
           $MapPath="&nbsp;";
			$ValueArray=array(
				array(0=>$CompanyId,	1=>"align='center'"),
				array(0=>$Forshort,		2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Symbol, 		1=>"align='center'"),
				array(0=>$Tel),
				array(0=>$Fax),
				array(0=>$Linkman),
				array(0=>$Mobile, 		1=>"align='center'"),
				array(0=>$Website, 		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Area),			
				array(0=>$Estate,		1=>"align='center'"),
				array(0=>$Remark, 		1=>"align='center'"),
				array(0=>$MapPath, 		1=>"align='center'")
				);
			}
		else{
			$ValueArray=array(
				array(0=>$CompanyId,	1=>"align='center'"),
				array(0=>$Forshort,		2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Symbol, 		1=>"align='center'"),
				array(0=>$Tel),
				array(0=>$Fax),
				array(0=>$Linkman),
				array(0=>$Mobile, 		1=>"align='center'"),
				array(0=>$Website, 		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Area),			
				array(0=>$Estate,		1=>"align='center'"),
				array(0=>$Remark, 		1=>"align='center'")
				);
			}
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>

<script language="javascript">
function showMaskDiv(WebPage){	//显示遮罩对话框
	//检查是否有选取记录
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		sOrhDiv(""+WebPage+"");
	}

function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sOrhDiv(WebPage){		
		var url="../admin/"+WebPage+"_mask.php"; 
	　	//var show=eval("divInfo");
	　	var ajax=InitAjax(); 
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
	　		if(ajax.readyState==4){// && ajax.status ==200
				var BackData=ajax.responseText;					
				divInfo.innerHTML=BackData;
				}
			}
		ajax.send(null); 
	}
</script>