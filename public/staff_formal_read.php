<?php 
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;
$tableMenuS=700;
ChangeWtitle("$SubCompany 需转正员工资料列表");
$funFrom="staff_formal";
$nowWebPage=$funFrom."_read";
	$Th_Col="选项|40|序号|40|工作地点|60|员工ID|50|姓名|60|身份证号码|130|部门|50|小组|80|职位|50|等级|50|考勤|40|移动电话|80|短号|60|邮件|40|分机号|40|入职日期|75|在职时间|80|性别|40|血型|40|籍贯|40|社保|50|入职档案|60|介绍人|50|iPhone|50|iPad|50|MacBook|50";
$ColsNumber=22;
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS = '169,170';
$Today=date("Y-m-d");
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	   $SearchRows="";
		if($FormalSign>0){$SearchRows.=" AND M.FormalSign='$FormalSign'";}
		
		/*$Formal=$Formal==""?1:$Formal;
		$selStr="Selected".$Formal;
		$$selStr="selected";
		$FormalDate="DATE_ADD(M.ComeIn,INTERVAL 2 MONTH)";
		echo"<select name='Formal' id='Formal' onchange='ResetPage(this.name)'>
			 <option value='1' $Selected1>已过转正期</option>
			 <option value='2' $Selected2>1---5天</option>
			 <option value='3' $Selected3>5---10天</option>";
		echo "</select>&nbsp;";
		switch($Formal){
		    //case 0: $SearchRows.="";break;
		    case 1: $SearchRows.=" AND datediff($FormalDate,'$Today')<'0'";break;
			case 2: 
			$SearchRows.=" AND datediff($FormalDate,'$Today')<='5' AND datediff($FormalDate,'$Today')>='0'";            break;
			case 3:
			$SearchRows.=" AND datediff($FormalDate,'$Today')<='10' AND datediff($FormalDate,'$Today')>'5'";            break;
		}*/
		
	}
//echo $SearchRows;
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//增加快带查询Search按钮
$searchtable="$DataPublic.staffmain|M|Name|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
$searchfile="../model/subprogram/Quicksearch_ajax.php";
include "../model/subprogram/QuickSearch.php";

//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
<tr>
<td class='A0010' width=\"70\" align=\"right\">已过转正期:</td>
<td width=\"50\" align=\"center\"><span class=\"redB\">(红色)></span></td>
<td width=\"65\" align=\"center\">还有5-10天:</td>
<td width=\"60\" align=\"left\"><span class=\"yellowB\">(橙色)</span></td>
<td width=\"70\" align=\"center\">还有1-5天:</td>
<td width=\"50\" align=\"left\"><span class=\"greenB\">(绿色)</span></td>
<td width=\"20\">&nbsp;</td>
<td width=\"50\"><span class=\"redB\">&nbsp;</span></td>
<td class=\"A0001\">&nbsp;</td>
</tr></table>";
//步骤6：需处理数据记录处理
$today=date("Y-m-d");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$sumIPhone=0;
$sumIPad=0;
$sumMacBook=0;
$mySql="SELECT 
	M.Id,M.Number,M.Name,M.Grade,M.Mail,M.ExtNo,M.ComeIn,M.WorkAdd,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,
	S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Dh,S.InFile,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName,M.IdNum,T.Name AS BloodGroup,DATE_ADD(M.ComeIn,INTERVAL 4 MONTH) AS ComeDays 
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	LEFt JOIN $DataPublic.bloodgroup_type T ON T.ID=S.BloodGroup 
	WHERE 1 AND M.Estate=1 $SearchRows AND  M.cSign='$Login_cSign' AND datediff(DATE_ADD(M.ComeIn,INTERVAL 4 MONTH),'$Today')<='10' ORDER BY M.BranchId,M.GroupId,M.JobId,M.ComeIn,M.Number";
//ßecho $mySql;
	$Keys = 31;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$IPhoneIcon="&nbsp;";	
        $IPhoneTitle="";
		$IPadIcon="&nbsp;";		
        $IPadTitle="";
		$MacBookIcon="&nbsp;";	
        $MacBookTitle="";
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$InFile=$myRow["InFile"];
		$gl_STR="&nbsp;";
		$BloodGroup=$myRow["BloodGroup"]==""?"&nbsp;":$myRow["BloodGroup"];
		$MonthSTR=0;
		//****************************年龄计算
		$Birthday =$myRow["Birthday"];
		$Age = date('Y', time()) - date('Y', strtotime($Birthday)) - 1;
		if (date('m', time()) == date('m', strtotime($Birthday))){
			if (date('d', time()) > date('d', strtotime($Birthday))){
				$Age++;
				}
			}
		else{
			if (date('m', time()) > date('m', strtotime($Birthday))){
			$Age++;
			}
		}
		$ViewId=anmaIn($Id,$SinkOrder,$motherSTR);
		if($Age<18){
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='redB'>$myRow[Name]</div></a>";
			}
		else{
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='greenB'>$myRow[Name]</div></a>";
			}
        /***************************/
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$IdNum=$myRow["IdNum"]==0?"&nbsp;":$myRow["IdNum"];
		$Grade=$myRow["Grade"]==0?"&nbsp;":$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"<div class='greenB'>√</div>":($KqSign==2?"<div class='yellowB'>√</div>":"&nbsp;");
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Dh=$myRow["Dh"]==""?"&nbsp;":$myRow["Dh"];
		$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email.gif' title='$myRow[Mail]' width='18' height='18' border='0'></a>";
		$ExtNo=$myRow["ExtNo"]==""?"&nbsp;":$myRow["ExtNo"];
		$ComeIn=$myRow["ComeIn"];
		$Name="<span class='greenB'>$Name</span>";
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Birthday=$myRow["Birthday"];
		$Rpr=$myRow["Rpr"];
		$Idcard=$myRow["Idcard"];
		$WorkAddFrom=$myRow["WorkAdd"];
		include "../model/subselect/WorkAdd.php";
/*********************************************/
		//需要提取此人领用过的物品记录，且属于最后领用人的			
		$checkMyHDSql=mysql_query("
					SELECT C.TypeId,D.User,C.Model,C.MID FROM $DataPublic.fixed_userdata D,
					   (SELECT A.MID,MAX(A.SDate) AS SDate,B.TypeId,B.Model
					      FROM $DataPublic.fixed_userdata A 
					      LEFT JOIN $DataPublic.fixed_assetsdata B ON A.MID=B.Id
					    WHERE B.TypeId IN (14,17,46) AND A.UserType=1 GROUP BY A.MID
                    ) C WHERE D.User='$Number' AND D.UserType=1 AND C.MID=D.MID AND C.SDate=D.SDate GROUP BY D.MID
					",$link_id);
     		if($checkMyHDRow=mysql_fetch_array($checkMyHDSql)){
			do{
				   $TypeId=$checkMyHDRow["TypeId"];
				   $Mid=$checkMyHDRow["Mid"];
				   $Model=$checkMyHDRow["Model"];
				   $User=$checkMyHDRow["User"];
				   switch($TypeId)
				  {
					case 14:	//MacBook
						$MacBookIcon="<a href='staff_receive.php?f=$Number&Mid=$Mid' target='_blank'>查看</a>";
						$MacBookTitle="Title=\"$Model\"";
						$sumMacBook++;
						break;	
					case 17:	//iPad
						$IPadIcon="<a href='staff_receive.php?f=$Number&Mid=$Mid' target='_blank'>查看</a>";
						$IPadTitle="Title=\"$Model\"";
						$sumIPad++;
						break;
					case 46:	//iPhone
						$IPhoneIcon="<a href='staff_receive.php?f=$Number&Mid=$Mid' target='_blank'>查看</a>";
						$IPhoneTitle="Title=\"$Model\"";
						$sumIPhone++;
						break;
				      }
				  }while($checkMyHDRow=mysql_fetch_array($checkMyHDSql));
			  }
		//********籍贯
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if ($rResult ){
				if($rRow = mysql_fetch_array($rResult)){
					$Rpr=$rRow["Name"];
					}
		    }
		//********社保
		$Idcard=$myRow["Idcard"]==""?"&nbsp;":$myRow["Idcard"];

		$sbResult = mysql_query("SELECT Id FROM $DataPublic.sbdata WHERE Number=$Number order by Id LIMIT 1",$link_id);
		if ($sbResult ){
				if($sbRow = mysql_fetch_array($sbResult)){
					$ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
					$Sb="<a href='staff_sbview.php?f=$ViewNumber' target='_blank'>查看</a>";
					}
                 else{
                         $Sb="&nbsp;";
                        }
			}
        //********介绍人
		$Introducer=$myRow["Introducer"];
		if ($Introducer){
			$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Introducer order by Id",$link_id);
			if($iRow = mysql_fetch_array($iResult)){
				   $Introducer=$iRow["Name"];
			       }
		      }
		else{
			   $Introducer="&nbsp;";
			}
         /*********************************************/
		 //工龄计算
		 include "subprogram/staff_model_gl.php";
         /*********************************************/

        /*********************************************/
        //员工请假超过半个月的显示颜色
        include "../model/subprogram/staff_qj_day.php";
        /*********************************************/
        //********入职档案
		if($InFile==1){
			$FileName="I".$Number.".pdf";
			$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
			$td=anmaIn("download/staffPhoto/",$SinkOrder,$motherSTR);			
			$InFile="<span onClick='OpenOrLoad(\"$td\",\"$tf\",6)' style='CURSOR: pointer;' class='yellowB'>查看</span>";
			}
		else{
			$InFile="&nbsp;";
			}
		$GroupName=$myRow["GroupName"]==""?"<div class=\"redB\">未设置</div>":$myRow["GroupName"];//小组
       $ComeDays=$myRow["ComeDays"];
        $Days=ceil((strtotime($ComeDays)-strtotime($Today))/3600/24);
        if($Days<0){
             $qjcolor="style='background:#FF0000' title='已过转正期'";
                 }
         else{
             if($Days<5){
                    $qjcolor="style='background:#FF6633' title='还有1-5天'";
                  }
            else{
                    $qjcolor="style='background:#009900' title='还有5-10天'";
                   }
          }
		$Locks=$myRow["Locks"];
			$ValueArray=array(
				array(0=>$WorkAdd,1=>"align='center'"),
				array(0=>$Number,1=>"align='center' $qjcolor"),
				array(0=>$Name,1=>"align='center'"),
				array(0=>$Idcard,1=>"align='center'"),
				array(0=>$Branch,1=>"align='center'"),
				array(0=>$GroupName,1=>"align='center'"),
				array(0=>$Job,1=>"align='center'"),
				array(0=>$Grade,1=>"align='center'",4=>$GradeHidden),
				array(0=>$KqSign,1=>"align='center'"),
				array(0=>$Mobile,1=>"align='center'"),
				array(0=>$Dh,1=>"align='center'"),
				array(0=>$Mail,1=>"align='center'"),
				array(0=>$ExtNo,1=>"align='center'"),
				array(0=>$ComeIn,1=>"align='center'"),
				array(0=>$Gl_STR,1=>"align='center'"),
				array(0=>$Sex,1=>"align='center'"),
				array(0=>$BloodGroup,1=>"align='center'"),
				array(0=>$Rpr,1=>"align='center'"),
				array(0=>$Sb,1=>"align='center'"),
				array(0=>$InFile,1=>"align='center'"),
				array(0=>$Introducer,1=>"align='center'"),
				array(0=>$IPhoneIcon,1=>"align='center' $IPhoneTitle"),
				array(0=>$IPadIcon,1=>"align='center' $IPadTitle"),
				array(0=>$MacBookIcon,1=>"align='center' $MacBookTitle")				 
				);
		$checkidValue=$Number;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		
echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";		
echo "<tr>";
echo "<td class='A0111' align='center' height='30'>iPhone,iPad,MacBook统计</td>";
echo "<td class='A0101' width=50 align='center' height='30'>$sumIPhone</td>";	
echo "<td class='A0101' width=50 align='center' height='30'>$sumIPad</td>";
echo "<td class='A0101' width=50 align='center' height='30'>$sumMacBook</td>";
echo "</tr></table>";
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