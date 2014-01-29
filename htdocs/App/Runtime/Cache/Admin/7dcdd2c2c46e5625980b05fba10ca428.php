<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>Eclipse++ Manage Platform</title><link rel="stylesheet" href="../Public/stylesheets/jquery.treeview.css" /><link rel="stylesheet" href="../Public/stylesheets/base.css" type="text/css" media="screen" /><link rel="stylesheet" href="../Public/stylesheets/jquery-ui.css" type="text/css" media="screen" /><link rel="stylesheet" id="current-theme" href="../Public/stylesheets/themes/bec/style.css" type="text/css" media="screen" /><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-1.5.1.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.scrollTo.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.localscroll.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/functions.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.form.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-ui-1.8.11.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.tablePagination.0.5.min.js"></script><script type="text/javascript" src="../Public/js/jquery.easing.1.1.min.js"></script><script type="text/javascript" src="../Public/js/jquery.lavalamp-0.2.min.js"></script><script type="text/javascript" src="../Public/js/neoalchemy.min.js"></script><script src="../Public/js/lib/jquery.cookie.js" type="text/javascript"></script><script src="../Public/js/jquery.treeview.js" type="text/javascript"></script><script src="../Public/js/ajaxFileUpload.js" type="text/javascript"></script></head><body><div id="container"><div id="header"><h1><a href="index.html">Eclipse++ Manage Platform</a></h1><div id="user-navigation"><ul class="wat-cf"><li><a href="#">Paris Ecole centrale d'electronique & UPMC LIB6</a></li><li><a class="logout" href="/Admin/Public/logout">Logout</a></li></ul></div></div></div><div id="sidebar"><div class="block"><h3>Eclipse P2 Space</h3><ul class="navigation"><li><a href="/Admin/Category/index">Eclipse Category</a></li><li><a href="/Admin/Feature/index">Eclipse Feature</a></li></ul><h3>Repository Space</h3><ul class="navigation"><li><a href="/Admin/Repository/index">Repository</a></li><li><a href="/Admin/Repository/add">Add</a></li></ul><h3>Feature Modeling</h3><ul class="navigation"><li><a href="/Admin/Group/index">SPL Feature</a></li><li><a href="/Admin/Group/update">Update a Model</a></li><li><a href="/Admin/Group/add">Import a new Model</a></li></ul><h3>Build Eclipse++</h3><ul class="navigation"><li><a href="/Admin/Debug/index">Build</a></li></ul></div></div><div id="wrapper" class="wat-cf"><div id="main"><div class="block" id="agencyForm"><div class="content"><h2 class="title">Repository Manager</h2><div class="inner"><form id="form1" action="/Admin/Repository/doAdd" method="post" class="form"><input type='hidden' id='agencyId' name='agencyId'><div class="group"><label class="label">URL</label><input type="text" class="text_field" id='repository' name='repository'/><span class="description">The url of Eclipse repository</span></div><div class="group navform wat-cf"><button class="button" type="submit"><img src="../Public/images/icons/tick.png" alt="Save" /> Save
                  </button><span class="text_button_padding">or</span><a class="text_button_padding link_button" href="javascript:cancel();" >Cancel</a></div></form></div></div></div><div class="inner div_hide" id="block-messages"><div class="flash"><div class="message error"><span><p>Error message</p></span></div><div class="message warning"><span><p>Warning message</p></span></div><div class="message notice"><span><p>Notice message</p></span></div></div></div><script>  /**
  * type: notice, warning, error
  */
  function showMessage(content, type) {
  	$('#block-messages').show();
  	
  	$('#block-messages p').each(function(){
  		$(this).html(content);
  	});
  	
  	$('.message').each(function(){
  		if(!$(this).hasClass(type)) {
 			$(this).addClass('div_hide');
  		}
  	});
  }
  
  function hideMessage() {
	$('#block-messages').hide();
  }
  
  
  </script></div></div></body></html>