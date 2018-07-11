// JavaScript Document
$(function() {	
	$('.main').css('height',$('.leftMenu').height());
	$('.xiala .next').click(function(e){  //点击	 
		$(this).next('.xlinne').slideToggle(10).is(":visible"); //显示BOx
		$(this).toggleClass('action') 		//xuanzhuan处理 	
		$(document).one("click", function(){   //点任何地方Hide
		  $(".xlinne").hide();
			console.log(this);
		}); 
		 e.stopPropagation();
	});
	$('.xlinne li').on("click", function(e){     //点下拉内容 设置 并HIde
		$(this).parent().parent().parent().find("input[type='text']").val($(this).text())
		 $(this).parent().parent().parent().find(".xlinne").hide();
		 $(this).parent().parent().parent().find("a.next").toggleClass('action');
		e.stopPropagation();
	});
	
	
	$('.tabtit a').click(function() {
        var $this = $(this);
        var _index = $this.index();

        $('.tabtit a').removeClass('action');
        $this.addClass('action');
		_index = _index/2;
        $('.tabInne').hide().eq(_index).show();
    });
	
	//留言板
	$('.msgContrl .huifu').click(function(){
		$('.msgContrl .txt4').show();
		$('.msgContrl .quxiao').show();
	});
	$('.msgContrl .quxiao').click(function(){
		$('.msgContrl .txt4').hide();
	});
	
	$('.Nhuifu').click(function(){
		$('.msgmask').show();
	});

	$('.popUp .pupcancle').click(function(){
		$('.mask').hide();
	});
	//留言图片
	$('.mess_img_list .item_box').click(function () {
		$(this).siblings().removeClass('check');
		$(this).addClass('check');
        $('.mess_img_box').show();
		var src01 = $(this).find('img').attr('src');
		var src02 = $('.mess_img_box img').attr('src');
		if(src01==src02){
            $(this).removeClass('check');
            $('.mess_img_box').hide();
            $('.mess_img_box img').attr('src','');
		}else {
            $('.mess_img_box img').attr('src',src01);
		}
    })

});
