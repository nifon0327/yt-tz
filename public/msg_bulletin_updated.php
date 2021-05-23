<?php 
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="电子公告";		//需处理
$upDataSheet="$DataPublic.msg1_bulletin";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$DateInStr=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;

switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
        case 87:
                $Log_Funtion="上传文件";
                $FilePath="../download/msgfile/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
		}
                $uploadNums=count($Picture);
		for($i=0;$i<$uploadNums;$i++){
                    //上传文档				
                    $upPicture=$Picture[$i];
                   
                    if ($upPicture!=""){
                            $fileName=$_FILES['Picture']['name'][$i]; 
                            $FileType=substr($fileName, -4, 4);
                            $PreFileName=$Id."_". strtotime(date('YmdHis', time())) ."_$i". $FileType;
                            
                            $uploadInfo=UploadFiles($upPicture,$PreFileName,$FilePath);
                            if($uploadInfo!=""){
                                    
                                $inRecode="INSERT INTO $DataPublic.msg1_picture (Id,Mid,Picture,Date,Operator) VALUES (NULL,'$Id','$PreFileName','$DateInStr','$Operator')";
                                //echo $inRecode;
                                $inAction=@mysql_query($inRecode);
                                if($inAction){
                                        $Log.="公告 $Id 的图片文件 $uploadInfo 添加成功.<br>";
                                        //include "../ipdAPI/push_kq.php";
                                        $EndNumber++;}
                                else{
                                        $Log.="<div class='redB'>公告 $Id 的图片文件 $uploadInfo 添加失败. $inRecode </div><br>";
                                        $OperationResult="N";
                                        }
                                }
                                    
                            }
                 }
                
                break;
	default:
		$SetStr="Title='$Title',Content='$Content',Type='$Type',Date='$Date',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>