<?php 
/*电信---yang 20120801
$DataPublic.my3_exadd
$DataPublic.my3_express 
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="我的快递单";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&Type=$Type&ComeFrom=$ComeFrom";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

//默认联系人信息
$PayerNo=FormatSTR($PayerNo);
$Receiver=FormatSTR($Receiver);
$Company=FormatSTR($Company);
$Country=FormatSTR($Country);
$ZIP=$ZIP;
$Address=FormatSTR($Address);
$Tel=$Tel;
$Mobile=$Mobile;
$Contents=FormatSTR($Contents);
$BoxSize=FormatSTR($BoxSize);
$Date=date("Y-m-d");

//检查联系人是否存在于快递列表，如果不存在，则保存，如果存在，则保存快递记录
//用联系人名称和公司、地址判断
           $checkLinkSql= mysql_query("SELECT * FROM $DataPublic.my3_exadd 
           WHERE Name LIKE '$Receiver' AND Tel like '$Tel' AND Company LIKE '$Company' AND Address LIKE '$Address'",$link_id);
          if($checkLinkRow = mysql_fetch_array($checkLinkSql) ){//有记录
	                  $Id=$checkLinkRow["Id"];
	             }
          else{	//没有记录
	                $LinkmanRecode="INSERT INTO $DataPublic.my3_exadd 
	               (Id,Name,Company,PayerNo,Address,ZIP,Country,Tel,Mobile,Email,Estate,Locks,Date,Operator) VALUES 
	               (NULL,'$Receiver','$Company','','$Address','$ZIP','$Country','$Tel','$Mobile','$Email','1','0','$Date','$Operator')";
	               $Linkman_res=@mysql_query($LinkmanRecode);
	              $Id=mysql_insert_id(); 
	             }
           if($Id!=""){
	                $InsertRecode="INSERT INTO $DataPublic.my3_express  (Id,SendDate,BillNumber,Shipper,Receiver,CompanyId,expressType,ShipType,PayType,PayerNo,Contents,SendContent,Pieces,Length,Width,Height,cWeight,dWeight,Amount,CFSAmount,Remark,Estate,Locks,Date,HandledBy) VALUES 
(NULL,'0000-00-00','','$pNumber','$Id','$CompanyId','$expressType','0','$PayType','$PayerNo','$Contents','$SendContent','$Pieces','$Length','$Width','$Height','$cWeight','0','0','0','','1','1','$Date','0')";
	          $InsertRow=@mysql_query($InsertRecode);
	          $Mid=mysql_insert_id(); 
	          if($InsertRow){
		                  $Log=$TitleSTR."成功.<br>";
		                }
	            else{
		                 $Log="<div class='redB'>".$TitleSTR."失败. $InsertRecode </div><br>";
		                 $OperationResult="N";
		                }
                 /*  if($Attached!=""){//有上传文件
	                   $FileType=substr("$Attached_name", -4, 4);
	                   $OldFile=$Attached;
	                   $FilePath="../download/myexpress/";
	                  if(!file_exists($FilePath)){
		                    makedir($FilePath);
		                   }
	                  $PreFileName="M".$Id. $FileType;
	                  $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
                      if ($Attached!=""){		
		                      $Log.="附件上传成功.<br>";
		                    }
	                else{
		                      $Log.="<div class='redB'>附件上传失败！可能没有上传源文件</div><br>";
		                      $OperationResult="N";
		                    }*/

	                }
           else{
	                $Log="<div class='redB'>联系人 $Receiver 资料添加或读取失败！ $LinkmanRecode </div>";
	                $OperationResult="N";
	              }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>