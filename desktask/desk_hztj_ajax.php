<?php   
/*电信---yang 20120801
$DataIn.cwdyfsheet
分开已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=1060;
$theMonth=$_GET["Month"];
$theTypeId=$_GET["TypeId"];
$theChoose=" AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND M.Estate IN (0,3) ORDER BY M.Date";
echo"<br><table width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr class=''>
		<td width='30'  align='center' height='20'>序号</td>
		<td width='80'  align='center'>费用名称</td>
		<td width='250' align='center'>费用说明</td>
		<td width='50'  align='center'>请款人</td>
		<td width='50'  align='center'>请款日期</td>
		<td width='40'  align='center'>凭证</td>
		<td width='50'  align='center'>金额</td>
		</tr>";
switch($theTypeId){
   case 2://IT/行政/业务/采购/开发/QC人工费
   $mySql="SELECT M.Id,'IT/行政/业务/采购/开发/QC人工费用' AS Description,(M.Amount+IFNULL(S.Amount,0)) AS Amount,
           M.Month AS Date,'' AS Bill,M.Operator,'人工费' AS ItemName 
           FROM $DataIn.cwxzsheet M 
           LEFT JOIN $DataIn.cw11_jjsheet S ON S.Number=M.Number  AND S.Month=M.Month AND S.Estate IN (0,3) 
	       LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
	       WHERE 1 AND B.TypeId=1 AND M.Month='$theMonth' AND M.Estate IN (0,3) ORDER BY M.Month";
   
  break;   
   case 3://IT/行政/业务/采购/开发/QC加班费
   $mySql="SELECT M.Id,'IT/行政/业务/采购/开发/QC加班费用' AS Description,M.Amount AS Amount,
           M.Date,'' AS Bill,M.Number AS Operator,'加班费' AS ItemName 
           FROM $DataIn.hdjbsheet M 
	       LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
	       WHERE 1 AND B.TypeId=1 AND M.Month='$theMonth' AND M.Estate IN (0,3) ORDER BY M.Month";
   break; 
   case 4://IT/行政/业务/采购/开发/QC社保
   $mySql="SELECT M.Id,'IT/行政/业务/采购/开发/QC社保费用' AS Description,(M.mAmount+M.cAmount) AS Amount,
           M.Date,'' AS Bill,M.Number AS Operator,'社保费' AS ItemName 
           FROM $DataIn.sbpaysheet M 
	       LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
	       WHERE 1 AND B.TypeId=1 AND M.Month='$theMonth' AND M.Estate IN (0,3) ORDER BY M.Month";
   break;  
   case 5://总务采购费用
   $mySql="SELECT M.Id,M.Remark AS Description,(M.Price*M.Qty) AS Amount,M.qkDate AS Date,M.Bill,M.Operator,
           T.TypeName AS ItemName 
           FROM $DataIn.zw3_purchases M 
		   LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=M.TypeId
	       WHERE 1 AND DATE_FORMAT(M.qkDate,'%Y-%m')='$theMonth' AND M.Estate IN (0,3) 
		   AND T.TypeId NOT IN(1,2,3,4,5) ORDER BY M.qkDate";
		   //echo $mySql;
   break;
   case 6://其他总务费用610
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'其他总务费用' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='610' $theChoose";
   break;
   case 7://厂房租金601
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'厂房租金' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='601' $theChoose";
   break;
   case 8://厂房水电费602
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'厂房水电费' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='602' $theChoose";
   break;
   case 9://厂区管理费603
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'厂房管理费' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='603' $theChoose";
   break;
   case 11://电话费607
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'电话费' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='607' $theChoose";
   break;
   case 12://办公耗材608
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'办公耗材费' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='608' $theChoose";
   break;
   case 13://车辆支出费用609
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'车辆支出费用' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='609' $theChoose";
   break;
   case 14://差旅费613
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'差旅费' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='613' $theChoose";
   break;
   case 16://交际费615
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'交际费' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='615' $theChoose";
   break;
   case 17://银行手续费617
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'银行手续费' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='617' $theChoose";
   break;
   case 18://税款624
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'税款' AS ItemName 
           FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='624' $theChoose";
   break;
   case 19://非月结快递费618
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'非月结快递费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='618' $theChoose";
   break;
   case 20://运费632
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'运费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='632' $theChoose";
   break;
   case 21://船务快递费(月结费用)
   $mySql="SELECT M.Id,M.Remark AS Description,M.Amount,M.Date,M.ExpressNO AS Bill,M.Operator
           ,'船务快递费' AS ItemName
 	       FROM $DataIn.ch9_expsheet M 
	       WHERE 1 $theChoose";
   break;
   case 22://船务寄样费
   $mySql="SELECT M.Id,M.Remark AS Description,M.Amount,M.SendDate AS Date,M.ExpressNO AS Bill,M.Operator,
           M.Description AS ItemName
 	       FROM $DataIn.ch10_samplemail M 
	       WHERE 1 AND DATE_FORMAT(M.SendDate,'%Y-%m')='$theMonth' AND M.Estate IN (0,3) ORDER BY M.SendDate";
   break;
   case 24:///模具费627
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'模具费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='627' $theChoose";
   break;
   case 25://样品费630
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'样品费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='630' $theChoose";
   break;
   case 26: //开发费用
   $mySql="SELECT M.Id,M.Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator,D.ItemName
 	       FROM $DataIn.cwdyfsheet M 
	       LEFT JOIN $DataIn.development D ON D.ItemId=M.ItemId 
           LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency 
	       WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND M.Estate IN (0,3) ORDER BY M.Date";
   break;
   case 27://开办费用643
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'开办费用' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='643' $theChoose";
   break;
   case 28://购买手机费用649
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'购买手机费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='649' $theChoose";
   break;
   case 29://参展/广告费650
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'参展/广告费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='650' $theChoose";
   break;
   case 30://电工费用662
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'电工费费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='662' $theChoose";
   break;
   case 31://购置杂费
   $mySql="SELECT M.Id,M.Content AS Description,(M.Amount*C.Rate) AS Amount,M.Date,M.Bill,M.Operator
           ,'购置杂费' AS ItemName FROM $DataIn.hzqksheet M 
		   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
	       WHERE 1 AND M.TypeId='639' $theChoose";
   break;
}	
//echo $mySql . "</br>";
$i=1;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
     // $Id="";$ItemName="";$Description="";$Bill="";$Date="";$Operator="";$Amount=0;
	 $SumAmount=0;
	  do{
         $Id=$myRow["Id"];
		 $ItemName=$myRow["ItemName"];
		 $Description=$myRow["Description"];
		 $Amount=sprintf("%.2f",$myRow["Amount"]);
		 $Bill=$myRow["Bill"];
		 $Date=$myRow["Date"];
         $Operator=$myRow["Operator"];
		 $SumAmount+=$Amount;
		 include "../model/subprogram/staffname.php";         
		 switch($theTypeId){
         case 26:
		 if($Bill==1){
		   $Bill="DYF".$Id.".jpg";
		   $Dir=anmaIn("../download/dyf/",$SinkOrder,$motherSTR);
		   $Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
	       $Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";}
		 else{ $Bill="&nbsp;";}
		 break;
		 case 21:
		 case 22:
		 $Lading="../download/expressbill/".$Bill.".jpg";
		   if(file_exists($Lading)){
			$f2=anmaIn($Bill.".jpg",$SinkOrder,$motherSTR);
			$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$d2\",\"$f2\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		 else{ $Bill="&nbsp;";}
		 break;
		 case 5:
		 $Dir=anmaIn("../download/zwbuy/",$SinkOrder,$motherSTR);
		 if($Bill==1){
			$Bill="Z".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{$Bill="&nbsp;";}
		 break;
		 default:
		 $Dir=anmaIn("../download/cwadminicost/",$SinkOrder,$motherSTR);
		 if($Bill==1){
			$Bill="H".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			 }
		 else{$Bill="&nbsp;";}
		 break;}
		 echo"<tr bgcolor='#ECEAED'><td align='center'>$i</td>";
		 echo"<td>$ItemName</td>";
		 echo"<td>$Description</td>";
		 echo"<td align='center'>$Operator</td>";
		 echo"<td align='center'>$Date</td>";
		 echo"<td align='center'>$Bill</td>";
		 echo"<td align='right'>$Amount</td>";
		 echo"</tr>";
		 $i++;
		}while($myRow = mysql_fetch_array($myResult));
	    echo"<tr bgcolor=#EAEAEA><td align='center' colspan='6'>合 计</td>";
	    echo"<td align='right'>$SumAmount</td>";
	    echo"</tr>";
        echo"</table><br>";
	   }
else{
	  echo"<tr><td height='30' colspan='7'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr></table>";
	}
?>