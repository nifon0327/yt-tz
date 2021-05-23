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
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="RoleId=$RoleId";
//Find the menus 
$menusArray  = array();
$actionArray   = array();
$usersArray    = array();
$menuResult = mysql_query("SELECT menu_id AS id FROM ac_rolemenus WHERE role_id=$RoleId",$link_id);
while ($menusRows = mysql_fetch_array($menuResult)) {
        $menu_id          = $menusRows["id"];
        $menusArray[$menu_id] =$menu_id;
    }

$usersResult = mysql_query("SELECT Id FROM usertable WHERE RoleId=$RoleId",$link_id);
while ($usersRows = mysql_fetch_array($usersResult)) {
        $users_id          = $usersRows["Id"];
        $usersArray[] =$users_id;
    }

$menusUpdates = array(); //前端表單回傳，變動後的菜單項目
$actionUpdates = array(); //前端表單回傳，變動後的菜單Action

for($i=1;$i<$IdCountNum;$i++){
		$tempValue=$checkid[$i];
		if($tempValue!=""){
		   //分解值：上级，本级，级别，权限，模块ID
			$Action=0;
			$Field=explode(",",$tempValue);
			$Grade=$Field[3];			
			if($Grade<3){
				$MenuId=$Field[4];
                 $menusUpdates[$MenuId]=$MenuId;
				 if($Grade==2){//2级菜单权限
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
                  $actionUpdates[$MenuId]=$Action;
			  }
		}//end if($tempValue!="")
}

$toAdd             = array(); //要新增的權限
foreach($menusUpdates  as $key =>$value){
     $existSign =0 ;
          foreach($menusArray  as $key1=>$value1){
                       if($value==$value1){
                                $existSign=1;  break;
                           }
              }
      if($existSign==0){
             $toAdd[$key] = $value;
          }
}

$toDelete          = array(); //要删除的權限
foreach($menusArray  as $key =>$value){
     $existSign =0 ;
          foreach($menusUpdates  as $key1=>$value1){
                       if($value1==$value){
                                $existSign=1;  break;
                           }
              }
      if($existSign==0){
            $toDelete[$key] = $value;
          }
}

$deleteCount  =count($toDelete);
$addCount  =count($toAdd);
if($deleteCount>0){
		$rolesDelSql =  "DELETE FROM ac_rolemenus WHERE role_id=$RoleId  AND menu_id IN (".implode(',', $toDelete).")";
		$rolesDelResult  =  @mysql_query($rolesDelSql);
		if($rolesDelResult){
				$Log=$Log."角色 $RoleId 的权限(".implode(",", $toDelete).")删除成功!<br>";
		}else{
				$Log=$Log."<div class='redB'>角色 $RoleId 的权限(".implode(",", $toDelete).")删除失败!</div><br>";
				$OperationResult="N";
		}
}
if($addCount>0){
		//加上角色的權限
		$insertRoleSql = "INSERT INTO ac_rolemenus (id, role_id, menu_id, action) VALUES ";
		foreach($toAdd  as $item){
		     $Action = $actionUpdates[$item];
			 $insertRoleSql .= "(null, '$RoleId','$item', '$Action'),";
		}
		$insertRoleSql = substr($insertRoleSql, 0, -1);
		$insertRoleResult  = @mysql_query($insertRoleSql);
		if($insertRoleResult){
				$Log=$Log."角色 $RoleId 新增的权限(".implode(",", $toAdd).")设定成功!<br>";
		}else{
				$Log=$Log."<div class='redB'>角色 $RoleId 新增的权限(".implode(",", $toAdd).")设定失败!</div><br>";
				$OperationResult="N";
		}
}

//幫符合角色的使用者套權限：
//先刪除角色的權限
if($deleteCount>0){
		$usersDelSql = "DELETE FROM ac_usermenus WHERE MenuId IN (".implode(",", $toDelete).") AND UserId IN (".implode(",", $usersArray).")";
		$usersDelResult = @mysql_query($usersDelSql);
		if($usersDelResult){
				$Log=$Log."角色 $RoleId 对应的用户(".implode(",", $usersArray).") 的权限(".implode(",", $toDelete).")删除成功!<br>";
		}else{
				$Log=$Log."<div class='redB'>角色 $RoleId 对应的用户(".implode(",", $usersArray).") 权限(".implode(",", $toDelete).")删除失败! $usersDelSql</div><br>";
				$OperationResult="N";
		}
}
if($addCount>0){
		//加上角色的權限
		$insertUserSql = "INSERT INTO ac_usermenus (Id, UserId, MenuId, Action, Estate, Locks, PLocks, creator, created, modifier, modified, Date, Operator) VALUES ";
		foreach($toAdd as $item){
		    $Action = $actionUpdates[$item];
			foreach($usersArray as $userId){
				$insertUserSql .= "(null, '$userId', '$item', '$Action','1','0','0', '$Operator', '$DateTime', '$Operator', '$DateTime', '$Date', '$Operator'),";
			}
		}
		$insertUserSql = substr($insertUserSql, 0, -1);
		$insertUserResult  = @mysql_query($insertUserSql);
		if($insertUserResult){
				$Log=$Log."角色 $RoleId 对应的用户(".implode(",", $usersArray).") 新增的权限(".implode(",", $toAdd).")设定成功!<br>";
		}else{
				$Log=$Log."<div class='redB'>角色 $RoleId 对应的用户(".implode(",", $usersArray).") 新增的权限(".implode(",", $toAdd).")设定失败! $insertUserSql</div><br>";
				$OperationResult="N";
		}
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";

?>