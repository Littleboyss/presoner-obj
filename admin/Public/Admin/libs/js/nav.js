$(function(){
    $(".mainNavLogo").click(function(){
        $(".mainNavLeftUl li").removeClass("action");
        $(".iframediv").removeClass("leftIfr").find(".leftMenu").hide();
        $(".iframediv iframe").attr("src", $(this).data('url'));
    });
    $(".mainNavLeftUl").on("click","li",function(){
        $(this).addClass("action").siblings("li").removeClass("action");
        if($(this).hasClass("shouYe")){
            $(".iframediv").removeClass("leftIfr").find(".leftMenu").hide();
        }else{
            $(".iframediv").addClass("leftIfr").find(".leftMenu").show();
        }
        $(".iframediv iframe").attr("src", $(this).data('url'));
    });
    //管理员
    $(".user").hover(function(){
        $(".userBtn").slideDown( 200);
    },function(){
        $(".userBtn").slideUp( 200);
    });
    //vip模块
    $(".vipWrapper li").hover(function(){
        //var n=$(this).index()*96;
        //$(".vipCenter").css("left",-400+n+"px").show();
        $(".vipCenter").show();
    });
    $(".vipSWiperDiv").hover(function(){ },function(){
        $(".vipCenter").hide();
    });
    //左侧栏菜单
   // $('.leftMenu').on("click",".menuTitle",function () {
   //     $('.menuUl').slideUp();
   //     if ($(this).siblings('.menuUl').is(':hidden')) {
   //         $(this).siblings('.menuUl').slideDown(200);
   //     }
   // });
   //  $('.leftMenu').on("click",".menuUl li",function(){
   //      $(this).addClass("action").siblings("li").removeClass("action");
   //  });
    //点任何地方Hide
    //$(document).on("click", function(e){
    //    if($(e.target).hasClass("barName") || $(e.target).hasClass("barNameSpan")){
    //        return false;
    //    }
    //    if(window.parent){
    //        $(".mainNavBarName",window.parent.document).removeClass("action").find(".searchBar").slideUp( 200);
    //    }else {
    //        $(".mainNavBarName").removeClass("action").find(".searchBar").slideUp( 200);
    //    }
    //});
});





























