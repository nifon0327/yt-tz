<?php 
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//$From=$From==""?"read":$From;
$ColsNumber=10;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 其它收入");
$funFrom="cw_otherin";
$nowWebPage=$funFrom."_m";
$Th_Col="选项|40|序号|40|类别|110|备注|500|金额|80|货币|50|凭证|60|日期|80|状态|50|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 200;
$sumCols=4;
$ActioToS="17";		
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	       $SearchRows="";       
     //类别
      $TypeResult = mysql_query("SELECT T.Id,T.Name FROM cw4_otherin I 
     LEFT JOIN $DataPublic.cw4_otherintype  T ON T.Id=I.TypeId
      WHERE T.Estate=1 AND I.Estate=1 $SearchRows  GROUP BY  T.Id",$link_id);
		if($TypeRow = mysql_fetch_array($TypeResult)){
                    echo"<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
              echo"<option value=''>--全部--</option>";
                    do{
                        $theTypeId=$TypeRow["Id"];
                        $Name=$TypeRow["Name"];
                        if ($TypeId==$theTypeId)
                        {
                            echo"<option value='$theTypeId' selected>$Name</option>";
                            $SearchRows.=" AND I.TypeId='$theTypeId'";
                        }
                        else{
                            echo"<option value='$theTypeId'>$Name</option>"; 
                        }
                       
                      }while($TypeRow = mysql_fetch_array($TypeResult));
                     echo"</select>&nbsp;";
                }       
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT I.Id,I.Amount,I.Remark,I.Bill,I.Date,I.Estate,I.Locks,I.Operator,C.Symbol,T.Name AS TypeName,B.Title
FROM $DataIn.cw4_otherin  I
LEFT JOIN $DataPublic.currencydata C ON C.Id=I.Currency 
LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=I.TypeId
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=I.BankId
WHERE 1 $SearchRows AND I.Estate=1 ORDER BY I.Estate DESC,I.Date ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$TypeName=$myRow["TypeName"];
		$BankName=$myRow["Title"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
       switch($myRow["Estate"]){
                    case 1:
                        $Estate="<div class='redB' >未审核</div>";
                        break;
                    case 3:
                        $Estate="<div class='blueB' >已审核</div>";
                        break;
                    case 0:
                        $Estate="<div class='greenB' >已收款</div>";
                        $LockRemark="已处理,强制锁定!.";
                        break;
                }
          /*   switch($myRow["oEstate"]){
                    case 1:
                        $oEstate="<div class='redB' >未处理</div>";
                        break;
                    case 3:
                        $oEstate="<div class='blueB' >审核中</div>";
                        break;
                    case 0:
                        $oEstate="<div class='greenB' >已处理</div>";
                        break;
                }*/
                
		$Locks=$myRow["Locks"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/otherin/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="O".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
     
			
		$ValueArray=array(
		//	array(0=>$BankName),
			array(0=>$TypeName,	1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Symbol,	1=>"align='center'"),
			array(0=>$Bill, 1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
		//	array(0=>$oEstate, 1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>