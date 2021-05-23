<?php   
//电信---yang 20120801
if($BarCode!="")
{
	include "../basic/parameter.inc";
	
	$mySql=mysql_query("SELECT P.cName,P.eCode,C.Forshort,T.TypeName FROM $DataIn.stuffdata D 
		LEFT JOIN $DataIn.pands N ON N.StuffId=D.StuffId
		LEFT JOIN $DataIn.productdata P ON P.ProductId=N.ProductId 
		LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		WHERE 1 AND D.StuffCname LIKE '%$BarCode%' LIMIT 1",$link_id);
	
	if($myRow=mysql_fetch_array($mySql)){
		//echo $myRow["cName"]."<br>".$myRow["eCode"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$Client=$myRow["Forshort"];
		$TypeName=$myRow["TypeName"];
		}
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</HEAD>
<BODY>
<form id="form2" name="form2" method="post" action="scandata_1.php"   target="topFrame" >
<table width="299" height="276" bgcolor="#CC99FF">
<tr><td>
<?php   
   if ($cName!="")  //查找出来的
   {
	   //echo "Here!";
	   echo "条　　码：".$BarCode."<br>";
	   echo "客　　户：".$Client."<br>";
	   echo "中文名称：".$cName."<br>";
	   echo "产品代码：".$eCode."<br>";	 
	   echo "产品分类：".$TypeName;
	   echo "<input name='Client' type='hidden' id='Client' value='$Client' >";
       echo "<input name='cName' type='hidden' id='cName' value='$cName' >";
	   echo "<input name='eCode' type='hidden' id='eCode' value='$eCode' >";
	   echo "<input name='BarCode' type='hidden' id='BarCode' value='$BarCode' >";
	   echo "<input name='TypeName' type='hidden' id='TypeName' value='$TypeName' >";
	   echo "<SCRIPT   language='JavaScript'> document.form2.submit(); </script>";
   }
   else {  //从Top上面的
       //echo "No here!";
	   if($BarCode!=""){
		   echo "条　　码：<span style='background-color:#F00'>$BarCode</span> <br>";
		   echo "　　　　　<span style='background-color:#F00'>未找到到相关产品！</span> <br>";
	   }
	   if($TBarCode!="")
	   {
		   echo "<br>最近一次<br>";
		   echo "条　　码：".$TBarCode."<br>";
		   echo "客　　户：".$TClient."<br>";
           echo "中文名称：".$TcName."<br>";
		   echo "产品代码：".$TeCode."<br>";
		   echo "产品分类：".$TTypeName;
	   	   echo "<input name='Client' type='hidden' id='Client' value='$TClient' >";
		   echo "<input name='cName' type='hidden' id='cName' value='$TcName' >";
		   echo "<input name='eCode' type='hidden' id='eCode' value='$TeCode' >";
		   echo "<input name='BarCode' type='hidden' id='BarCode' value='$TBarCode' >";
	       echo "<input name='TypeName' type='hidden' id='TypeName' value='$TTypeName' >";		   
		   echo "<SCRIPT   language='JavaScript'> document.form2.submit(); </script>";
	   }
	   echo "<SCRIPT   language='JavaScript'> document.form2.submit(); </script>";
   }
?>


</td></tr>
</table>
</form>
</BODY>
</HTML>
