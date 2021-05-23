<!DOCTYPE html>
<html>
<head>
  <title>测试接口</title>
  <script type="text/javascript" src="https://cdn.staticfile.org/jquery/1.12.4/jquery.min.js"></script>
  <script type="text/javascript">
  	var text_searchPCTradeInfo=function(){
  		$.post('http://8.tag5.cn/controller/index.php',
  	   	  	     {action:'searchPCTradeInfo',tradeid:34,buildingno:13},
  	   	  	     function(data){
		  	   	   console.log(data);
		  	     });
  	}

  	var text_setTradeTime=function(){
  		$.post('http://8.tag5.cn/weixin/controller/index.php',
  	   	  	     {action:'setTradeTime',drawingid:34739,requestdatetime:'2019-01-19',reqid:1,reqname:'Hello World'},
  	   	  	     function(data){
		  	   	   console.log(data);
		  	     });
  	}


  	var getTradeSentPlan=function(){
  		$.post('http://matech.tag5.cn/weixin/controller/index.php',
  	   	  	     {action:'getTradeSentPlan',objectid:34,buildingno:15,floorno:17},
  	   	  	     function(data){
		  	   	   console.log(data);
		  	     });
  	}

  	var getTradeInfoPageExt=function(){
  		$.post('http://matech.tag5.cn/weixin/controller/index.php',
  	   	  	     {action:'getTradeInfoPageExt',tradeid:34,buildingno:15,floorno:17,current:0,pagenum:15},
  	   	  	     function(data){
		  	   	   console.log(data);
		  	     });
  	}

  	var setTradeTimeExt=function(){
  		$.post('http://matech.tag5.cn/weixin/controller/index.php',
  			  {action:'setTradeTimeExt',tradeid:34,buildingno:15,floorno:17,cmpttypeid:8001,requestdatetime:'2019-02-02',reqid:2,reqname:'测试人员',openid:''},
  			  function(data){
  			  	  console.log(data);
              });
  	}

    var setTradeStateExt=function(){
      $.post('http://matech.tag5.cn/weixin/controller/index.php',
          {action:'setTradeStateExt',tradeid:34,buildingno:15,floorno:17,cmpttypeid:8011,state:2},
          function(data){
              console.log(data);
              });
    }

    var getPMCTradeRequestInfoPageExt=function(){
       $.post('http://8.tag5.cn/weixin/controller/index.php',
          {
            action:'getPMCTradeRequestInfoPageExt',
            tradeid:34,
            buildingno:15,
            floorno:17,
            requestdatetime:'',
            current:1,
            pagenum:15
          },
          function(data){
              console.log(data);
          });
    }

    var setShipMentsTime=function(){
       $.post('http://matech.tag5.cn/weixin/controller/index.php',
          {
            action:'setShipMentsTime',
            TradeId:34,
            BuildingNo:15,
            FloorNo:17,
            CmptTypeId:8010,
            DeliveryDate:'2019-10-02'

          },
          function(data){
              console.log(data);
          });
    }

    var getReplenishTransportRecord=function(){
      $.post('http://matech.tag5.cn/weixin/controller/index.php',
          {
            action:'getReplenishTransportRecord',
            carnumber:'JL2019011401'

          },
          function(data){
              console.log(data);
          });
    }


    var setReplenishTransportRecord=function(){
       $.post('http://matech.tag5.cn/weixin/controller/index.php',
          {
            action:'setReplenishTransportRecord',
            typeid:'1',  
            address:'',       
            createdatetime:'2019-01-25'
            

          },
          function(data){
              console.log(data);
          });
    }

    var getShipsAndReplenishTransportRecordPc=function(){
       $.post('http://matech.tag5.cn/weixin/controller/index.php',
          {
            action:'getShipsAndReplenishTransportRecordPc',
            tradeId:'34',
            carnumber:'JL2019011801',
            carno:'',
            data:''

          },
          function(data){
              console.log(data);
          });
    }
    
    var cardata=function(){
       $.post('http://matech.tag5.cn/weixin/controller/index.php',
           {
             action:'cardata'
          },
          function(data){
              console.log(data);
          });
    } 

    var getopen=function(){
      $.post('http://matech.tag5.cn/weixin/controller/index.php',{action:'getopenid'},function(data){
          console.log(data);
      });
    }
  	$(function(){
  	   $('#btn-coller').on('click',function(){
  	   	  // getTradeInfoPageExt();
          getopen(); 
  	   });

  	   $('#btn-setTradeTime').on('click',function(){
  	   	// text_searchPCTradeInfo();
  	    //getTradeInfoPageExt();
  	   	//setTradeTimeExt();
        //setTradeStateExt(); 
        //getPMCTradeRequestInfoPageExt();
        //setShipMentsTime();
        //getPMCTradeRequestInfoPageExt();
        //getTradeSentPlan();
          getReplenishTransportRecord();
         // setReplenishTransportRecord();
         //getShipsAndReplenishTransportRecordPc();
         //setTradeTimeExt();
  	   })
  	})
  </script>
</head>
<body>
  <button id="btn-coller">
  	 测试一
  </button>

  <button id="btn-setTradeTime">
  	  测试二
  </button>
</body>
</html>