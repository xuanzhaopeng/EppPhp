<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>Eclipse++ Manage Platform</title><link rel="stylesheet" href="../Public/stylesheets/jquery.treeview.css" /><link rel="stylesheet" href="../Public/stylesheets/base.css" type="text/css" media="screen" /><link rel="stylesheet" href="../Public/stylesheets/jquery-ui.css" type="text/css" media="screen" /><link rel="stylesheet" id="current-theme" href="../Public/stylesheets/themes/bec/style.css" type="text/css" media="screen" /><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-1.5.1.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.scrollTo.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.localscroll.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/functions.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.form.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-ui-1.8.11.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.tablePagination.0.5.min.js"></script><script type="text/javascript" src="../Public/js/jquery.easing.1.1.min.js"></script><script type="text/javascript" src="../Public/js/jquery.lavalamp-0.2.min.js"></script><script type="text/javascript" src="../Public/js/neoalchemy.min.js"></script><script src="../Public/js/lib/jquery.cookie.js" type="text/javascript"></script><script src="../Public/js/jquery.treeview.js" type="text/javascript"></script><script src="../Public/js/ajaxFileUpload.js" type="text/javascript"></script></head><body><div id="container"><div id="box"><div class="block" id="block-login"><h2>Eclipse++ Manage Platform</h2><div class="content login"><div class="flash"><div id='msgDiv' class="message notice hide"><p><span id='msgSpan'>Paris ECE & UPMC LIB6</span></p></div></div><form action="#" class="form login"><div class="group wat-cf"><div class="left"><label class="label right">Account</label></div><div class="right"><input type="text" class="text_field" id="account"/></div></div><div class="group wat-cf"><div class="left"><label class="label right">Password</label></div><div class="right"><input type="password" class="text_field" id="password" /></div></div><div class="group navform wat-cf"><div class="right"><button id="btnSubmit" class="button" type="button"><img src="images/icons/key.png" alt="Save" /> Login
                </button></div></div></form></div></div></div></div><script>$(function(){
	$('#btnSubmit').click(function(){
		
		var account = $('#account').val();
		account = $.trim(account);
		if (account == ''){
			var msg = "<?php echo L('pls_enter_account');?>";
			showMsgBox(msg);
			return;
		}
		var password = $('#password').val();
		password = $.trim(password);
		$.post('/Admin/Public/checkLogin',{account:account,password:password},function(data){
			var obj = $.parseJSON(data);
			if (obj.status == 1) {
				location.href = '/Admin/Public/index';
			} else {
				showMsgBox(obj.msg);	
			}
		});
	});
});

function showMsgBox(msg) {
	$('#msgSpan').html(msg);
	$('#msgDiv').removeClass('hide');
}

function hideMsgBox() {
	$('#msgDiv').hide();
}


</script></body></html>