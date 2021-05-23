<?php 
//员工资料明细
//$Number=10868;
$ReadAccessSign=1;
include "user_access.php";  //用户权限
       
$mySql="SELECT 
	 M.Name,M.Grade,M.Mail,M.AppleID, M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.WorkAdd,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,
	S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Dh,S.InFile,M.KqSign,S.Photo,S.IdcardPhoto,S.HealthPhoto,S.DriverPhoto,S.Tel,B.Name AS Branch,J.Name AS Job,G.GroupName,M.IdNum,T.Name AS BloodGroup,S.PassPort,S.PassTicket,S.Weixin,S.LinkedIn
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
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
		$Job=$myRow["Job"];
		$WorkName=$Branch . "-" . $Job;
		
		$WorkAddFrom=$myRow["WorkAdd"];
		$WorkAddResult = mysql_query("SELECT  A.Name FROM $DataPublic.staffworkadd A WHERE A.Id='$WorkAddFrom' LIMIT 1",$link_id);
		if($WorkAddRow = mysql_fetch_array($WorkAddResult)){
		     $WorkName.="(" . $WorkAddRow["Name"] . ")";
		}
      
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
		
		//血型
		$BloodGroup=$myRow["BloodGroup"]==""?"":$myRow["BloodGroup"];
		//入职日期
		$ComeIn=$myRow["ComeIn"];
		 //工龄计算
		 $ComeInYM=substr($ComeIn,0,7);
		 include "../../public/subprogram/staff_model_gl.php";//输出$glPad
		 
		 $IdNum=$myRow["IdNum"]==""?"":$myRow["IdNum"];//ID卡
		 $headArray=array("Number"=>"$Number",
		                                "Name"=>"$Name",
		                                "Work"=>"$WorkName",
		                                "Age"=>"$Age" . "岁",
		                                "Blood"=>"$BloodGroup",
		                                "ComeIn"=>"$ComeIn",
		                                "Gl"=>"$glPad",
		                                "IdNum"=>"$IdNum",
		                                "Photo"=>"download/staffPhoto/P$Number.png"
		 );
		 
			$Mobile=$myRow["Mobile"]==""?"":$myRow["Mobile"];
			$Dh=$myRow["Dh"]==""?"":$myRow["Dh"];
			$Mail = $myRow["Mail"] == ""?"":strtolower($myRow["Mail"]);
			//$Tel="+86-0755-6113 9580";
			$Tel=$myRow["ExtNo"]==""?"":$myRow["ExtNo"];
			$Weixin=$myRow["Weixin"]==""?"":$myRow["Weixin"];
			$LinkedIn=$myRow["LinkedIn"]==""?"":$myRow["LinkedIn"];
		     
		     $dataArray[]=array(
		             "0"=>array("Title"=>"$Mobile","Icon"=>"tel","Value"=>" "),
		             "1"=>array("Title"=>"$Dh","Icon"=>"tel_dh","Value"=>" "),
		             "2"=>array("Title"=>"$Tel","Icon"=>"tel_ext","Value"=>" "),
		             "3"=>array("Title"=>"$Mail","Icon"=>"emailbox","Value"=>" "),
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
			     $tjResult = mysql_query("SELECT tjDate FROM $DataIn.cw17_tjsheet WHERE Number='$Number' AND tjType=4 
			     UNION ALL SELECT tjDate FROM $DataOut.cw17_tjsheet WHERE Number='$Number' AND tjType=4 ",$link_id);
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
			             "1"=>array("Title"=>"证件","Icon"=>"cert","ImgValue"=>"$portSTR","onTap"=>"$portSign","Args"=>"Image"),
			             "2"=>array("Title"=>"证书","Icon"=>"papers","ImgValue"=>"","onTap"=>"$CertificateSign","Args"=>"Image") 
			      );
		      }
		      $tmpArray=array();
		     if (in_array("1035",$modelArray) || $LoginNumber==$Number){
			      //薪资
			       $xzResult =mysql_fetch_array(mysql_query("SELECT Month,Amount FROM $DataIn.cwxzsheet WHERE Number='$Number' AND Estate=0 
																					UNION ALL 
																					       SELECT Month,Amount FROM $DataOut.cwxzsheet WHERE Number='$Number' AND Estate=0 
																					        ORDER BY Month DESC LIMIT 1",$link_id));
				  $xzAmount=$xzResult["Amount"]==0?"":"¥". number_format($xzResult["Amount"]);
			      $xzOnTap=$xzAmount==""?0:1;	
			       $Power=in_array("1035",$modelArray)?3:1;
			       
			       $xzAmount=($Power==3 || $LoginNumber==$Number)?$xzAmount:"";
			       $xzOnTap=($Power==3 || $LoginNumber==$Number)?1:0;
			       
			      $tmpArray[]=array("Title"=>"薪资","Icon"=>"wage","Value"=>"$xzAmount","onTap"=>"$xzOnTap","Power"=>"$Power",
											      "Args"=>"Wage","List"=>array());	
             }
             
              if (in_array("1225",$modelArray) || $LoginNumber==$Number){
			      //年终奖
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
             }
             
             if (in_array("1521",$modelArray) || $LoginNumber==$Number){
			      //助学补助
			      $studyResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS Amount FROM $DataIn.cw19_studyfeesheet    S 
																					      LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
																					      WHERE A.Number='$Number' AND   (S.Estate=0 OR S.Estate=3)
																					UNION ALL 
																					       SELECT SUM(S.Amount) AS Amount FROM $DataOut.cw19_studyfeesheet   S 
																					       LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
																					       WHERE A.Number='$Number' AND   (S.Estate=0 OR S.Estate=3) 
																					",$link_id));
																					
				  $studyAmount=$studyResult["Amount"]==0?"":"¥". number_format($studyResult["Amount"]);
				  if ($studyResult["Amount"]>0){
				      $hzOnTap=$studyAmount==""?0:1;
				      $tmpArray[]=array("Title"=>"助学补助","Icon"=>"student","Value"=>"$studyAmount","onTap"=>"$hzOnTap","Args"=>"Study","List"=>array());	
			      }
			 }
   
              if (in_array("1102",$modelArray) || $LoginNumber==$Number){ 
			      //行政费用
			      $hzResult =mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS Amount FROM (
									SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.hzqksheet S 
									                                    LEFT JOIN $DataIn.hzqkmain M ON S.Mid=M.Id 
									                                    LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
									                                   WHERE S.Operator='$Number' AND (S.Estate=0 OR S.Estate=3)
									UNION ALL 
									SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataOut.hzqksheet S 
									                                    LEFT JOIN $DataOut.hzqkmain M ON S.Mid=M.Id 
									                                   LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency 
									                                   WHERE S.Operator='$Number' AND (S.Estate=0 OR S.Estate=3))A ",$link_id));
			      /*
			      $hzResult =mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS Amount FROM (
									SELECT SUM(IF(S.Estate=0,M.PayAmount*IF(M.Payee=0,1,M.Payee),S.Amount*C.Rate)) AS Amount FROM $DataIn.hzqksheet S 
									                                    LEFT JOIN $DataIn.hzqkmain M ON S.Mid=M.Id 
									                                    LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
									                                   WHERE S.Operator='$Number' AND (S.Estate=0 OR S.Estate=3)
									UNION ALL 
									SELECT SUM(IF(S.Estate=0,M.PayAmount*IF(M.Payee=0,1,M.Payee),S.Amount*C.Rate)) AS Amount FROM $DataOut.hzqksheet S 
									                                    LEFT JOIN $DataOut.hzqkmain M ON S.Mid=M.Id 
									                                   LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency 
									                                   WHERE S.Operator='$Number' AND (S.Estate=0 OR S.Estate=3))A ",$link_id));
				 */					                                  
				  $hzAmount=$hzResult["Amount"]==0?"":"¥". number_format($hzResult["Amount"]);
			      $hzOnTap=$hzAmount==""?0:1;
			      
			       $tmpArray[]=array("Title"=>"行政费用","Icon"=>"expenses","Value"=>"$hzAmount","onTap"=>"$hzOnTap","Args"=>"Cost","List"=>array());	
		     }
		    if (in_array("1070",$modelArray) || $LoginNumber==$Number){  
		      //社保缴费
		      $sbResult =mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS Amount FROM (
                        SELECT SUM(mAmount+cAmount) AS Amount FROM $DataIn.sbpaysheet WHERE Number='$Number' AND TypeId=1
                         UNION ALL
                        SELECT SUM(mAmount+cAmount) AS Amount FROM $DataOut.sbpaysheet WHERE Number='$Number' AND TypeId=1
                  ) A ",$link_id));
                 $sbAmount=$sbResult["Amount"]==0?"":"¥". number_format($sbResult["Amount"],2);
		         $sbOnTap=$sbAmount==""?0:1;
			    $tmpArray[]=array("Title"=>"社保缴费","Icon"=>"si","Value"=>"$sbAmount","onTap"=>"$sbOnTap","Args"=>"Sb","List"=>array());
			    
			//公积金
			$cpfResult =mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS Amount FROM (
                        SELECT SUM(mAmount+cAmount) AS Amount FROM $DataIn.sbpaysheet WHERE Number='$Number' AND TypeId=2
                         UNION ALL
                        SELECT SUM(mAmount+cAmount) AS Amount FROM $DataOut.sbpaysheet WHERE Number='$Number' AND TypeId=2
                  ) A ",$link_id));
                 $cpfAmount=$cpfResult["Amount"]==0?"":"¥". number_format($cpfResult["Amount"]);
		         $cpfOnTap=$cpfAmount==""?0:1;
		        $tmpArray[]=array("Title"=>"公积金","Icon"=>"cpf","Value"=>"$cpfAmount","onTap"=>"$cpfOnTap","Args"=>"Cpf","List"=>array());
		      }
		      
		      if (count($tmpArray)>0) $dataArray[]=$tmpArray;
		       /*  
		      $dataArray[]=array(
		             "0"=>array("Title"=>"薪资","Icon"=>"wage","Value"=>"$xzAmount","onTap"=>"$xzOnTap"),
		             "1"=>array("Title"=>"行政费用","Icon"=>"expenses","Value"=>"$hzAmount","onTap"=>"$hzOnTap"),
		             "2"=>array("Title"=>"社保缴费","Icon"=>"si","Value"=>"$sbAmount","onTap"=>"$sbOnTap"),
		             "3"=>array("Title"=>"公积金","Icon"=>"cpf","Value"=>"$cpfAmount","onTap"=>"$cpfOnTap")
		       );
		       */
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
          UNION  ALL 
         SELECT Id,Month FROM $DataOut.sbpaysheet WHERE  Number=$Number AND TypeId=3  ) A 
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
		    /*
			  //$UserName=$Number==10001?"陈经理":$Name;
			 $checkCarSql=mysql_query("SELECT Id,BrandId,CarNo FROM $DataPublic.cardata WHERE User='$UserName'",$link_id);
			 if($checkCarRow = mysql_fetch_array($checkCarSql)) {
			        $BrandId=$checkCarRow["BrandId"];
			        $CarNo=$checkCarRow["CarNo"];
			        
			         $dataArray[]=array(
			             "0"=>array("Title"=>"车辆信息","Icon"=>"car","Value"=>"¥0","onTap"=>"0","Args"=>"Car",
								             "UnderText"=>"$CarNo","BrandId"=>"$BrandId"),
			              "1"=>array("Title"=>"维护费用","Icon"=>"","Value"=>"¥0","onTap"=>"1","Args"=>"CarMaintain","List"=>array()),
			              "2"=>array("Title"=>"违规纪录","Icon"=>"","Value"=>"¥0","onTap"=>"1","Args"=>"CarFine","List"=>array()),  
			              "3"=>array("Title"=>"粤通卡","Icon"=>"","Value"=>"¥0","onTap"=>"1","Args"=>"ETC","List"=>array()),  
			              "4"=>array("Title"=>"加油卡","Icon"=>"","Value"=>"¥0","onTap"=>"1","Args"=>"Refuel","List"=>array()) 
			       );
			 }
			 */
		       $jsonArray= array("head"=>$headArray,"navTitle"=>"$GroupName","data"=>$dataArray);
 }

?>