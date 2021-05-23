<?php 
include "../basic/parameter.inc";
$Date=date("Y-m-d");
$CheckResult=mysql_query("SELECT  P.cName,P.ProductId,I.Picture
	FROM $DataIn.productdata P
    LEFT JOIN $DataIn.productimg I ON I.ProductId=P.ProductId
WHERE P.Estate>0 AND I.Id IS  NULL ", $link_id);
while($CheckRow=mysql_fetch_array($CheckResult)){
     $ProductId =$CheckRow["ProductId"];
     $cName =$CheckRow["cName"];
     $Picture =$CheckRow["Picture"];
     $filenme="T".$ProductId."_H".".zip";
echo $filenme."<br>";
   /*  if(file_exists($filenme)){
				        $inRecode1="INSERT INTO $DataIn.productimg (Id,ProductId,Picture,Date,Type,Operator) VALUES (NULL,'$ProductId','$filenme','$Date','1','10871')";
						$inAction1=@mysql_query($inRecode1);
						if($inAction1){     
							  $Log.="产品 $ProductId 的带包装高清图片 $HuploadInfo1 上传成功.<br>";
							}
        }*/
}