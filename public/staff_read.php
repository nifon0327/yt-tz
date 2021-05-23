<style type="text/css">
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}

.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:100px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
</style>
<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
*/
include "../model/modelhead.php";
include "public_appconfig.php";
$From=$From==""?"read":$From;
//需处理参数
$tableMenuS=1050;
ChangeWtitle("$SubCompany 员工资料列表");
$funFrom="staff";
$nowWebPage=$funFrom."_read";
$ColsNumber=41;
if($Keys & mLOCK){
	$Th_Col="选项|40|序号|30|所属公司|60|工作<br>地点|40|员工ID|45|姓名|55|英文名|70|APP图|35|身份证号码|120|ID卡号|60|部门|60|小组|60|职位|60|等级|30|考勤|30|移动电话|100|短号|50|分机号|40|公司<br>邮箱|40|邮件|40|Apple ID|130|入职日期|70|合同到期日|70|在职时间|70|年龄|30|性别|30|血型|40|籍贯|40|介绍人|50|紧急联系人-电话|100|工作职责|50|社保|40|公积金|50|意外险|65|职业体检|70|健康体检|70|驾照|40|护照|40|通行证|40|入职档案|60|安全培训|50|iPhone|50|iPad|50|MacBook|50|工衣尺寸|150|购房住址|250";
	$GradeHidden="";
	}
else{
	$Th_Col="选项|40|序号|30|所属公司|60|工作<br>地点|40|员工ID|45|姓名|55|英文名|70|APP图|35|身份证号码|120|ID卡号|60|部门|60|小组|60|职位|60|考勤|30|移动电话|100|短号|50|分机号|40|公司<br>邮箱|40|邮件|40|Apple ID|130|入职日期|70|合同到期日|70|在职时间|70|年龄|30|性别|40|血型|30|籍贯|40|介绍人|50|紧急联系人-电话|100|工作职责|50|社保|40|公积金|50|意外险|65|职业体检|70|健康体检|70|驾照|40|护照|40|通行证|40|入职档案|60|安全培训|50|iPhone|50|iPad|50|MacBook|50|工衣尺寸|150|购房住址|250";
	$GradeHidden="Y";
	}
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
if($FormalSign==2){
	$ActioToS="1,2,3,4,7,8,31,69,32,49,105,87";
}
else{
	$ActioToS="1,2,3,4,7,8,31,32,49,105,127,38,87";
}
//步骤3：
include "../model/subprogram/read_model_3.php";
//include "../model/subprogram/read_cSign.php";

$ChooseOffStaffSign=0;

 if($From!="slist"){
	  $cSignTB="M";$SelectFrom=5;
	 //选择公司
	   include "../model/subselect/cSign.php"; 
	    $SelectTB="M";$SelectFrom=1; 
	   include "../model/subselect/WorkAdd.php";        
       //选择部门
	   include "../model/subselect/BranchId.php";   
	  
	  
	$selectResult = mysql_query("SELECT B.GroupId,B.GroupName FROM  $DataIn.staffgroup B  
								 WHERE B.Estate=1  ORDER BY B.GroupId",$link_id);
	if($selectRow = mysql_fetch_array($selectResult)){
				$SelectName="GroupId";
				$SelectListStr="<select name=$SelectName id=$SelectName onchange='document.form1.submit()'>
				<option value='' selected>--全部小组--</option>";
				do{
						$theId=$selectRow["GroupId"];
						$theName=$selectRow["GroupName"];
						if ($theId==$GroupId){
							 $SelectListStr.="<option value='$GroupId' selected>$theName</option>";
							 if ($SelectTB!="") $SearchRows.=" AND $SelectTB.GroupId='$theId' ";
							}
						else{
							$SelectListStr.="<option value='$theId'>$theName</option>";
							}
				}while ($selectRow = mysql_fetch_array($selectResult));
				$SelectListStr.="</select>&nbsp;";
	}
	
	echo $SelectListStr;	  
	  
	//选择员工类别
     include "../model/subselect/FormalSign.php";
     
    //选择考勤楼层 
     include "../model/subselect/FloorAdd.php";
     
     //************入职月份
       echo "<select id='ComeMonth' name='ComeMonth' onchange='ResetPage(this.name)'>";
	   echo "<option value='' selected>--入职月份--</option>";
       for($i=1;$i<=12;$i++){
	      if($i<10)$tempMonth="0".$i;
		  else $tempMonth=$i;
	      $everyMonth=$tempMonth."月";
	      if($i==$ComeMonth){
		     echo "<option value='$i' selected>$everyMonth</option>";
			 $SearchRows.=" AND substring(M.ComeIn,6,2)='$tempMonth'";
		      }
		  else{
	             echo "<option value='$i'>$everyMonth</option>";
	             }
	     }
	    echo "</select>&nbsp;";
//**************************血型
       $bloodResult=mysql_query("SELECT Id,Name FROM  $DataPublic.bloodgroup_type WHERE Estate=1",$link_id);
       if($bloodRow=mysql_fetch_array($bloodResult)){
       echo "<select id='BloodType' name='BloodType' onchange='ResetPage(this.name)'>";
	   echo "<option value='' selected>--血型--</option>";
             do{
                   $bloodId=$bloodRow["Id"];
                   $bloodName=$bloodRow["Name"];
                   if($bloodId==$BloodType){
                          echo "<option value='$bloodId' selected>$bloodName</option>";   
			              $SearchRows.="  AND T.Id='$bloodId'";
                          }
                   else{
                          echo "<option value='$bloodId'>$bloodName</option>";   
                         }
                }while($bloodRow=mysql_fetch_array($bloodResult));
          }
	   echo "</select>&nbsp;";
	 
	 if ( in_array($Login_P_Number, $APP_CONFIG['OFFSTATT_LIST_NUMBER'])) 
	 {
	    $ChooseOffStaffSign=1;
	    $OffStaffSign=$OffStaffSign==''?0:$OffStaffSign;
		echo "<select id='OffStaffSign' name='OffStaffSign' onchange='ResetPage(this.name)'>";
		if ($OffStaffSign==1){
			echo "<option value='0'>在岗</option>"; 
	        echo "<option value='1' selected>编外</option>"; 
		}
		else{
			echo "<option value='0' selected>在岗</option>"; 
	        echo "<option value='1'>编外</option>"; 
		}
	    
	    echo "</select>&nbsp;";
	    
	    $SearchRows.=" AND M.OffStaffSign='$OffStaffSign' ";
	 }
	 
	 
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

//增加快带查询Search按钮
$searchtable="$DataPublic.staffmain|M|Name|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
$searchfile="../model/subprogram/Quicksearch_ajax.php";
include "../model/subprogram/QuickSearch.php";

echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='rs_unpayed_read.php' target='_blank'  title=''><font color='red'>请假后未结付工资显示</font></a>"	;
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$today=date("Y-m-d");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$sumIPhone=0;
$sumIPad=0;
$sumMacBook=0;

if ($APP_FACTORY_CHECK==true) $SearchRows.=" AND M.JobId!='38' AND M.WorkAdd!=6 AND M.Number != '10744'";//验厂
//$SearchRows.= " AND M.Number NOT IN (12175,12176,10192,12205)";
if ($ChooseOffStaffSign=='') $SearchRows.= " AND M.OffStaffSign='0' ";
$mySql="
	SELECT 
	M.Id,M.Number,M.Name,M.Nickname,M.Grade,M.Mail,M.AppleID,M.GroupEmail,M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.WorkAdd,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,
	S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Dh,S.InFile,M.KqSign,S.Photo,S.IdcardPhoto,S.HealthPhoto,S.DriverPhoto,S.Tel,B.Name AS Branch,J.Name AS Job,G.GroupName,M.IdNum,T.Name AS BloodGroup,S.PassPort,S.PassTicket,DU.Description AS Jobduties,J.WorkNote,S.ClothesSize,S.HouseSize,M.cSign
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.staffgroup G ON G.GroupId=M.GroupId
LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
LEFT JOIN $DataPublic.bloodgroup_type T ON T.ID=S.BloodGroup 
LEFT JOIN $DataPublic.attendance_floor AT On M.AttendanceFloor = AT.Id
LEFT JOIN $DataPublic.staff_jobduties  DU ON DU.Number=M.Number
WHERE 1 AND M.Estate=1 $SearchRows ORDER BY B.SortId,M.GroupId,J.SortId,M.JobId,M.ComeIn,M.Number";
// echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$IPhoneIcon="&nbsp;";	$IPhoneTitle="";
		$IPadIcon="&nbsp;";		$IPadTitle="";
		$MacBookIcon="&nbsp;";	$MacBookTitle="";
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Currency=$myRow["Currency"];
		
		$Currency=$Currency=="TWD"?"<span class='redN'>$Currency</sapn>":$Currency;
		
		//屏蔽人员
		include_once("../model/subprogram/factoryCheckDate.php");
		if(skipStaff($Number) && $APP_FACTORY_CHECK)
		{
			continue;
		}
		
		$InFile=$myRow["InFile"];
		$gl_STR="&nbsp;";
		$BloodGroup=$myRow["BloodGroup"]==""?"&nbsp;":$myRow["BloodGroup"];
        $ClothesSize=$myRow["ClothesSize"]==""?"&nbsp;":$myRow["ClothesSize"];
        $HouseSize=$myRow["HouseSize"]==""?"&nbsp;":$myRow["HouseSize"];
        $WorkNote=$myRow["WorkNote"];
        $Jobduties=$myRow["Jobduties"]==""?$WorkNote:$myRow["Jobduties"];
        $Jobduties=$Jobduties==""?"&nbsp;":"<img src='../images/remark.gif' title='$Jobduties' width='18' height='18'>";
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
		
	    $pih="";
		$Photo=$myRow["Photo"];
		if($Photo==0)$pih.="P";
		$IdcardPhoto=$myRow["IdcardPhoto"];
		if($IdcardPhoto==0)$pih.="I";
		$HealthPhoto=$myRow["HealthPhoto"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		//if($HealthPhoto==0)$pih.="H";
		
		//app示图
         $AppFilePath="../download/staffPhoto/P" .$Number.".png";
		   if(file_exists($AppFilePath)){
		         $noStatue="onMouseOver=\"window.status='none';return true\"";
			     $AppFileSTR="<span class='list' >View<span><img src='$AppFilePath' $noStatue/></span>";
			     
			}
           else{
	            $AppFileSTR="&nbsp;";
           }
		if($Age<18){
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='redB'>$myRow[Name]  $pih </div></a>";
			}
		else{
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='greenB'>$myRow[Name]  </div> <div class='redB'> $pih  </div></a>";
			}
        /***************************/
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
	    $IdNum=$myRow["IdNum"]==0?"&nbsp;":$myRow["IdNum"];
         if($myRow["Grade"]!=0){
                 $GradeResult=mysql_fetch_array(mysql_query("SELECT  Id   FROM $DataPublic.redeployg  WHERE Number=$Number",$link_id));
                if($GradeResult){
                       $Grade="<a href='Rs_tdg_read.php?Number=$Number' target='_blank'>".$myRow["Grade"]."</a>";
                      }
                 else $Grade=$myRow["Grade"];
                }
         else $Grade="&nbsp;";
		//$Grade=$myRow["Grade"]==0?"&nbsp;":"<a href='Rs_tdg_read.php?Number=$Number' target='_blank'>".$myRow["Grade"]."</a>";;
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"<div class='greenB'>√</div>":($KqSign==2?"<div class='yellowB'>√</div>":"&nbsp;");
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Dh=$myRow["Dh"]==""?"&nbsp;":$myRow["Dh"];
		$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email.gif' title='$myRow[Mail]' width='18' height='18' border='0'></a>";
		$AppleID=$myRow["AppleID"]==""?"&nbsp;":$myRow["AppleID"];
		$GroupEmail=$myRow["GroupEmail"]==""?"&nbsp;":"<a href='mailto:$myRow[GroupEmail]'><img src='../images/email.gif' title='$myRow[GroupEmail]' width='18' height='18' border='0'></a>";
		$ExtNo=$myRow["ExtNo"]==""?"&nbsp;":$myRow["ExtNo"];
		
		$ComeIn=$myRow["ComeIn"];
		$Nickname=$myRow["Nickname"]==""?"&nbsp;":$myRow["Nickname"];
		$ContractSDate=$myRow["ContractSDate"];		
		$ContractEDate=$myRow["ContractEDate"];	
		if($ContractEDate=='0000-00-00'){
			if($ContractSDate!="0000-00-00"){
				$ContractEDate="<div class=\"greenB\" title='合同开始日期:$ContractSDate'>无期限</div>";
			}
			else {
				$ContractEDate="&nbsp;";
			}
		}
		else {
			$tempY=date("Y-m");
			if(substr($ContractEDate,0,7)==$tempY){
				$ContractEDate="<div class=\"redB\" title='合同开始日期:$ContractSDate'>$ContractEDate</div>";
			}
			else{
				$ContractEDate="<div  title='合同开始日期:$ContractSDate'>$ContractEDate</div>";
			}
			
		}
		
		$Name="<span class='greenB'>$Name</span>";
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Birthday=$myRow["Birthday"];
		$Rpr=$myRow["Rpr"];
		$Idcard=$myRow["Idcard"];
		if(strlen($Idcard)==18){
			$IdBirthday=substr($Idcard,6,4)."-".substr($Idcard,10,2)."-".substr($Idcard,12,2);
			if ($IdBirthday!=$Birthday){
				//echo "$IdBirthday!=$Birthday";
			}
		}
		
		$WorkAddFrom=$myRow["WorkAdd"];
		include "../model/subselect/WorkAdd.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
/*********************************************/
		//需要提取此人领用过的物品记录，且属于最后领用人的			
		$checkMyHDSql=mysql_query("
					SELECT C.TypeId,D.User,C.Model,C.MID FROM $DataPublic.fixed_userdata D,
					   (SELECT A.MID,MAX(A.SDate) AS SDate,B.TypeId,B.Model
					      FROM $DataPublic.fixed_userdata A 
					      LEFT JOIN $DataPublic.fixed_assetsdata B ON A.MID=B.Id
					    WHERE B.TypeId IN (14,17,46) AND A.UserType=1 GROUP BY A.MID
                    ) C WHERE D.User='$Number' AND D.UserType=1 AND C.MID=D.MID AND C.SDate=D.SDate GROUP BY D.MID",$link_id);
     		if($checkMyHDRow=mysql_fetch_array($checkMyHDSql)){
			do{
				   $TypeId=$checkMyHDRow["TypeId"];
				   $Mid=$checkMyHDRow["Mid"];
				   $Model=$checkMyHDRow["Model"];
				   $User=$checkMyHDRow["User"];
				   switch($TypeId)
				  {
					case 14:	//MacBook
						$MacBookIcon="<a href='staff_receive.php?f=$Number&Mid=$Mid' target='_blank' class='yellowB'>View</a>";
						$MacBookTitle="Title=\"$Model\"";
						$sumMacBook++;
						break;	
					case 17:	//iPad
						$IPadIcon="<a href='staff_receive.php?f=$Number&Mid=$Mid' target='_blank' class='yellowB'>View</a>";
						$IPadTitle="Title=\"$Model\"";
						$sumIPad++;
						break;
					case 46:	//iPhone
						$IPhoneIcon="<a href='staff_receive.php?f=$Number&Mid=$Mid' target='_blank' class='yellowB'>View</a>";
						$IPhoneTitle="Title=\"$Model\"";
						$sumIPhone++;
						break;
				      }
				  }while($checkMyHDRow=mysql_fetch_array($checkMyHDSql));
			  }
		$Idcard=$myRow["Idcard"]==""?"&nbsp;":$myRow["Idcard"];
		//*********************************************籍贯
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if ($rResult ){
				if($rRow = mysql_fetch_array($rResult)){
					$Rpr=$rRow["Name"];
					}
		    }
		//*********************************************社保
		$sbResult = mysql_query("SELECT Id FROM $DataPublic.sbdata WHERE  Number=$Number order by Id LIMIT 1",$link_id);
		if ($sbResult ){
				if($sbRow = mysql_fetch_array($sbResult)){
					$ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
					$Sb="<a href='staff_sbview.php?f=$ViewNumber' target='_blank' class='yellowB'>View</a>";
					}
                 else{
                         $Sb="&nbsp;";
                        }
			}
		//*********************************************公积金
		$gjjResult = mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.epfdata WHERE  Number=$Number order by Id LIMIT 1",$link_id));
		if ($gjjResult ){
			  $ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
			  $gjj="<a href='staff_gjjview.php?f=$ViewNumber' target='_blank' class='yellowB'>View</a>";
			   }
       else    $gjj="&nbsp;";
	    //*********************************************意外险
	    $Casualty="&nbsp;";
		 $CasualtyResult = mysql_fetch_array(mysql_query("SELECT * FROM (
          SELECT Id,Month,ValidMonth FROM $DataIn.sbpaysheet WHERE  Number=$Number AND TypeId=3 ) A 
         ORDER BY  A.Month DESC  LIMIT 1",$link_id));
         $CasualtyColor="";
		 if($CasualtyResult ){
			  $ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
              $CasualtyValidMonth =$CasualtyResult["ValidMonth"];  
              $CasualtyMonth=$CasualtyResult["Month"]; 
              $NowMonth=substr(date("m"),0,2);
              $NowYear=date("Y");
              
              
              if($CasualtyValidMonth == "0000-00"){
		          $CasualtyY=substr(date("Y-m-d"),0,4)-substr($CasualtyMonth,0,4);	
	              $ThisMonth  = date("m",strtotime($CasualtyMonth."-01")); 
	              if($CasualtyY>=1){
	                    if($CasualtyY>1 || substr($ThisMonth,0,2)==$NowMonth)$CasualtyColor="style='background:#FF0000' title='意外险缴费超过一年以上'";
	                    else $CasualtyColor="";
	              }
	              else $CasualtyColor="";
	              $Casualty=$CasualtyMonth;
				  $Casualty=date("Y-m",strtotime("+1 year",strtotime("$Casualty"."-01")));          
              }else{
	              //$CasualtyY=substr(date("Y-m-d"),0,4)-substr($CasualtyValidMonth,0,4);
	              $ThisMonth  = date("m",strtotime($CasualtyValidMonth."-01")); 
	              $ThisYear   = date("Y",strtotime($CasualtyValidMonth."-01")); 
	             
	              /*if($CasualtyY>=1){
	                     if($CasualtyY>1 || substr($ThisMonth,0,2)==$NowMonth)$CasualtyColor="style='background:#FF0000' title='意外险缴费超过一年以上'";
	                     else $CasualtyColor="";
	              }
	              else $CasualtyColor="";*/
	              
	              if($NowMonth>=$ThisMonth && $NowYear>=$ThisYear){
		              $CasualtyColor="style='background:#FF0000' title='意外险缴费到期'";
	              }else{
		              $CasualtyColor ="";
	              }
	              
	              $Casualty =  $CasualtyValidMonth;
              }
			  
		   }
        //*********************************************岗位体检
       $HGColor="";$TJ ="&nbsp;";
	   $tjResult1 = mysql_fetch_array(mysql_query("SELECT Id,tjDate,HG FROM $DataIn.cw17_tjsheet WHERE Number=$Number AND tjType<=3 order by Date DESC LIMIT 1",$link_id));
		if ($tjResult1){
			$ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
            $tjDate=$tjResult1["tjDate"];
			$NexttjDate=date("Y-m-d",strtotime("+1 year",strtotime($tjDate))); 
			
			if ($NexttjDate>date("Y-m-d")){
				$TJ="<a href='staff_tjview.php?f=$ViewNumber' target='_blank'><span style='color:blue; '>$tjDate </span></a>";		
			}
			else {
				$TJ="<a href='staff_tjview.php?f=$ViewNumber' target='_blank'><span style='color:red; '>$tjDate </span></a>";		
			}
								 
			
            $HGColor=$tjResult1["HG"]==0?"style='background:#FF0000' title='不合格'":"";
		}
    
        //*********************************************职工体检
       $ZGHGColor=""; $ZGTJ ="&nbsp;";
	   $tjResult1 = mysql_fetch_array(mysql_query("SELECT Id,tjDate,HG FROM $DataIn.cw17_tjsheet WHERE Number=$Number AND tjType=4 order by Date DESC LIMIT 1",$link_id));
		if ($tjResult1){
			$ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
            $tjDate=$tjResult1["tjDate"];
			$NexttjDate=date("Y-m-d",strtotime("+1 year",strtotime($tjDate))); 
			
			if ($NexttjDate>date("Y-m-d")){
				$ZGTJ="<a href='staff_tjview.php?f=$ViewNumber' target='_blank'><span style='color:blue; '>$tjDate </span></a>";		
			}
			else {
				$ZGTJ="<a href='staff_tjview.php?f=$ViewNumber' target='_blank'><span style='color:red; '>$tjDate </span></a>";		
			}
								 
			
            $ZGHGColor=$tjResult1["HG"]==0?"style='background:#FF0000' title='不合格'":"";
		}
     	
        //*********************************************介绍人
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
		 $ComeInYM=substr($ComeIn,0,7);
		 include "subprogram/staff_model_gl.php";
         /*********************************************/

        /*********************************************/
        //员工请假超过半个月的显示颜色
        include "../model/subprogram/staff_qj_day.php";
        /*********************************************/
		//驾照
		$DriverPhoto=$myRow["DriverPhoto"];
		 if($DriverPhoto==1){
			$FileName="D".$Number.".jpg";
			$DriverPhoto="<span onclick='upMainData(\"staff_DriverPhoto\",\"$Number\")' >&nbsp</span><a href=\"staff_DriverPhoto_view.php?Number=$Number\"   target=\"_blank\"  style='CURSOR: pointer;' class='yellowB'>View</a>";			 
		 }
		 else $DriverPhoto="<span onclick='upMainData(\"staff_DriverPhoto\",\"$Number\")' >&nbsp;</span>";
		 //护照PassPort
		$PassPort=$myRow["PassPort"];
		 if($PassPort==1){
			$FileName="PP".$Number.".jpg";
			$PassPort="<span onclick='upMainData(\"staff_PassPort\",\"$Number\")' >&nbsp</span><a href=\"staff_PassPort_view.php?Number=$Number\"   target=\"_blank\"  style='CURSOR: pointer;' class='yellowB'>View</a> <span onclick='upMainData(\"staff_PassPort\",\"$Number\")' >&nbsp;</span> ";			 
		 }
		 else $PassPort="<span onclick='upMainData(\"staff_PassPort\",\"$Number\")' >&nbsp;</span>";

        //通行证PassTicket
		 $PassTicket=$myRow["PassTicket"];
		 if($PassTicket==1){
			$FileName="PT".$Number.".jpg";
			$PassTicket="<span onclick='upMainData(\"staff_PassTicket\",\"$Number\")' >&nbsp</span><a href=\"staff_PassTicket_view.php?Number=$Number\"   target=\"_blank\"  style='CURSOR: pointer;' class='yellowB'>View</a> <span onclick='upMainData(\"staff_PassTicket\",\"$Number\")' >&nbsp;</span>";			 
		 }
		 else $PassTicket="<span onclick='upMainData(\"staff_PassTicket\",\"$Number\")' >&nbsp;</span>";
		 //检查培训记录、考核记录、责任人培训记录
		 $checkTraningRow=mysql_fetch_array(mysql_query("SELECT count(* ) AS Nums FROM (
													  SELECT Id FROM $DataPublic.aqsc08 WHERE Number='$Number'
													  UNION ALL
													  SELECT Id FROM $DataPublic.aqsc09 WHERE Number='$Number'
													  UNION ALL 
													  SELECT Id FROM $DataPublic.aqsc10 WHERE Number='$Number'
													  ) Z
													  ",$link_id));
		if($checkTraningRow["Nums"]>0){
			$Training="<a href='aqsc00_training_view.php?Number=$Number' target=\"_blank\"  style='CURSOR: pointer;' class='yellowB'>View</a>";
			}
		else{
			$Training="&nbsp;";
			}
		//********入职档案
		if($InFile==1){
			$FileName="I".$Number.".pdf";
			$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
			$td=anmaIn("download/staffPhoto/",$SinkOrder,$motherSTR);			
			$InFile="<span onClick='OpenOrLoad(\"$td\",\"$tf\",6)' style='CURSOR: pointer;' class='yellowB'>View</span>";
			}
		else{
			$InFile="&nbsp;";
			}
		$GroupName=$myRow["GroupName"]==""?"<div class=\"redB\">未设置</div>":$myRow["GroupName"];//小组
		$Locks=$myRow["Locks"];
         $CertificateResult=mysql_fetch_array(mysql_query("SELECT Picture FROM $DataPublic.staff_Certificate WHERE Number=$Number LIMIT 1",$link_id));
         $CertificatePicture=$CertificateResult["Picture"];
         if($CertificatePicture!=""){
                 $Certificate="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staff_Certificate\",\"$Number\")' src='../images/edit.gif' title='上传证书' width='13' height='13'>&nbsp;&nbsp;<a href=\"staff_Certificate_view.php?Number=$Number\"   target=\"_blank\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
                 }
        else {
                  $Certificate="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staff_Certificate\",\"$Number\")' src='../images/edit.gif' title='上传证书' width='13' height='13'>";
                }
         $passName = $myRow["Name"];

        $ageMin = 16;
        $ageMax = $Sex == '男'?55:50;


        if($Age <= $ageMin || $Age >= $ageMax){
            $Age = "<span class='redB'>$Age</span>";
        }


			$ValueArray=array(
			    array(0=>$cSign,1=>"align='center'"),
				array(0=>$WorkAdd,1=>"align='center'"),
				array(0=>$Number,1=>"align='center' $qjcolor"),
				array(0=>$Name,1=>"align='center'"),
				array(0=>$Nickname),
				array(0=>$AppFileSTR,1=>"align='center'"),
				array(0=>$Idcard,1=>"align='center'"),
				array(0=>$IdNum,1=>"align='center'"),
				array(0=>$Branch,1=>"align='center'"),
				array(0=>$GroupName,1=>"align='center'"),
				array(0=>$Job,1=>"align='center'"),
				array(0=>$Grade,1=>"align='center'",4=>$GradeHidden),
				array(0=>$KqSign,1=>"align='center'"),
				//array(0=>$Currency,1=>"align='center'"),
				array(0=>$Mobile,1=>"align='center'"),
				array(0=>$Dh,1=>"align='center'"),
				array(0=>$ExtNo,1=>"align='center'"),
				array(0=>$GroupEmail,1=>"align='center'"),
				array(0=>$Mail,1=>"align='center'"),
				array(0=>$AppleID,1=>"align='left'"),
				array(0=>$ComeIn,1=>"align='center'"),
				array(0=>$ContractEDate,1=>"align='center'"),
				array(0=>$Gl_STR,1=>"align='center'"),
				array(0=>$Age,1=>"align='center'"),
				array(0=>$Sex,1=>"align='center'"),
				array(0=>$BloodGroup,1=>"align='center'"),
				array(0=>$Rpr,1=>"align='center'"),
			    array(0=>$Introducer,1=>"align='center'"),
				array(0=>$Tel,1=>"align='left'"),
			    array(0=>$Jobduties,1=>"align='center'"),
				array(0=>$Sb,1=>"align='center'"),
				array(0=>$gjj,1=>"align='center'"),
				array(0=>$Casualty,1=>"align='center' $CasualtyColor"),
				array(0=>$TJ,1=>"align='center' $HGColor"),
				array(0=>$ZGTJ,1=>"align='center' $ZGHGColor"),
				array(0=>$DriverPhoto,1=>"align='center'"),
				array(0=>$PassPort,1=>"align='center'"),
				array(0=>$PassTicket,1=>"align='center'"),
				array(0=>$InFile,1=>"align='center'"),
				array(0=>$Training,1=>"align='center'"),
				array(0=>$IPhoneIcon,1=>"align='center' $IPhoneTitle"),
				array(0=>$IPadIcon,1=>"align='center' $IPadTitle"),
				array(0=>$MacBookIcon,1=>"align='center' $MacBookTitle"),
				array(0=>$ClothesSize,2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,42,$Number,1,$Number,\"$passName\")' style='CURSOR: pointer'"),
				array(0=>$HouseSize,2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,43,$Number,2,$Number,\"$passName\")' style='CURSOR: pointer'")
				);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		
echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";		
echo "<tr>";
echo "<td class='A0111' align='center' height='30'>iPhone,iPad,MacBook统计</td>";
echo "<td class='A0101' width=50 align='center' height='30'>$sumIPhone</td>";	
echo "<td class='A0101' width=50 align='center' height='30'>$sumIPad</td>";
echo "<td class='A0101' width=50 align='center' height='30'>$sumMacBook</td>";
echo "<td class='A0101' width=150 align='center' height='30'>&nbsp;</td>";
echo "<td class='A0101' width=250 align='center' height='30'>&nbsp;</td>";
echo "</tr></table>";
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
include "../model/subprogram/read_model_menu.php";
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script>
function updateJq(TableId,RowId,runningNum,toObj,sId,staffname){//行即表格序号;列，流水号，更新源
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
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){

			case 1:
				InfoSTR="更新员工 "+staffname+"<input name='runningNum' type='hidden' id='runningNum' value='"+runningNum+"'> 的工衣尺寸:<textarea id='ClothesSize'   cols='55' rows='3'></textarea><br>";
           break;

			case 2:
				InfoSTR="更新员工 "+staffname+"<input name='runningNum' type='hidden' id='runningNum' value='"+runningNum+"'> 的购房住址:<textarea id='HouseSize'   cols='55' rows='3'></textarea><br>";
                break;
           }
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+sId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  
			theDiv.filters.revealTrans.apply();
			theDiv.filters.revealTrans.play(); 
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
	if (isIe()) { 
		theDiv.filters.revealTrans.apply();
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	closeMaskBack();   
	}

function aiaxUpdate(sId){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
			case "1":		
			var tempClothesSize0=document.form1.ClothesSize.value;
			var tempClothesSize1=encodeURIComponent(tempClothesSize0);
			myurl="staff_updated.php?Number="+temprunningNum+"&tempClothesSize="+tempClothesSize1+"&ActionId=ClothesSize"+"&sId="+sId;
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML=tempClothesSize0;
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;

			case "2":		
			var tempHouseSize0=document.form1.HouseSize.value;
			var tempHouseSize1=encodeURIComponent(tempHouseSize0);
			myurl="staff_updated.php?Number="+temprunningNum+"&tempHouseSize="+tempHouseSize1+"&ActionId=HouseSize"+"&sId="+sId;
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML=tempHouseSize0;
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
		}
	}
</script>