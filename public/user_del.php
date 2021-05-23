<?php
//电信-EWEN
//代码共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="用户帐号";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;

$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
    $Id=$checkid[$i];
    if ($Id!=""){
        // 删除相应的印章
        $userInfo=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.UserTable WHERE Id=$Id",$link_id));
        $File="u". $userInfo["Number"].".gif";
        $FilePath="../download/userseal/$File";
        if(file_exists($FilePath)){
            unlink($FilePath);
            }

        $uType = isset($uType) && $uType ? $uType : $userInfo["uType"];

        switch($uType){
            case 1://删除员工相应权限
            case 4://删除外部人员相应权限：员工和外部人员、参观人员都是对系统功能使用权限
            case 5://删除参观人员权限
                $DelSql = "DELETE A,B FROM $DataIn.usertable A LEFT JOIN $DataIn.upopedom B ON A.Id=B.UserId WHERE A.Id=$Id";
                $DelResult= mysql_query($DelSql);
                //$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.usertable,$DataIn.upopedom");
                break;
            case 2:
            //删除客户相应权限
                $DelSql= "DELETE A,B
                    FROM $DataIn.usertable A
                    LEFT JOIN $DataIn.sys_clientfunpower B ON A.Id=B.UserId
                    WHERE A.Id=$Id";
                $DelResult= mysql_query($DelSql);
                //$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.usertable,$DataIn.sys_clientfunpower");
            break;
            case 3://删除供应商权限
            $DelSql= "DELETE A,B
                    FROM $DataIn.usertable A
                    LEFT JOIN $DataIn.sys4_gysfunpower B ON A.Id=B.UserId
                    WHERE A.Id=$Id";
                $DelResult= mysql_query($DelSql);
                //$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.usertable,$DataIn.sys4_gysfunpower");
            break;
            }

        if($DelResult){
            $Log.="&nbsp;&nbsp;$x-ID号为 $Id 的 $TitleSTR 成功。<br>";
            $y++;
            }
        else{
            $Log.="<div class='redB'>&nbsp;&nbsp;$x-ID号为 $Id 的 $TitleSTR 失败! $DelSql</div><br>";
            $OperationResult="N";
            }//end if ($result)
        //踢出已登录者
        $delOnline = "DELETE FROM $DataIn.online WHERE uId='$Id'";
        $delResult = mysql_query($delOnline);
        if($delResult){
            $Log.="&nbsp;&nbsp;在线踢出成功.<br>";
            }
        else{
            $Log.="<div class='redB'>&nbsp;&nbsp;在线踢出失败.</div><br>";
            $OperationResult="N";
            }
        $x++;
        }//end if ($Id!="")
    }//end for($i=1;$i<$IdCount;$i++)
//如果该页记录全删，则返回第一页
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&uType=$uType&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>