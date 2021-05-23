$.ajaxSetup({cache:false});
var site,url;
    site='http://cz.matechstone.com';
    url=site+'/weixin/wx_plan/controller/index.php';

function myPost (obj) {
  $.ajax({
    type: "POST",
    url: url,
    dataType: "json",
    data: obj.data,
    success: obj.successFn,
  });
}

var webPOST=function(param,fun){
    //url+='?t='+timestamp();
    $.post(url,param,fun);
}

var _isNull=function(val){
   val=val==undefined?'':val;
   if (val.length==0 ) { 
        return true;
      }
    return false;
}

var initpage=function(current,pagesize){
     var uppage,downpage;
     current=parseInt(current);
     uppage=(current>1)?(current-1):1;
     downpage=(current<pagesize)?(current+1):pagesize;
     var _page= '<a class="first" page="1">首页</a>';
         _page+='<a class="up" page="'+uppage+'">上一页</a>';;
         for(i=0;i<parseInt(pagesize);i++){

           if(i==(current-1))
             _page+='<a class="selected" page="'+(i+1)+'">'+(i+1)+'</a>';
           else
           	 _page+='<a page="'+(i+1)+'">'+(i+1)+'</a>';
         }
         _page+='<a class="down" page="'+downpage+'">下一页</a>';
         _page+='<a class="last" page="'+pagesize+'">尾页</a>';
     $('.row .page-container').empty().append(_page);
}

