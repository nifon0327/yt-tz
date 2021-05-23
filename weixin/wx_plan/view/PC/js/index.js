function Pagination(option) {
  function getPath() {
    var pathLength = window.document.location.pathname.split('/').length - 2;
    var path = '';
    for (var i = 0; i < pathLength; i++) {
      path += '../';
    }
    return path;
  }
  $.get('./page.html', function (data) {
    $(option.father).html(data);
    var activeIndex = option.activeIndex || 0;
    var totalPage = option.totalPage || 0;
    var btnPrevPagination = $(option.father + ' #btnPrevPagination');
    var btnNextPagination = $(option.father + ' #btnNextPagination');
    var btnGoWhichPage = $(option.father + ' #btnGoWhichPage');
    var btnChangePageNumber = $(option.father + ' #numberOfPage');
    var textActivePaginationIndex = $(option.father + ' #textActivePaginationIndex');
    var textTotalPaginationNumber = $(option.father + ' #textTotalPaginationNumber');
    var goToThisPage = $(option.father + ' #goToThisPage');
    var numberOfPage = option.numberOfPage || 20;
    (function () {
      var showNumberOfPage = option.showNumberOfPage || false;
      $(option.father + ' #numberOfPage .s-dropdown-active span').html($('#numberOfPage a[data-num=' + numberOfPage + ']').html())
      if (!showNumberOfPage) $(option.father + ' #numberOfPage').hide();
      if (Number(totalPage) < 1) $(option.father).hide();
      else $(option.father).show();
      if (Number(totalPage) < 7) goToThisPage.parent().hide();
      else goToThisPage.parent().show();
      //$(option.father + ' .s-dropdown-active i').removeClass('s-icon-times-down').addClass('s-icon-times-up');
      $(option.father + ' #numberOfPage .s-dropdown-active span').html($(option.father + '[data-num=' + option.numberOfPage + ']').html());
    })();
    function goToPage() {
      var pageVal = goToThisPage.val();
      if (!pageVal) return;
      option.goToPage(activeIndex);
      initPagination(pageVal);
    }
    function initPagination() {
      if (activeIndex === totalPage && activeIndex === 1) {
        btnPrevPagination.hide();
        btnNextPagination.hide();
        //if (numberOfPage > 20) btnChangePageNumber.show();
        //else btnChangePageNumber.hide();
      } else if (activeIndex === 1) {
        btnPrevPagination.hide();
        btnNextPagination.show();
        //btnChangePageNumber.show();
      } else if (activeIndex === totalPage) {
        btnPrevPagination.show();
        btnNextPagination.hide();
        //btnChangePageNumber.show();
      } else {
        btnPrevPagination.show();
        btnNextPagination.show();
        //btnChangePageNumber.show();
      }
      textActivePaginationIndex.html(activeIndex);
      textTotalPaginationNumber.html(totalPage);
    }
    initPagination();
    btnPrevPagination.off('click');
    btnPrevPagination.on('click', function () {
      //initPagination();
      activeIndex--;
      option.goToPage(activeIndex);
    });
    btnNextPagination.off('click');
    btnNextPagination.on('click', function () {
      //initPagination();
      activeIndex++;
      option.goToPage(activeIndex);
    });
    btnGoWhichPage.off('click');
    btnGoWhichPage.on('click', function () {
      goToPage();
    });
    goToThisPage.off('input');
    goToThisPage.on('input', function () {
      goToThisPage.val(goToThisPage.val().replace(/\D/, ''));
      if (goToThisPage.val() > totalPage) goToThisPage.val(totalPage);
      activeIndex = goToThisPage.val();
    });
    goToThisPage.on('keydown', function (event) {
      if (event.keyCode == "13") {
        goToPage();
      }
    });
    $(option.father + ' #numberOfPage .s-dropdown-active').off('click');
    $(option.father + ' #numberOfPage .s-dropdown-active').on('click', function () {
      var dropdownMenu = $(option.father + ' #numberOfPage .s-dropdown-menu');
      if (dropdownMenu.hasClass('hide')) {
        if (document.documentElement.clientHeight < $(this).offset().top + 145) $(option.father + ' .s-dropdown .s-dropdown-menu').css('top', '-145px');
        dropdownMenu.removeClass('hide').addClass('show');
        $(this).find('i').removeClass('s-icon-times-up').addClass('s-icon-times-down');
      } else {
        dropdownMenu.removeClass('show').addClass('hide');
        $(this).find('i').removeClass('s-icon-times-down').addClass('s-icon-times-up');
      }
      return false;
    });
    //$('*:not(' + option.father + ' #numberOfPage .s-dropdown-active)').off('click');
    $('*:not(' + option.father + ' #numberOfPage .s-dropdown-active)').on('click', function () {
      $(option.father + ' #numberOfPage .s-dropdown-menu').removeClass('show').addClass('hide');
      $(option.father + ' #numberOfPage .s-dropdown-active i').removeClass('s-icon-times-down').addClass('s-icon-times-up');
      //return false;
    });
    $(option.father + ' #numberOfPage .s-dropdown-menu a').off('click');
    $(option.father + ' #numberOfPage .s-dropdown-menu a').on('click', function () {
      $(option.father + ' #numberOfPage .s-dropdown-menu').removeClass('show').addClass('hide');
      $(option.father + ' #numberOfPage .s-dropdown-active span').html($(this).html());
      $(option.father + ' .s-dropdown-active i').removeClass('s-icon-times-down').addClass('s-icon-times-up');
      option.changeNumberOfPage($(this).attr('data-num'));
      return false;
    });
  })
}