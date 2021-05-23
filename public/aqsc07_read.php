<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
$ColsNumber=17;				
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc07";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4,138";
ChangeWtitle("$SubCompany 安全生产培训计划");
$Th_Col="选项|45|序号|30|培训日期|70|培训类型|80|培训对象|60|培训项目|250|培训<br>工时|40|组织<br>单位|40|是否<br>考核|40|授课方式|50|教程<br>文件|40|状态|40|讲师|50|现场<br>图片|40|现场<br>视频|40|签到<br>扫描|40|受训<br>员工|40|核实人|50";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";
	$YearResult = mysql_query("SELECT DATE_FORMAT(A.Date,'%Y') AS Year FROM $DataPublic.aqsc07 A GROUP BY DATE_FORMAT(A.Date,'%Y') ORDER BY A.Date DESC",$link_id);
	if($YearRow = mysql_fetch_array($YearResult)) {
		echo"<select name='chooseYear' id='chooseYear' onchange='document.form1.submit()'>";
		do{
			$theYear=$YearRow["Year"];
			if($chooseYear==$theYear){
				echo "<option value='$theYear' selected>$theYear 年度培训计划</option>";
				$PEADate=" AND DATE_FORMAT(A.Date,'%Y')='$theYear'";
				}
			else{
				echo "<option value='$theYear'>$theYear 年度培训计划</option>";					
				}
			}while($YearRow = mysql_fetch_array($YearResult));
		echo"</select>&nbsp;";
		}
	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.DefaultDate,A.ItemName,A.ItemTime,A.Tutorial,A.Lecturer,A.Reviewer,A.ExamId,A.Date,A.Estate,A.Locks,A.Operator,A.Img,A.Movie,A.List,
B.Name AS theObject,
C.Name AS theOU,
D.Name AS theTeach,
E.Name AS theType,
F.Attached
FROM $DataPublic.aqsc07 A 
LEFT JOIN $DataPublic.aqsc07_object B ON B.Id=A.ObjectId
LEFT JOIN $DataPublic.aqsc07_ou C ON C.Id=A.OUId
LEFT JOIN $DataPublic.aqsc07_teach D ON D.Id=A.TeachId
LEFT JOIN $DataPublic.aqsc07_type E ON E.Id=A.TypeId
LEFT JOIN $DataPublic.aqsc04 F ON F.Id=A.Tutorial
WHERE 1 $SearchRows ORDER BY A.DefaultDate,A.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];
		$DefaultDate=$myRow["DefaultDate"];
		$theType=$myRow["theType"];
		$theObject=$myRow["theObject"];
		$ItemName=$myRow["ItemName"];
		$ItemTime=$myRow["ItemTime"];
		$theOU=$myRow["theOU"];
		$theTeach=$myRow["theTeach"];
		$ExamId=$myRow["ExamId"]==1?"<sapn class='greenB'>是</sapn>":"<sapn class='redB'>否</sapn>";
		$Attached=$myRow["Attached"];
		$FileType=substr($Attached, -3, 3);
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			switch($FileType){
				case "ppt":
				$FileTitle="幻灯片文件";
				break;
				case "mp4":
				$FileTitle="视频文件";
				break;
				case "pdf":
				$FileTitle="PDF文件";
				break;
				}
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/$FileType.gif' title='$FileTitle'/></a>";
			}
		else{
			$Attached="-";
			}
		$Estate=$myRow["Estate"];
		if($Estate==0){
			$Estate="<sapn class='redB'>未执行</sapn>";
			$Img="-";
			$Movie="-";
			$List="-";
			$Lecturer="-";
			$Reviewer="-";
			}
		else{
			$Estate="<sapn class='greenB'>已执行</sapn>";
			$Lecturer=$myRow["Lecturer"];
			$Img=$myRow["Img"]==1?$myRow["Img"]:"-";
			$Movie=$myRow["Movie"]==1?$myRow["Movie"]:"-";
			$List=$myRow["List"]==1?$myRow["List"]:"-";
			$Reviewer=$myRow["Reviewer"];
			if($Img==1){
				$Img="aqsc07_img_".$Id.".pdf";
				$Img=anmaIn($Img,$SinkOrder,$motherSTR);
				$Img="<a href=\"openorload.php?d=$d&f=$Img&Type=&Action=6\" target=\"download\"><img src='../images/pdf.gif' /></a>";
				}
			if($Movie==1){
				$Movie="aqsc07_movie_".$Id.".mp4";
				$Movie=anmaIn($Movie,$SinkOrder,$motherSTR);
				$Movie="<a href=\"openorload.php?d=$d&f=$Movie&Type=&Action=6\" target=\"download\"><img src='../images/mp4.gif' /></a>";
				}
			if($List==1){
				$List="aqsc07_list_".$Id.".pdf";
				$List=anmaIn($List,$SinkOrder,$motherSTR);
				$List="<a href=\"openorload.php?d=$d&f=$List&Type=&Action=6\" target=\"download\"><img src='../images/pdf.gif' /></a>";
				}
			}
		//检查是否存在受训名单
		$checkSubSql=mysql_query("SELECT Id FROM $DataPublic.aqsc08 WHERE ItemId='$Id'",$link_id);
		if($checkSubRow=mysql_fetch_array($checkSubSql)){
			$Loster="<a href='javascript:;' onClick='Model_ShowOrHide($i,Sub$i,\"$funFrom\",$Id);return false;'  style='CURSOR: pointer'>查看</a>";
			$SubTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='Sub$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30' valign='top' align='right'><div id='SubDiv$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			}
		else{
			$Loster="-";
			$SubTB="";
			}
		$ValueArray=array(
			array(0=>$DefaultDate,1=>"align='center'"),
			array(0=>$theType),
			array(0=>$theObject),
			array(0=>$ItemName),
			array(0=>$ItemTime,1=>"align='center'"),
			array(0=>$theOU,1=>"align='center'"),
			array(0=>$ExamId,1=>"align='center'"),		
			array(0=>$theTeach),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Lecturer,1=>"align='center'"),
			array(0=>$Img,1=>"align='center'"),
			array(0=>$Movie,1=>"align='center'"),
			array(0=>$List,1=>"align='center'"),
			array(0=>$Loster,1=>"align='center'"),
			array(0=>$Reviewer,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $SubTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
var xmlHttp;
var requestType = "";
var TempRowId;
function createXMLHttpRequest(){
	if(window.ActiveXObject){
    	xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
       }
	else if(window.XMLHttpRequest){
    	xmlHttp = new XMLHttpRequest();
       }
	}

function startRequest(url){
	var url;
    createXMLHttpRequest();
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
	}

function handleStateChange(){
	if(xmlHttp.readyState == 4){
    	if(xmlHttp.status == 200){
        	listAllStates();
            }
       }
	}

function listAllStates(){
	var tempTB="";
	var j=1;
	var xmlDoc = xmlHttp.responseText;
	jsonData = eval(xmlDoc);//这是关键点，需要用eval处理
	for(var i=0;i<jsonData.length;i++){
		tempTB+="<tr bgcolor='#FFFFFF' align='center'><td height='20'>"+j+"</td><td align='left'>"+jsonData[i][0]+"</td><td >"+jsonData[i][1]+"</td><td>"+jsonData[i][2]+"</td><td >"+jsonData[i][3]+"</td><td>"+jsonData[i][4]+"</td></tr>";
		j++;
		}
	eval("SubDiv"+TempRowId).innerHTML="<table cellspacing='1' border='1'><tr align='center' bgcolor='#CCCCCC'><td width='40' height='20'>序号</td><td width='60'>受训员工</td><td width='60'>部门</td><td width='60'>职位</td><td width='100'>入职日期</td><td width='60'>考核成绩</td></tr>"+tempTB+"</table>";
	}
	
function Model_ShowOrHide(RowId,e,toPage,tempValue){//e:隐藏的表格ID;f:展开与收起图片的名称;	tempId:
	TempRowId=RowId;
	if(e.style.display=="none"){
		e.style.display="";
		if(tempValue!=""){
			var url="../public/"+toPage+"_ajax.php?tempValue="+tempValue+"&RowId="+RowId;
			createXMLHttpRequest();
			xmlHttp.onreadystatechange = handleStateChange;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			}
		}
	else{
		e.style.display="none";
		}
	}
</script>