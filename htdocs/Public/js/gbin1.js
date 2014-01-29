/*
background wave effect by gbin1.com
*/

$(document).ready(function(){
	Cufon.replace('.navitem').CSS.ready(function() {
		animateWave();
	
		$('#nav').onePageNav({
			filter: ':not(.external)',
			scrollThreshold: 0.25
		});	
	});
});


function animateWave(){
					
	$("div.menuitem").on("mouseenter",function animationEnter(evt){

		var innerMenuWrapper = $(this).find("div.inner-menu-wrapper");
		var innerMenuWrapperInv = $(this).find("div.inner-menu-wrapper-inv");

		$(this).css({"position":"relative"});
		$(this).stop().animate({ "boxShadow" : "15px 15px 15px rgba(0,0,0,0.8)", "top":"-10px","left":"-10px"}, "fast");

		//wave animation 1
		function animation1(){
			
			innerMenuWrapper.animate({backgroundPosition:"-1500px -120px"},{duration:5000, easing: "easeInOutQuad", complete: function() { 
		
				innerMenuWrapper.animate({backgroundPosition:"0px -120px"},{duration:5000, easing: "easeInOutQuad", complete:function(){animation1();}});  
				
			}});
			
		}
		
		//wave animation 2
		function animation2(){
			
			innerMenuWrapperInv.animate({backgroundPosition:"-1500px -130px"},{duration:8000, easing: "easeInOutQuad", complete: function() { 
		
				innerMenuWrapperInv.animate({backgroundPosition:"-200px -130px"},{duration:8000, easing: "easeInOutQuad", complete:function(){animation2();}});  
				
			}});
			
		}
		
		innerMenuWrapper.animate({opacity:0.6},{queue:false, duration:400, easing: "easeInOutQuad"});
		animation1();
		
		innerMenuWrapperInv.animate({opacity:0.3},{queue:false, duration:400, easing: "easeInOutQuad"});
		animation2();
	});
	
	$("div.menuitem").on("mouseleave", function animationLeave(evt){
		var innerMenuWrapper = $(this).find("div.inner-menu-wrapper");
		var innerMenuWrapperInv = $(this).find("div.inner-menu-wrapper-inv");

		$(this).stop().animate({ "boxShadow" : "1px 1px 15px #303030", "top":"0px","left":"0px"}, "fast");
				
		innerMenuWrapper.stop(true).animate({opacity:0},{duration:300, easing: "easeInOutQuad", complete:function(){innerMenuWrapper.css("backgroundPosition","0px -120px");}});
		innerMenuWrapperInv.stop(true).animate({opacity:0},{duration:300, easing: "easeInOutQuad", complete:function(){innerMenuWrapperInv.css("backgroundPosition","-600px -150px");}});
	});

	$("div.menuitem").on("mousedown", function(){
		$(this).stop().animate({ "boxShadow" : "1px 1px 15px #303030", "top":"0px","left":"0px"}, "fast");
	});
}