<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}
/* 为 图片 加阴影 */
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; }
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0;}
.imgContainer img {     display:block; }
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php
//电信-EWEN
//代码共享-EWEN
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 系统用户登录资料");
$funFrom="user";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|在线|50|登录名|100|登录密码(已加密)|350|姓名|80|公司或部门|120|用户角色|80|状态|40|最后登录日期|200";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
$uType=$uType==""?1:$uType;

$SelectFrom=1;
$cSignTB="A";
include "../model/subselect/userType.php";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
switch($uType){
	case 1://要区分公司
			$mySql="SELECT A.Id,A.uName,A.uPwd,A.Number,A.lDate,A.Estate,A.Locks,A.uSign,B.Name,concat(C.CShortName,'-',D.Name) AS Branch,E.Name AS RoleName,B.Estate AS mEstate
			FROM $DataIn.UserTable A 
			LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
			LEFT JOIN $DataPublic.companys_group C ON C.cSign=B.cSign
			LEFT JOIN $DataPublic.branchdata D ON D.Id=B.BranchId
            LEFT JOIN $DataIn.ac_roles  E  ON E.id = A.roleId
			WHERE 1 $SearchRows ORDER BY B.cSign DESC,A.Estate DESC,D.SortId,A.Number";
		break;
	case 2:
		//读取用户名称和客户名称
		$mySql="SELECT A.Id,A.uName,A.uPwd,A.Number,A.lDate,A.Estate,A.Locks,A.uSign,B.Name,C.Forshort AS Branch,E.Name AS RoleName
			FROM $DataIn.UserTable A 
			LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
            LEFT JOIN $DataIn.ac_roles  E  ON E.id = A.roleId
			WHERE 1 $SearchRows ORDER BY A.Estate DESC,C.Forshort,B.Id";
		break;
	case 3:
		//读取用户名称和供应商名称
		$mySql="SELECT A.Id,A.uName,A.uPwd,A.Number,A.lDate,A.Estate,A.Locks,A.uSign,B.Name,C.Forshort AS Branch,E.Name AS RoleName
			FROM $DataIn.UserTable A 
			LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
            LEFT JOIN $DataIn.ac_roles  E  ON E.id = A.roleId
			WHERE 1 $SearchRows ORDER BY A.Estate DESC,C.Forshort,B.Id";
		break;
	case 4://外部人员资料
		$mySql="SELECT A.Id,A.uName,A.uPwd,A.Number,A.lDate,A.Estate,A.Locks,A.uSign,B.Name,B.Forshort AS Branch,E.Name AS RoleName
			FROM $DataIn.UserTable A 
			LEFT JOIN $DataIn.ot_staff B ON B.Number=A.Number
            LEFT JOIN $DataIn.ac_roles  E  ON E.id = A.roleId
			WHERE 1 $SearchRows ORDER BY A.Estate DESC,B.Name,B.Number";
		break;
	case 5://参观人员
	break;
	}
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$uType=$myRow["uType"];
		$uName=$myRow["uName"];
		$uPwd=$myRow["uPwd"];
		$Number=$myRow["Number"];
		$lDate=$myRow["lDate"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$uSign=$myRow["uSign"];
		$mEstate=$myRow["mEstate"];
		$Name=$myRow["Name"] ;
		if($uType ==1){
		    $Name = $mEstate>0?$Name:"<span class='redB'>$Name</span>";
		}
		$Branch=$myRow["Branch"];

         $RoleName=$myRow["RoleName"]==""?"&nbsp;":$myRow["RoleName"];
		//在线检测
		$oResult = mysql_query("SELECT uId FROM $DataIn.online WHERE 1 AND uId=$Id ORDER BY uId LIMIT 1",$link_id);
		if($oRow = mysql_fetch_array($oResult)){
			$Online="<div class='greenB'>●</div>";
			}
		else{
			$Online="&nbsp;";
			}
		$ValueArray=array(
			array(0=>$Online,1=>"align='center'"),
			array(0=>$uName),
			array(0=>$uPwd),
			array(0=>$Name),
			array(0=>$Branch),
			array(0=>$RoleName,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,7,$Id,2,\"$uName\")' style='CURSOR: pointer'"),
			array(0=>$Estate,1=>"align='center'"),array(0=>$lDate,1=>"align='center'")
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
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script>
function UpdateFaxNO(FaxF,Id){
	var FaxNO=FaxF.value;
	myurl="user_updated.php?ActionId=9&FaxNO="+FaxNO+"&Id="+Id;
	retCode=openUrl(myurl);
	}

function updateJq(TableId,RowId,runningNum,toObj,uName){//行即表格序号;列
	showMaskBack();
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==25){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	//theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){

			case 2://用户角色
				InfoSTR="<input name='runningNum' type='hidden' id='runningNum' value='"+runningNum+"' size='12' class='TM0000' readonly/>账号为："+uName+"的用户角色<select id='RoleId' name='RoleId' style='width:150px;'><option value='' 'selected'>请选择</option>";

				<?PHP
					$roleResult = mysql_query("SELECT id,name FROM $DataIn.ac_roles  ORDER BY Id",$link_id);
		          if($roleRow = mysql_fetch_array($roleResult)){
				  do{
					           $echoInfo.="<option value='$roleRow[id]'>$roleRow[name]</option>";
					  } while($roleRow = mysql_fetch_array($roleResult));
			      }
				?>
				 InfoSTR=InfoSTR+"<?PHP echo $echoInfo; ?>"+"</select><br>";
				break;
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9;
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
	}

function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
			case "2":		//用户角色
            var msg = "请确定要更新该用户的角色?";
           if(confirm(msg)){
                    var RoleIdObj = document.form1.RoleId;
					var tempRoleId=document.form1.RoleId.value;
					myurl="user_ajax.php?UserId="+temprunningNum+"&RoleId="+tempRoleId+"&ActionId=RoleId";
					//alert (myurl);
					var ajax=InitAjax();
					ajax.open("GET",myurl,true);
					ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200
//alert(ajax.responseText)
							CloseDiv();
					      if (ajax.responseText=="Y") eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML=RoleIdObj.options[RoleIdObj.options.selectedIndex].text;
							else alert("用户角色更新失败!");
							}
						}
					ajax.send(null);
                 }else{
					CloseDiv();
              }
			break;
		}
	}
</script>