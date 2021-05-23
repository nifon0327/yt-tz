<?php 

	include "../../basic/parameter.inc";
	//include "../../model/modelhead.php";
	
	$mySql="SELECT M.Id,M.Number,M.Name,M.Grade,M.Mail,M.ExtNo,M.ComeIn,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Dh,S.InFile,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName,M.IdNum
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	WHERE 1 AND M.Estate=1  ORDER BY M.BranchId,M.GroupId,M.JobId,M.ComeIn,M.Number";

	$staffArray = array();
	$no = 1;
	$staffResult = mysql_query($mySql);
	if($myRow = mysql_fetch_assoc($staffResult))
	{
		do 
		{
			$m=1;
			$IPhoneIcon="&nbsp;";	$IPhoneTitle="";
			$IPadIcon="&nbsp;";		$IPadTitle="";
			$MacBookIcon="&nbsp;";	$MacBookTitle="";
			$Id=$myRow["Id"];
			$Number=$myRow["Number"];
			$InFile=$myRow["InFile"];
			$glPad = "";
			$MonthSTR=0;
			
			$Birthday =$myRow["Birthday"];
			$Age = date('Y', time()) - date('Y', strtotime($Birthday)) - 1;
			if (date('m', time()) == date('m', strtotime($Birthday)))
			{
				if (date('d', time()) > date('d', strtotime($Birthday)))
				{
					$Age++;
				}
			}
			else
			{
				if (date('m', time()) > date('m', strtotime($Birthday)))
				{
					$Age++;
				}
			}
			//$ViewId=anmaIn($Id,$SinkOrder,$motherSTR);
			$Name = $myRow["Name"];
		
			if($Age<18){
			//$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='redB'>$myRow[Name]</div></a>";
			}
			else{
			//$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='greenB'>$myRow[Name]</div></a>";
			}
			$Branch=$myRow["Branch"];
			$Job=$myRow["Job"];
			$IdNum=$myRow["IdNum"]==0?"":$myRow["IdNum"];
			$Grade=$myRow["Grade"]==0?"":$myRow["Grade"];
			$KqSign=$myRow["KqSign"];
			$KqSign=$KqSign==1?"√":($KqSign==2?"√":"");
			$Mobile=$myRow["Mobile"]==""?"":$myRow["Mobile"];
			$Dh=$myRow["Dh"]==""?"":$myRow["Dh"];
			//$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email.gif' title='$myRow[Mail]' width='18' height='18' border='0'></a>";
			$Mail = ($myRow["Mail"] == "")?"":$myRow["Mail"];
			$ExtNo=$myRow["ExtNo"]==""?"":$myRow["ExtNo"];
			$ComeIn=$myRow["ComeIn"];
			//$Name="<span class='greenB'>$Name</span>";
			$Sex=$myRow["Sex"]==1?"男":"女";
			$Birthday=$myRow["Birthday"];
			$Rpr=$myRow["Rpr"];
			$Idcard=$myRow["Idcard"];
						
		
		//需要提取此人领用过的物品记录，且属于最后领用人的			
		$checkMyHDSql=mysql_query("
					SELECT C.TypeId,D.User,C.Model,C.MID
					FROM $DataPublic.fixed_userdata D,
					(SELECT A.MID,MAX(A.SDate) AS SDate,B.TypeId,B.Model
					FROM $DataPublic.fixed_userdata A 
					LEFT JOIN $DataPublic.fixed_assetsdata B
					ON A.MID=B.Id
					WHERE B.TypeId IN (14,17,46) AND A.UserType=1 
					GROUP BY A.MID) C
					WHERE D.User='$Number' AND D.UserType=1 
					AND C.MID=D.MID AND C.SDate=D.SDate
					GROUP BY D.MID
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
						//$MacBookIcon="<a href='stuff_receive.php?f=$Number&Mid=$Mid' target='_blank'>查看</a>";
						//$MacBookTitle="Title=\"$Model\"";
						$sumMacBook++;
						break;
							
					case 17:	//iPad
						//$IPadIcon="<a href='stuff_receive.php?f=$Number&Mid=$Mid' target='_blank'>查看</a>";
						//$IPadTitle="Title=\"$Model\"";
						$sumIPad++;
						break;
							
					case 46:	//iPhone
						$IPhoneIcon="<a href='stuff_receive.php?f=$Number&Mid=$Mid' target='_blank'>查看</a>";
						$IPhoneTitle="Title=\"$Model\"";
						$sumIPhone++;
						break;
				}
					
					
				}while($checkMyHDRow=mysql_fetch_array($checkMyHDSql));
			
			}
		
		///////////////////
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if($rRow = mysql_fetch_array($rResult)){
			$Rpr=$rRow["Name"];
			}
		
		
        //include "../../basic/parameter.inc";
		$Introducer=$myRow["Introducer"];
		if($Introducer != "")
		{
			$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Introducer order by Id",$link_id);
			if($iRow = mysql_fetch_array($iResult))
			{
				$Introducer=$iRow["Name"];
			}
		}
			
			
//////////////////////////////////////////////
		//工龄计算
		include "../../admin/subprogram/staff_model_gl.php";
//////////////////////////////////////////////

//员工请假超过半个月的显示颜色
        $qjcolor="";
		$qjResult=mysql_query("SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet WHERE Number='$Number' AND EndDate>='$today'",$link_id);
		if($qjRow=mysql_fetch_array($qjResult)){
		   do{
		     $bcType=$qjRow["bcType"];
		     $StartDate=$qjRow["StartDate"];
			 $EndDate=$qjRow["EndDate"];
			 $HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
			 $Days=intval($HoursTemp/24);
			 $HolidayTemp=0;$isHolday=0;
			 $DateTemp=$StartDate;
			 $DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
			 for($n=0;$n<=$Days;$n++){
				$isHolday=0;  //0 表示工作日
				$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				$weekDay=date("w",strtotime("$DateTemp"));	 
				if($weekDay==6 || $weekDay==0){
					$HolidayTemp=$HolidayTemp+1;
					$isHolday=1;
					}
				else{
					//读取假日设定表
					$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					if($holiday_Row = mysql_fetch_array($holiday_Result)){
						$HolidayTemp=$HolidayTemp+1;
						$isHolday=1;
						}
					}
                
				//分析是否有工作日对调
				if($isHolday==1){  //节假日上班，所以其休息时间要减
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp-1;
					   }				
				    }			
				
				else{  //非节假日调班，则其休息时间要加,
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp+1;
					      }
			         }
               
			 }//endfor
			//计算请假工时
			$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			if($bcType==0){
				$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
				}
			$HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时 
			$HourTotal=$HourTotal<0?0:$HourTotal;
			
			   if($HourTotal>=105){//超过15天颜色显示
			     $qjcolor="style='background:#FF0000' title='请假超过半个月'";
			     }	    
		    }while($qjRow=mysql_fetch_array($qjResult));		
		}

//==================================================
		if($InFile==1){
			$FileName="I".$Number.".pdf";
			//$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
			//$td=anmaIn("download/staffPhoto/",$SinkOrder,$motherSTR);			
			$InFile="查看";
			}
		else{
			$InFile="";
			}
		$GroupName=$myRow["GroupName"]==""?"未设置":$myRow["GroupName"];
		$Locks=$myRow["Locks"];		
		
		
		$staffInfo = array("$no","$Number","$Name","$Idcard","$Branch","$GroupName","$Job","$Grade","$KqSign","$Mobile","$Dh","$Mail","$ExtNo","$ComeIn","$glPad","$Sex","$Rpr","$Sb","$InFile","$Introducer","","$IPadIcon","$MacBookIcon","$Id");
			
		$staffArray[] = $staffInfo;
		$no = $no+1;
		}
		while($myRow = mysql_fetch_assoc($staffResult));
	}
	
	echo json_encode($staffArray);
?>