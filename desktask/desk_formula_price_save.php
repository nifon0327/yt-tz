<?php   
//电信---yang 20120801
include "../model/modelhead.php";
$Log_Item="售价表数据";			
$funFrom="desk_formula_price";
$fromWebPage=$funFrom;
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$Date=date("Y-m-d");
$OperationResult="Y";
$StuffName0=$_POST['StuffName0'];
$SalePrice=$_POST['SalePrice'];
$count=count($StuffName0);
$TableMain="$DataPublic.calcuate_pricemain";
$TableSheet="$DataPublic.calcuate_pricesheet";

$ProductResult=mysql_query("SELECT Id,ProductName From $TableMain WHERE ProductName='$ProductName'",$link_id);
if($ProductRow=mysql_fetch_array($ProductResult)){
       $Mid=$ProductRow["Id"];
       $DelMain=mysql_query("DELETE FROM $TableMain WHERE Id='$Mid'",$link_id);//删除主表
       $delSheet=mysql_query("DELETE FROM $TableSheet WHERE Mid='$Mid'",$link_id);//删除从表
   }
$In_main="INSERT INTO $TableMain(Id, ProductName,UsdPrice, Date, Operator)
          VALUES(NULL,'$ProductName','$SalePrice','$Date','$Operator')";
$Result1=@mysql_query($In_main,$link_id);
$Mid=mysql_insert_id();
$j=1;
if($Result1){
      $Log.="售价主表calcuate_pricemain添加成功!"."<br>";
	  for($i=0;$i<$count;$i++){
	    if($StuffName0[$i]!=""){
		  if($i==0||$i==1){$TypeId=2;}else{$TypeId=1;}
		  $Value="Value".$i;
		  $Value=$_POST[$Value];
		  $Price=$Value[0];
		  $Number=$Value[1];
		  $In_sheet="INSERT INTO $TableSheet(Id, Mid, TypeId, StuffName, Price,                  Number) VALUES(NULL,'$Mid','$TypeId','$StuffName0[$i]','$Price','$Number')";
		            $Result2=@mysql_query($In_sheet,$link_id);
		            if($Result2){
		                         $Log.=$j."---售价从表calcuate_pricesheet添加成功."."<br>";
			                    }
		                    else{
		                         $log.=$j."<span class='redB'>---售价从表calcuate_pricesheet添加失败</span>"."<br>";
			                     $OperationResult="N";
			                    }
				$j++;
		  }//end if($StuffName0!="")
		}//end for($i=0;$i<=$count;$i++)
	}
else{
	 $Log.="<span class='redB'>售价主表calcuate_pricemain添加失败!</span>"."<br>";
     $OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
