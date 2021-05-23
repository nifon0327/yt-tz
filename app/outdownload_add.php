<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php   
     include "../basic/chksession.php";
     include "../basic/parameter.inc";
     include "phpqrcode/qrcodelib.php";
     
     $today=date("Ymd");
    $mySql="SELECT A.cer,A.appname,A.name  FROM $DataPublic.app_sheet A WHERE A.id='$Id' limit 1";
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
        $cer=$myRow["cer"];
        $name=$myRow["name"];
        $appname=$myRow["appname"];
        if ($appname=='AshCloudApp'){
             $newCer=md5($appname . $today . $DomainCerKey);
        }
        else{
	         $newCer=md5($appname . $DomainCerKey);
        }
       
        if ($cer=="" || $cer!=$newCer){
	        //$cer=md5($appname . $today);
	        
	        $cer=$newCer;
	        $upSql="UPDATE $DataPublic.app_sheet SET cer='$cer' WHERE Id='$Id'";
	        $upResult = mysql_query($upSql,$link_id);
        }
        
        $outLink="http://$OutDomainNameStr/app/outdownload.php?Id=$cer";
         //生成QR码
	      $code_data=$outLink;
	      include "phpqrcode/createqrcode.php";
    }
?>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<link rel="stylesheet" href="app.css" />
		<title>App应用下载</title>
          <body style='background:#ffffff;margin-left:50px;'>
             <br><br><b> <?php echo $name;?> </b> 外部下载地址：<br><br>
               <span><?php echo $outLink;?></span></br>
               <br><img src='<?php echo $qrcode_File;?>' style='margin-top:-5px;'/>
         </body>
</html>