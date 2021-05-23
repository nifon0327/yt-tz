<?php 
//步骤1 $DataIn.cw16_modelfee  二合一已更新//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
$Estate=$Estate==""?1:$Estate;
ChangeWtitle("$SubCompany 模具费用退回列表");
$funFrom="modelfee_th";
$Th_Col="选项|60|序号|40|模具项目|280|最低配件数量|80|配件已采购数|80|退回金额|80|供应商|60|备注|250|凭证|40|状态|40|更新日期|65|请款人|50";
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="3,4,14";
if($Estate==1 && $From!="slist"){
	//$otherAction="<span onclick='modelfeeQk()' $onClickCSS>请款</span>";
	}
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$monthResult = mysql_query("SELECT M.Date FROM $DataIn.cw16_modelfee  M  
	        WHERE 1 $SearchRows group by DATE_FORMAT(M.Date,'%Y-%m') order by M.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue=="")$FirstValue=$dateValue;
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($dateValue==$chooseMonth){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";	
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(M.Date,'%Y-%m')='$FirstValue'";
			}			
		}
		$SearchRows.=$PEADate;	
	
	switch($Estate){
		case "0":			$EstateSTR0="selected";			break;
		case "1":			$EstateSTR1="selected";			break;
		case "2":			$EstateSTR2="selected";			break;
		case "3":			$EstateSTR3="selected";			break;
		}	
	echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>审核中</option>
	<option value='3' $EstateSTR3>审核通过</option>
    <option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	if ($Estate!="")$SearchRows.=" and M.Estate='$Estate'";
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Id,M.Mid,M.Moq,M.ItemName ,M.OutAmount ,M.Remark,M.Operator,M.Date,M.Locks,M.Estate,S.Provider  
FROM $DataIn.cw16_modelfee  M 
LEFT JOIN cwdyfsheet S  ON M.Mid=S.Id
WHERE 1 $SearchRows";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
//echo $mySql;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Moq=$myRow["Moq"];
		$ItemName=$myRow["ItemName"];
		$OutAmount=$myRow["OutAmount"];
		//$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		$Estate=$myRow["Estate"];		
       $Provider =$myRow["Provider"];	
		switch($Estate){
				case "1":
					$Estate="<div align='center' class='redB' title='未处理'>×</div>";
					$LockRemark="";
					break;
				case "2":
					$Estate="<div align='center' class='yellowB' title='审核中...'>×.</div>";
					$LockRemark="记录已经请款,锁定操作!";
					break;
				case "3":
					$Estate="<div align='center' class='yellowB' title='审核通过'>√.</div>";
                    $LockRemark="审核通过,等待结付!";
               case "0":
					$Estate="<div align='center' class='greenB' title='已结付'>√.</div>";
                    $LockRemark="已结付!";
					break;
				}
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/modelfee/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="M".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
			//配件已采购数量
			$cg_stuffQty=0;
			$cg_stuffResult=mysql_query("SELECT MAX(Qty) AS sumQty FROM (
                                            SELECT SUM( G.AddQty + G.FactualQty ) AS Qty
                                             FROM  $DataIn.cg1_stocksheet G
                                            LEFT JOIN $DataIn.modelfeestuff M ON M.StuffId = G.StuffId
                                            WHERE M.mId ='$Id'  AND (G.FactualQty >0  OR G.AddQty >0)GROUP BY M.StuffId
                                            ) A",$link_id);
                $cg_stuffQty=mysql_result($cg_stuffResult, 0, "sumQty");
			     $cg_stuffQty=$cg_stuffQty==""?0:$cg_stuffQty;
			     $TempcgstuffQty=$cg_stuffQty;
			      if($cg_stuffQty>=$Moq)$cg_stuffQty="<span class='greenB'>$cg_stuffQty</span>";
			      else $cg_stuffQty="<span class='redB'>$cg_stuffQty</span>";
			     
				//动态读取
		$showPurchaseorder="<img onClick='Showstuff(stuffList$i,showtable$i,stuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' title='显示相关配件.' width='13' height='13' style='CURSOR: pointer'><input  type='hidden' id='Moq$i' name='Moq$i' value='$Moq'><input  type='hidden' id='cgstuffQty$i' name='cgstuffQty$i' value='$TempcgstuffQty'>";
		$stufflistTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='stuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showstuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$ItemName),		
			array(0=>$Moq,1=>"align='right'"),
			array(0=>$cg_stuffQty,1=>"align='right'"),
			array(0=>$OutAmount ,1=>"align='right'"),
            array(0=>$Provider,3=>"..."),	
			array(0=>$Remark)	,
			array(0=>$Bill,1=>"align='center'"),			
			array(0=>$Estate,1=>"align='center'"),	
			array(0=>$Date,1=>"align='center'"),		
			array(0=>$Operator,1=>"align='center'")	
			);
		   $checkidValue=$Id;
		   include "../model/subprogram/read_model_6.php";
		   echo $stufflistTB;
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
<script>
function modelfeeQk(){
   var Message="";
	var upId="";
	var moq="";
	var cgstuffQty;
	var choosedRow=0;
	var  j=1;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
				alert(i);
					if(e.checked){
						     if(upId=="")upId=e.value;
						      else  upId=upId+"^^"+e.value;
						      choosedRow=choosedRow+1;
						      // cgstuffQty=document.getElementById("cgstuffQty"+j).value;
						     // alert(cgstuffQty);
					      } 
					  } 
				}
			}
	//如果没有选记录
	       if(choosedRow==0){
			       Message="该操作要求选定记录！";
			     }
	     	if(Message!=""){
		          alert(Message);return false;
		          }
		else{
		         var url="modelfee_th_updated.php?IdArray="+upId+"&ActionId=14";
			     document.form1.action=url;
		          document.form1.submit();
		       }
    }
function Showstuff(e,f,stuff_Rows,sId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		stuff_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		stuff_Rows.myProperty=false;
		//动态加入采购明细
		if(sId!=""){			
			var url="modelfee_th_ajax.php?Id="+sId+"&RowId="+RowId; 
		　	var show=eval("showstuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange=function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}
</script>




