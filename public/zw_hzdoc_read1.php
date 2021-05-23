<?php
//需处理参数
ChangeWtitle("$SubCompany 行政文件");
$Th_Col="选项|45|序号|45|所属公司|80|分类名称|150|行政资料说明|400|附件|60|日期|80|失效日期|80|排序|60|操作员|60";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	//echo "Hereewere! $OMTypeId  : $TypeId";
	$SearchRows ="";
      //选择公司名称
      $SelectFrom=1;
       $cSignTB="D";
      $SharingShow="Y";
       include "../model/subselect/cSign.php";
     /* $company=$company==""?$Login_cSign:$company;
      $companyStr="company".$company;
      $$companyStr="selected";
	  echo"<select name='company' id='company' onchange='RefreshPage(\"$nowWebPage\")'>";
      echo "<option value='7' $company7>研砼</option>";
      echo "<option value='3' $company3>鼠宝</option>";
	  echo"</select>&nbsp;";
      if($company==7)$DataInchoose="d7";
      if($company==3)$DataInchoose="d3";*/
	//类型
    $TypeResult = mysql_query("SELECT T.Id,T.Name
                            FROM  $DataPublic.zw2_hzdoctype T   WHERE 1 GROUP BY T.Id",$link_id);
	if($TypeRow = mysql_fetch_array($TypeResult)) {
		echo"<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
       echo"<option value='' selected>全部</option>";	
		do{			
              $thisTypeId=$TypeRow["Id"];
              $thisName=$TypeRow["Name"];
               //$TypeId=$TypeId==""?$thisTypeId:$TypeId;
			  if($TypeId==$thisTypeId){
				     echo"<option value='$thisTypeId' selected>$thisName</option>";
				     $SearchRows.=" and T.Id='$thisTypeId' ";
				     }
			  else{
				      echo"<option value='$thisTypeId'>$thisName</option>";					
				    }
			}while ($TypeRow = mysql_fetch_array($TypeResult));
		echo"</select>&nbsp;";
		}
	$orderby="";	
	if($TypeId==9){
		$orderby=" D.cSign desc,D.Date desc, ";
	}
		
	$Today=date("Y-m-d");
	$WarningDate=date("Y-m-d",strtotime($Today."+ 31 day")); //一个月警告
	echo"<select name='SelectDate' id='SelectDate' onchange='zhtj(this.name)'>";
       echo"<option value='' >全部</option>";	
		if ($SelectDate==1){
			   echo"<option value='1' selected>未过期</option>";
			   $SearchRows.=" AND D.EndDate>'$WarningDate'";   
		      }
		else {
			     echo"<option value='1' >未过期</option>";
		        }
		if ($SelectDate==2){
			    echo"<option value='2' selected>过期中...</option>";
			    $SearchRows.=" AND D.EndDate<='$WarningDate'";
		       }
		else{
			    echo"<option value='2' >过期中...</option>";
		     }	
	echo"</select>&nbsp;&nbsp;";
}

$aTypeIds = array(1,2,20,23,35,42);
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.Caption,D.Attached,D.Date,D.EndDate,D.Locks,T.Name AS Type,T.Id as aTypeId ,T.SubName,D.Operator ,D.SortId,D.cSign 
FROM $DataIn.zw2_hzdoc D
LEFT JOIN $DataPublic.zw2_hzdoctype T ON T.Id=D.TypeId 
WHERE 1 $SearchRows ORDER BY $orderby T.SortId ASC,D.SortId ASC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Caption=$myRow["Caption"];
		$Attached=$myRow["Attached"];
		$aTypeId = $myRow["aTypeId"];
		//echo "$Attached";
		$d=anmaIn("download/hzdoc/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			
			//$Attached="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">View</a>";
			/*
				$aPdf = "../download/hzdoc/$Attached";
			$aJpg = "../download/hzdocjpg/$Id.jpg";
			$aJpg0 = "../download/hzdocjpg/$Id".'-0'.".jpg";
				if ($Login_P_Number==11965 && in_array($aTypeId, $aTypeIds) && !file_exists($aJpg) && !file_exists($aJpg0)) {
exec("$execImageMagick -colorspace sRGB -transparent white -trim $aPdf $aJpg");
			}
			*/
			}
		else{
			$Attached="-";
			}
		$Date=$myRow["Date"];
		
		$Today=date("Y-m-d");
		$EndDate=$myRow["EndDate"];
		$WarningDate=date("Y-m-d",strtotime($EndDate."- 31 day")); //一个月警告
		$unLimited = false;
		if ( $EndDate=="0000-00-00" ) {
			$unLimited = true;
			$EndDate = "无限期";
		}
		if($WarningDate<$Today && $unLimited==false){ //一个月警告
			$EndDate="<div class='redB' title='警告'>$EndDate</div>";
		}

		$cSignFrom=$myRow["cSign"];
		include "../model/subselect/cSign.php";
		
       // $cSign=$myRow["cSign"]==7?"研砼":"<span class='yellowB'>皮套</span>";
		/*
		else {
			if ($EndDate<=$Today){
				$EndDate="<div class='redB' title='警告'>$EndDate</div>";
			}
		}
		*/
		$Type=$myRow["Type"];
		$SubName=$myRow["SubName"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $SortId=$myRow["SortId"]==999?"":$myRow["SortId"];
        $Sortstr="<input type='text' id='sortId' name='sortId' size='6' value='$SortId' onblur='TypeSort(this,$Id,2,\"$DataIn\")'>";

		$ValueArray=array(
			array(0=>$cSign,1=>"align='center'"),
			array(0=>$Type),
			array(0=>$Caption),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$EndDate,1=>"align='center'"),	
			array(0=>$Sortstr,1=>"align='center'"),		
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
