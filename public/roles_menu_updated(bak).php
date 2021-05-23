<?php  
include "../model/modelhead.php";
//步骤2：
$Log_Item="角色默认菜单";			//需处理
$fromWebPage="roles_menu_read";
$nowWebPage="roles_menu_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="RoleId=$RoleId";
//步骤3：需处理
$upSql = "UPDATE $DataIn.ac_rolemenus  SET Action='0' WHERE role_id='$RoleId'";
$upResult = mysql_query($upSql);
$x=1;
if($upResult){//清0成功	
   //print_r($checkid);
	$Log="角色 $RoleId 的权限已做清0准备，开始更新...<br>";
	for($i=1;$i<$IdCountNum;$i++){
       $insertSign = 0 ;
		$tempValue=$checkid[$i];
		if($tempValue!=""){
		//分解值：上级，本级，级别，权限，模块ID
			$Action=0;
			$Field=explode(",",$tempValue);
			$Grade=$Field[3];			
			if($Grade<3){
				$MenuId=$Field[4];
				if($Grade==2){//2级菜单权限
					$tAction=0;
					for($j=2;$j<=6;$j++){
						$i++;
						$tValue=$checkid[$i];
						if($tValue!=""){
							$tField=explode(",",$tValue);
							$Action=$Action+$tField[4];
							}//end if($tValue!="")
						}//end for($j=2;$j<6;$j++)
					}//end if($Grade==2)
				else{
					$Action=1;
					}//end if($Grade==2)
				$CheckResult = mysql_query("SELECT Id FROM $DataIn.ac_rolemenus WHERE 1 AND role_id='$RoleId' AND menu_id='$MenuId' ORDER BY Id LIMIT 1",$link_id);
				if ($CheckRow = mysql_fetch_array($CheckResult)) {
					//更新权限
					$upSql2="UPDATE $DataIn.ac_rolemenus SET Action='$Action' WHERE role_id='$RoleId' AND menu_id='$MenuId'";
					}
				else{
					////新增
					$upSql2="INSERT INTO $DataIn.ac_rolemenus (Id,role_id,menu_id,Action) VALUES (NULL,'$RoleId','$MenuId','$Action')";
                     $insertSign =1;
					}				
				$upResult2=mysql_query($upSql2);
				if($upResult2){
					$Log=$Log."角色 $RoleId 使用 $MenuId 的权限 $Action 设定成功!<br>";
                     /*if($insertSign==1){
                                 $CheckResult2 = mysql_fetch_array(mysql_query("SELECT  M.UserId FROM $DataIn.ac_usermenus   M 
                                   LEFT JOIN $DataIn.usertable  U ON U.Id = M.UserId
                                   WHERE  U.roleId=$RoleId  GROUP BY M.UserId",$link_id));
                                  while ($CheckRow = mysql_fetch_array($CheckResult2)){
                                            $UserId = $CheckRow["UserId"];
				                             $InSql  =  "INSERT INTO $DataIn.ac_usermenus (Id, UserId, MenuId, Action, Estate, Locks, PLocks, creator, created, modifier, modified, Date, Operator) VALUES( NULL,'$UserId','$RoleId','$Action','1','0','0','$Operator', '$DateTime','$Operator', '$DateTime','$Date','$Operator')";
				                            $InResult  = @mysql_query($InSql);
                                     }
                           }*/
					}
				else{
					$Log=$Log."<div class='redB'>角色 $RoleId 使用 $MenuId 的权限 $Action 设定失败!</div><br>";
					$OperationResult="N";
					}
				$x++;
				}
			}//end if($tempValue!="")
		}//end for($i=1;$i<$IdCount;$i++)
	}//end if(upResult)
else{
	$Log="<div class='redB'>用户 $User 的权限清0失败!</div><br>";
	$OperationResult="N";
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>