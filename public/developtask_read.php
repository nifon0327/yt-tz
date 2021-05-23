<?php 
/*电信---yang 20120801
$DataIn.development
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
?>
<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;background:#bbb;margin:10px auto;width:220px; } 
.imgContainer {position:relative; top:-5px;left:-5px;background:#fff;border:1px solid #555;padding:0;} 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php 
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=10;
$tableMenuS=500;
ChangeWtitle("$SubCompany 开发任务列表");
$funFrom="developtask";
$From=$From==""?"read":$From;
$Th_Col="选项|55|序号|40|客户|60|项目编号|60|项目名称|280|产品效果图|80|AI图档|70|数量|60|开发负责人|80|登记时间|80|样品交期|80|备注|60|项目登记人|80";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,3,96,94,15,23";			

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
$SearchRows.=" and D.Estate=2";
//开发人员
if($From!="slist"){	

  //检查进入者是否有开发记录:是则默认显示该员工的记录，否则显示读入的第一个员工记录
	$checkSql = mysql_query("SELECT D.Developer,P.Name FROM $DataIn.development D 
	               LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Developer 
				   WHERE 1 $SearchRows AND D.Developer!='' AND D.Developer=$Login_P_Number GROUP BY D.Developer ORDER BY D.Developer",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		$Number=$Number==""?$Login_P_Number:$Number;//首次打开页面时，如果员工有记录，则为默认
		}

	$developerSql = mysql_query("SELECT D.Developer,P.Name FROM $DataIn.development D 
	               LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Developer 
				   WHERE 1 $SearchRows AND D.Developer!='' GROUP BY D.Developer ORDER BY D.Developer",$link_id);
	if($developerRow = mysql_fetch_array($developerSql)){
		echo"<select name='Number' id='Number' onchange='document.form1.submit();'>";
		do{
			$DeveloperId=$developerRow["Developer"];
			$DevelopName=$developerRow["Name"];
			if ($Number==$DeveloperId){
				  echo "<option value='$DeveloperId' selected>$DevelopName</option>";
				  $SearchRows.=" AND D.Developer='$DeveloperId'";
				}
			else{
				  echo "<option value='$DeveloperId'>$DevelopName</option>";
				}
			}while ($developerRow = mysql_fetch_array($developerSql));
		echo"</select>&nbsp;";
		}
	//状态为已审核,未审核。
	
	}		
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.ItemId,D.Attached,D.ItemName,D.Content,D.StartDate,D.Locks,D.Operator,D.EndDate,C.Forshort,P.Name,D.Qty,D.Plan,D.Developer,D.Gfile
FROM $DataIn.development D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Developer 
WHERE 1 $SearchRows ORDER BY D.ItemId desc";
/*echo "SELECT D.Id,D.ItemId,D.Attached,D.ItemName,D.Content,D.StartDate,D.Locks,D.Operator,D.EndDate,C.Forshort,P.Name,
D.Qty,D.Plan,D.Developer
FROM $DataIn.development D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Developer WHERE 1 $SearchRows ORDER BY D.ItemId desc";*/
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Forshort=$myRow["Forshort"];
		$Qty=$myRow["Qty"]==0?"&nbsp;":$myRow["Qty"];
		$ItemId=$myRow["ItemId"];
		$Plan=$myRow["Plan"]==""?"":"开发进度:".$myRow["Plan"];
		$ItemName="<span title='$Plan'>$myRow[ItemName]</span>";
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"]=="0000-00-00"?"&nbsp;":$myRow["EndDate"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";	
		$Name=$myRow["Name"]==""?"&nbsp;":$myRow["Name"];
		$Content=trim($myRow["Content"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Content]' width='16' height='16'>";
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/kfimg/",$SinkOrder,$motherSTR);
		if($Attached!=0){
		$f=anmaIn($Attached,$SinkOrder,$motherSTR);//加密字串
		//$Attached=$myRow["Attached"]==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
		}
		else{
		$Attached="&nbsp;";
		}
		$Gfile=$myRow["Gfile"];
		if($Gfile!=0){
		$f1=anmaIn($Gfile,$SinkOrder,$motherSTR);//加密字串
		$Gfile="<a href=\"openorload.php?d=$d&f=$f1&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='' alt='ai图档' width='18' height='18' style='border:0'></a>";}
		else{
		$Gfile="&nbsp;";
		}
		//$Locks=$myRow["Locks"];
		//如果权限非最高，则锁定非自己的项目
		$Developer=$myRow["Developer"];
		if($Developer!=$Login_P_Number){
			$Locks=0;
	        $LockRemark="非自己的项目锁定";
			}
		//已添加配件的任务突出显示
		$staffSql="SELECT * FROM $DataIn.developsheet WHERE  ItemId='$ItemId'";
		$staffResult=mysql_query($staffSql,$link_id);
		if($staffRow=mysql_fetch_array($staffResult)){
		$ItemColor="style='color:#FF3366'";}
		else{$ItemColor="&nbsp;";}
		
		 $showPurchaseorder="<img onClick='Showaddstuff(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏新增配件明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;
			</div><br></td></tr></table>";
		$ValueArray=array(
			0=>array(0=>$Forshort,1=>"align='center'"),
			1=>array(0=>$ItemId,1=>"align='center'"),
			2=>array(0=>$ItemName,1=>"align='left' $ItemColor"),
			3=>array(0=>$Attached,1=>"align='center'"),
			4=>array(0=>$Gfile,1=>"align='center'"),
			5=>array(0=>$Qty,1=>"align='center'"),
			6=>array(0=>$Name,1=>"align='center'"),
			7=>array(0=>$StartDate,1=>"align='center'"),
			8=>array(0=>$EndDate,1=>"align='center'"),
			9=>array(0=>$Content,1=>"align='center'"),
			10=>array(0=>$Operator,1=>"align='center'")
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
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language="JavaScript">

function updateRelation(TableId,RowId,runningNum,sId){//行即表格序号;列，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;//表格名称
		InfoSTR="<input name='sId' type='hidden' id='sId' value='"+sId+"'><input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='6' class='TM0000' readonly>的开发项目所属配件对应关系:<input name='Relation' type='text' id='Relation' size='8' maxlength='8' class='INPUT0100'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+RowId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		theDiv.filters.revealTrans.apply();//防止错误
		theDiv.filters.revealTrans.play(); //播放
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	theDiv.filters.revealTrans.apply();
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	}

function aiaxUpdate(RowId){
	var tempTableId=document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var tempRelation=document.form1.Relation.value;
	var tempsId=document.form1.sId.value;
	//alert(tempsId);
	var tempRowId=document.form1.ActionRowId.value;
	myurl="developtask_ajax_update.php?ItemId="+temprunningNum+"&Relation="+tempRelation+"&sId="+tempsId+"&Action=jq";
    retCode=openUrl(myurl);
	if (retCode!=-2){
		eval(tempTableId).rows[RowId].cells[4].innerHTML=tempRelation;
		CloseDiv();
	   }
	}
</script>

