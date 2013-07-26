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
 * jQuery.mb.components: jquery.mb.linearGallery
 * version: 1.0
 * © 2001 - 2011 Matteo Bicocchi (pupunzi), Open Lab
 */

// jQuery.support.transition
// to verify that CSS3 transition is supported (or any of its browser-specific implementations)
$.support.transition = (function(){
  var thisBody = document.body || document.documentElement,
    thisStyle = thisBody.style,
    support = thisStyle.transition !== undefined || thisStyle.WebkitTransition !== undefined || thisStyle.MozTransition !== undefined || thisStyle.MsTransition !== undefined || thisStyle.OTransition !== undefined;
  return support;
})();


(function($) {

  $.mbLinearGallery={
    name:"mb.linearGallery",
    author:"Matteo Bicocchi",
    version:"1.0",

    defaults:{
      elements:[], // if empty get children
      thumbPlaceHolder:"#thumbs",
      resizeEnabled:false,
      transitionTime:600,
      imageWrapperWidth:"50%",
      imageMinWidth:550,
      defaultScale:.2,
      defaultOpacity:.4,
      enableZoom:true,
      showDesc:true,
      ease:"cubic-bezier(.35,.21,.71,.46)",
      onStart:function(){},
      onChange:function(){},
      onCreateThumbs:function(){}
    },

    build:function(opt){
      return this.each(function(){
        var gallery=this;
        var $gallery=$(gallery);

        $gallery.children().hide();

        gallery.opt={};
        $.extend(gallery.opt,$.mbLinearGallery.defaults,opt);

        if (gallery.opt.elements.length == 0){
          gallery.opt.elements = $gallery.children();
        }

        var galleryWrapper=$("<div/>").addClass("galleryWrapper");
        $gallery.append(galleryWrapper);

        galleryWrapper.css({
          overflow:"hidden",
          position: "relative",
          whiteSpace: "nowrap",
          "-moz-box-sizing": "border-box",
          verticalAlign: "top"
        });

        for(var i=0; i<= gallery.opt.elements.length-1;i++){
          var newImg=$.mbLinearGallery.buildImage(gallery,gallery.opt.elements[i],i+1);
          galleryWrapper.append(newImg);
          $.mbLinearGallery.buildThumbs(gallery,gallery.opt.elements[i]);
        }

        // Add empty elements at the beginning and at the end.
        galleryWrapper.prepend($.mbLinearGallery.buildImage(gallery));
        galleryWrapper.append($.mbLinearGallery.buildImage(gallery));

        // set the height of the gallery
        galleryWrapper.css({height:$(".galleryWrapper").parent().height()});

        var elements=$(".element",".elementWrapper");
        elements.each(function(i){
          var el = $(this);
          el.css({width:"auto",height:"100%"}).hide();

          function initElements(el) {
            setTimeout(function() {
              if (el.width() >= el.parent().width()) {
                el.css({width:"100%",height:"auto"});
              }
              if(gallery.opt.showDesc)
                $(".imageDesc").css({position:"absolute", left:$("img", el.parent()).position().left, width:$("img", el.parent()).width()});
              el.fadeIn(3000);
            }, 1000);
          }

          if(el.is("img"))
            el.load(function(){
              initElements(el);
            });
          else
            initElements(el);

        });

        $(window).resize(function(){$.mbLinearGallery.refresh(gallery)});

        if(typeof gallery.opt.onCreateThumbs=="function")
          gallery.opt.onCreateThumbs(gallery.opt.thumbPlaceHolder);

        $gallery.goTo(1,false);

        if(typeof gallery.opt.onStart == "function")
          gallery.opt.onStart();

      });
    },

    buildImage:function(gallery, imgObj, idx){
      var elementWrapper= $("<div/>").addClass("elementWrapper");

      elementWrapper.css({
        position: "relative",
        overflow:"hidden",
        width: gallery.opt.imageWrapperWidth,
        minWidth: gallery.opt.imageMinWidth,
        height: "101%",
        display: "inline-block",
        "text-align": "center",
        "-moz-box-sizing": "border-box",
        padding: 0,
        "vertical-align":"top"
      }).click(function(){
          if($(this).children().length>0){
            var idx= $(this).index();
            $(gallery).goTo(idx,true);
          }
        });

      if(typeof imgObj == "object"){
        var url= imgObj.url ? imgObj.url : $(imgObj).attr("src");
        var link= imgObj.link ? imgObj.link : $(imgObj).data("link");
        var element;

        if($(imgObj).data("nozoom") || link)
          elementWrapper.addClass("noZoom");



        if(url){
          element=$("<img>").addClass("galleryImage element").css({position:"relative"});
          element.css({
            margin: "auto",
            cursor: "pointer",
            height: "100%"
          });
          element.attr("src",url);
        }else{
          element=$("<div/>").addClass("galleryBox element").css({position:"relative"});
          element.css({
            margin: "auto",
            cursor: "pointer",
            height: "100%"
          });
          var c= $(imgObj).clone().css({height: $(gallery).height()}).show();
          element.html(c);
        }

        if(idx){
          element.data("index",idx);
        }
        elementWrapper.append(element);

        //custom for Officine Grafiche
        var descIt=$("<span data-lang='it'></span>").html($(imgObj).data("desc-it"));
        var descEn=$("<span data-lang='en'></span>").html($(imgObj).data("desc-en"));

        if(descIt && gallery.opt.showDesc){
          var description=$("<div>").addClass("imageDesc")
            .css({position:"absolute",left:element.position().left, width:elementWrapper.width(), display:"none"}).append(descIt).append(descEn).hide();
          elementWrapper.append(description);
          if(element.data("index")==1){
            description.fadeIn();
          }
        }
        if(link){
          element.addClass("link");
          element.click(function(){
            var idx= element.data("index");
            if(idx == gallery.opt.actualIdx ){
              window.open(link);
            }
          });
        }else if(gallery.opt.enableZoom && !$(imgObj).data("nozoom")){

          var anim;

          element.click(function(){
            var idx= element.data("index");
            if(idx == gallery.opt.actualIdx ){
              var t=gallery.opt.transitionTime/1.5;
              var zoomEl=$(this).clone().addClass("zoomEl");

              if(gallery.opt.showDesc){
                var zoomDesc=$(this).parent().find(".imageDesc").clone().addClass("zoomDesc");
                zoomDesc.css({position:"absolute",left:$(this).offset().left, width:$(this).outerWidth()}).hide();
              }

              var startTop=$(this).offset().top;
              var startLeft=$(this).offset().left;
              var startWidth=$(this).width();
              var startHeight=$(this).height();
              var overlay=$("<div>").addClass("overlay").hide();
              $(overlay).append(zoomEl);

              zoomEl.css({position:"absolute", top:startTop,left:$(this).offset().left, width:$(this).width(), height:$(this).height()});

              if(gallery.opt.showDesc)
                $(overlay).append(zoomDesc);

              $("body").append(overlay);

              var isHorizzontal=zoomEl.height()<zoomEl.width();

              var scale= isHorizzontal ? ($(window).width()*50/zoomEl.width())/100 : ($(window).height()*60/zoomEl.height())/100;

              var elHeight= zoomEl.height()*scale;
              var elWidth= zoomEl.width()*scale;

              var elTop=($(window).height()- elHeight)/2;
              var elLeft=($(window).width()-elWidth)/2;

              overlay.fadeIn(t, function(){
                anim = $.support.transition ? {width:elWidth, height:elHeight, top:elTop, left:elLeft} : {top:elTop};
                zoomEl.CSSAnimate(anim,t/2,gallery.opt.ease,"all");
                if(gallery.opt.showDesc)
                  zoomDesc.fadeIn();
              });
              overlay.one("click",function(){
                if(gallery.opt.showDesc)
                  zoomDesc.fadeOut();
                anim= $.support.transition ? {width:startWidth, height:startHeight, top:startTop, left:startLeft} : {top:startTop};

                zoomEl.CSSAnimate(anim, t/2,gallery.opt.ease,"all", function(){
                  overlay.fadeOut(t/2,function(){overlay.remove()});
                });
              });
            }
          });
        }
      }
      return elementWrapper;
    },

    buildThumbs:function(gallery,imgObj){

      var thumbPlaceHolder= $(gallery.opt.thumbPlaceHolder);
      var thumbURL = imgObj.thumb ? imgObj.thumb : $(imgObj).data("thumb");
      thumbURL = thumbURL ? thumbURL : imgObj.src;
      var thumbTitle=imgObj.title; //.replace(/\|/g,".")
      var thumb=$("<img>").addClass("imgThumb").hide().load(function(){
        thumb.fadeIn(2000);
      }).attr({"src":thumbURL});
      var thumbWrapper= $("<div>").addClass("thumbWrapper");

      thumbWrapper.append(thumb);

      thumbWrapper.click(function(){
        if($(this).find("img").length>0){
          var idx= $(this).index()+1;
          $(gallery).goTo(idx,true);
        }
      });

      thumbWrapper.bind("mouseenter",function(){

        var titleDiv=$("<div>").addClass("thumbTitle");
        titleDiv.html(thumbTitle);
        titleDiv.css({position:"absolute", top:$(this).offset().top+$(this).height(), left:$(this).offset().left});
        $("body").append(titleDiv);
      }).bind("mouseleave",function(){
          $(".thumbTitle").remove();
        });

      thumbPlaceHolder.append(thumbWrapper);
    },

    goTo:function(idx, anim){
      var g= this.get(0);

      if(anim==undefined)
        anim=true;

      if(idx==g.opt.actualIdx && anim)
        return;

      g.opt.actualIdx=idx;

      var gallery=$(".galleryWrapper",g);
      var target= $(".elementWrapper").eq(g.opt.actualIdx);
      var allImages= $(".elementWrapper",g);

      var t=anim? g.opt.transitionTime:0;

      var transitionOut= $.support.transition ? {opacity:g.opt.defaultOpacity, "transform": "scale("+g.opt.defaultScale+")"} : {opacity:g.opt.defaultOpacity};

      allImages.CSSAnimate(transitionOut,t, g.opt.ease,"all",function(){
        allImages.removeClass("sel");
        target.addClass("sel");
        if(g.opt.enableZoom && !target.hasClass("noZoom"))
          target.addClass("zoom");
        allImages.css("z-index",0);
        target.css("z-index",1);
        if(anim)
          $(".imageDesc", $(this)).fadeOut();
      });

      var transitionIn=$.support.transition ? {opacity:1, "transform": "scale(1)"} : {opacity:1};

      target.CSSAnimate(transitionIn,t,g.opt.ease,"all",function(){
        if(anim)
          $(".imageDesc", target).fadeIn();
        $(".imageDesc", target).css({position:"absolute",left:$(".element",target).position().left, width:$(".element",target).width()});
      });

      var thumbContainer=$(g.opt.thumbPlaceHolder);
      $(".thumbWrapper",thumbContainer).removeClass("sel");
      var targetThumb= $(".thumbWrapper",thumbContainer).eq(g.opt.actualIdx-1);
      targetThumb.addClass("sel");

      var scrollLeft = (target.width()*g.opt.actualIdx) - (($(".galleryWrapper",g).outerWidth()-target.outerWidth())/2);

      $(".galleryWrapper",g).animate({scrollLeft:scrollLeft},t,function(){ //"easeOutSine",
        if(typeof g.opt.onChange == "function"){
          g.opt.onChange(g.opt.actualIdx);
        }
      });

    },

    refresh:function(gallery){

      var galleryWrapper= $(".galleryWrapper",$(gallery));
      var elements=$(".element",".elementWrapper");
      var prop = galleryWrapper.height()/ galleryWrapper.width();


      galleryWrapper.css({height:$(gallery).height()});

      elements.each(function(){
        $(this).css({width:"auto",height:"100%"});
        if($(this).width()>=$(this).parent().width()){
          $(this).css({width:"100%",height:"auto"})
        }
      });

      var h= $(".galleryWrapper",$(gallery)).width()*prop;

      if(gallery.opt.resizeEnabled){
        $(".galleryWrapper",$(gallery)).css({height:h});
      }

      $(gallery).goTo(gallery.opt.actualIdx,false);

    }
  };

  $.fn.mbLinearGallery= $.mbLinearGallery.build;
  $.fn.goTo= $.mbLinearGallery.goTo;

})(jQuery);