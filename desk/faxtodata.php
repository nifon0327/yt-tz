<?php   
//电信-zxq 2012-08-01
/*
$DataPublic.faxdata	传真列表
*/
$FilePath="../download/faxfile/";
if(!file_exists($FilePath)){
	makedir($FilePath);
	}

$Faxdir="../faxin/";
$handle=opendir($Faxdir);
while($File=readdir($handle)) {
	$FileType=substr($File,-4,4);
	if (($File!=".")and($File!="..")and($FileType==".pdf" || $FileType==".jpg")) {

		$InDateTime=date("Y-m-d H:i:s",filemtime($Faxdir."".$File));
		$NewFile=date("YmdHis",filemtime($Faxdir."".$File)). $FileType;
		if (!copy("$Faxdir/$File", "../download/faxfile/$NewFile")) {
			print ("failed to copy $file...<br>\n");}
		else{
			//读写数据库
			$IN_recodeN="INSERT INTO $DataPublic.faxdata (Id,InDateTime,FileName,Claimer,ClaimDate,Sign,Title) VALUES (NULL,'$InDateTime','$NewFile','','0000-00-00 00:00:00','1','')";
			$resN=@mysql_query($IN_recodeN);
    		unlink($Faxdir.$File);
   			}
   		}
	}
closedir($handle); 
//个人传真目录的处理
if($Login_ExtNo!=""){
	$Faxdir="../faxin/".$Login_ExtNo."/";
	$handle=opendir($Faxdir);
	while($File=readdir($handle)) {
		$FileType=substr($File,-4,4);
		if (($File!=".")and($File!="..")and($FileType==".pdf" || $FileType==".jpg")) {
			$InDateTime=date("Y-m-d H:i:s",filemtime($Faxdir."".$File));
			$NewFile=date("YmdHis",filemtime($Faxdir."".$File)). $FileType;
			if (!copy("$Faxdir/$File", "../download/faxfile/$NewFile")) {
				print ("failed to copy $file...<br>\n");}
			else{
				//读写数据库
				$ClaimDate=date("Y-m-d H:i:s");
				$IN_recodeN="INSERT INTO $DataPublic.faxdata (Id,InDateTime,FileName,Claimer,ClaimDate,Sign,Title) VALUES (NULL,'$InDateTime','$NewFile','$Login_P_Number','$ClaimDate','1','')";
				$resN=@mysql_query($IN_recodeN);
				unlink($Faxdir.$File);
				}
			}
		}
	closedir($handle); 
	}
?>