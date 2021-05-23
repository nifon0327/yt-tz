<div id="uedPagninationBox">
  <div class="sui-pagnination" id="uedPagnination">
    <ul id="numberOfPage">
      <li class="s-dropdown s-dropdown-default">
        <a href="#" class="s-dropdown-label s-dropdown-active">
          <span>每页显示20行</span>
          <i class="s-icon-times-up"></i>
        </a>
        <ul class="s-dropdown-menu hide">
          <li><a href="#" data-num="20">每页显示20行</a></li>
          <li><a href="#" data-num="50">每页显示50行</a></li>
          <li><a href="#" data-num="100">每页显示100行</a></li>
          <li><a href="#" data-num="200">每页显示200行</a></li>
        </ul>
      </li>
    </ul>
    <button type="button" class="sui-pagination-btn sui-pagination-btn-prev" id="btnPrevPagination">
      <i class="s-icon-triangle-left"></i>
    </button>
    <span class="sui-pager--current" id="textActivePaginationIndex">1</span> / <span class="sui-pager--total" id="textTotalPaginationNumber">20</span>
    <button type="button" class="sui-pagination-btn sui-pagination-btn-next" id="btnNextPagination">
      <i class="s-icon-triangle-right"></i>
    </button>
    <span class="sui-d-flex sui-pagination__jump">
            <input type="text" class="sui-pagination__editor" id="goToThisPage">
            <button type="button" class="sui-pagination-btn" id="btnGoWhichPage">跳转</button>
        </span>
  </div>
</div>