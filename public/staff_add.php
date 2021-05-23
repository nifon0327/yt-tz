<?php
/*
代码、数据库合并后共享-ZXQ 2012-08-08
加入血型字段 EWEN 2012-10-29
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工资料");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
?>
<style>#red{color: red}</style>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
            <td align="right">身份证号</td>
            <td><input name="Idcard" type="text" id="Idcard" style='width:380px' maxlength="20" onblur="checkIdCard(this);" oninput="idCardonChange();"  onchange="idCardonChange();" dataType="IdCard"  msg="身份证号码不正确"><span name="checkInfo" id="checkInfo" style="color:#FF0000;display:none;">*身份证号已存在!</span><span id="red">*</span>
             </td>
          </tr>
          <tr>
            <td align="right" scope="col">介 绍 人</td>
            <td scope="col"><input name="StaffName" type="text" id="StaffName" style="width:380px" onclick="SearchData('<?php  echo $funFrom?>',1,-1,1)" readonly="readonly" />
                            <input name="Introducer" id="Introducer"  type='hidden' /> </td>
          </tr>
        <tr>
            <td align="right" scope="col">姓&nbsp;&nbsp;名</td>
            <td scope="col"><input name="Name" type="text" id="Name" style="width:380px" maxlength="8" dataType="Chinese" msg="只允许中文"><span id="red">*</span> </td>
          </tr>
        <tr>
            <td align="right" scope="col">英 文 名</td>
            <td scope="col"><input name="Nickname" type="text" id="Nickname"style="width:380px" > </td>
          </tr>
        <tr>
          <td colspan="2" scope="col">基本信息</td>
          </tr>
        <tr>
          <td align="right" scope="col">性&nbsp;&nbsp;别</td>
          <td scope="col">
          <select name="Sex" id="Sex" style="width:380px" dataType="Require"  msg="未选择性别" >
            <option value="" selected>--请选择--</option>
            <option value="1">男 </option>
            <option value="0">女 </option>
          </select><span id="red">*</span>
            </td>
          </tr>
                <tr>
            <td height="23" align="right" scope="col">民&nbsp;&nbsp;族</td>
            <td scope="col">
            <select class=selet id=Nation style="width:380px" size=1 name=Nation dataType="Require"  msg="未选择民族">
             <option value="" selected>--请选择--</option>
              <?php
             $Result2 = mysql_query("SELECT Id,Name FROM $DataPublic.nationdata WHERE Estate=1 order by Id",$link_id);
             if($myRow2 = mysql_fetch_array($Result2)){
                do{
                    echo" <option value='$myRow2[Id]'>$myRow2[Name]</option>";
                    }while($myRow2 = mysql_fetch_array($Result2));
                }
             ?>
            </select><span id="red">*</span>
            </td>
          </tr>
                <tr>
                  <td align="right" scope="col">籍&nbsp;&nbsp;贯</td>
                  <td scope="col"><select name="Rpr" size="1" id="select2" style="width:380px" dataType="Require"  msg="未选择籍贯">
                    <option value="" selected>--请选择--</option>
                      <?php
                     $Result3 = mysql_query("SELECT Id,Name FROM $DataPublic.rprdata WHERE Estate=1 order by Id",$link_id);
                     if($myRow3 = mysql_fetch_array($Result3)){
                        do{
                            echo" <option value='$myRow3[Id]'>$myRow3[Name]</option>";
                            }while($myRow3 = mysql_fetch_array($Result3));
                        }
                     ?>
                  </select><span id="red">*</span>
                  </td>
          </tr>
<tr>
            <td align="right">学&nbsp;&nbsp;历</td>
            <td><select name="Education" size="1" id="select" style="width:380px" dataType="Require"  msg="未选择学历">
              <option value="" selected>--请选择--</option>
                      <?php
                     $Result4 = mysql_query("SELECT Id,Name FROM $DataPublic.education WHERE Estate=1 order by Id",$link_id);;
                     if($myRow4 = mysql_fetch_array($Result4)){
                        do{
                            echo" <option value='$myRow4[Id]'>$myRow4[Name]</option>";
                            }while($myRow4 = mysql_fetch_array($Result4));
                        }
                     ?>
            </select><span id="red">*</span>
            </td>
          </tr>
          <tr>
            <td align="right">婚姻状况</td>
            <td><select name="Married" size="1" id="Married" style="width:380px" dataType="Require"  msg="未选择婚姻状况">
              <option value="">--请选择--</option>
              <option value="1">未 婚</option>
              <option value="0">已 婚</option>
            </select><span id="red">*</span>
            </td>
          </tr>          <tr>
            <td width="113" align="right">出生日期</td>
            <td><input name="Birthday" type="text" id="Birthday" style="width:380px" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="生日日期不正确" readonly><span id="red">*</span>
            </td>
          </tr>
          <tr>
            <td align="right">照&nbsp;&nbsp;片</td>
            <td><input name="Photo" type="file" id="Photo" style="width:380px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="9" Cel="1"></td>
          </tr>
          <tr>
            <td align="right">身 份 证</td>
            <td><input name="IdcardPhoto" type="file" id="IdcardPhoto" style="width:380px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="10" Cel="1"></td>
          </tr>
          <tr>
            <td align="right">健康体检</td>
            <td><input name="HealthPhoto" type="file" id="HealthPhoto" style="width:380px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="10" Cel="1"></td>
          </tr>
          <tr>
            <td align="right">职业体检</td>
            <td><input name="vocationHPhoto" type="file" id="vocationHPhoto" style="width:380px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="10" Cel="1"></td>
          </tr>
          <tr>
            <td align="right">血&nbsp;&nbsp;型</td>
            <td><select name="BloodGroup" size="1" id="BloodGroup" style="width:380px" datatype="Require"  msg="未选择血型">
              <option value="">--请选择--</option>
              <option value="0">未确定</option>
              <?php
              $checkBGSql=mysql_query("SELECT Id,Name FROM $DataPublic.bloodgroup_type WHERE Estate=1 ORDER BY Id",$link_id);
              if($checkBGRow=mysql_fetch_array($checkBGSql)){
                  do{
                      echo"<option value='$checkBGRow[Id]'>$checkBGRow[Name]</option>";
                      }while($checkBGRow=mysql_fetch_array($checkBGSql));
                  }
              ?>
              </select><span id="red">*</span></td>
          </tr>

          <tr>
            <td align="right">家庭地址</td>
            <td><input name="Address" type="text" id="Address" style="width:380px"  maxlength="100"></td>
          </tr>
          <tr>
            <td align="right">邮政编码</td>
            <td><input name="Postalcode" type="text" id="Postalcode" style="width:380px"  maxlength="6" require="false" dataType="Zip" msg="邮政编码不存在"></td>
          </tr>



          <tr>
            <td align="right">紧急联系人-电话</td>
            <!--
            <td><input name="Tel" type="text" id="Tel" style="width:380px"  maxlength="30" require="false" dataType="Phone" msg="电话号码不正确"></td>
            -->
            <td><input name="Tel" type="text" id="Tel" style="width:380px"  maxlength="40" ></td>
          </tr>
          <tr>
            <td colspan="2"><div align="left">公司信息</div></td>
          </tr>
          <tr>
          <td align="right">员工类别</td>
          <td><?php  $SelectWidth="380px"; include "../model/subselect/FormalSign.php"; ?><span id="red">*</span></td>
          </tr>
          <tr>
             <td align="right">入职公司</td>
             <td> <?php
                  $cSignWidth="380px";
                  include "submodel/select_cSign_read.php";
             ?></td>
          </tr>
           <tr>
             <td align="right">工作地点</td>
             <td><?php
             include "../model/subselect/WorkAdd.php";
             ?><span id="red">*</span></td>
        </tr>

        <tr>
            <td align="right">考勤楼层</td>
            <td>
                <?php
                    include "../model/subselect/FloorAdd.php";
                ?>
            </td>
        </tr>

          <tr>
            <td align="right">部&nbsp;&nbsp;门</td>
            <td>
            <?php
             $SelectFrom="";
             include"../model/subselect/BranchId.php";
            ?><span id="red">*</span>
            </td>
          </tr>
          <tr>
            <td align="right">小&nbsp;&nbsp;组</td>
            <td><?php
             $SelectFrom="";
             include"../model/subselect/GroupIdType.php";
            ?><span id="red">*</span>
            </td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;位</td>
            <td>
            <?php
             $SelectFrom="";
             include"../model/subselect/JobIdType.php";
            ?><span id="red">*</span>
            </td>
          </tr>
          <tr>
            <td><div align="right">ID卡号</div></td>
            <td><input name="IdNum" type="text" id="IdNum" style="width:380px"  maxlength="10"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:380px"  maxlength="16"></td>
          </tr>
          <tr>
            <td align="right">短&nbsp;&nbsp;号</td>
            <td><input name="Dh" type="text" id="Dh" style="width:380px" >
              </td>
          </tr>
          <tr>
            <td align="right">公司邮箱</td>
            <td><input name="GroupEmail" type="text" id="GroupEmail" style="width:380px"  maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td align="right">电子邮件</td>
            <td><input name="Mail" type="text" id="Mail" style="width:380px"  maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
          <td align="right">微&nbsp;&nbsp;信</td>
            <td><input name="Weixin" type="text" id="Weixin" style="width:380px"  maxlength="50" require="false"></td>
          </tr>
          <tr>
          <td align="right">LinkedIn</td>
            <td><input name="LinkedIn" type="text" id="LinkedIn" style="width:380px"  maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
           <tr>
            <td align="right">AppleID</td>
            <td><input name="AppleID" type="text" id="AppleID" style="width:380px"  maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td align="right">入职日期</td>
            <td><input name="ComeIn" type="text" id="ComeIn" value="<?php  echo date("Y-m-d");?>" style="width:380px"  maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="入职日期不正确" readonly>
            </td>
          </tr>
          <tr>
            <td align="right" valign="top">工衣尺寸</td>
            <td><textarea name="ClothesSize" style="width:380px" rows="3" id="ClothesSize"></textarea></td>
          </tr>

          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;注</td>
            <td><textarea name="Note" style="width:380px" rows="4" id="Note"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<input name="DataIn" type="hidden" id="DataIn" value="<?php  echo $DataIn?>">
<input name="Number" type="hidden" id="Number" value="<?php  echo $Number?>">
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";

//加载快速选择员工列表功能
$staffName_InputID="StaffName";  //显示员工姓名
$staffNumber_InputID="Introducer"; //获取员工Number
include  "../model/subprogram/staffname_input.php";
?>

<script language="JavaScript" type="text/JavaScript">
function idCardonChange()
{
   document.getElementById("checkInfo").style.display="none";
}

function checkIdCard(e){
     var idcard=e.value;
     if (idcard.length>=10 && idcard.length<=18){
         document.getElementById("checkInfo").style.display="none";
          url="staff_info_ajax.php?IdCard="+idcard+"&Action=IdCard";
          //alert(url);
             var ajax=InitAjax();
             ajax.open("GET",url,true);
             ajax.onreadystatechange =function(){
                 if(ajax.readyState==4){
                         if (ajax.responseText=="Y"){
                              document.getElementById("checkInfo").style.display="";
                         }
                   }
              }
             ajax.send(null);
    }
}


function SearchData(fSearchPage,SearchNum,Action,TimeId){//来源页面，可取记录数，动作（因共用故以参数区别）
    var num=Math.random();
            var tSearchPage="../public/staff";
            BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+num+"&Action="+Action+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
            if(!BackData){
                    if(document.getElementById('SafariReturnValue')){
                    var SafariReturnValue=document.getElementById('SafariReturnValue');
                    BackData=SafariReturnValue.value;
                    SafariReturnValue.value="";
                    }
              }
              //alert (TimeId);
              TimeId=TimeId*1;
                if(BackData){
                         switch(TimeId){
                              case 1:
                                //alert (BackData);
                                var FieldArray=BackData.split("^^");//分拆记录中的字段
                                  document.getElementById("StaffName").value=FieldArray[1];
                                document.getElementById("Introducer").value=FieldArray[0];

                                  break;
                              case 2:
                                  document.getElementById("StaffName").value=BackData;
                                  break;
                               }
                   }
    }

</script>