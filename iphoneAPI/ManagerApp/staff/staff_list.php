<?php 
//员工资料明细
//$Number=10868;
$ReadAccessSign=1;
include "user_access.php";  //用户权限
//add by cabbage 20150105 傳入kq_YearHolday
$iPhoneTag = "yes";
include "../../model/kq_YearHolday.php";

// include "submodel/staff_worktime.php";



$BundleId = 'AshCloudApp';
$Device = 'iphoneAPI';
$appVersion = $AppVersion;
$segment = 'staff';
$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];

$uri = 'staff/staff_list';
				          
				          
$sql = "INSERT INTO `ac`.`app_userlog`
(
`BundleId`,
`Device`,
`Version`,
`IP`,
`Segment`,
`Uri`,
`creator`,
`Parameter`)
VALUES
(
'$BundleId',
'$Device',
'$appVersion',
'$user_IP',
'$segment',
'$uri',
'$LoginNumber',
'readNumber=>$Number');
";


if (versionToNumber($AppVersion) <= 426)
mysql_query($sql,$link_id);
       
$mySql="SELECT 
	 M.Name,M.Grade,M.Mail,M.AppleID, M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.WorkAdd,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,M.KqSign,M.cSign,M.JobId,M.BranchId,M.GroupEmail,
	S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Dh,S.InFile,M.KqSign,S.Photo,S.IdcardPhoto,S.HealthPhoto,S.DriverPhoto,S.Tel,B.Name AS Branch,J.Name AS Job,IF(M.cSign=7,G.GroupName,SG.GroupName) AS GroupName,M.IdNum,T.Name AS BloodGroup,S.PassPort,S.PassTicket,S.Weixin,S.LinkedIn,S.Rpr,S.Address 
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataSub.staffgroup SG ON SG.GroupId=M.GroupId
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	LEFt JOIN $DataPublic.bloodgroup_type T ON T.ID=S.BloodGroup  
	WHERE  M.Number='$Number' LIMIT 1";
 // echo $mySql; 
 $Result = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($Result)) {
          $Name = $myRow["Name"];
          $GroupName=$myRow["GroupName"]==""?$myRow["Branch"]:$myRow["GroupName"];
            
           //工作地点
		$Branch=$myRow["Branch"];
		$Job=$myRow["BranchId"]==8?$myRow["GroupName"]:$myRow["Job"];
		$WorkName=$Branch . "-" . $Job;
		
		$WorkAddFrom=$myRow["WorkAdd"];
		$WorkAddResult = mysql_query("SELECT  A.Name FROM $DataPublic.staffworkadd A WHERE A.Id='$WorkAddFrom' LIMIT 1",$link_id);
		if($WorkAddRow = mysql_fetch_array($WorkAddResult)){
		    // $WorkName.="(" . $WorkAddRow["Name"] . ")";
		    $WorkAdd= $WorkAddRow["Name"] ;
		}
      
        //****************************年龄计算
		$Birthday =$myRow["Birthday"];
	  
		//籍贯
		$Rpr=$myRow["Rpr"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id='$Rpr' order by Id",$link_id);
		if ($rResult ){
				if($rRow = mysql_fetch_array($rResult)){
					$Rpr=$rRow["Name"];
					}
		    }
		    
		//血型
		$BloodGroup=$myRow["BloodGroup"]==""?"":$myRow["BloodGroup"];
		//入职日期
		$ComeIn=$myRow["ComeIn"];
		 //工龄计算
		 $GL_CheckFrom='iPhone';
		 $ComeInYM=substr($ComeIn,0,7);
		 include "../../public/subprogram/staff_model_gl.php";//输出$glPad
		 
	    $State=0;
		$DataLink=$myRow["cSign"]==7?$DataIn:$DataOut;
	    if ($myRow["JobId"]!=38){
	         $checkqSql=mysql_query("SELECT dFrom FROM $DataLink.checkinout   WHERE CheckTime>=CURDATE()  AND Number='$Number' AND Number NOT IN(SELECT A.Number FROM (SELECT Number,Max(IF(CheckType='I',CheckTime,'')) AS inTime,Max(IF(CheckType='O',CheckTime,'')) AS outTime FROM $DataIn.checkinout WHERE Number='$Number' AND DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE()) A 
WHERE A.outTime>A.inTime) ",$link_id);
	         if($checkqRow = mysql_fetch_array($checkqSql)) {
	                 $ClockLoc=$checkqRow["dFrom"];
	         }
	         
	         $State=mysql_num_rows($checkqSql)==1?1:4;
	   }
	   else {//保安跨日值班
		      $kqCheckRow=mysql_fetch_array(mysql_query("SELECT  MAX(IF(CheckType='I',CheckTime,'')) AS inTime,MAX(IF(CheckType='O',CheckTime,'')) AS outTime  FROM $DataLink.checkinout   WHERE CheckTime>=CURDATE()  AND Number='$Number'",$link_id));
		       $inTime=$kqCheckRow["inTime"];
               $outTime=$kqCheckRow["outTime"];
               $State=$inTime>$outTime?1:4;
                   
               if ($outTime=="" && $State==4 ){
                   $krCheckRow=mysql_fetch_array(mysql_query("SELECT  MAX(IF(CheckType='I',CheckTime,'')) AS inTime,MAX(IF(CheckType='O',CheckTime,'')) AS outTime FROM $DataLink.checkinout    WHERE  DATE_FORMAT(CheckTime,'%Y-%m-%d')=DATE_ADD(CURDATE(),INTERVAL -1 DAY)  AND Number='$Number'",$link_id));
                  $inTime=$krCheckRow["inTime"];
                  $outTime=$krCheckRow["outTime"];
                  $State=$inTime>$outTime?1:$State;
             }
       }
		 
		  //车辆信息
		 $carSign=0;$CarLicenseNo="";
		$checkCarSql=mysql_query("SELECT Id,BrandId,CarNo FROM $DataPublic.cardata WHERE User='$Name' and Estate=1",$link_id);
		 if($checkCarRow = mysql_fetch_array($checkCarSql)) {
		        $carSign=$checkCarRow["BrandId"];
		        $CarNo=$checkCarRow["CarNo"];
		 }
		
		$Auth=($myRow["JobId"]==1 || 	$myRow["JobId"]==39)?1:2;	//暂设定 
		 //年、休假信息		 
		 $YearDays=0;$BxDays=0;
		 $checkHsql=mysql_query("SELECT YearDays,BxDays FROM $DataPublic.staffholiday WHERE Number='$Number'",$link_id);
		 if($checkHrow = mysql_fetch_array($checkHsql)) {
		       $YearDays=$checkHrow["YearDays"];
		       $BxDays=$checkHrow["BxDays"];
		       
		       $YearDays=abs($YearDays-round($YearDays))>=0.1?number_format($YearDays,1):number_format($YearDays);
		       $BxDays=abs($BxDays-round($BxDays)>=0.1)?number_format($BxDays,1):number_format($BxDays);
		 }
		 
		 
		 //modify by cabbage 20150105 修正年假的算法，改成和系統算法相同
	 	//$YearDays = GetYearHolDayDays($Number, date("Y"), date("Y")."-12-31", $DataIn, $DataPublic, $link_id);
		 
		 /*
//傳入 Kq_lj_read.php 參數：1. iPhoneTag 2. From = slist(搜尋) 
		 $From = "slist";
		 //sql select 條件：1. SearchRows(篩選條件) 2. Kq_lj_read.php要求傳入的參數
		 $SearchRows = " AND M.Number = '$Number'";
		 $Login_cSign = "7";
		 //紀錄年假的回傳值
		 $AnnualLeave = 0;
		 
		 //紀錄搜尋所需要的時間
	 	 include "../../basic/class.php";
		 $timer = new timer;  
		 $timer->start();		 
		 
		 ob_start();
		 chdir("../../public");
		 include("./Kq_lj_read.php");	
		 ob_end_clean();
		 
		 $YearDays = intval($AnnualLeave/8);
*/
		 
		 
		 //助学信息
				$childInfo="";
				$checkStudySql=mysql_query("SELECT Sex FROM $DataPublic.childinfo WHERE Number='$Number' Order by Sex desc",$link_id);
				while($studyRow = mysql_fetch_array($checkStudySql)){
				      $childInfo.=$childInfo==""?$studyRow["Sex"]:"|" . $studyRow["Sex"];
				}



		 $IdNum=$myRow["IdNum"]==""?"":$myRow["IdNum"];//ID卡
		 $headArray=array("Number"=>"$Number",
		                                "Name"=>"$Name",
		                                "Auth"=> "$Auth",
		                                "State"=>"$State",
		                               "Job" =>"$Job",
									    "Unit" =>"$Branch",
									    "WorkAdd" => "$WorkAdd",
									    "Car" => "$carSign",
									    "CarLicenseNo" => "$CarNo",
		                               // "Age"=>"$Age" . "岁",
		                               // "Blood"=>"$BloodGroup",
		                                "Child"=>"$childInfo",
		                                "ComeIn"=>"$ComeIn",
		                                "Gl"=>"$glPhone",
		                                "BirthPlace" =>"$Rpr",
		                                "Vacation"=>array("T0"=>"$YearDays","T1"=>"$BxDays"),
		                                //"IdNum"=>"$IdNum",
		                                "Photo"=>"download/staffPhoto/P$Number.png"
		 );
		 //工作职责
		 if (versionToNumber($AppVersion)>=287 ){
			 $cehckResult=mysql_fetch_array(mysql_query("SELECT Description FROM $DataPublic.staff_jobduties WHERE Number='$Number' LIMIT 1",$link_id));
			 $Description=$cehckResult["Description"]==""?"":$cehckResult["Description"];
			 
			  $cehckResult=mysql_query("SELECT Description FROM $DataPublic.staff_performance WHERE Number='$Number'",$link_id);
			  $bxCounts=mysql_num_rows($cehckResult);
			   $bxSign=$bxCounts>0?1:0;
			   
			 $dataArray[]=array(
			             "0"=>array("Title"=>"工作职责","Icon"=>"duty","Value"=>"$Description"),
			             "1"=>array("Title"=>"在职表现","Icon"=>"performance","Value"=>"$bxCounts","onTap"=>"$bxSign","Args"=>"Performance","List"=>array())
			       );	
			
			$Introducer=$myRow["Introducer"];
		    if ($Introducer){
					$iResult = mysql_query("SELECT M.Name,S.Mobile FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
 WHERE M.Number='$Introducer'",$link_id);
					if($iRow = mysql_fetch_array($iResult)){
						   $Introducer=$iRow["Name"];
						   $introducerMobile=$iRow["Mobile"];
					  }
					  
				    $listArray=array();
					$listArray[]=array("Title"=>"关  系","Icon"=>"","Value"=>"");
					$listArray[]=array("Title"=>"电  话","Icon"=>"","Value"=>"$introducerMobile");     
				   $dataArray[]=array(       
					       "0"=>array("Title"=>"介绍人","Icon"=>"introducer","Value"=>"$Introducer","onTap"=>"1","Args"=>"Introducer","List"=>$listArray)
		             );
            }
            $Tel= $myRow["Tel"];
            if ($Tel!=""){
                    $TelArray=explode("|", $Tel);
                    if (count($TelArray)==3){
	                    $Emergency=$TelArray[0];$EmergencyRelation=$TelArray[1];$EmergencyTel=$TelArray[2];
                    }
                    else{
	                     $Emergency="";$EmergencyRelation="";$EmergencyTel=$TelArray[0];
                    }
	                $listArray=array();
					$listArray[]=array("Title"=>"关  系","Icon"=>"","Value"=>"$EmergencyRelation");
					$listArray[]=array("Title"=>"电  话","Icon"=>"","Value"=>"$EmergencyTel");     
				   $dataArray[]=array(       
					       "0"=>array("Title"=>"紧急联系人","Icon"=>"emergency","Value"=>"$Emergency","onTap"=>"1","Args"=>"Emergency","List"=>$listArray)
		             );
            }
      }
			$Mobile=$myRow["Mobile"]==""?"":$myRow["Mobile"];
			$Dh=$myRow["Dh"]==""?"":$myRow["Dh"];
			$Mail = $myRow["Mail"] == ""?"":strtolower($myRow["Mail"]);
			$GroupEmail= $myRow["GroupEmail"] == ""?"":strtolower($myRow["GroupEmail"]);
			$Tel=$myRow["ExtNo"]==""?"":$myRow["ExtNo"];
			$Weixin=$myRow["Weixin"]==""?"":$myRow["Weixin"];
			$LinkedIn=$myRow["LinkedIn"]==""?"":$myRow["LinkedIn"];
		    $Address=$myRow["Address"]==""?"":$myRow["Address"];
		     
		     $dataArray[]=array(
		             "0"=>array("Title"=>"地　　址","Icon"=>"add","Value"=>"$Address"),
		             "1"=>array("Title"=>"手　　机","Icon"=>"tel","Value"=>"$Mobile"),
		             "2"=>array("Title"=>"短　　号","Icon"=>"tel_dh","Value"=>"$Dh"),
		             "3"=>array("Title"=>"座　　机","Icon"=>"tel_ext","Value"=>"$Tel"),
		             "4"=>array("Title"=>"公司邮箱","Icon"=>"mail","Value"=>"$GroupEmail"),
		             "5"=>array("Title"=>"邮　　箱","Icon"=>"emailbox","Value"=>"$Mail"),
		             "6"=>array("Title"=>"微　　信","Icon"=>"weixin","Value"=>"$Weixin"),
		            // "6"=>array("Title"=>"LinkedIn","Icon"=>"linkedIn","Value"=>"$LinkedIn"),
		       );	
		       
		    $qjSql=mysql_query("SELECT Id  FROM $DataPublic.kqqjsheet J   WHERE J.Number='$Number'  AND TIMESTAMPDIFF(DAY,J.StartDate,Now())<366",$link_id);
		    $qjCounts=mysql_num_rows($qjSql);
		    $qjSign=$qjCounts>0?1:0;
           
           $bxSql=mysql_query("SELECT Id  FROM $DataPublic.bxSheet   WHERE Number='$Number'  AND TIMESTAMPDIFF(DAY,StartDate,Now())<366",$link_id);
		    $bxCounts=mysql_num_rows($bxSql);
		    $bxSign=$bxCounts>0?1:0;
		    
		           
		       $dataArray[]=array(
		             "0"=>array("Title"=>"请假记录","Icon"=>"record","Value"=>"$qjCounts","onTap"=>"$qjSign","Args"=>"Leave","List"=>array()),
		             "1"=>array("Title"=>"补休记录","Icon"=>"CompensatedLeave","Value"=>"$bxCounts","onTap"=>"$bxSign","Args"=>"CompensatedLeave","List"=>array())
		       );	        
		     
		    if (in_array("1070",$modelArray) || $LoginNumber==$Number){  
			     //体检报告
			     $tjResult = mysql_query("SELECT tjDate FROM $DataIn.cw17_tjsheet WHERE Number='$Number' ORDER BY ID DESC LIMIT 1 ",$link_id);
				$tjSign=mysql_num_rows($tjResult)>0?1:0;
				
				 $portSTR=$myRow["IdcardPhoto"]==1?"idcard":""; //身份证
				 if ($myRow["DriverPhoto"]==1){ //驾驶证
					 $portSTR.=$portSTR==""?"driver":"|driver";
				 }
			     if ($myRow["PassTicket"]==1){//通行证PassTicket
			         $portSTR.=$portSTR==""?"pass_hk":"|pass_hk";
			     }
			 	if ($myRow["PassPort"]==1){	//护照PassPort
			 	     $portSTR.=$portSTR==""?"pass":"|pass";
			 	 }
			 	 $portSign=$portSTR==""?0:1;
			 	 
			 	 //证书
			    $CertificateResult=mysql_query("SELECT Picture FROM $DataPublic.staff_Certificate WHERE Number=$Number LIMIT 1",$link_id);
			    $CertificateSign=mysql_num_rows($CertificateResult)>0?1:0;
			    
			     $dataArray[]=array(
			             "0"=>array("Title"=>"体检报告","Icon"=>"oe","onTap"=>"$tjSign","Args"=>"Image"),
			             "1"=>array("Title"=>"证　　件","Icon"=>"cert","ImgValue"=>"$portSTR","onTap"=>"$portSign","Args"=>"Image"),
			             "2"=>array("Title"=>"证　　书","Icon"=>"papers","ImgValue"=>"","onTap"=>"$CertificateSign","Args"=>"Image") 
			      );
		      }
		      $tmpArray=array();
		     if (in_array("1035",$modelArray) || $LoginNumber==$Number){
		     		//modify by cabbage 20150105 加上幣別的顯示
			      //薪资
			       $xzResult =mysql_fetch_array(mysql_query("SELECT S.Month, S.Amount, D.PreChar FROM $DataIn.cwxzsheet S
			       											LEFT JOIN $DataPublic.currencydata D ON D.Id = S.Currency
			       											WHERE S.Number='$Number' AND S.Estate=0
			       											
															ORDER BY Month DESC LIMIT 1",$link_id));
															
				  $jbhResult =mysql_fetch_array(mysql_query("SELECT S.Amount 
				                                             FROM $DataIn.hdjbsheet  S WHERE S.Number='$Number' AND S.Estate=0 
			       																									",$link_id));
															
				  $jbfAmount=$jbhResult["Amount"]==""?0:$jbhResult["Amount"];																	
				  $xzAmount=$xzResult["Amount"]==0?"":$xzResult["PreChar"]. number_format($xzResult["Amount"]+$jbfAmount);
			      $xzOnTap=$xzAmount==""?0:1;	
			       $Power=in_array("1035",$modelArray)?3:1;
			       
			       $xzAmount=($Power==3 || $LoginNumber==$Number)?$xzAmount:"";
			       $xzOnTap=($Power==3 || $LoginNumber==$Number)?1:0;
			       
			      $tmpArray[]=array("Title"=>"薪　　资","Icon"=>"wage","Value"=>"$xzAmount","onTap"=>"$xzOnTap","Power"=>"$Power",
											      "Args"=>"Wage","List"=>array());	
             }
             
              if (in_array("1225",$modelArray) || $LoginNumber==$Number){
			      //年终奖
			      /*
			      $jjResult =mysql_query("SELECT ItemName,Month,Rate,Amount FROM $DataIn.cw11_jjsheet_frist 
																					      WHERE Number='$Number' AND  ItemName LIKE '%年终奖金' 
																					UNION ALL 
																					       SELECT ItemName,Month,Rate,Amount FROM $DataOut.cw11_jjsheet_frist   
																					       WHERE Number='$Number' AND  ItemName LIKE '%年终奖金'
																					ORDER BY Month DESC LIMIT 1",$link_id);
				 if($jjRow = mysql_fetch_array($jjResult)) {																
					  $ItemName=$jjRow["ItemName"];																
					  $jjRate=number_format($jjRow["Rate"]);																	
					  $jjAmount=$jjRow["Amount"];														
					  $jjAmount=$jjAmount==0?"":"¥". number_format($jjAmount);
				       $Power=in_array("1225",$modelArray)  || $LoginNumber==$Number ?3:1;
				      $tmpArray[]=array("Title"=>"$ItemName","Icon"=>"bonus","Value"=>"$jjAmount","AboveText"=>"$jjRate%","Power"=>"$Power");	
			      }
			      */
			      //modify by cabbage 20150108 改成有明細的顯示
				  $lastYear = date("Y") - 1; 
				  $bonusResult = mysql_query("SELECT SUM(Amount) AS Amount FROM
												((SELECT Amount FROM $DataIn.cw11_jjsheet_frist WHERE Number = '$Number' AND Date >= '$lastYear')
											  ) Bonus",$link_id);
												
				  if ($bonusRow = mysql_fetch_array($bonusResult)) {
				  	  
				  	  $bonusAmount = $bonusRow["Amount"];
				  	  $bonusValue = ($bonusAmount == 0) ? "" : "¥".number_format($bonusAmount);
				  	  $bonusOnTap = ($bonusAmount == 0) ? 0 : 1;
				  	  
					  $tmpArray[] = array("Title" => "奖　　金", "Icon" => "bonus", "Value" => $bonusValue, 
					  					  "onTap" => $bonusOnTap, "Args" => "Bonus", "Power"=>"$Power", "List" => array());
				  }
             }
             
             if (in_array("1521",$modelArray) || $LoginNumber==$Number){
			      //助学补助
			      $studyResult =mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.Amount,0)) AS Amount FROM (
																			      SELECT SUM(S.Amount) AS Amount FROM $DataIn.cw19_studyfeesheet    S 
																					      LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
																					      WHERE A.Number='$Number' AND   (S.Estate=0 OR S.Estate=3)
																					UNION ALL 
																					       SELECT SUM(S.Amount) AS Amount FROM $DataOut.cw19_studyfeesheet   S 
																					       LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
																					       WHERE A.Number='$Number' AND   (S.Estate=0 OR S.Estate=3) 
																					)A",$link_id));
																					
				  $studyAmount=$studyResult["Amount"]==0?"":"¥". number_format($studyResult["Amount"]);
				  
				  //add by cabbage 20150122 加上小孩的圖片(imageValue)
				  $childInfo="";
				  $checkStudySql=mysql_query("SELECT Sex FROM $DataPublic.childinfo WHERE Number='$Number' Order by Sex desc",$link_id);
				  while($studyRow = mysql_fetch_array($checkStudySql)) {
					  $childInfo.=$childInfo==""?$studyRow["Sex"]:"|" . $studyRow["Sex"];
					  }
				  
				  if ($studyResult["Amount"]>0){
				      $hzOnTap=$studyAmount==""?0:1;
				      $tmpArray[]=array("Title"=>"助学补助","Icon"=>"eduGrant","Value"=>"$studyAmount", "ImgValue" => "$childInfo","onTap"=>"$hzOnTap","Args"=>"EduGrant","List"=>array());	
			      }
			 }
   
              if (in_array("1102",$modelArray) || $LoginNumber==$Number){ 
			      //行政费用
			      $hzResult =mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS Amount FROM (
									SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.hzqksheet S 
									                                    LEFT JOIN $DataIn.hzqkmain M ON S.Mid=M.Id 
									                                    LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
									                                   WHERE S.Operator='$Number' AND (S.Estate=0 OR S.Estate=3)
									)A ",$link_id));
					                                  
				  $hzAmount=$hzResult["Amount"]==0?"":"¥". number_format($hzResult["Amount"]);
			      $hzOnTap=$hzAmount==""?0:1;
			      
			       $tmpArray[]=array("Title"=>"行政费用","Icon"=>"expenses","Value"=>"$hzAmount","onTap"=>"$hzOnTap","Args"=>"Cost","List"=>array());	
		     }
		    if (in_array("1070",$modelArray) || $LoginNumber==$Number){  
		      //社保缴费
		      $sbResult =mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS Amount FROM (
                        SELECT SUM(mAmount+cAmount) AS Amount FROM $DataIn.sbpaysheet WHERE Number='$Number' AND TypeId=1
                         
                  ) A ",$link_id));
                 $sbAmount=$sbResult["Amount"]==0?"":"¥". number_format($sbResult["Amount"],2);
		         $sbOnTap=$sbAmount==""?0:1;
			    $tmpArray[]=array("Title"=>"社保缴费","Icon"=>"si","Value"=>"$sbAmount","onTap"=>"$sbOnTap","Args"=>"Sb","List"=>array());
			    
			//公积金
			$cpfResult =mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS Amount FROM (
                        SELECT SUM(mAmount+cAmount) AS Amount FROM $DataIn.sbpaysheet WHERE Number='$Number' AND TypeId=2
                        
                  ) A ",$link_id));
                 $cpfAmount=$cpfResult["Amount"]==0?"":"¥". number_format($cpfResult["Amount"]);
		         $cpfOnTap=$cpfAmount==""?0:1;
		        $tmpArray[]=array("Title"=>"公积金","Icon"=>"cpf","Value"=>"$cpfAmount","onTap"=>"$cpfOnTap","Args"=>"Cpf","List"=>array());
		      }
		      
		    if (count($tmpArray)>0) $dataArray[]=$tmpArray;
		   if (in_array("1070",$modelArray) || $LoginNumber==$Number){ 
			 //劳动合同期限
			$ContractEDate="";     
			$ContractSDate=$myRow["ContractSDate"];		
			$ContractEDate=$myRow["ContractEDate"];	
			if($ContractEDate=='0000-00-00'){
				if($ContractSDate!="0000-00-00"){
					$ContractEDate="无期限";
				}
				else{
				  $ContractEDate="无";
				}
			}
			
			//意外险期限
			 $CasualtyResult = mysql_fetch_array(mysql_query("SELECT * FROM (
          SELECT Id,Month FROM $DataIn.sbpaysheet WHERE  Number=$Number AND TypeId=3
          ) A 
         ORDER BY  A.Month DESC  LIMIT 1",$link_id));
         $Casualty="";
		 if($CasualtyResult ){
              $CasualtyMonth=$CasualtyResult["Month"];   
              $CasualtyY=substr(date("Y-m-d"),0,4)-substr($CasualtyMonth,0,4);	
              $ThisMonth  =date("m",strtotime($CasualtyMonth."-01")); 
              $NowMonth=substr(date("m"),1,1);
              $Casualty=$CasualtyMonth;
			  $Casualty=date("Y-m",strtotime("+1 year",strtotime("$Casualty"."-01"))); 
			  
			   }

		      $dataArray[]=array(
		             "0"=>array("Title"=>"劳动合同","Icon"=>"contract","Value"=>"$ContractEDate"),
		             "1"=>array("Title"=>"保险期限","Icon"=>"pai","Value"=>"$Casualty")
		       );
		    }
		 if (in_array("1291",$modelArray) || $LoginNumber==$Number){     
		       //需要提取此人领用过的物品记录，且属于最后领用人的			
			$checkMyHDSql=mysql_query("
						SELECT C.TypeId,D.User,C.Model,C.MID FROM $DataPublic.fixed_userdata D,
						   (SELECT A.MID,MAX(A.SDate) AS SDate,B.TypeId,B.Model,B.Estate 
						      FROM $DataPublic.fixed_userdata A 
						      LEFT JOIN $DataPublic.fixed_assetsdata B ON A.MID=B.Id
						    WHERE B.TypeId IN (14,17,46) AND A.UserType=1 GROUP BY A.MID
	                    ) C WHERE D.User='$Number' AND D.UserType=1 AND C.Estate=1 AND C.MID=D.MID AND C.SDate=D.SDate GROUP BY D.MID",$link_id);
	              $asValue=mysql_num_rows($checkMyHDSql);
	              $asOnTap=$asValue>0?1:0;
	
			       $dataArray[]=array(
			             "0"=>array("Title"=>"领用资产","Icon"=>"asset","Value"=>"$asValue","onTap"=>"$asOnTap","Args"=>"Fixed","List"=>array()) 
			       );
		    } 
		  
		   if (in_array("1070",$modelArray) || $LoginNumber==$Number){   
			  //$UserName=$Number==10001?"陈经理":$Name;
				 $checkCarSql=mysql_query("SELECT Id,BrandId,CarNo FROM $DataPublic.cardata WHERE User='$Name'",$link_id);
				 if($checkCarRow = mysql_fetch_array($checkCarSql)) {
				        $BrandId=$checkCarRow["BrandId"];
				        $CarNo=$checkCarRow["CarNo"];
				        $CarId=$checkCarRow["Id"];
				        
				        $carFee_2=0; $carFee_3=0;$carFee_4=0;$carFee_5=0;$carFee_6=0;$carFee_7=0;$carFee_8=0;
				        $checkCarfeeSql=mysql_query("SELECT TypeId,Amount FROM $DataIn.carfee WHERE CarId='$CarId' AND Estate IN(0,3)",$link_id);
				         while($checkfeeRow = mysql_fetch_array($checkCarfeeSql)) {
				                $TypeId=$checkfeeRow["TypeId"];
				                $feeSTR="carFee_" . $TypeId;
				                $Amount=$checkfeeRow["Amount"];
				                $$feeSTR+=$Amount;
				         }
				         
				        $TotalFee= $carFee_2+$carFee_3+$carFee_4+$carFee_5+$carFee_6+$carFee_7+$carFee_8;
				        
				        $carFee_3+=$carFee_8;
				        $carFee_5+=$carFee_6+$carFee_7;
	
				       $onTap1=$carFee_5>0?1:0;
				       $onTap2=$carFee_2>0?1:0;
				       $onTap3=$carFee_3>0?1:0;
				       $onTap4=$carFee_4>0?1:0;
				         $dataArray[]=array(
				             "0"=>array("Title"=>"车辆信息","Icon"=>"car","Value"=>"¥".number_format($TotalFee),"onTap"=>"0","Args"=>"Car",
									             "UnderText"=>"$CarNo","BrandId"=>"$BrandId"),
				              "1"=>array("Title"=>"维护费用","Icon"=>"maintain","Value"=>"¥".number_format($carFee_5),"onTap"=>"$onTap1","Args"=>"CarMaintain","List"=>array()),
				              "2"=>array("Title"=>"违规纪录","Icon"=>"fine","Value"=>"¥".number_format($carFee_2),"onTap"=>"$onTap2","Args"=>"CarFine","List"=>array()),  
				              "3"=>array("Title"=>"粤通卡","Icon"=>"etc","Value"=>"¥".number_format($carFee_3),"onTap"=>"$onTap3","Args"=>"ETC","List"=>array()),  
				              "4"=>array("Title"=>"加油卡","Icon"=>"refuel","Value"=>"¥".number_format($carFee_4),"onTap"=>"$onTap4","Args"=>"Refuel","List"=>array()) 
				       );
				 }
			}
		   $jsonArray= array("head"=>$headArray,"navTitle"=>"$GroupName","data"=>$dataArray);
 }

?>