<?php   	
//**********电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>每月下单、出货数量条形图</title>
</head>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" method="post" action="">
<?php   
//产品类型
   $ProductResult=mysql_query("SELECT T.TypeId,T.TypeName,M.Color 
   FROM $DataIn.producttype T 
   LEFT JOIN $DataIn.productmaintype M ON M.Id=T.mainType
   WHERE 1");
echo"<select id='TypeId' name='TypeId'  onchange='document.form1.submit()'>";
   if($ProductRow=mysql_fetch_array($ProductResult)){
       do{
	      $thisTypeId=$ProductRow["TypeId"];
		  $TypeName=$ProductRow["TypeName"];
		  $Color=$ProductRow["Color"];
         $TypeId=$TypeId==""?$thisTypeId:$TypeId;
		  if($thisTypeId==$TypeId){
		      echo "<option value='$thisTypeId' style='color:$Color' selected>$TypeName</option>";
		      }
		  else{
		      echo "<option value='$thisTypeId' style='color:$Color'>$TypeName</option>";
		      }
	      }while($ProductRow=mysql_fetch_array($ProductResult));
       echo "</select>&nbsp;";
       }
	  //月份
	  $ChooseMonth=$ChooseMonth==""?12:$ChooseMonth;
	  $MonthType="Month".$ChooseMonth;
	  $$MonthType="Selected";
	  echo "<select id='ChooseMonth' name='ChooseMonth' onchange='document.form1.submit()'>";
	  echo "<option value='12' $Month12>最近12个月</option>
	             <option value='24' $Month24>最近24个月</option>
			     <option value='36' $Month36>最近36个月</option>
			     </select>";
echo"<img src='charttopng_7.php?Y=$ChooseMonth&chartType=$Type&Id=$TypeId'><p>&nbsp;</p>";
//if($TypeId==8029)echo"<img src='charttopng_profit.php?Y=$ChooseMonth&chartType=$Type&Id=$TypeId'><p>&nbsp;</p>";
echo"<img src='charttopng_profit.php?Y=$ChooseMonth&chartType=$Type&Id=$TypeId'><p>&nbsp;</p>";
?>
</form>
</body>
</html>
