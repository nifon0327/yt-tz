<?php
    include "../model/modelhead.php";
    $fromWebPage=$funFrom."_read";
    $nowWebPage=$funFrom."_updated";
    $_SESSION["nowWebPage"]=$nowWebPage;
    //步骤2：
    $Log_Item="职位调动资料";     //需处理
    $upDataSheet="$DataPublic.redeployj";   //需处理
    $Log_Funtion="更新";
    $TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
    ChangeWtitle($TitleSTR);
    $DateTime=date("Y-m-d H:i:s");
    $Date= date("Y-m-d");
    $Operator=$Login_P_Number;

    $operateState = true;

    mysql_query("START TRANSACTION");
    $updateStaffStateSql = "UPDATE $DataPublic.staffmain SET Estate=0, locks=0 WHERE Id=$Id";

    if(!mysql_query($updateStaffStateSql)){
        $operateState = false;
    }

    if($operateState){
        $inRrecode="INSERT INTO $DataPublic.dimissiondata SELECT NULL,Number,'$theDate','$LeavedType','$Reason','$LeavedType','0','$Date','$Operator', NULL FROM $DataPublic.staffmain WHERE Id=$Id";

        $res=@mysql_query($inRrecode);
        if($res){
            $Log.="&nbsp;&nbsp;离职资料存档成功.<br>";
        }
        else{
            $Log.="<div class='redB'>&nbsp;&nbsp;离职资料存档失败.</div><br>";
            mysql_query("ROLLBACK");
            $operateState = false;
            $OperationResult="N";
        }
    }

    if($operateState){
        //删除登录相关的资料:在线、用户表、权限表、特殊权限表以及外联的在线、用户表、权限、特殊权限表;其它部门调动、职位调动、考勤调动、默认奖金数据不清除，但不显示
        $delResult = mysql_query("DELETE D,O,U 
        FROM  $DataIn.usertable D 
        LEFT JOIN $DataIn.online O ON O.uId=D.Id 
        LEFT JOIN $DataIn.upopedom U ON D.Id=U.UserId 
        WHERE D.Number IN (SELECT Number FROM $DataPublic.staffmain WHERE Id='$Id')",$link_id);
        if($delResult){
            $Log.="&nbsp;&nbsp;相关系统帐号资料处理成功.<br>";
            //删除外联公司资料???????????
            $delResult = mysql_query("DELETE D,O,U 
		        FROM  $DataSub.usertable D 
		        LEFT JOIN $DataSub.online O ON O.uId=D.Id 
		        LEFT JOIN $DataSub.upopedom U ON D.Id=U.UserId 
		        WHERE D.Number IN (SELECT Number FROM $DataPublic.staffmain WHERE Id='$Id')",$link_id);

        }else{
            $Log.="&nbsp;&nbsp;相关系统帐号资料处理失败.<br>";
            mysql_query("ROLLBACK");
            $operateState = false;
            $OperationResult="N";
         }
    }

    if($operateState){
        mysql_query("COMMIT");
    }

    $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
    $IN_res=@mysql_query($IN_recode);
    include "../model/logpage.php";

?>