<?php 
/*二合一已更新*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany  资产领用记录");
$funFrom="zw_record";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|45|序号|30|资产类型|100|设备名称-型号|210|服务编号|100|现使用情况|180|使用状态|60| 原领用人|70|交接日期|70|现领用人|70|操作|70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,37,34,41";
//步骤3：
include "../model/subprogram/read_model_3.php";

//表示是那个公司的，在Companys_group中，7表示研砼，5表示鼠宝，3表示皮套。*****************************************
//$cSigntmp=$cSign_XY==""?"7":$cSign_XY;  //可以把$cSign放在登录时就加载,这样就通用
$cSigntmp=$_SESSION["Login_cSign"];
$cSignSTR=" AND D.cSign=$cSigntmp ";
$httpstr="";  //指向所在地，如图片，要指到原来的地方显示才行

if($From!="slist"){
	$SearchRows ="  AND O.Id IN (1,2,6)";
		echo"<select name='OMTypeId' id='OMTypeId' onchange='zhtj(this.name)'>";
        echo"<option value='' selected>--主类--</option>";//手机，相机，电脑
	$checkType= mysql_query("SELECT O.Id,O.Name FROM $DataPublic.oa1_fixedmaintype  O WHERE O.Estate=1 $SearchRows ORDER BY O.Letter",$link_id);
	if($TypeRow = mysql_fetch_array($checkType)){			
		do{
			$thisTypeId=$TypeRow["Id"];
			$thisName=$TypeRow["Name"];
                        //$TypeId=$TypeId==""?$thisTypeId:$TypeId;
                        if ($thisTypeId==$OMTypeId){
			              echo"<option value='$thisTypeId' selected>$thisName</option>";
                           $SearchRows.=" AND O.Id='$OMTypeId'";
                        }else{
                           echo"<option value='$thisTypeId'>$thisName</option>"; 
                        }
			}while ($TypeRow = mysql_fetch_array($checkType));
		}	
		echo"</select>&nbsp;&nbsp;";
	    echo"<select name='TypeId' id='TypeId' onchange='zhtj(this.name)'>";
        echo"<option value='' selected>--细类--</option>";
	    $checkType= mysql_query("SELECT T.Id,T.Name,T.MainTypeID FROM $DataPublic.oa2_fixedsubtype  T
							 LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
							 WHERE 1 $SearchRows AND T.Estate=1   ORDER BY T.Letter",$link_id);
	
	if($TypeRow = mysql_fetch_array($checkType)){			
		do{
			$SubTypeId=$TypeRow["Id"];
			$SubName=$TypeRow["Name"];
			$thisMainTypeID=$TypeRow["MainTypeID"];
                        //$TypeId=$TypeId==""?$thisTypeId:$TypeId;
                        if ($SubTypeId==$TypeId){
			   echo"<option value='$SubTypeId' selected>$SubName</option>";
                           $SearchRows=" AND D.TypeId='$TypeId'";
                        }else{
                           echo"<option value='$SubTypeId'>$SubName</option>"; 
                        }
			}while ($TypeRow = mysql_fetch_array($checkType));
		}	
		echo"</select>&nbsp;&nbsp;";		

	echo"<select name='BranchId' id='BranchId' onchange='zhtj(this.name)'>";
        echo"<option value='' selected>--使用部门--</option>";
	$checkType= mysql_query("SELECT Id,Name FROM $DataPublic.branchdata WHERE Estate=1 ORDER BY Id",$link_id);
	
	if($TypeRow = mysql_fetch_array($checkType)){			
		do{
			  $BranchIdTypeId=$TypeRow["Id"];
			  $BranchIdName=$TypeRow["Name"];
              if ($BranchIdTypeId==$BranchId){
			                echo"<option value='$BranchIdTypeId' selected>$BranchIdName</option>";
                            $SearchRows.=" AND D.BranchId='$BranchId'";
                          }
                   else{
                           echo"<option value='$BranchIdTypeId'>$BranchIdName</option>"; 
                         }
			  }while ($TypeRow = mysql_fetch_array($checkType));
		 }	
		echo"</select>&nbsp;&nbsp;";		

			  //选择公司名称
     $cSignTB="D";$SelectFrom=1;
     $EstateChoose="9";//给杰藤用显示杰藤公司
     include "../model/subselect/cSign.php";
    //****************领用人
    /* $UserSql="SELECT  M.Number,M.Name
     FROM $DataPublic.fixed_userdata U
     LEFT JOIN $DataPublic.staffmain M ON M.Number=U.User
     LEFT JOIN $DataPublic.fixed_assetsdata D  ON D.Id=U.Mid
     LEFT JOIN $DataPublic.companys_group  C ON C.cSign=D.cSign
     LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
     LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
     WHERE 1 AND D.Estate=1 $SearchRows  AND M.Number IS NOT NULL GROUP BY  M.Number ";
     $UserResult=mysql_query($UserSql,$link_id);
     if($UserRow=mysql_fetch_array($UserResult)){
	     echo"<select name='Number' id='Number' onchange='zhtj(this.name)'>";
         echo"<option value='' selected>--领用人--</option>";
         do{
            $thisNumber=$UserRow["Number"];
			$thisName=$UserRow["Name"];
            //$Number=$Number==""?$thisNumber:$Number;
                if ($Number==$thisNumber){
			              echo"<option value='$thisNumber' selected>$thisName</option>";
                           $SearchRows.=" AND U.User='$thisNumber'";
                        }
                 else{
                           echo"<option value='$thisNumber'>$thisName</option>"; 
                        }

            }while($UserRow=mysql_fetch_array($UserResult));
		echo"</select>&nbsp;&nbsp;";
      }*/
}
        
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
include "../model/subprogram/Getstaffname.php";

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT D.Id,D.CpName,C.CShortName,C.cSign,D.BranchId,K.CShortName as BuyCompany,D.BuycSign,D.Buyer,D.Model,D.SSNumber,D.BuyDate,D.MTCycle,D.Estate,D.Warranty,D.Attached,T.Name AS TypeName,D.Remark,D.Date,D.Operator,D.Locks,S.Id AS CId,D.CompanyId,S.Forshort,D.Operator 
FROM $DataPublic.fixed_assetsdata D 
LEFT JOIN $DataPublic.companys_group  C ON C.cSign=D.cSign
LEFT JOIN $DataPublic.companys_group  K ON K.cSign=D.BuycSign
LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
LEFT JOIN $DataPublic.dealerdata S ON S.CompanyId=D.CompanyId
WHERE 1 $SearchRows  ORDER BY FIELD(D.Estate, 1,3,0),D.TypeId";
//echo $mySql;
//LEFT JOIN $DataPublic.fixed_userdata U ON U.Mid=D.Id
//WHERE 1 $SearchRows $cSignSTR ORDER BY D.CpName DESC";
//echo "$mySql <Br>";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$CompanyData="$DataPublic.dealerdata";
    $TempK=0;
	do{
		$m=1;
		$Id=$myRow["Id"];
		$BoxCode='8'.str_pad($Id,11,"0",STR_PAD_LEFT);  //条码不够位前边补800000****
		$BoxCode=GetCode($BoxCode,13,"0",1);  //获得条码: include "subprogram/Getstaffname.php";	
		$cSign=$myRow["cSign"];
		//获取使用人员,维修人员表
		$User="&nbsp;";
		$UserDate="";
		$User_Date=GetSName_Date($Id,'fixed_userdata','1',$DataIn,$DataPublic,$link_id);
		//if(($cSign==$cSigntmp) &&($User_Date!="") ){
         if($User_Date!="" ){
			$Temp_User=explode('|',$User_Date);	
			$User=$Temp_User[0];
			$UserDate=$Temp_User[1];
			$SNumber=$Temp_User[2]; 
			$SEstate=$Temp_User[3];//状态
		}
		$User="<span  title='$UserDate'>$User</span>";
		$CpName=$myRow["CpName"];
		$TypeName=$myRow["TypeName"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"]==""?"&nbsp;":$myRow["Forshort"];
		$Idc=anmaIn($CompanyData,$SinkOrder,$motherSTR);
		$CId=$myRow["CId"];
		$Ids=anmaIn($CId,$SinkOrder,$motherSTR);
		/*$BranchId=$myRow["BranchId"];
		$Branch="&nbsp";
		if(($cSign==$cSigntmp)){		
			$Branch=GetBranchName($BranchId,$DataIn,$link_id);
		}*/	
		$BuyCompany=$myRow["BuyCompany"];
		$Buyer=$myRow["Buyer"];
		$Model=$myRow["Model"];
		$IDdSTR=anmaIn($Id,$SinkOrder,$motherSTR);
		$BoxCode="<a href='fixed_assets_view.php?d=$IDdSTR' target='_blank'>$BoxCode</a>";        
        $SSNumber=$myRow["SSNumber"]==""?"&nbsp;":$myRow["SSNumber"];
		$BuyDate=$myRow["BuyDate"];
		$tempBuyDate=$BuyDate;
		$FixedIDSTR=anmaIn($Id,$SinkOrder,$motherSTR);	
		$CpNameStr="<a href='fixed_maintain_view.php?f=$FixedIDSTR' target='_blank' title='点击进入保养详情'>$CpName</a>";	
		
		$Date=$myRow["Date"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		$Locks=$myRow["Locks"];

		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB' title='报废'>×报废</div>";
				break;
			case 1:
				$Estate="<div class='greenB' title='使用中'>√使用中</div>";
				break;
			case 3:
				$Estate="<div class='yellowB' title='闲置'>√闲置</div>";
				break;
		    case 5:
		        $Estate="<div class='yellowB' title='寄回客户'>×寄回客户</div>";
				break;
			}		
			 //获得附件
                 $Attached1="&nbsp;";
				 $WorkFile="&nbsp;";
                 $Dir="download/fixedFile/";
                 $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);	
                 $AttachedResult = mysql_query("SELECT Id,FileName,Type  FROM $DataPublic.fixed_file WHERE Mid=$Id order by Type",$link_id); 
                 while ($AttachedRow=mysql_fetch_array($AttachedResult)){
                     $FileName=$AttachedRow["FileName"];
                     $FileName=anmaIn($FileName,$SinkOrder,$motherSTR);	
                     switch($AttachedRow["Type"]){
                       case 1:
                           $Model="<span onClick='OpenOrLoad(\"$Dir\",\"$FileName\")' style='CURSOR: pointer;color:#F00;'>$Model</span>"; break;
                       case 2: 
                          $Attached1="<a href=\"../admin/openorload.php?d=$Dir&f=$FileName&Type=&Action=6\" target=\"download\">view</a>";break;
                       case 3:
                           $Warranty="<span onClick='OpenOrLoad(\"$Dir\",\"$FileName\")' style='CURSOR: pointer;color:#F00;'>$Warranty</span>"; break;
                       case 4: 
                          $WorkFile="<a href=\"../admin/openorload.php?d=$Dir&f=$FileName&Type=&Action=6\" target=\"download\">view</a>";break;			   
                     }
                 }
           //交接前领用人
           $k=0;
           $LastGetName="";
           $tempresult = array(); // 按时间排序，去掉重复记录
           $tempNumber=array();
           $LastGetReuslt=mysql_query("SELECT Y.Name,X.SDate ,Y.Number FROM $DataPublic.staffmain  Y
								Left join  $DataPublic.fixed_userdata X ON X.User=Y.Number
								WHERE X.Mid=$Id AND X.UserType=1  AND X.cSign=$cSign GROUP BY X.User ORDER BY X.SDate ASC ",$link_id);
		   if( $LastGetRow=mysql_fetch_array($LastGetReuslt)){
			   do{
			       $LName=$LastGetRow["Name"];
			       $LNumber=$LastGetRow["Number"];
			       $tempresult [$k]= $LName;
			        $tempNumber[$k]= $LNumber;
			      $k++;
                   } while( $LastGetRow=mysql_fetch_array($LastGetReuslt));
		    }		
		  // print_r($tempNumber );
		  if($k<=1)$LastGetName="原始记录";
		  else{//找到上一次领用的记录
			  $k=$k-2;
			  $LastGetName=$tempresult [$k];
			  $LastGetNumber=$tempNumber[$k];
		   }
		   
		   //*********************************
		   $LockRemark="";
		   	     if($SEstate==1){ 
	               if($k==1){
	                            $OnclikStr="<input type='button' id='GetRecord' name='GetRecord' onclick='GetRecords(\"$Id\")' value='确  认'>";
	                            }
	                  else{
	                             if($LastGetNumber==$Login_P_Number){
		                                        $OnclikStr="<span class='redB'>交接中 ...</span>";
		                                       $LockRemark=" 等待交接 .....";
	                                          }
	                              else{
		                                      $OnclikStr="<input type='button' id='GetRecord' name='GetRecord' onclick='GetRecords(\"$Id\")' value='确  认'>";
		                                   }
	                          }
	              }
	     else{
		         $OnclikStr="&nbsp;";
	          }
        if($cSign==9){//给杰藤用的这些不用显示
                $LastGetName="&nbsp;";$User="&nbsp;";$OnclikStr="&nbsp;";$UserDate="&nbsp;";
                 $LockRemark="给杰滕使用,已报废";
              }
	 // if($Login_P_Number!='10006'){//英姿看所有的记录
	    //   if(($SNumber==$Login_P_Number|| $LastGetNumber==$Login_P_Number && $SEstate==1 ) ){//自己的领用记录＋刚交接等待接收者确认的记录
		        $ValueArray=array(
			        //  array(0=>$CpNameStr, 	1=>"align='center'"),
			          array(0=>$TypeName,		1=>"align='left'"),		
			          array(0=>$Model),
		              array(0=>$SSNumber),
			          array(0=>$Remark),
			         array(0=>$Estate,1=>"align='center'") ,
			         array(0=>$LastGetName,1=>"align='center'") ,
			         array(0=>$UserDate,1=>"align='center'"),
			         array(0=>$User,1=>"align='center'"),
			         array(0=>$OnclikStr,1=>"align='center'"));
		              $checkidValue=$Id;
		              include "../model/subprogram/read_model_6.php";
		              $TempK++;
	         	   // }
	         	//}
	/*	else{
		         $ValueArray=array(
			          array(0=>$CpNameStr, 	1=>"align='center'"),
			          array(0=>$TypeName,		1=>"align='center'"),		
			          array(0=>$Model),
			          array(0=>$SSNumber),
			          array(0=>$Remark),
			         array(0=>$Estate,1=>"align='center'") ,
			         array(0=>$LastGetName,1=>"align='center'") ,
			         array(0=>$UserDate,1=>"align='center'"),
			         array(0=>$User,1=>"align='center'"),
			         array(0=>$OnclikStr,1=>"align='center'") );
			       $checkidValue=$Id;
		            include "../model/subprogram/read_model_6.php";
		            $TempK++;
		        }*/
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
  if($TempK==0 && $TempK !=""){noRowInfo($tableWidth);}
//步骤7：
echo '</div>';
//$myResult = mysql_query($mySql,$link_id);
//$RecordToTal= mysql_num_rows($myResult);
$RecordToTal=$TempK;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script language="JavaScript" type="text/JavaScript">
function toTempValue(thisE){
	document.form1.TempValue.value=thisE.value;
	}
function Indepot(thisE,SumQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue>SumQty) || thisValue==0){
			alert("不在允许值的范围！");
			thisE.value=oldValue;
			return false;
			}
		}
	}
	function zhtj(obj){
	switch(obj){
		case "OMTypeId"://改变采购
			//document.forms["form1"].elements["PayMode"].value="";
			var TypeId= document.getElementById("TypeId");
			if(TypeId!=null){
				TypeId.value="";
				}
       
		break;
		
		}
	document.form1.action="zw_record_read.php";
	document.form1.submit();
}	
function GetRecords(TempId){
	//alert(TempId);
	var url="zw_record_ajax.php?Id="+TempId+"&Action=1";
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	   ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			    var BackData=ajax.responseText;
			          if(BackData=="Y"){
				          alert("交接成功！") ;
				         document.form1.action="zw_record_read.php";
	                     document.form1.submit();
			          }
			          else{
				          alert("交接失败");
			          }
	           }
	     	}
　	ajax.send(null);
	}

</script>