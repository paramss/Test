/*******************************************************************************
 jquery.mb.components
 Copyright (c) 2001-2011. Matteo Bicocchi (Pupunzi); Open lab srl, Firenze - Italy
 email: mbicocchi@open-lab.com
 site: http://pupunzi.com

 Licences: MIT, GPL
 http://www.opensource.org/licenses/mit-license.php
 http://www.gnu.org/licenses/gpl.html
 ******************************************************************************/

/*
 * jQuery.mb.components: jquery.mb.pagination
 * version: 1.0
 * © 2001 - 2011 Matteo Bicocchi (pupunzi), Open Lab
 */


$.fn.initPagination = function(opt) {

  var container = this.get(0);


  container.opt = {
    elements:null,
    elsPerPage:null,
    navigationID:null,
    showNextPrev:null,
    showIdxList:null,
    showIdx:null,
    maxPage:null,

    nextLabel:"next",
    prevLabel:"prev"
  };

  $.extend(container.opt, opt);

  if (!container.opt.elements)
    return;

  container.opt.elements.hide();

  container.paginationIDX = -1;
  container.pages = [];
  container.totPages = Math.ceil(container.opt.elements.length / container.opt.elsPerPage);

  $(container).buildIndex(container.opt.elements, container.opt.elsPerPage, container.opt.navigationID);
  $(container).paginateNext(container.opt.elements, container.opt.elsPerPage, container.opt.navigationID);
};

$.fn.buildIndex = function(els, n, navID) {
  var container = this.get(0);

  var mx = container.opt.maxPage && container.totPages > container.opt.maxPage ? container.opt.maxPage : container.totPages;
  for (var i = 0; i < mx; i++) {
    container.pages[i] = [];
    for (var x = n * i; x < (n * i) + n; x++) {
      if (els.eq(x).length > 0)
        container.pages[i].push(els.eq(x));
    }
  }

  var nav = $("#" + navID);

  if(container.totPages>1)
    nav.show();

  if (container.opt.showIdxList) {
    nav.find(".navIdx").remove();
    var navIdx = $("<div/>").addClass("navIdx");
    nav.append(navIdx);

    for (var i = 0; i < mx; i++) {
      var idxTemplate = $("<span/>").html(i + 1).addClass("index").attr("idx", i);
      navIdx.append(idxTemplate);
      idxTemplate.bind("click.pagination", function() {
        $(container).paginateIdx(els, $(this).attr("idx"));
      });
    }
  }

  if (container.opt.showNextPrev) {
    nav.find(".navNextPrev").remove();
    var navNextPrev = $("<div/>").addClass("navNextPrev");

    var next = $("<span/>").addClass("next").html(container.opt.nextLabel).bind("click.pagination", function(e) {
      $(container).paginateNext(els);
      e.stopPropagation();
    });

    var prev = $("<span/>").addClass("prev").html(container.opt.prevLabel).bind("click.pagination", function(e) {
      $(container).paginatePrev(els);
      e.stopPropagation();
    });

    navNextPrev.append(prev).append(next).append(idx);
    nav.prepend(navNextPrev);
  }

  if (container.opt.showIdx) {
    var idx = $("<span/>").addClass("idx").html("1&#8211;" + mx);
    navNextPrev.append(idx);
  }

  $(container).manageNav();

};

$.fn.paginateNext = function(els) {
  var container = this.get(0);
  var mx = container.opt.maxPage && container.totPages > container.opt.maxPage ? container.opt.maxPage : container.totPages;

  if (container.paginationIDX >= mx - 1)
    return;

  container.paginationIDX++;
  els.hide();

  var nav = $("#" + container.opt.navigationID);

  var myEls = container.pages[container.paginationIDX];
  for (var x in myEls) {
    myEls[x].fadeIn(100);
  }

  $(container).manageNav();
};

$.fn.paginatePrev = function(els) {
  var container = this.get(0);

  if (container.paginationIDX < 1)
    return;
  container.paginationIDX--;
  els.hide();

  var myEls = container.pages[container.paginationIDX];
  for (var i in myEls) {
    myEls[i].fadeIn(100);
  }

  $(container).manageNav();
};

$.fn.paginateIdx = function(els, idx) {
  var container = this.get(0);

  els.hide();

  container.oldPaginationIDX=container.paginationIDX;
  container.paginationIDX = idx;
  $(container).manageNav();
  var myEls = container.pages[container.paginationIDX];

  for (var i in myEls) {
    myEls[i].show();
  }

  var goToEl=0;
  if(container.oldPaginationIDX>container.paginationIDX)
    goToEl=myEls.length-1;

 // console.debug(container.oldPaginationIDX, container.paginationIDX,goToEl)

  if(container.oldPaginationIDX != container.paginationIDX)
    myEls[goToEl].click();
};

$.fn.getPageFromIndex=function(idx, elsPerPage, changeImg){
  var container = this.get(0);

  if(!elsPerPage)
    elsPerPage = container.opt.elsPerPage;

  var page= Math.floor((idx-1)/elsPerPage);

  if(container.paginationIDX!=page){
    $(".index").eq(page).click();
  }
};

$.fn.manageNav = function() {
  var container = this.get(0);
  var mx = container.opt.maxPage && container.totPages > container.opt.maxPage ? container.opt.maxPage : container.totPages;
  var nav = $("#" + container.opt.navigationID);
  var el = nav.find("[idx]").eq(container.paginationIDX);
  nav.find("[idx]").removeClass("sel");
  el.addClass("sel");

  // if there is just one page than remove navigation's controls
  if (container.totPages <= 1) {
    nav.find(".prev").remove();
    nav.find(".next").remove();
    nav.find(".index").remove();
    return;
  }

  if (container.paginationIDX == 0)
    nav.find(".prev").addClass("disabled");
  else
    nav.find(".prev").removeClass("disabled");

  if (container.paginationIDX == mx - 1)
    nav.find(".next").addClass("disabled");
  else
    nav.find(".next").removeClass("disabled");

  if (container.opt.showIdx)
    nav.find(".idx").html((container.paginationIDX + 1) + "&#8211;" + mx);
};
