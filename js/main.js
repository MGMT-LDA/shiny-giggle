var commentsParent	=	'';
var savePage		=	0;
$(document).ready(function() {
	// $(".save-changes").append("<img src='images/home/file.png'/>")
	// $(".searchList").remove();
	// $(".searchList-box").remove();
	// console.log("hello")
	// $("select.select_patent").css("background-image", "url(images/bottom.jpg)");
	var headerHeight	=	jQuery('.header').outerHeight();
	var windowHeight	=	window.innerHeight;
	var footerHeight	=	jQuery('footer.coppywrite-final').outerHeight();
	jQuery('.main-content').css({'height':(windowHeight-headerHeight-footerHeight-1)});
	jQuery('body').css({'height':(windowHeight-headerHeight)});

	$(document).on("click","a[href^='#']",function(e){
	  var href=$(this).attr("href"),target=$(href).parents(".mCustomScrollbar");
	  $('li').removeClass('active');
	  //$(this).parent('li').addClass('active');
	  var_clicked = true;
	  if(target.length){
		e.preventDefault();
		target.mCustomScrollbar("scrollTo",href);
	  }
	});
	$(".backPageSearch").submit(function() {
		$("#frontPageSearch").submit();
	})
	$(".user").click(function() {
		if($(this).hasClass("active")) {
			$(this).removeClass("active");
			$(".login-form-start").hide();
			$(".forget_password_form").hide();
		} else {
			$(".login-form-start").show();
			$(this).addClass("active");
		}
	})
	// $("body").click(function() {
	// 	if($(".user").hasClass("active")) {
	// 		$(".user").removeClass("active");
	// 		$(".login-form-start").hide();
	// 	}
	// })
	$(".forgot_password").click(function() {
		$(".login-form-start").hide();
		$(".forget_password_form").show();
	})
	$("#back_to_login").click(function() {
		$(".login-form-start").show();
		$(".forget_password_form").hide();
	})
	$("#close_btn").click(function() {
		$(".user").removeClass("active");
	})
	$(".togale-claim").click(function() {
		if($(this).hasClass("active")) {
			$(this).removeClass("active");
		} else {
			$(".togale-claim").removeClass("active");
			$(this).addClass("active");
		}
	});
	$("#showPassword").click(function() {
		if($(this).is(":checked")) {
			$("#user_passeord").attr("type", "text");
		} else {
			$("#user_passeord").attr("type", "password");
		}
	});
	if($('.searchList').length > 0)
	{
		$('.searchList').css({'width':'150'});
		$('.main-content').css({'width':$(window).outerWidth()-$('.searchList').outerWidth()-40});
	}
	
	$('body').on("click",".disabled",function(){
		return false;
	});
	// $('select>option:eq(1)').prop('selected', true);
	jQuery(".select_arrow .up_arrow").click(function() {
		var selected_opt = jQuery(".select_patent").val();
		console.log(selected_opt);
		var selected_val = jQuery(".select_patent").find("option[value=" + selected_opt + "]");
		selected_val = selected_val.attr("val");
		console.log(selected_val);
		if(selected_val > 1) {
			selected_val = selected_val - 1;
			jQuery(".select_patent option[val="+ selected_val + "]").prop('selected', true);
		}
	});
	jQuery(".down_arrow").click(function() {
		var selected_opt = jQuery(".select_patent").val();
		var selected_val = jQuery(".select_patent").find("option[value=" + selected_opt + "]");
		selected_val = selected_val.attr("val");
		if(selected_val < 3) {
			selected_val = parseInt(selected_val) + 1;
			jQuery(".select_patent option[val="+ selected_val + "]").prop('selected', true);
		}
	})
    $('body').on('click','.togale-show',function(){
		if(!$(this).hasClass('disabled') && !$(this).hasClass("active"))
		{
			$(this).addClass("active");
			$(".right-side").animate({'width':'0'},50);
			$(".left-side").animate({'width':'99%'},50);
			$(".left-side").css({'border-right':'none'});
			$(this).find("img").attr("src", "images/minimize_screen.png");
		} else {
			$(this).removeClass("active");
			$(".right-side").animate({'width':'50%'},50);
			$(".left-side").animate({'width':'50%'},50);
			$(".left-side").css({'border-right':'none'});
			$(this).find("img").attr("src", "images/togal-2.png");
		}
    });
   /*  $('body').on('click','.togale-hide',function(){
		if(!$(this).hasClass('disabled'))
		{alert('here');
			$(".right-side").animate({'width':'49%'},50);
			$(".left-side").animate({'width':'49%'},50);
			$(".left-side").css({'border-right':'1px solid #c2c2c2'});
		}
    }); */
	// $('#frontPageSearch').on('submit',function() {
	// 	var value = $("#search-input").val();
	// 	console.log(value);
    //     if (/\s/.test(value)) {
	// 		console.log("value");
	// 		console.log(value);
    //         value = value.split(" ");
    //         $(".searchList").append("<div class='patent_id'></div>");
    //         value.forEach(function(val) {
    //             $(".patent_id").append("<label for='" + val + "'>" + val + "</label><input class='patent_val' type='radio' name='radio' id='" + val + "'/>");
    //         })
    //     }
	// });
	// $("body").on("click", ".patent_val", function() {
	// 	$("#search-input").val($(this).attr("id"));
	// 	$("#frontPageSearch").click();
	// });
	var search_value = $("#search-input").attr("value");
	var clickable_radio = $(".searchList-box ul li").find("input[id=" + search_value + "]");
	clickable_radio.click();
	$(".searchList-box ul li input:radio").click(function() {
		$(this).next("a").click();
	})
	$(".searchList-box ul li a").click(function() {
		console.log("clikckcck");
		// $(this).next("a").click();
		// $(this).prev("input:radio").attr('checked', 'checked');
		$(this).prev("input:radio").prop("checked",true);
	})
		$(".togale-pdf").click(function(){
			$(".right-side").animate({'width':'97%'},50);
			$(".left-side").animate({'width':'0'},50);
			$(".left-side").css({'border-right':'none'});
		});
	
	$('body').on('click','.patentFamilies',function(){
		$(this).parent().toggleClass('active');
		$('.patentFamiliesList').toggleClass('hide');
		return false;
	});
	
	$('body').on('click','.searchList a',function(){
		var link	=	$(this).attr('href');
		var patentNumber	=	$(this).html();
		$.ajax({
			url: link,
			type: 'get',
			success: function(response)
			{
				window.history.pushState({url: "" + link + ""},"", link);
				$('#selectContainerTop').replaceWith($($.parseHTML(response)).find('#selectContainerTop'));
				$('#contentBody').replaceWith($($.parseHTML(response)).filter('#contentBody'));
				$('.searchList').css({'width':'150'});
				$('.main-content').css({'width':$(window).outerWidth()-$('.searchList').outerWidth()-40});
				$('#search-input').val(patentNumber);
			}
			
		});
		return false;
	});
	
	var searchListWidth	=	parseInt(jQuery('.searchList').outerWidth())-35;
	if(isNaN(searchListWidth))

		searchListWidth	=	0;
	$('body').on('click','.toggleSearchList',function() {
		var leftDiv			=	$('.content-section-container.active > .left-side:first-child');
		var lineSeperator	=	$('.content-section-container.active > .lineSeperator');
		var lineSeperator2	=	$('.content-section-container.active > .lineSeperator-group');
		leftDivWidth		=	leftDiv.width();

		$(this).parents('.searchList').eq(0).toggleClass('toggleFilter');
		if($(this).parents('.searchList').eq(0).hasClass('toggleFilter'))
		{
			$('.searchList').animate({'width':35},0);
			$('.main-content').animate({'width':$('.main-content').width()+searchListWidth},0);
			$('.searchList  ul').hide();
			leftDiv.css({'width':leftDivWidth+searchListWidth});
			
			
			if($('.content-section-container.active').hasClass('content-section-3'))
			{
				$('#lineSeperator3').css({'left':leftDivWidth+searchListWidth});
				$('#lineSeperator4').css({'left':$('#lineSeperator4').position().left+searchListWidth});
			}
			else
			{
				$('.lineSeperator').css({'left':leftDivWidth+searchListWidth});
			}
		}
		else
		{
			$('.searchList').animate({'width':150},0);
			$('.main-content').animate({'width':$('.main-content').width()-searchListWidth},0);
			$('.searchList  ul').show();
			leftDiv.css({'width':leftDivWidth-searchListWidth});
			if($('.content-section-container.active').hasClass('content-section-3'))
			{
				$('#lineSeperator3').css({'left':leftDivWidth-searchListWidth});
				$('#lineSeperator4').css({'left':$('#lineSeperator4').position().left-searchListWidth});
			}
			else
			{
				$('.lineSeperator').css({'left':leftDivWidth-searchListWidth});
			}
		}
		
		setCommentIcons();
	});
	
	$('body').on('click', '.patentFamiliesList a', function(){
		window.open($(this).attr('href'), '_blank');
		return false;
	});
	
	initiateContentsDrag();
	loginLogoutUser();
	forgotPasswordForm();
	registerUser();
	resetPassword();
	saveUserPatent();
	addColorPicker();
	addCommentIcons();
	$('.bg-overlay, .close-popup').click(function(){
		$('.bg-overlay').hide();
		$('embed').show();
		$('.forgot-pwd-form-container').hide();
		$('.login-form-container').hide();
		$('.register-form-container').hide();
		$('.message-box-container').hide();
	});
	
	/* $('.bottom-left').scroll(function(){
		$('.comment-icon').each(function(){
			setCommentIconPosition($(this).attr('id').split('-')[2]);
		});
	}); */
	$('.bottom-left, .right-side').bind( 'scroll', function(){
		$('.comment-icon').each(function(){
			setCommentIconPosition($(this).attr('id').split('-')[2]);
		});
	});
	
	if($('.highlightedText').length > 0)
		$('#user-options').prepend('<a class="remove-all-highlighting">Remove Highlighting</a>');
	
	var divPositionArray	=	new Array();
	jQuery('.scrollTags').each(function(){
		divPositionArray[jQuery(this).attr('href')]	= jQuery('#content-2').find(jQuery(this).attr('href')).position().top;
	});
	jQuery('body').on('click','.scrollTags', function() {
		console.log("accc");
		jQuery('#content-2').scrollTop(divPositionArray[jQuery(this).attr('href')]);
		return false;
	});
	
	jQuery('body').on('mouseover','.context-menu-item',function(){
		var text	=	jQuery(this).html();
		if(jQuery(this).hasClass('context-menu-icon-color'))
			text	=	'Highlighting Colors';
		jQuery('body').append('<span class="custom-tooltip">'+text+'</span>');
		jQuery('.custom-tooltip').css({'display':'block','position':'absolute','top':jQuery(this).offset().top+40,'left':jQuery(this).offset().left,'padding':'4px 10px','border-radius':'5px', 'z-index':99,'color':'#ffffff','background':'#343131'});
	});
	
	jQuery('body').on('mouseout','.context-menu-item',function(){
		jQuery('.custom-tooltip').remove();
	});
});


var compareFlag1	=	0;
var compareFlag2	=	0;
$('body').on('click','#compare',function() {
	compareFlag2	=	0;
	$('.togale-claim > a').removeClass('active');
	if(compareFlag1 == 0)
	{
		$(this).addClass('active');
		$(".content-section-2.hide.content-section-container .right-side").css("height", "790px");
		$('.content-section-2').addClass('active').show();
		$('.content-section').removeClass('active').hide();
		$('.content-section-3').removeClass('active').hide();
		$('.togale-hide').trigger('click');
		$('.right-sec li a').not('.togale-claim > a').addClass('disabled');
		$('.togale-hide').addClass('disabled');
		$('.togale-show').addClass('disabled');
		
		var divWidth	=	$('.content-section-2').width()/2 - 10;
		$('.content-section-2 .left-side, .content-section-2 .right-side').css({'width':divWidth});
		compareFlag1++;
	}
	else
	{
		$('.togale-claim > a').removeClass('active');
		$('.content-section-2').removeClass('active').hide();
		$('.content-section-3').removeClass('active').hide();
		
		$('.content-section').addClass('active').show();
		$('.togale-hide').trigger('click');
		$('.right-sec li a').removeClass('disabled');
		$('.togale-hide').removeClass('disabled');
		$('.togale-show').removeClass('disabled');
		compareFlag1 = 0;
		
		var divWidth	=	$('.content-section').width()/2 - 10;
		$('.content-section .left-side, .content-section .right-side').css({'width':divWidth});
	}
	$('.lineSeperator').css({'left':divWidth});
	// $('.top-div .top-left').css({'width':'702px'});
	$('.top-div').removeClass('collapse-top-div');
	
	setDivHeight();
	setCommentIcons();
});

$('body').on('click','#compare2',function(){
	compareFlag1	=	0;
	$('.togale-claim > a').removeClass('active');
	if(compareFlag2 == 0)
	{
		$(this).addClass('active');
		$('.content-section-3').addClass('active').show();
		
		$('.content-section').removeClass('active').hide();
		$('.content-section-2').removeClass('active').hide();
		$('.right-sec li a').not('.togale-claim > a').addClass('disabled');
		$('.togale-hide').addClass('disabled');
		$('.togale-show').addClass('disabled');
		var divWidth2	=	$('.content-section-3').width()/3 - 10;
		$('.content-section-3 .left-side, .content-section-3 .right-side').css({'width':divWidth2});
		compareFlag2++;
	}
	else
	{
		$('.content-section-2').removeClass('active').hide();
		$('.content-section-3').removeClass('active').hide();
		
		$('.content-section').addClass('active').show();
		$('.togale-hide').trigger('click');
		$('.right-sec li a').removeClass('disabled');
		$('.togale-hide').removeClass('disabled');
		$('.togale-show').removeClass('disabled');
		compareFlag2 = 0;
		var divWidth	=	$('.content-section').width()/2 - 10;
		$('.content-section .left-side, .content-section .right-side').css({'width':divWidth});
	}
	$('#lineSeperator3').css({'left':divWidth2});
	$('#lineSeperator4').css({'left':(divWidth2*2)});
	// $('.top-div .top-left').css({'width':'702px'});
	$('.top-div').removeClass('collapse-top-div');
	
	setTimeout(setDivHeight(),1);
	setCommentIcons();
});


function initiateContentsDrag()
{
	var dragDistance	=	300;
	var topNavMaxWidth	=	$('.top-left').outerWidth();
	$( ".lineSeperator" ).draggable({
		containment: "parent",
		drag: function( event, ui){
			ui.position.left = Math.min( ui.position.left,  ui.helper.next().offset().left + ui.helper.next().width()-dragDistance);
			ui.position.left = Math.max(ui.position.left, ui.helper.prev().offset().left + dragDistance);
			var searchListWidth	=	parseInt(jQuery('.searchList').outerWidth());
			if(isNaN(searchListWidth))
				searchListWidth	=	0;
			var parent		=	$(this).parents('.content-section-container').eq(0);
			var posLeft		=	$(this).offset().left-searchListWidth-18;
			var parentWidth	=	parent.outerWidth();
			parent.find('.left-side').css({'width':posLeft});
			if(topNavMaxWidth > posLeft)
				$('#contentBody .top-left').css({'width':posLeft});
			if(posLeft > 665)
				$('.top-div').removeClass('collapse-top-div');
			else
				$('.top-div').addClass('collapse-top-div');
			
			parent.find('.right-side').css({'width':(parentWidth-posLeft-15)});
			setDivHeight();
		},
		stop: function(event, ui){
			ui.position.left = Math.min( ui.position.left,  ui.helper.next().offset().left + ui.helper.next().width()-dragDistance);
			ui.position.left = Math.max(ui.position.left, ui.helper.prev().offset().left + dragDistance);
			
			var searchListWidth	=	parseInt(jQuery('.searchList').outerWidth());
			if(isNaN(searchListWidth))
				searchListWidth	=	0;
			var parent		=	$(this).parents('.content-section').eq(0);
			var posLeft		=	$(this).offset().left-searchListWidth-18;
			var parentWidth	=	parent.outerWidth();
			parent.find('.left-side').css({'width':posLeft});
			if(topNavMaxWidth > posLeft)
				$('#contentBody .top-left').css({'width':posLeft});
			if(posLeft > 665)
				$('.top-div').removeClass('collapse-top-div');
			else
				$('.top-div').addClass('collapse-top-div');
			parent.find('.right-side').css({'width':(parentWidth-posLeft-15)});
			setDivHeight();
		}
	});
	
	
			
	var widthContainer1 = 0;
	var widthContainer2 = 0;
	
	$( "#lineSeperator3" ).draggable({
		containment: "parent",
		drag: function( event, ui){			
			ui.position.left = Math.min( ui.position.left,  ui.helper.next().offset().left + ui.helper.next().width()-dragDistance);
			ui.position.left = Math.max(ui.position.left, ui.helper.prev().offset().left + dragDistance);
			var searchListWidth	=	parseInt(jQuery('.searchList').outerWidth());
			if(isNaN(searchListWidth))
				searchListWidth	=	0;
	
			if(widthContainer1 == 0)
			{
				var descWidth	=	$('#description1').outerWidth();
				var claimWidth	=	$('#claim1').outerWidth();
				widthContainer1	=	descWidth+claimWidth;
			}
			
			var parent		=	$(this).parents('.content-section-container').eq(0);
			var posLeft		=	$(this).offset().left-searchListWidth-18;
			
			parent.find('#claim1').css({'width':posLeft});
						
			parent.find('#description1').css({'width':(widthContainer1-posLeft-15)});
			setDivHeight();
		},
		stop: function( event, ui){			
			ui.position.left = Math.min( ui.position.left,  ui.helper.next().offset().left + ui.helper.next().width()-dragDistance);
			ui.position.left = Math.max(ui.position.left, ui.helper.prev().offset().left + dragDistance);
			var searchListWidth	=	parseInt(jQuery('.searchList').outerWidth());
			if(isNaN(searchListWidth))
				searchListWidth	=	0;
			if(widthContainer1 == 0)
			{
				var descWidth	=	$('#description1').outerWidth();
				var claimWidth	=	$('#claim1').outerWidth();
				widthContainer1	=	descWidth+claimWidth;
			}
			
			var parent		=	$(this).parents('.content-section-container').eq(0);
			var posLeft		=	$(this).offset().left-searchListWidth-18;
			
			parent.find('#claim1').css({'width':posLeft});
						
			parent.find('#description1').css({'width':(widthContainer1-posLeft-15)});
			
			var claimWidth	=	$('#claim1').outerWidth();
			var descWidth	=	$('#description1').outerWidth();
			var pdfWidth	=	$('#pdf1').outerWidth();
			widthContainer1	=	descWidth+claimWidth;
			widthContainer2	=	descWidth+pdfWidth;
			
			if(topNavMaxWidth > widthContainer1)
				$('#contentBody .top-left').css({'width':widthContainer1});
			else
				$('#contentBody .top-left').css({'width':topNavMaxWidth});
			
			if(widthContainer1 > 665)
				$('.top-div').removeClass('collapse-top-div');
			else
				$('.top-div').addClass('collapse-top-div');
			setDivHeight();
		}
	});
	$( "#lineSeperator4" ).draggable({
		containment: "parent",
		drag: function( event, ui){
			ui.position.left = Math.min( ui.position.left,  ui.helper.next().offset().left + ui.helper.next().width()-dragDistance);
			ui.position.left = Math.max(ui.position.left, ui.helper.prev().offset().left + dragDistance);
			
			var searchListWidth	=	0;
			
			if(widthContainer2 == 0)
			{
				var descWidth	=	$('#description1').outerWidth();
				var pdfWidth	=	$('#pdf1').outerWidth();
				widthContainer2	=	descWidth+pdfWidth;
			}
			
			var parent		=	$(this).parents('.content-section-container').eq(0);
				
			var posLeft		=	$(this).offset().left-($('#lineSeperator3').offset().left)-searchListWidth-10;
			parent.find('#description1').css({'width':posLeft});						
			parent.find('#pdf1').css({'width':(widthContainer2-posLeft-25)});
			
			posLeft			=	$(this).offset().left-10;
			if(topNavMaxWidth > posLeft)
				$('#contentBody .top-left').css({'width':posLeft});
			if(posLeft > 665)
				$('.top-div').removeClass('collapse-top-div');
			else
				$('.top-div').addClass('collapse-top-div');
			setDivHeight();
		},
		stop: function( event, ui){
			ui.position.left = Math.min( ui.position.left,  ui.helper.next().offset().left + ui.helper.next().width()-dragDistance);
			ui.position.left = Math.max(ui.position.left, ui.helper.prev().offset().left + dragDistance);
			var searchListWidth	=	0;
			
			if(widthContainer2 == 0)
			{
				var descWidth	=	$('#description1').outerWidth();
				var pdfWidth	=	$('#pdf1').outerWidth();
				widthContainer2	=	descWidth+pdfWidth;
			}
			
			var parent		=	$(this).parents('.content-section-container').eq(0);
				
			var posLeft		=	$(this).offset().left-($('#lineSeperator3').offset().left)-searchListWidth-10;
			parent.find('#description1').css({'width':posLeft});						
			parent.find('#pdf1').css({'width':(widthContainer2-posLeft-25)});
			
			var claimWidth	=	$('#claim1').outerWidth();
			var descWidth	=	$('#description1').outerWidth();
			var pdfWidth	=	$('#pdf1').outerWidth();
			widthContainer1	=	descWidth+claimWidth;
			widthContainer2	=	descWidth+pdfWidth;
			
			if(topNavMaxWidth > widthContainer1)
				$('#contentBody .top-left').css({'width':widthContainer1});
			else
				$('#contentBody .top-left').css({'width':topNavMaxWidth});
			
			if(widthContainer1 > 665)
				$('.top-div').removeClass('collapse-top-div');
			else
				$('.top-div').addClass('collapse-top-div');
			
			setDivHeight();
		}
	});
	
	var divWidth	=	$('.content-section').width()/2 - 10;
	$('.content-section .left-side, .content-section .right-side').css({'width':divWidth});
}


function setDivHeight()
{
	var bodyHeight		=	$('body').outerHeight();
	var topDivHeight	=	$('.top-div').outerHeight();
	$('.left-side').css({'height':(bodyHeight-topDivHeight+35)});
	setCommentIcons();
}



$('body').on("click",".main-content", function (e) {
    var selected = window.getSelection();
	
	$('.markedText.no-comments:not(.fade,.underline,.isHighlighted)').each(function(){
		$(this).parents('.markedText').eq(0).find('.highlightComments').remove();
		$(this).find('.highlightComments').remove();
		$(this).replaceWith($(this).html());
	});
	if(selected.rangeCount >= 1)
	{
		var range = selected.getRangeAt(0);
		
		if(selected.toString().length > 1){
			var newNode = document.createElement("span");
			newNode.setAttribute("class", "markedText no-comments");
			range.surroundContents(newNode);

			if($('#user-options').find('.save-changes').length == 0)
			{
				$('#user-options').prepend('<a class="save-changes"><img src="images/home/file.png"/></a>');
				$('#user-options').prepend('<a class="remove-all-highlighting">Remove Highlighting</a>');
			}
		}
		selected.removeAllRanges();
	}
 });

 $('body').on("click",".main-content .markedText", function (e) {
    return false;
 });
  $.contextMenu({
            selector: '.markedText.no-comments', 
            callback: function(key, options) {
                if(key == 'remove')
				{
					$('.markedText.context-menu-active').parents('.markedText').eq(0).find('.highlightComments').remove();
					$('.markedText.context-menu-active').find('.highlightComments').remove();
					$('.markedText.context-menu-active').parents('.markedText').eq(0).replaceWith($('.markedText.context-menu-active').parents('.markedText').eq(0).html());
					$('.markedText.context-menu-active').replaceWith($('.markedText.context-menu-active').html());
				}
				else if(key == 'add_color')
				{
					return false;
				}
				else if(key == 'add_comment')
				{
					var activeDiv	=	$('.markedText.context-menu-active');
					commentsParent	=	activeDiv;
					activeDiv.addClass('isHighlighted highlightedText');
					$('.commentPopup').remove();
					$('body').append('<div class="commentPopup"><textarea></textarea><div class="action-panel"><a class="saveComments">Add</a><a class="cancelComments">Cancel</a></div></div>');
					$('.commentPopup').css({'position':'absolute','top':activeDiv.offset().top+activeDiv.outerHeight()+10,'left':activeDiv.offset().left});
				}
				else if(key == 'underline_text')
				{
					var activeDiv	=	$('.markedText.context-menu-active');
					commentsParent	=	activeDiv;
					activeDiv.addClass('isHighlighted');
					$('.commentPopup').remove();
					activeDiv.addClass('underline');
				}
				else if(key == 'fade_text')
				{
					var activeDiv	=	$('.markedText.context-menu-active');
					commentsParent	=	activeDiv;
					activeDiv.addClass('isHighlighted');
					$('.commentPopup').remove();
					activeDiv.addClass('fade');
				}
				else if(key == 'copy_text')
				{
					var activeDiv	=	$('.markedText.context-menu-active');
					commentsParent	=	activeDiv;
					$('.commentPopup').remove(activeDiv.html());
					$('.markedText').attr('id','');
					activeDiv.attr('id','copy-target');
					var clipboard = new Clipboard('.context-menu-icon-copy', {
						target: function() {
							return document.querySelector('#copy-target');
						}

					});
					clipboard.on('success', function(e) {
						activeDiv.html(activeDiv.html());
					});
					
				}
            },
            items: {
				name: {
					name: "add_color", 
					type: 'text', 
					value: "Add Color", 
					icon: "color",
					events: {
						
					}
				},
                "underline_text": {name: "Underline Text", icon: "underline"},
                "fade_text": {name: "Fade Text", icon: "fade"},
                "copy_text": {name: "Copy Text", icon: "copy"},
                "add_comment": {name: "Add Comments", icon: "comment"},
                "remove": {name: "Remove Highlighting", icon: "remove"}
            }
        }); 
		
		$.contextMenu({
            selector: '.markedText.commented', 
            callback: function(key, options) {
                if(key == 'remove')
				{
					$('.markedText.context-menu-active').find('.highlightComments').each(function(){
						$('#comment-icon-'+$(this).attr('id').split('-')[1]).remove()
						$(this).remove();
					});
					$('.markedText.context-menu-active').parents('.markedText').eq(0).find('.highlightComments').remove();
					$('.markedText.context-menu-active').parents('.markedText').eq(0).replaceWith($('.markedText.context-menu-active').parents('.markedText').eq(0).html());
					$('.markedText.context-menu-active').replaceWith($('.markedText.context-menu-active').html());
				}
				else if(key == 'add_color')
				{
					return false;
				}
				else if(key == 'underline_text')
				{
					var activeDiv	=	$('.markedText.context-menu-active');
					commentsParent	=	activeDiv;
					activeDiv.addClass('isHighlighted');
					$('.commentPopup').remove();
					activeDiv.addClass('underline');
				}
				else if(key == 'fade_text')
				{
					var activeDiv	=	$('.markedText.context-menu-active');
					commentsParent	=	activeDiv;
					activeDiv.addClass('isHighlighted');
					$('.commentPopup').remove();
					activeDiv.addClass('fade');
				}
				else if(key == 'copy_text')
				{
					var activeDiv	=	$('.markedText.context-menu-active');
					commentsParent	=	activeDiv;
					$('.commentPopup').remove(activeDiv.html());
					$('.markedText').attr('id','');
					activeDiv.attr('id','copy-target');
					var clipboard = new Clipboard('.context-menu-icon-copy', {
						target: function() {
							return document.querySelector('#copy-target');
						}

					});
					clipboard.on('success', function(e) {
						activeDiv.html(activeDiv.html());
					});
				}
            },
            items: {
                "add_color": {name: "Add Color", icon: "color"},
                "underline_text": {name: "Underline Text", icon: "underline"},
                "fade_text": {name: "Fade Text", icon: "fade"},
                "copy_text": {name: "Copy Text", icon: "copy"},
                "remove": {name: "Remove Highlighting", icon: "remove"}
            }
        });

$('body').on('click','.cancelComments',function(){
	$('.commentPopup').remove();
});

$('body').on('click','.remove-all-highlighting',function(){
	if(confirm("Are you sure you want to remove all the highlighting and comments from the document?"))
	{
		$('.highlightComments').remove();
		$('.comment-icon').remove();
		$('.markedText').each(function(){
			$(this).replaceWith($(this).text());
		});
		$('.remove-all-highlighting').remove();
	}
});

$('body').on('click','.saveComments',function(){
	if(commentsParent == '')
		alert('Unable to add your comments. Please contact site admin.');
	else
	{
		var idCount	=	$('.highlightComments').length + 1;
		
		for(i=0;i<=idCount;i++)
		{
			if($("#comment-"+i).length == 0)
				idCount	=	i;
		}
		
		commentsParent.find('.highlightComments').remove();
		commentsParent.append('<span class="highlightComments" id="comment-'+idCount+'" style="display:none;" contentEditable="true">'+nl2br($('.commentPopup').find('textarea').val())+'</span>').removeClass('no-comments').addClass('commented highlightedText');
		// commentsParent.find('.highlightComments').show();
		$('.commentPopup').remove();
		$('body').append('<div class="comment-icon" id="comment-icon-'+idCount+'"></div>');
		setCommentIconPosition(idCount);
	}
});

$('body').on('click','.comment-icon',function(){
	var idCount	=	$(this).attr('id').split('-')[2];
	if($('#comment-'+idCount).hasClass('visible'))
		$('#comment-'+idCount).css({'display':'none'}).removeClass('visible');
	else
		$('#comment-'+idCount).css({'display':'block','top':($('#comment'+idCount).parent('.markedText').outerHeight())}).addClass('visible');
});

/* $('body').on('mouseout','.markedText.commented:not(.highlightComments)',function(event){
	$(this).find('.highlightComments').hide();
}); */

function nl2br (str, is_xhtml)
{
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function loginLogoutUser()
{
	$('body').on('click','.login-link',function(){
		$('embed').hide();
		$('.login-form-container').show();
		$('.bg-overlay').show();
	});
	
	$('body').on('click','.already-registered-now-link',function(){
		$('embed').hide();
		$('.forgot-pwd-form-container').hide();
		$('.register-form-container').hide();
		$('.login-form-container').show();
		$('.bg-overlay').show();
	});
	
	$('body').on('click','.login-now-link',function(){
		$('.close-popup').trigger('click');
		$('.login-link').trigger('click');
	});
	
	$('body').on('click','.logout-user',function(){
		$.ajax({
			type: "POST",
			url: "ajax/manageUsers.php",
			data: {'action':'logout'},
			cache: false,
			dataType:'json',
			success: function(response)
			{
				if(response.status == 'ok')
				{
					window.location.reload();
					$('#user-options').html('<a class="login-link">Login</a><a class="register-link">Register</a>');
					reloadPageContent();
					$('.comment-icon').remove();
				}
			} 
		});
	});
	
	// $('.login-form').on('submit',function(){
		
	// 	var errorCounter	=	0;
	// 	var testEmail		=	/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
	// 	var email			=	$('.login-form').find('input[name=email]');
	// 	var password		=	$('.login-form').find('input[name=password]');
		
		
	// 	if(email.val().trim() == '')
	// 	{
	// 		email.parent('.input-container').find('.error').html('Please enter email address').show();
	// 		errorCounter++;
	// 	}
	// 	else if(!testEmail.test(email.val()))
	// 	{
	// 		email.parent('.input-container').find('.error').html('Please enter a valid email address').show();
	// 		errorCounter++;
	// 	}
	// 	else
	// 		email.parent('.input-container').find('.error').html('').hide();
		
	// 	if(password.val().trim() == '')
	// 	{
	// 		password.parent('.input-container').find('.error').html('Please enter password').show();
	// 		errorCounter++;
	// 	}
	// 	else
	// 		password.parent('.input-container').find('.error').html('').show();
		
	// 	if(errorCounter == 0)
	// 	{
	// 		$.ajax({
	// 			type: "POST",
	// 			url: "ajax/manageUsers.php",
	// 			data: $(this).serialize()+'&action=login',
	// 			cache: false,
	// 			dataType:'json',
	// 			success: function(response)
	// 			{
	// 				if(response.status == 'ok')
	// 				{
	// 					if(savePage == 1)
	// 					{
	// 						$('.save-changes').trigger('click');
	// 					}
						
	// 					$('#user-options').html('<a class="save-changes">Save</a><a class="logout-user">Logout</a>');
	// 					reloadPageContent();
	// 					$('.login-form-container').hide();
	// 					$('.message-box').html(response.message);
	// 					$('.message-box-container').show();
	// 					hideMessagePopUp();
	// 				}
	// 				else if(response.status == 'error')
	// 				{
	// 					$('.login-form-container .error-box').html(response.message);
	// 				}
	// 			} 
	// 		});
	// 	}
	// 	return false;
	// });
}
function forgotPasswordForm()
{
	$('body').on('click','.forgot-password-link',function(){
		$('embed').hide();
		$('.login-form-container').hide();
		$('.forgot-pwd-form-container').show();
	});
	
	
	
	
	$('.forgot-pwd-form').on('submit',function() {
		console.log("forgot-pwd");
		
		var errorCounter	=	0;
		var testEmail		=	/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		// var email			=	$('.forgot-pwd-form').find('input[name=email]');
		var email = $(this).find("div input");
		console.log(email.val());
		var password		=	$('.forgot-pwd-form').find('input[name=password]');
		
		
		if(email.val().trim() == '')
		{
			email.parent('.input-container').find('.error').html('Please enter email address').show();
			errorCounter++;
		}
		else if(!testEmail.test(email.val()))
		{
			email.parent('.input-container').find('.error').html('Please enter a valid email address').show();
			errorCounter++;
		}
		else
			email.parent('.input-container').find('.error').html('').hide();
		
		if(errorCounter == 0)
		{
			$.ajax({
				type: "POST",
				url: "ajax/manageUsers.php",
				data: $(this).serialize()+'&action=forgotPassword',
				cache: false,
				dataType:'json',
				complete: function(response)
				{
					console.log(response);
					response['responseText'] = response['responseText'].split("{");
					response['responseText'] = response['responseText'][1];
					response['responseText'] = "{" + response['responseText'];
					var res = $.parseJSON(response['responseText']);
					if(res.status == 'ok')
					{
						// $('.message-box').html(res.message);
						$('.forgot-pwd-form').find(".error-box").html(res.message).show();
						// $('.forgot-pwd-form-container').hide();
						// $('.message-box-container').show();
					}
					else if(res.status == 'error')
					{
						$('.forgot-pwd-form').find(".error-box").html(res.message).show();
						// $('.forget_password_form').find(".error").html(response.message);
					} else {
						console.log("some error occured");
						$('.forgot-pwd-form-container .error-box').html(res.message);
					}
				} 
			});
		}
		return false;
	});
}

function registerUser()
{
	$('body').on('click','.register-link',function(){
		$(".login-form-start").hide();
		$('embed').hide();
		$('.register-form-container').show();
		$('.bg-overlay').show();
	});
	
	$('body').on('click','.register-now-link',function(){
		$('.close-popup').trigger('click');
		$('.register-link').trigger('click');
	});
	

	$('.register-form').on('submit',function() {
		var errorCounter	=	"0";
		var testEmail		=	/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		var firstName		=	$('#firstName');
		var lastName		=	$('#lastName');
		var email			=	$('#email');
		var password		=	$('#password');
		var confirmPassword	=	$('#confirmPassword');
		
		if(firstName.val().trim() == '')
		{	
			firstName.parent('.input-container').find('.error').html('Please enter first name').show();
			errorCounter++;
		}
		else
			firstName.parent('.input-container').find('.error').html('').hide();
		
		if(lastName.val().trim() == '')
		{
			lastName.parent('.input-container').find('.error').html('Please enter last name').show();
			errorCounter++;
		}
		else
			lastName.parent('.input-container').find('.error').html('').hide();
		
		if(email.val().trim() == '')
		{
			email.parent('.input-container').find('.error').html('Please enter email address').show();
			errorCounter++;
		}
		else if(!testEmail.test(email.val()))
		{
			email.parent('.input-container').find('.error').html('Please enter valid email address').show();
			errorCounter++;
		}
		else
			email.parent('.input-container').find('.error').html('').hide();
		
		if(password.val().trim() == '')
		{
			password.parent('.input-container').find('.error').html('Please enter password').show();
			errorCounter++;
		}
		else
			password.parent('.input-container').find('.error').html('').hide();
		
		if(confirmPassword.val().trim() == '')
		{
			confirmPassword.parent('.input-container').find('.error').html('Please re-enter password').show();
			errorCounter++;
		}
		else if (password.val().trim() != confirmPassword.val().trim())
		{
			confirmPassword.parent('.input-container').find('.error').html('Password mismatch').show();
			errorCounter++;
		}
		else
			confirmPassword.parent('.input-container').find('.error').html('').hide();
		
		
		if(errorCounter == "0")
		{
			$.ajax({
				type: "POST",
				url: "ajax/manageUsers.php",
				data: $(this).serialize()+'&action=register',
				cache: false,
				dataType:'json',
				complete: function(response)
				{
					console.log(response);
					var res = $.parseJSON(response['responseText']);
					if(res.status == 'ok')
					{
						console.log("comeing");
						if(savePage == 1)
						{
							$('.save-changes').trigger('click');
						}
						$('#user-options').html('<a class="save-changes">Save</a><a class="logout-user">Logout</a>');
						reloadPageContent();
						$('.login-form-start').hide();
						$('.message-box').html(res.message);
						$('.message-box-container').show();
						window.location.reload();
						
						hideMessagePopUp();
					}
					else if(res.status == 'error')
					{
						$('.register-form .error-box').html(res.message).show();
					}
				} 
			});
		}
		return false;
	});
}

function resetPassword()
{
	$('.resetPassword').on('submit',function(){
		
		var errorCounter	=	0;
		var testEmail		=	/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		var email			=	$('.resetPassword').find('input[name=email]');
		var password		=	$('.resetPassword').find('input[name=password]');
		var confirmPassword	=	$('.resetPassword').find('input[name=confirmPassword]');
		
		if(email.val().trim() == '')
		{
			email.parent('.input-container').find('.error').html('Please enter email address').show();
			errorCounter++;
		}
		else if(!testEmail.test(email.val()))
		{
			email.parent('.input-container').find('.error').html('Please enter valid email address').show();
			errorCounter++;
		}
		else
			email.parent('.input-container').find('.error').html('').hide();
		
		if(password.val().trim() == '')
		{
			password.parent('.input-container').find('.error').html('Please enter password').show();
			errorCounter++;
		}
		else
			password.parent('.input-container').find('.error').html('').hide();
		
		if(confirmPassword.val().trim() == '')
		{
			confirmPassword.parent('.input-container').find('.error').html('Please re-enter password').show();
			errorCounter++;
		}
		else if (password.val().trim() != confirmPassword.val().trim())
		{
			confirmPassword.parent('.input-container').find('.error').html('Password mismatch').show();
			errorCounter++;
		}
		else
			confirmPassword.parent('.input-container').find('.error').html('').hide();
		
		
		if(errorCounter > 0)
			return false;
	});
}

function saveUserPatent()
{
	$('body').on('click','.save-changes',function(){
		$('.highlightComments').removeClass('visible').hide();
		$.ajax({
			type: "POST",
			url: "ajax/managePatents.php",
			data: {'action':'save','content':$('#content-2').html(),'claim':$('#content-1-1').html(),'description':$('.content-section-2 .right-side').html(),'claim_1':$('#content-3').html(),'description_1':$('#content-4').html()},
			cache: false,
			dataType:'json',
			success: function(response)
			{
				if(response.status == 'error')
				{
					savePage	=	1;
					$('.login-link').trigger('click');
				}
			} 
		});
		return false;
	});
}

function reloadPageContent()
{
	var link	=	window.href;
	$.ajax({
		url: link,
		type: 'get',
		success: function(response)
		{
			window.history.pushState({url: "" + link + ""},"", link);
			$('#selectContainerTop').replaceWith($($.parseHTML(response)).find('#selectContainerTop'));
			$('#contentBody').replaceWith($($.parseHTML(response)).filter('#contentBody'));
			$('.searchList').css({'width':'150'});
			$('.main-content').css({'width':$(window).outerWidth()-$('.searchList').outerWidth()-40});
			
			if(savePage == 1)
			{
				savePage	=	0;
				$('embed').hide();
			}
			
			if($('.highlightedText').length > 0 && $('.remove-all-highlighting').length == 0)
				$('#user-options').prepend('<a class="remove-all-highlighting">Remove Highlighting</a>');
			
			addCommentIcons();
			
			$('.bottom-left, .right-side').bind( 'scroll', function(){
				$('.comment-icon').each(function(){
					setCommentIconPosition($(this).attr('id').split('-')[2]);
				});
			});
		}
		
	});
}

function hideMessagePopUp()
{
	setTimeout(function(){
		$('.bg-overlay').hide();
		$('.message-box-container').hide();
		$('embed').show();
	},2000);
}

function addColorPicker()
{
	var target = document.querySelectorAll('.context-menu-item.context-menu-icon-color');
    for (var i = 0, len = target.length; i < len; ++i) {
        (new CP(target[i])).on("change", function(color) {
            jQuery('.markedText.context-menu-active').css({'background':'#' + color}).addClass('isHighlighted highlightedText');
        });
    }
}

function setCommentIconPosition(idCount)
{
	var headerHeight	=	$('.header').outerHeight();
	var topDivHeight	=	$('.top-div').outerHeight();
	var topPos			=	($('#comment-'+idCount).parent('.markedText').offset().top+$('#comment-'+idCount).parent('.markedText').outerHeight()/2-10);
	var leftPos			=	$('#comment-'+idCount).parent('.markedText').offset().left-20;
	$('#comment-icon-'+idCount).css({'top':topPos,'left':leftPos});
	
	if(topPos-(headerHeight+topDivHeight) > 0 )
		$('#comment-icon-'+idCount).show();
	else
		$('#comment-icon-'+idCount).hide();
		
}

function addCommentIcons()
{
	if($('.highlightComments').length > 0)
	{
		$('.highlightComments').each(function(){
			var idCount	=	$(this).attr('id').split('-')[1];
			var id		=	'comment-icon-'+idCount;
			$('body').append('<div class="comment-icon" id="'+id+'"></div>');
			setCommentIconPosition(idCount);
		});
	}
}

function setCommentIcons()
{
	$('.highlightComments').each(function(){
		var idCount	=	$(this).attr('id').split('-')[1];
		setCommentIconPosition(idCount);
	});
}