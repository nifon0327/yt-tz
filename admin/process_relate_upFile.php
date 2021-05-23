<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 半成品配件工序图片上传");//需处理

//步骤3：//需处理
$StuffId=$_GET["StuffId"];
$ActionId=$_GET["ActionId"];
$mStuffId=$_GET["mStuffId"];
$ProcessId=$_GET["ProcessId"];
$upDataFlag=$_GET["upDataFlag"];
if ($upDataFlag=='Y' && $ActionId!=""){
		 $Log_Funtion="图档上传";
		 $FilePath=$_SERVER['DOCUMENT_ROOT'] ."/download/processimg/";
            if(!file_exists($FilePath)){
	           makedir($FilePath);
            }

		 $upPicture=$_FILES['upPicture']['tmp_name'];
		 if ($upPicture!=""){
				$upFile=$upPicture;
				$PreFileName=$mStuffId."_".$StuffId."_".$ProcessId.".jpg";
				$Gfile=$PreFileName;
				$PreFileName=$FilePath . $PreFileName;
				$uploadInfo=move_uploaded_file($upPicture,$PreFileName);
		 	   }
		if($uploadInfo!=""){
		       $Log=$PreFileName."图档上传成功";
		       $OperationResult="Y";
		     }
		 else{
		      $Log="<span style='redB'>".$PreFileName."图档上传失败"."</span>";
		      $OperationResult="N";
		     }
		if ($OperationResult=="Y"){
		      $d=anmaIn("download/processimg/",$SinkOrder,$motherSTR);
		      $Gfile=anmaIn($PreFileName,$SinkOrder,$motherSTR);
		      $retValue="Y@$d@$Gfile";
		     }
		 else{
		      $retValue="N@NULL@NULL";
		     }
    }
else{
       $upResult = mysql_query("SELECT ProcessName,ProcessId FROM $DataIn.process_data 
	   WHERE ProcessId='$ProcessId' LIMIT 1",$link_id);
       if($upData = mysql_fetch_array($upResult)){
	      $ProcessId=$upData["ProcessId"];
	      $ProcessName=$upData["ProcessName"];
	    }
      $SelectCode="($ProcessId) $ProcessName";
  }
?>
<html>
<base target="_self">
<script language="JavaScript" type="text/JavaScript">
  function   centerWindow()   
  {  
    var   xMax   =   screen.width;
    var   yMax   =   screen.height;
    window.moveTo(xMax/2-100,yMax/2-100-80);
  }  
  centerWindow();  
</script>
<body style="background:#F2F2F2">
<?php  if ($upDataFlag!="Y"){ ?>
<form action="process_relate_upFile.php?ActionId=<?php  echo $ActionId?>&StuffId=<?php  echo $StuffId?>&mStuffId=<?php  echo $mStuffId?>&ProcessId=<?php  echo $ProcessId?>&upDataFlag=Y" method="post" enctype="multipart/form-data" onSubmit="return checkFile()" name='upForm' id='upForm'>
<p>&nbsp;</p>
<p>上传加工工序图片<font style='color:#FF6F28;font-weight:bold;'><?php  echo $SelectCode?></font>(限JPG格式)</p>
 <p><input name="upPicture" type="file" id="upPicture" size="52" DataType="Filter" Accept="jpg" Msg="格式不对,请重选" Row="1" Cel="3"></p>
 <br />
<p align="center"><input type="submit" id="saveButton" name="saveButton"  value="上 传" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" id=="close" name="close" value="关 闭" onclick= "window.close(); "/></p>
<input name='retValue' type='hidden' id='retValue' value=''>
</form>
 <?php  }else{ ?>
<br /><br />
<p align='center'><font style='color:#FF6F28;font-weight:bold;'>操作结果：</font><?php  echo $Log?></p>
<br /><br />
<p align="center"><input type="button" id=="close" name="close" value="关 闭" onclick= "window.close(); "/></p>
<input name='retValue' type='hidden' id='retValue' value='<?php  echo $retValue?>'>
<?php  } ?>
</body>
</html>

<script language="JavaScript" type="text/JavaScript">
	var retVal=document.getElementById('retValue').value;
	if(!-[1,]){  //判断是否为IE
	  window.dialogArguments.document.getElementById("backValue").value =retVal; 
	}
	else{
        window.opener.document.getElementById("backValue").value =retVal; 
	}
function checkFile(){ 
  var filepath=document.upForm.upPicture.value;
  if (filepath.length<4){
	  if (filepath.length==0) {
	    if(window.confirm("未选择上传文件，确定删除原有上传文件?")){ return true;}
        else {return false};
	 }
	 else {return false;} 
  }
  else{
   var re = /(\\+)/g;     
   var filename=filepath.replace(re,"#");       
   var one=filename.split("#");       
   var two=one[one.length-1];       
   var three=two.split("."); 
   var last=three[three.length-1];      
   var tp ="jpg";      
   var rs=tp.indexOf(last);   
   if(rs>=0){   
       return true;   
       }
   else{   
      alert("请选JPG格式文件！");
      return false;   
     } 
  }
}
</script>