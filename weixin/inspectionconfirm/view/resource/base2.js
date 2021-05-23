
(function($) {
  var pop = {
    dialog: {
      open: function(jdom, type) {
        if(!jdom) return false
        if($('.tztdialog').length < 1) {
          $('body').append(`<div class="tztdialog"><div class="tztmask"></div><div class="tztdialogcontent"></div></div>`);
        }
        var ct = $('.tztdialogcontent'), dd = $('.tztdialog')
        if(type == '0') dd.addClass('toast-ct')
        this.removeExistingPops()
        ct.append(jdom);
        var _width = jdom.outerWidth(), _height = jdom.outerHeight();
        ct.css('width', _width + 'px').css('height', _height + 'px');
        dd.fadeIn(100);
      },
      close: function() {
        this.removeExistingPops()
        $('.tztdialog').hide();
      },
      removeExistingPops:function(){
        var pops = $('.tztdialogcontent').children()
        if(pops.length !== 0) {
          pops.remove();
        }
      }
    },
    ShowAlert: function(msg, text, callback) {
      var _self = this,
        text = text || '确定'
      var alertBox = $(`<div class="confirm-dialog"></div>`),
      msgDiv = $(`<div class="content">${msg}</div>`),
      btnOk = $(`<div class="footer">${text}</div>`);

      btnOk.on('click', function() {
        if(callback) callback();
        _self.dialog.close();
      });

      alertBox.append(msgDiv).append(btnOk);
      this.dialog.open(alertBox);
    },
    ShowConfirm: function(msg, lBtnTxt, rBtnTxt, leftFn, rightFn) {
      var _self = this;
      var confimBox = $(`<div class="confirm-dialog"></div>`),
      msgDiv = $(`<div class="content">${msg}</div>`),
      footer = $(`<div class="footer"></div>`),
      leftBtn = $(`<div class="btn btn-left">${lBtnTxt||'取消'}</div>`),
      rightBtn = $(`<div class="btn btn-right">${rBtnTxt||'确定'}</div>'`);

      leftBtn.on('click', function() {
        _self.dialog.close();
        leftFn && leftFn();
      });

      rightBtn.on('click', function() {
        _self.dialog.close();
        rightFn && rightFn()
      });

      footer.append(leftBtn).append(rightBtn);
      confimBox.append(msgDiv).append(footer);
      this.dialog.open(confimBox);
    },
    ShowToast: function(msg) {
      var _self = this;
      var alertBox = $(`<div class="toast-dialog">
                          <div class="icon-success-gray"></div>
                          <div class="info-content">${msg}</div>
                        </div>`);

      this.dialog.open(alertBox, 0);
      setTimeout(function() {
        _self.dialog.close();
      }, 2000);
    },
  }
  window.POP = pop;
})(jQuery);


(function($, POP, undefined) {
  var SERVICE = {
    sendSHR: function(serviceUrl,oSend, fnSuccess, fnFailed, fnError) {
      var _self = this
      $.ajax({
          url:serviceUrl,
          type:'post',
          dataType:'json',
          data:oSend,
          success: function(oData) {
            if(oData.status === 0) {
              fnSuccess && fnSuccess(oData)
              return;
            } else {
              fnFailed = fnFailed || _self.defaultXHRFailedHandler;
              fnFailed && fnFailed(oData);
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            fnError = fnError || _self.defaultXHRErrorHandler;
            fnError && fnError(XMLHttpRequest, textStatus, errorThrown);
          }
      });
    },
    uploadfile: function(serviceUrl,oSend, fnSuccess, fnFailed, fnError) {
      var _self = this
      $.ajax({
          url:serviceUrl,
          type:'post',
          dataType:'json',
          data:oSend,
          cache: false,
          processData: false,
          contentType: false,
          success: function(oData) {
            if(oData.status === 0) {
              fnSuccess && fnSuccess(oData)
              return;
            } else {
              fnFailed = fnFailed || _self.defaultXHRFailedHandler;
              fnFailed && fnFailed(oData);
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            fnError = fnError || _self.defaultXHRErrorHandler;
            fnError && fnError(XMLHttpRequest, textStatus, errorThrown);
          }
      });
    },
    defaultXHRErrorHandler: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log('defaultXHRErrorHandler', XMLHttpRequest, textStatus, errorThrown)
      POP.ShowAlert('出错了，请稍后再试')
    },
    defaultXHRFailedHandler: function(oData) {
      console.log('defaultXHRFailedHandler',oData)
      var msg = oData.msg || '前方拥堵，请稍后再试'
      POP.ShowAlert(msg)
    }
  };
  window.SERVICE = SERVICE;
})(jQuery, POP);
