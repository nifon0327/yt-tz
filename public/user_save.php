<?php
//电信-EWEN
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="登录帐户";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$fromWebPage="user_read";
$ALType="uType=$uType";
//新增操作
$uPwd=MD5($uPwd);
$uName=Chop(trim($uName));
$Date=date("Y-m-d");
$uSeal=0;

if($Attached!=""){
	$FilePath="../download/userseal/";
	$OldFile=$Attached;
	$PreFileName="u".$pNumber.".gif";
	$uSeal=UploadFiles($OldFile,$PreFileName,$FilePath);
	if($uSeal){
		$Log="&nbsp;&nbsp;&nbsp;&nbsp;上传印章成功！<br>";
		$uSeal=1;
		}
	else{
		$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;上传印章失败！</div><br>";
//		$OperationResult="N";
		$uSeal=1;
		}
	}
$WebStyle=$Login_cSign==7?2:4;
$inRecode="INSERT INTO $DataIn.usertable (Id,uType,uName,uPwd,Number,uSeal,lDate,Date,WebStyle,FaxNO,uSign,roleId,Estate,Locks,Operator) VALUES (NULL,'$uType','$uName', '$uPwd','$pNumber','$uSeal','0000-00-00','$Date','$WebStyle','','$uSign','$roleId','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
$UserId = mysql_insert_id();
if ($inAction){
	     $Log.="登录名为 $uName 的 $TitleSTR 成功! <br>";
         //默认权限
          $insertSql ="INSERT INTO $DataIn.ac_usermenus(`Id`, `UserId`, `MenuId`, `Action`, `Estate`, `Locks`, `PLocks`, `creator`, `created`, `modifier`, `modified`, `Date`, `Operator`)  SELECT  NULL,'$UserId',menu_id,action,'1','0','0','$Operator', '$DateTime','$Operator', '$DateTime','$Date','$Operator'  FROM $DataIn.ac_rolemenus  WHERE role_id=$roleId";
         $insertResult =@mysql_query($insertSql);
        if($insertResult){
                $Log.="登录名为 $uName 的 默认角色 权限 添加成功! <br>";
              }
        else{
               $Log.="<div class=redB>登录名为 $uName 的 默认角色 权限 添加失败$insertSql! </div><br>";
            }
	}
else{
	$Log.="<div class=redB>登录名为 $uName 的 $TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
