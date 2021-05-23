<?php    
//电信-EWEN

$MyPDOEnabled=1;  //启用PDO连接数据库
include "../model/modelhead.php";
//include "../model/stuffcombox_function.php";
//步骤2：
$Log_Item="客户订单";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&CompanyId=$CompanyId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$SubClientId=0;
if (count($Pid)>0){
	$_ProductId=implode("|", $Pid);
	$_Qty=implode("|", $Qty);
	$_Price=implode("|", $ProductPrice);
	$myResult=$myPDO->query("CALL proc_yw1_ordersheet_insert($CompanyId,$SubClientId,'$OrderPO','$OrderDate','$_ProductId','0','$_Qty','$_Price',$Operator);");
	$myRow = $myResult->fetch(PDO::FETCH_ASSOC);
	$OperationResult = $myRow['OperationResult'];
	$OrderNumber=$myRow['OrderNumber'];
	$Log=$myRow['OperationLog'];
	
	//需清除上次查询，否则执行下面sql会报错
	$myResult=null;
	
	//echo "$OrderNumber/$OperationResult";
	
	if ($OperationResult=="Y"){
	    $Log="<div class=greenB>$Log</div>"; 

		//检查并上传文件
		if($ClientOrder!=""){
			$FilePath="../download/clientorder";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			$FileType=substr("$ClientOrder_name", -4, 4);
			$OldFile=$ClientOrder;
			$PreFileName=$OrderNumber.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			
			//更新主单
		    $upSql="UPDATE $DataIn.yw1_ordermain SET ClientOrder='$uploadInfo' WHERE OrderNumber='$OrderNumber'";
		    //echo $upSql;
		    $count = $myPDO->exec($upSql);
		    
		    if ($count>0){
			    $Log.="<div class=greenB>&nbsp;&nbsp;客户下单资料上传成功</div>";

		    }else{
			    $Log.="<div class=redB>&nbsp;&nbsp;客户下单资料上传失败</div>"; 
			    $OperationResult="N";
		    } 
		}

        include_once "../weixin/weixin_api.php";

        $weixin = new weixin_api();

        $touser = 'op_Tyw_xfVJTGxaUQ3FxgjTIo7Qs'; //微信陈纲 open_id

        $next_user = '陈纲';//发送给的用户名字，与$touser相对应

        $login_user = $_SESSION['Login_Name'];  //当前登录用户


        $login_time = date('Y-m-d H:i:s');//操作时间

        $time = explode(' ', $login_time);

        $time = $time[1];

        $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"采购"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

        $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

        $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

        if ($res){
            $Log.="已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
        }

        $touser = 'op_Tyw2A4FDzjmILjn9y-s1oV3u4'; //微信姚尚程 open_id

        $next_user = '姚尚程';//发送给的用户名字，与$touser相对应

        $ress = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

        if ($ress){
            $Log.="已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
        }

	}
	else{
		$Log="<div class=redB>&nbsp;&nbsp;客户订单添加失败</div>" . "proc_yw1_ordersheet_insert($CompanyId,$SubClientId,'$OrderPO','$OrderDate','$_ProductId','0','$_Qty','$_Price',$Operator);";
	    $OperationResult="N";
	}
	
 }

include "../model/logpage.php";
?>
