<style type="text/css">
    input {font-size:14px;font-family:arial;border: none;background:none;}
    /*--这里设置输入框文字字体，并设置输入框背景和边框为无--*/
    span.search {
        /*--将输入框放在span中并设置浮动--*/
        padding:0;margin:0;
        width:120px;height:45px;
        /*--设置输入框的外层元素宽度和高度--*/
        background:#DFE0E2;color: #9e9c9c!important;
        /*--设置背景颜色和输入框文字颜色--*/
        border: 0;
        border-radius:2px;
        /*--设置边框，并将左上角和左下角设置圆角--*/
    }
    span.search input {
        width: 120px;height:18px;
        /*--设置输入框本身的宽度和高度--*/
        line-height:18px;
        /*--设置输入框行距和高度相等以便垂直居中--*/
        padding:0 5px;
        /*--设置输入框本身离外层的内边距，输入文字更美观--*/
        outline:none;
        /*--为了美观，将获取焦点时的轮廓线去掉--*/
        color: #9e9c9c!important;
    }
    span.button {
        /*--设置提交按钮放在span中并设置浮动--*/
        padding:0;margin:0;
        border: 0;
        border-radius:5px;
        /*--设置边框，并将右上角和右下角设置圆角--*/
    }
    span.button input {
        height:28px;
        line-height:28px;
        /*--设置按钮本身的高度，行距和高度相等以便垂直居中--*/
        padding:0;
        /*--为了美观，适当设置按钮文字的左右边距--*/
        background:#628DFF;color:#fff;
        /*--将提交按钮的背景颜色设置与边框颜色相同--*/
    }
    ::-webkit-input-placeholder {color:#AEAFB1;}
    /*--设置输入框内默认文字的颜色--*/
</style>
<?php
//关系到lookup.js, readmodel_3.php if ($FromSearch=="FromSearch") {
$keywords="Keywords";
if($Login_uType==2){
          $KeyButton="Search";
         }
else{
           $keywords="";
          $KeyButton="";
         }

if ($multUd) {//多重查询 by ckt 2017-12-14
    echo "<span class=\"search\"><input name='sokeyword' type='text' id='sokeyword' value='$keywords:' style='width:42px;border:0px;background:Transparent' readonly='readonly' /></span>";
    foreach ($searchtable as $key => $value) {
        $searchValue = 'search' . (string)$key;
        $searchValue = $$searchValue;
        echo '&nbsp;' . $value['name'] . ":<span class=\"button\"><input name='search" . $key . "' type='text' id='search" . $key . "' value='" . $searchValue . "' autocomplete='off' style='width:100' /></span>";
    }
} else {
    echo "<div style='width:260px;display:inline-block;position: relative;'><span class=\"search\"><input name='search' type='hidden' id='search'  autocomplete='off' placeholder='请输入关键字'/></span>";
    echo "<input name='sokeyword' type='text' id='sokeyword' value='' style='width:16px;border:0px;position:relative;right:76px' readonly='readonly' /><i style='position:relative;right:33px' class='button iconfont icon-sousuofangdajing' onClick='openLoading();ToSearch()'></i>";
}
echo "   
  <input type='button' name='Submit' value='$KeyButton' id='sure'>
  <input name='searchtable' type='hidden' id='searchtable' value='$searchtable'>
  <input name='searchfile' type='hidden' id='searchfile' value='$searchfile'>
  <input name='FromSearch' type='hidden' id='FromSearch' value='$FromSearch'>";  //$searchfile 默认为 Quicksearch_ajax.php, 可自行设定
echo "
 <script language='javascript' type='text/javascript'>
 	window.oninit=InitQueryCode('search','querydiv'); 
 	var search = document.getElementById(\"search\").value;
 	var sure = document.getElementById(\"sure\");
 	sure.onclick=function () { 
 	    location.reload(); 
 	 };
	//clearDiv();
	
 </script> ";


if (!$multUd) {//非多重查询
    echo "
 <script language='javascript' type='text/javascript'>
 	window.oninit=InitQueryCode('search','querydiv'); 
	//clearDiv();
 </script> ";
}//
?>