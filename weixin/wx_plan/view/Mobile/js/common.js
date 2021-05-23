$.ajaxSetup({cache:false});
var site='http://cz.matechstone.com';
var url='http://cz.matechstone.com/weixin/wx_plan/controller/index.php';
var urlflash=site+'/weixin/wx_plan/view/Mobile/plugin/webuploader/Uploader.swf';
var fileURL =site+'/weixin/wx_plan/controller/upload.php';
function myPost (obj) {
  $.ajax({
    type: "POST",
    url: url,
    dataType: "json",
    data: obj.data,
    success: obj.successFn,
  });
}
function myGet (obj) {
  $.ajax({
    type: "GET",
    url: obj.url,
    dataType: "json",
    data: obj.data,
    success: obj.successFn,
  });
}

var initWXConfiguration=function(redirectURI){
    var appId,appSecret,code;
    appId='wx39190f186cd2c4ff';
    appSecret='01b01c021b008c0d23a7ea0d89976d43';
    var code=getParam('code');
    if(!_isNull(code)){
      return code; 
    }else{
      $.post(url,{action:'getwxcode'},function(data){
         WxInfo=data.result;
         window.location.href="https://open.weixin.qq.com/connect/oauth2/authorize?appid="+appId+"&redirect_uri="+encodeURIComponent(mySite+redirectURI)+"&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
        
      });
    }
    
}
/*
 * 判断是否为空
 */
var _isNull=function(val){
   val=val==undefined?'':val;
   if (val.length==0 ) { 
        return true;
      }
    return false;
}
/*
 * 获取GET参数
 */
var getParam = function(paramName) { 
    paramValue = "", isFound = !1; 
    if (this.location.search.indexOf("?") == 0 && this.location.search.indexOf("=") > 1) { 
        arrSource = unescape(this.location.search).substring(1, this.location.search.length).split("&"), i = 0; 
        while (i < arrSource.length && !isFound) arrSource[i].indexOf("=") > 0 && arrSource[i].split("=")[0].toLowerCase() == paramName.toLowerCase() && (paramValue = arrSource[i].split("=")[1], isFound = !0), i++ 
    } 
    return paramValue == "" && (paramValue = null), paramValue 
}

/*
 * OpenMessage
 */
var layerMsg=function(message){
      layer.open({
        content: message
        ,skin: 'msg'
        ,time: 2 //2秒后自动关闭
      });
}

var layerAlert=function(message){
  layer.open({
    content: message,
    shadeClose: false
    ,btn: '确定'
  });
}



var layerAlertLocation=function(message,location){
  layer.open({
    content: message,
    btn: '确定',
    shadeClose: false,
    yes: function(){
      window.location.href=location;
    }
  });
}

var layerMsgFunction=function(message,fun){
   layer.open({
        content: message
        ,skin: 'msg'
        ,time: 2
        ,yes:fun
    });
}

var layerAlertFunction=function(message,fun){
   layer.open({
      content:message,
      btn:'确定',
      shadeClose:false,
      yes:fun
   });
}

var layerConfirmFunction = function(message,fun){
      layer.open({
          content: '是否通过项目审核？'
          ,btn: ['审核', '不审核']
          ,yes: function(index){
            fun();
            layer.close(index);
          }
      })
}


var layerOpenWindos=function(title,message,btn,fun){
      layer.open({
         title:[title,'background-color:#009688; color:#fff;'],
         content:message,
         btn:btn,
         shadeClose: false,
         yes:function(index){
            fun();
            layer.close(index);
         }
      });
}