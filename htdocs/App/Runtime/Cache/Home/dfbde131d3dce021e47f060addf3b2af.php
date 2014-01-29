<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>Offrez un Sourire Eternel </title><link rel="stylesheet" href="__APP__/Public/css/common.css" type="text/css" /><link rel="stylesheet" href="__APP__/Public/css/public.css" type="text/css" /><link rel='stylesheet' href='__APP__/Public/css/jquery-ui.css' type='text/css' ><link rel='stylesheet' href='__APP__/Public/css/AnimationStyle.css' type='text/css' ><link rel='stylesheet' href='__APP__/Public/css/buttonStyle.css' type='text/css' ><link rel='stylesheet' href='__APP__/Public/css/loginStyle.css' type='text/css' ><link rel="shortcut icon" href="__APP__/Public/images/favicon.ico"><script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script><script type="text/javascript" src="__APP__/Public/js/jquery-ui-1.8.11.min.js"></script><script type="text/javascript" src="__APP__/Public/js/functions.js"></script><script type="text/javascript" src="__APP__/Public/js/jquery.proximity.js"></script><script type="text/javascript" src="__APP__/Public/js/jquery.easing.1.3.js"></script><script type="text/javascript" src="__APP__/Public/js/animateBackground-plugin.js"></script><script type="text/javascript" src="__APP__/Public/js/jquery.animate-shadow-min.js"></script><script type="text/javascript" src="__APP__/Public/js/jquery.scrollTo.js"></script><script type="text/javascript" src="__APP__/Public/js/jquery.nav.min.js"></script><script type="text/javascript" src="__APP__/Public/js/cufon-yui.js"></script><script type="text/javascript" src="__APP__/Public/js/voxBOX_400.font.js"></script><script type="text/javascript" src="__APP__/Public/js/gbin1.js"></script><script type="text/javascript" src="__APP__/Public/js/fixie.js"></script></head><body><!--header开始--><div id="header" class="header"><div style="width:60%;"><div class="publicleft"><a href="/" class="logo"></a></div><div class="publicleft" style="margin-top:10px;text-align:center;"><h1 style="margin:0 auto;font-family: 'frscript'; font-size:40px;font-weight:300;">Ose</h1><h1 style="font-family: 'frscript'; font-size:30px;font-weight:400;">Offrez un Sourire Eternel</h1></div></div><div class="pse_search"><form name="template_search" id="template_search" action="/Public/searchUser" method="get" class="pse_search_form"><div class="pse_search_div"><input id="q" name="q" type="text" maxlength="250" class="pse_search_input" value="Nom ou Prénom" onfocus="this.value=''" /><div style="float: left; width: 18px; height: 24px; margin-left: 8px;"><a onclick="document.forms['template_search'].submit();" href="#" ><img name="search_button" id="search_button" src="__APP__/Public/images/spacer.gif" border="0" width="18" height="24"></a></div></div></form></div><div><?php if(isset($_SESSION[C('AGENCY_AUTH_KEY')])): ?><div class="compte"><a href="/Public/Logout" style="float:right; margin-left:10px;">Se déconnecter</a><a class="compteLink"  href="/Agency/index">Bienvenue <?php echo ($_SESSION['agencyname']); ?></a></div><?php elseif(isset($_SESSION[C('USER_AUTH_KEY')]) ): ?><div class="compte"><a href="/Public/Logout" style="float:right; margin-left:10px;">Se déconnecter</a><a class="compteLink"  href="/Users/login">Bienvenue <?php echo ($_SESSION['clientname']); ?></a></div><?php else: ?><a class="compte compteLink" href="/Agency/login">Espace Agence</a><a class="compte compteLink" style="margin-right:140px;" href="/Users/login">Espace Famille</a><?php endif; ?></div><!--
		<?php if(isset($_SESSION[C('AGENCY_AUTH_KEY')])): ?><h3 class="headerInfo">Welcome </h3><?php endif; ?>		--><!-- <a class="compte" href="/Agency/login">Espace Agence</a> --><!-- <span style="float:right;margin-top: 26px;"><a href="/Public/searchUser">Search</a><br /><?php echo L('header_tag2');?></span>  --></div><!--header结束--><div class="wrapper"><div class="mainindex"><div class="publicleft" style="width:70%"><section class="pe-container"><ul id="pe-thumbs" class="pe-thumbs"><li><a href="/PLorisse"><img src="__APP__/Public/animationImages/thumbs/1.jpg" /></a></li><li><a href="/PLorisse"><img src="__APP__/Public/animationImages/thumbs/2.jpg" /></a></li><li><a href="/PLorisse"><img src="__APP__/Public/animationImages/thumbs/3.jpg" /></a></li><li><a href="/PLorisse"><img src="__APP__/Public/animationImages/thumbs/4.jpg" /></a></li><li><a href="/PLorisse"><img src="__APP__/Public/animationImages/thumbs/5.jpg" /></a></li><li><a href="/PLorisse"><img src="__APP__/Public/animationImages/thumbs/6.jpg" /></a></li></ul></section><div class="news" id="haha3"><div><ul style="text-align:center;margin-top:-25px;"><li><h1>"Le bonheur ne crée rien que des souvenirs", Balzac</h1></li><li><h1>"La mémoire, ce passé au présent", François Chalais</h1></li><li><h1>"Les larmes sont l'extrême sourire", Stendhal</h1></li></ul></div><br /><br /><div style="text-align:center;"><ol class="activeOL" style="width:60px;" ></ol></div></div></div><div class="publicright" style="margin-top:30px; width:30%"><div class="menuitem button35060" style="float:left;"><div><a href="/Public/whoweare"><div class="inner-menu-wrapper"></div><div class="inner-menu-wrapper-inv"></div><H1>Le Concept</H1></a><br /></div></div><br /><br /><br /><br /><div class="menuitem button35060" style="float:left;"><div><a href="/Public/createUser"><div class="inner-menu-wrapper"></div><div class="inner-menu-wrapper-inv"></div><div style="margin:0 auto;text-align:center;"><H1 style=" margin:0 auto;">Offrir un Sourire Eternel</H1></div></a><br /></div></div></div></div></div><script>function scrollNews(selector,Entry,time,StartIndex)
{
	var _self=this;
	this.Selector=selector;
	this.Entry=Entry;
	this.time = time;
	this.i=StartIndex||0;
	this.Count=$(this.Selector+" ul li").length;
	$(this.Selector+" ul li").hide();//全部隐藏
	$(this.Selector+" ul li").eq(this.i).show();//第i个显示
	$(this.Selector).bind("mouseenter",function(){
	    	if(_self.sI){clearInterval(_self.sI);}
	}).bind("mouseleave",function(){
			_self.showIndex(_self.i++);
	})
	/*生成激活OL项目*/
	for(var j=0;j<this.Count;j++)
		$(this.Selector+" .activeOL").append('<li><a onclick="'+this.Entry+'.showIndex('+j+');" ><img src="__APP__/Public/images/crystal.gif"></a></li>');
	$(this.Selector+" ol li a").eq(this.i).addClass("active");
	this.sI=setInterval(this.Entry+".showIndex(null)",this.time);
	
	this.GetSelector=function(){return this.Selector;}
	this.showIndex=function(index)
	{
		this.i++;//显示下一个
		if(this.sI){clearInterval(this.sI);}
		this.sI=setInterval(this.Entry+".showIndex()",this.time);
		if (index!=null)
		{
			this.i=index;
		}
		if(this.i==this.Count)
			this.i=0;
		$(this.Selector+" ul li").hide();
		$(this.Selector+" ul li").eq(this.i).slideDown();
		$(this.Selector+" ol li a").removeClass("active");
		$(this.Selector+" ol li a").eq(this.i).addClass("active");
	}
}
var haha3=new scrollNews("#haha3","haha3"  , 5000 , 1);
</script><script type="text/javascript">			$(function() {
				
				var Photo	= (function() {
					
					var $list		= $('#pe-thumbs'),
						listW		= $list.width(),
						listL		= $list.offset().left,
						$elems		= $list.find('img'),
						$descrp		= $list.find('div.pe-description'),
						settings	= {
							maxScale	: 0.9,
							maxOpacity	: 0.9,
							minOpacity	: Number( $elems.css('opacity') )
						},
						init		= function() {
							
							settings.minScale = _getScaleVal();
							_loadImages( function() {
								
								_calcDescrp();
								_initEvents();
							
							});
						
						},
						// Get Value of CSS Scale through JavaScript:
						// http://css-tricks.com/get-value-of-css-rotation-through-javascript/
						_getScaleVal= function() {
						
							var st = window.getComputedStyle($elems.get(0), null),
								tr = st.getPropertyValue("-webkit-transform") ||
									 st.getPropertyValue("-moz-transform") ||
									 st.getPropertyValue("-ms-transform") ||
									 st.getPropertyValue("-o-transform") ||
									 st.getPropertyValue("transform") ||
									 "fail...";

							if( tr !== 'none' ) {	 

								var values = tr.split('(')[1].split(')')[0].split(','),
									a = values[0],
									b = values[1],
									c = values[2],
									d = values[3];

								return Math.sqrt( a * a + b * b );
							
							}
							
						},
						_calcDescrp	= function() {
							
							$descrp.each( function(i) {
							
								var $el		= $(this),
									$img	= $el.prev(),
									img_w	= $img.width(),
									img_h	= $img.height(),
									img_n_w	= settings.maxScale * img_w,
									img_n_h	= settings.maxScale * img_h,
									space_t = ( img_n_h - img_h ) / 2,
									space_l = ( img_n_w - img_w ) / 2;
								
								$el.data( 'space_l', space_l ).css({
									height	: settings.maxScale * $el.height(),
									top		: -space_t,
									left	: img_n_w - space_l
								});
							
							});
						
						},
						_initEvents	= function() {
						
							$elems.on('proximity.Photo', { max: 170, throttle: 10, fireOutOfBounds : true }, function(event, proximity, distance) {
								
								var $el			= $(this),
									$li			= $el.closest('li'),
									$desc		= $el.next(),
									scaleVal	= proximity * ( settings.maxScale - settings.minScale ) + settings.minScale,
									scaleExp	= 'scale(' + scaleVal + ')';
								
								var $desc		= $el.next();
								if( scaleVal === settings.maxScale ) {
									
									$li.css( 'z-index', 1000 );
									/*
									if( $desc.offset().left + $desc.width() > listL + listW ) {
										
										$desc.css( 'left', -$desc.width() - $desc.data( 'space_l' ) );
									
									}
									*/
									$desc.fadeIn( 800 );
									
								}	
								else {
									
									$li.css( 'z-index', 1 );
									
									$desc.stop(true,true).hide();
								
								}	
								
								$el.css({
									'-webkit-transform'	: scaleExp,
									'-moz-transform'	: scaleExp,
									'-o-transform'		: scaleExp,
									'-ms-transform'		: scaleExp,
									'transform'			: scaleExp,
									'opacity'			: ( proximity * ( settings.maxOpacity - settings.minOpacity ) + settings.minOpacity )
								});

							});
						
						},
						_loadImages	= function( callback ) {
							
							var loaded 	= 0,
								total	= $elems.length;
							
							$elems.each( function(i) {
								
								var $el = $(this);
								
								$('<img/>').load( function() {
									
									++loaded;
									if( loaded === total )
										callback.call();
									
								}).attr( 'src', $el.attr('src') );
							
							});
						
						};
					
					return {
						init	: init
					};
				
				})();
				
				Photo.init();
				
			});
		</script><!--footer开始--><div id="footer"><div class="main"><div class="center"><!--
				<ul class="footnav"><li><a href="/Public/createUser"> Offrir un Sourire Eternel</a></li><li><a href="/Public/whoweare"> Qui sommes-nous?</a></li><li><a href="/Public/mention">Mentions Légales</a></li><li class="last-child"><a href="/Public/FAQ"> Questions-Réponses </a></li></ul>				--><div style="display:inline;"><a href="/Public/createUser"> Offrir un Sourire Eternel</a> &nbsp;|&nbsp; </div><div style="display:inline;"><a href="/Public/whoweare"> Qui sommes-nous?</a> &nbsp;|&nbsp;  </div><div style="display:inline;"><a href="/Public/mention">Mentions Légales</a> &nbsp;|&nbsp;  </div><div style="display:inline;"><a href="/Public/FAQ"> Questions-Réponses </a></div></div><div class="clear"></div></div></div><!--footer结束--></body></html>