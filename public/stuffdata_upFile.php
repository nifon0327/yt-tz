<?php 
//步骤1 $DataIn.stuffdata 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 配件图档上传");//需处理

//步骤3：//需处理
$StuffId=$_GET["StuffId"];
$ActionId=$_GET["ActionId"];
$upDataFlag=$_GET["upDataFlag"];
if ($upDataFlag=='Y' && ActionId!=""){
		 $Log_Funtion="图档上传";
		// $StuffId=$_GET["StuffId"];
		 $FilePath=$_SERVER['DOCUMENT_ROOT'] ."/download/stufffile/";
		 $upDataSheet="$DataIn.stuffdata";
		 $upPicture=$_FILES['upPicture']['tmp_name'];
		/////////////////////////////////////////
		//之前最后一个记录
		$Date=date("Y-m-d");
	    $SetStr="";
			if ($upPicture!=""){
				$upFile=$upPicture;
				$PreFileName=$StuffId.".pdf";
				$Gfile=$PreFileName;
				$PreFileName=$FilePath . $PreFileName;
				$uploadInfo=move_uploaded_file($upPicture,$PreFileName);
				if($uploadInfo!=""){
					$SetStr="Picture=2";   //上传成功
					 //生成JPG图片
					 $pdfFile=$PreFileName;
					 $jpgFile=$FilePath . $StuffId . "_s.jpg";
					 $jpgFileIpad = $FilePath . $StuffId . ".jpg";
					 exec("convert -quality 80 -colorspace sRGB -density 300 -trim $pdfFile $jpgFileIpad");
					 exec("convert -colorspace sRGB -transparent white -trim $pdfFile $jpgFile");
					/* $nmw =NewMagickWand();
					 $pdfFile=$PreFileName;
					 $jpgFile=$FilePath . $StuffId . "_s.jpg";
                     MagickReadImage($nmw,$pdfFile);
                     MagickWriteImage($nmw,$jpgFile);
                     DestroyMagickWand($nmw);*/
				  }	
		 	   }
			   else{   //文件为空，则去掉
				$SetStr="Picture=0";
				$Log_Funtion="图档删除";
			    }
	      	if($SetStr!=""){
			   $sql = "UPDATE $upDataSheet SET $SetStr WHERE   StuffId=$StuffId";
			   $result = mysql_query($sql);
			   if($result){
			        $Log="配件号为:$StuffId 的记录 $Log_Funtion 成功.</br>";
					$OperationResult="Y";
			      }
			      else{
			        $Log="配件号为: $StuffId 的记录 $Log_Funtion 失败! $sql</br>";
			        $OperationResult="N";
			        }
		      }
    $DateTime=date("Y-m-d H:i:s");
    $Operator=$Login_P_Number;
	$Log_Item="配件资料";	
	$Log_Funtion="更新";
	$Operator=$Login_P_Number;
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
    $IN_res=@mysql_query($IN_recode);		
	if ($OperationResult=="Y"){
	  if (substr($SetStr,-1,1)==2){
		  $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		  $Gfile=anmaIn($Gfile,$SinkOrder,$motherSTR);
		  $retValue="Y@$d@$Gfile";
	      /* echo "<script language='JavaScript'>window.returnValue='Y@$d@$Gfile';window.close();</script> ";*/
	   }
	  else{
		  $retValue="D@NULL@NULL";
		  /* echo "<script language='JavaScript'>window.returnValue='D@NULL@NULL';window.close();</script> ";*/
	   }
	}
	else{
		$retValue="N@NULL@NULL";
	  /*echo "<script language='JavaScript'>window.returnValue='N@NULL@NULL';window.close();</script> ";*/
	}
 }
else{
$upResult = mysql_query("SELECT StuffCname,StuffId FROM $DataIn.stuffdata WHERE StuffId='$StuffId' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$StuffId=$upData["StuffId"];
	$StuffCname=$upData["StuffCname"];
	}
//步骤4：
$SelectCode="($StuffId) $StuffCname";
//$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
//步骤5：//需处理
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
<form action="stuffdata_upFile.php?ActionId=<?php  echo $ActionId?>&StuffId=<?php  echo $StuffId?>&upDataFlag=Y" method="post" enctype="multipart/form-data" onSubmit="return checkFile()" name='upForm' id='upForm'>
<p>&nbsp;</p>
<p>上传配件<font style='color:#FF6F28;font-weight:bold;'><?php  echo $SelectCode?></font>图档(限PDF格式)</p>
 <p><input name="upPicture" type="file" id="upPicture" size="52" DataType="Filter" Accept="pdf" Msg="格式不对,请重选" Row="1" Cel="3"></p>
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
   var tp ="pdf";      
   var rs=tp.indexOf(last);   
   if(rs>=0){   
       return true;   
       }
   else{   
      alert("请选PDF格式文件！");
      return false;   
     } 
  }
}
</script>