<?php 
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|40|类别|110|收款日期|80|收款单号|100|收款金额|80|币种|50|收款备注|380|状态|50|操作人|70";
$ColsNumber=8;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//非必选,过滤条件
$Parameter.=",Bid,$Bid";
include "../model/subprogram/s1_model_3.php";
echo $CencalSstr;

//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.getmoneyNO, S.Amount, C.Symbol AS Currency, S.payDate, S.Remark, S.Estate, S.Locks, S.Operator,T.Name AS TypeName
 	FROM $DataIn.cw4_otherinsheet S 
   LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=S.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $sSearch   $SearchRows AND S.Id NOT IN (SELECT OtherId FROM $DataIn.hzqksheet WHERE 1 AND OtherId!=0)  order by Id DESC";//客户在使用中，记录可用中
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/otherin/",$SinkOrder,$motherSTR);		
	do{
			$m=1;
		    $Id=$myRow["Id"];
           $getmoneyNO=$myRow["getmoneyNO"];
			switch($Action){
		 	         case "1"://选择产品以便进行操作
			    	$Bdata=$Id."^^".$getmoneyNO;
				    break;
				}		
		   $Amount=$myRow["Amount"];
		   $Currency=$myRow["Currency"];	
		   $payDate=$myRow["payDate"];
		  $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];		
		  $Estate=$myRow["Estate"];		
		  $TypeName=$myRow["TypeName"];	
          switch($Estate){
              case "0":
		                $Estate="<div class='greenB'>已结付</div>";
                 break;
              case "3":
		                $Estate="<div class='greenB'>未结付</div>";
                 break;
              case "2":
		                $Estate="<div class='blueB'>审核中...</div>";
                 break;

            }
		$Locks=$myRow["Locks"];	
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";   
		$f1=anmaIn($getmoneyNO,$SinkOrder,$motherSTR);
		$getmoneyNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$getmoneyNO</a>";
		
			$ValueArray=array(
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$payDate,1=>"align='center'"),
			array(0=>$getmoneyNO,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Bdata;
      $Keys=31;
		include "../model/subprogram/s1_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>