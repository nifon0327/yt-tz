<?php 
/*$DataIn.电信---yang 20120801
$DataPublic.staffmain
$DataIn.stufftype
$DataIn.stuffdata
$DataIn.bps
/二合一已更新
*/
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 设置配件开发信息");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_develop";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.StuffId,S.StuffCname,S.Estate,S.DevelopState,P.GroupId,P.Number,P.dFile,P.CompanyId,
										P.Targetdate,P.Finishdate,P.Estate AS PEstate,P.Remark,P.KfRemark,M.Name,G.GroupName 
									    FROM $DataIn.stuffdata S 
										LEFT JOIN $DataIn.stuffdevelop P  ON P.StuffId=S.StuffId 
										LEFT JOIN $DataPublic.staffmain M ON M.Number=P.Number  
	                                    LEFT JOIN $DataIn.staffgroup G ON G.GroupId=P.GroupId
										WHERE S.Id='$Id' LIMIT 1",$link_id));

$StuffId=$upData["StuffId"];
$StuffCname=$upData["StuffCname"];
$Estate=$upData["Estate"];
$PEstate=$upData["PEstate"];
$DevelopState=$upData["DevelopState"];
$GroupId=$upData["GroupId"];
$DevelopNumber=$upData["Number"];
$Remark=$upData["Remark"];
$KfRemark=$upData["KfRemark"];
$GroupName=$upData["GroupName"];
$DevelopName=$upData["Name"];

$dFile=$upData["dFile"];

$CompanyId=$upData["CompanyId"];

$Targetdate=$upData["Targetdate"];
if ($Targetdate!="")
{
   $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Targetdate',1) AS TargetWeek",$link_id));
   $TargetWeek=substr($dateResult["TargetWeek"],4,2) . "周";
}

 if ($GroupId=="" || $DevelopNumber==""){
	 $oldResult= mysql_fetch_array(mysql_query("SELECT T.DevelopGroupId,T.DevelopNumber,G.GroupId,G.GroupName,M.Name  FROM $DataIn.stuffdata S 
	   LEFT JOIN  $DataIn.StuffType T  ON T.TypeId=S.TypeId 
	   LEFT JOIN $DataIn.staffgroup G ON G.Id=T.DevelopGroupId
	   LEFT JOIN $DataIn.staffmain M ON M.Number=T.DevelopNumber
	    WHERE S.StuffId='$StuffId' LIMIT 1",$link_id));
		$DevelopNumber=$oldResult["DevelopNumber"];
		$GroupId=$oldResult["GroupId"];
		$GroupName=$oldResult["GroupName"];
		$DevelopName=$oldResult["Name"];
		
 }
//步骤4：
$tableWidth=850;$tableMenuS=500;
//$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,StuffId,$StuffId,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input id="PackingUnit" name="PackingUnit" type="hidden" value="1" />
	<table width="800" border="0" align="center" cellspacing="5">
		<tr>
            <td width="103" align="right" scope="col">配件名称</td>
            <td scope="col"><?php  echo $StuffId . "-" . $StuffCname?></td>
          </tr>
          <tr>
            <td width="103" align="right" scope="col">开发小组</td>
            <td scope="col"><?php  echo $GroupName;?></td>
            <input type='hidden' name="GroupId" id="GroupId" value='<?php  echo $GroupId;?>'>
          </tr>
          
          
		   <tr >
		            <td width="103" align="right" scope="col">需求客户</td>
		            <td><select name="ClientCompanyId" id="ClientCompanyId" style="width: 480px;"  dataType="Require"  msg="未选择需求客户" >
					<option value=''>请选择</option>
					<?php 
			       $result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate=1 AND  ObjectSign IN (1,2)  ORDER BY Id",$link_id);
				   if($myrow = mysql_fetch_array($result)){
					do{
		              if($CompanyId==$myrow["CompanyId"]){
		                  $Forshort=$myrow["Forshort"];
		                   echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
		                 }
					else	echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
						} while ($myrow = mysql_fetch_array($result));
					}
					?>		 
					</select>
					</td>
		    </tr>          
         <!--  
          <tr>
            <td width="103" align="right" scope="col">责任人</td>
            <td scope="col"><?php  echo $DevelopName;?></td>
             <input type='hidden' name="DevelopNumber" id="DevelopNumber" value='<?php  echo $DevelopNumber;?>'>
          </tr>

         
          <tr>
            <td align="right">开发小组</td>
            <td><select name="GroupId" id="GroupId" style="width:480px"  dataType="Require"  msg="未选择" onchange="GroupChange(this)">
            
			  <?php 
			    if ($GroupId=="") echo "<option value='' selected>请选择</option>";
				$GroupResult = mysql_query("SELECT GroupId,GroupName FROM $DataIn.staffgroup WHERE (BranchId='5' OR GroupId='102') AND Estate='1' order by Id",$link_id);
				while ($GroupRow = mysql_fetch_array($GroupResult)){
					$theGroupId=$GroupRow["GroupId"];
					$GroupName=$GroupRow["GroupName"];
					if($GroupId==$theGroupId){
						echo "<option value='$theGroupId' selected>$GroupName</option>";
						}
					else{
						echo "<option value='$theGroupId'>$GroupName</option>";
						}
					}
			 	?>
            </select>
              </td>
          </tr>
        -->   
           <tr>
            <td align="right">责任人</td>
            <td><select name="DevelopNumber" id="DevelopNumber" style="width:480px"  dataType="Require"  msg="未选择">
			    <?php  
			  
			        $branchIdStr = implode(',',$APP_CONFIG['DEVELOPMENT_BRANCHIDS']);
				    $StaffSql = mysql_query("SELECT M.Number,M.Name
							FROM $DataPublic.staffmain M 
							WHERE  M.BranchId IN($branchIdStr)  AND M.Estate='1'  ORDER BY M.Number",$link_id);
							
                      	while ($StaffRow = mysql_fetch_array($StaffSql)){
                      	    $theNumber=$StaffRow["Number"];
							$StaffName=$StaffRow["Name"];
							if($DevelopNumber==$theNumber){
								echo "<option value='$theNumber' selected>$StaffName</option>";
								}
							else{
								echo "<option value='$theNumber'>$StaffName</option>";
								}
		                }
			    ?>
            </select>
              </td>
          </tr>
         
          <!--
           <tr>
            <td align="right">计划完成日期</td>
            <td>
            <?php 
            // $checkBranchId=mysql_query("SELECT BranchId FROM  $DataPublic.staffmain WHERE Number='$Login_P_Number' AND BranchId='3'",$link_id);
            //mysql_num_rows($checkBranchId)>0 || 
            if ($Targetdate=="" || $Targetdate=="0000-00-00" || $Login_P_Number==10007 ||  $Login_P_Number==10341 ||  $Login_P_Number==10868){
	
            //if ($Targetdate=="" || $Login_P_Number==10007 ||  $Login_P_Number==10341 ||  $Login_P_Number==10868){ 
	            
            ?>
             <input name="TargetWeek" type="text" id="TargetWeek" value="<?php  echo $Targetdate?>" style="width:120px"  align='absmiddle'   dataType="Require"   msg="未填写完成日期"  onclick='set_weekdate(this)' readonly>
             <input name="Targetdate" type="hidden" id="Targetdate" value="<?php  echo $Targetdate?>" >
             
            <!--
            <input name="Targetdate" type="text" id="Targetdate" value="<?php  echo $Targetdate?>" style="width:120px" onclick="WdatePicker({el:'Targetdate',minDate:'%y-%M-%d',isShowWeek:true,onpicked:function(){document.getElementById('Week').innerHTML=$dp.cal.getP('W','WW')+'周';}})" src='../model/DatePicker/skin/datePicker.gif'  align='absmiddle'  dataType="Date" format="ymd" msg="日期不正确" readonly>
            -->
           <!--
            <?php }else{ ?> 
               <input name="Targetdate" type="text" id="Targetdate" value="<?php  echo $Targetdate?>" style="width:120px" readonly>
             <?php } ?>    
            <span id='Week' style='margin-left:5px;color:#0000FF'><?php echo $TargetWeek ?></span></td>
          </tr>    
         -->  
         <tr>
            <td align="right">计划完成日期</td>
            <td><span id='Week' style='margin-left:5px;color:#0000FF'><?php echo $TargetWeek ?></span></td>
         </tr>        
        <tr>
            <td align="right">开发说明</td>
            <td><textarea name="Remark" style="width:480px" rows="4" id="Remark"><?php echo $Remark?></textarea></td>
        </tr>
        
        <tr>
            <td align="right">开发文档</td>
            <td><input name="developfile" type="file" id="developfile" size="60" DataType="Filter"  msg="非法的文件格式" accept="pdf,psd,eps,jpg,ai,cdr,rar,zip"></textarea>
            <?php 
        	if($dFile!=""){
            echo"
				<input type='checkbox' name='delFile' id='delFile' value='$dFile'><LABEL for='delFile'>删除已上传开发文档</LABEL> ";
            }
            ?>
            </td>
            
        </tr>
         <?php 
         
         if ($PEstate==='0' && ($Login_P_Number=='10341' || $Login_P_Number=='10868')){
      ?>
	    <tr>
            <td align="right">开发状态</td>
            <td><select name="developEstate" id="developEstate" style="width:480px">
                <option value='0'>已完成</option>
                <option value='1'>重新打样</option>
                </select>
            </td>
        </tr>
      <?php
         }
      ?>   
                
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";

 $StaffSql = mysql_query("SELECT M.GroupId,M.Number,M.Name
	FROM $DataPublic.staffmain M 
	WHERE (M.BranchId='5' OR M.GroupId='102')  AND M.Estate='1'  ORDER BY M.GroupId,M.Number",$link_id);

	while ($StaffRow = mysql_fetch_array($StaffSql)){
		$sNumber=$StaffRow["Number"];
         $sName=$StaffRow["Name"];
		 $sGroupId=$StaffRow["GroupId"];
         $subName[]=array($sGroupId,$sNumber,$sName);
	};
	
?>
<script src='../model/weekdate.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
var weekdate=new WeekDate();
var subName=<?php echo json_encode($subName);?>;

function GroupChange(e){
    var sLen=subName.length;
    if (sLen>0){
       Main_SelectChanged('DevelopNumber',subName,e.value); 
    }
}

function Main_SelectChanged(selectObj,OptionList,selIndex){
   if (typeof selectObj != 'object')
     {
       selectObj = document.getElementById(selectObj);
     }
     
    // 清空选项
    var slen = selectObj.options.length;
 
    for (var i=0; i < slen; i++)
    {
        // 移除当前选项
        selectObj.options[0] = null;
    }
    
    var len = OptionList.length;
    
    selectObj.options[0] = new Option('请选择', '');
    
    var n=1;
    
    for (var i=0; i < len; i++)
    {
        if (OptionList[i][0]==selIndex)
        {
           selectObj.options[n] = new Option(OptionList[i][2],OptionList[i][1]);
           n++;
        }
    }
    
 }
 
 function set_weekdate(el){
	  var saveFun=function(){
			     if (weekdate.Value>0){
					       var tempWeeks=weekdate.Value.toString();
					       tempWeeks="Week "+tempWeeks.substr(4, 2);
					       el.value=tempWeeks;
						   var tempDate=weekdate.getFriday("-");
						  document.getElementById("Targetdate").value=tempDate;
				   }
		};
	   
	   weekdate.show(el,1,saveFun,"");
}


/*
function CheckForm()
{
     var sign=0;
     if (document.getElementById("oldStuffCname").value!=document.getElementById("StuffCname").value){
	     sign++;
     }
     
     if (document.getElementById("oldPrice").value*1!=document.getElementById("Price").value*1){
	     sign++;
     }
     
      if (document.getElementById("oldCompanyId").value!=document.getElementById("CompanyId").value){
	     sign++;
     }
     
     if (sign>0){
           var Reason=document.getElementById("Reason").value;
           Reason=Reason.replace(/(^\s*)|(\s*$)/g, "");
            if (Reason==""){
	             alert("请填写修改原因！");return false;
	        }
     }
     
	 Validator.Validate(document.getElementById(document.form1.id),3,"stuffdata_updated");
}
*/
</script>