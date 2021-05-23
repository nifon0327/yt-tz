<?php 

	include "../basic/parameter.inc";
	
	$loginInfo = $_POST["info"];
	$loginArray = explode("|",$loginInfo);
	$loginTag = "not";
	$loginServer=1;
	
	$username = $loginArray[0];
	$password = md5($loginArray[1]);
	
	$mysql = "Select * From $DataIn.UserTable Where uName = '$username' And uPwd = '$password'  And uType IN (1,4)";//uType!=13
	$loginResult = mysql_query($mysql);
	$loginCount = mysql_num_rows($loginResult);

	if ($loginCount ==0){
		  $mysql = "Select * From $DataSub.UserTable Where uName = '$username' And uPwd = '$password' And uType IN (1,4)";
	      $loginResult = mysql_query($mysql);
	      $loginCount = mysql_num_rows($loginResult);
	      if($loginCount == 1) $loginServer=2;
	}
	
	if($loginCount == 1)
	{
		$loginRow = mysql_fetch_assoc($loginResult);
		$estate = $loginRow["Estate"];
		$userNumber = $loginRow["Number"];

		$tmpData=$loginServer==1?$DataIn:$DataSub;
		
		if ($userNumber=='50019'){
			$csginResult = mysql_query("Select '7' AS cSign,M.Name,M.Forshort AS GroupName,M.Estate  From $DataIn.ot_staff M
	                                               Where M.Number = '$userNumber'");
		}
		else{
			$csginResult = mysql_query("Select M.cSign,M.Name,GroupName,M.Estate  From $DataPublic.staffmain M
	                                              LEFT JOIN $tmpData.staffgroup G ON G.GroupId=M.GroupId
	                                               Where M.Number = '$userNumber'");
         }                                     
		$csginRow = mysql_fetch_assoc($csginResult);
		$job = $csginRow["JobId"];
		$estate=($estate==1 && $job>0)?$csginRow["Estate"]:$estate;
		if($estate == "1")
		{	
		       $loginTag = "pass";
		        $userCsgin = $csginRow["cSign"];
		        $userCName = $csginRow["Name"];
		        
		       $GroupName=$csginRow["GroupName"];
		        $loginQueue = array("$loginTag","$userNumber","$loginServer","$userCName","$GroupName");
		}
		
		
		else if($estate == "0")
		{
			$loginErr = "该帐号目前禁用!!";
			$loginQueue = array("$loginTag","$loginErr");
		}
		
	}
	else
	{
		$loginErr = "帐号或密码错误!";
		$loginQueue = array("$loginTag","$loginErr");
	}
	
	echo json_encode($loginQueue);
	
?>