<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>Eclipse++ Manage Platform</title><link rel="stylesheet" href="../Public/stylesheets/jquery.treeview.css" /><link rel="stylesheet" href="../Public/stylesheets/base.css" type="text/css" media="screen" /><link rel="stylesheet" href="../Public/stylesheets/jquery-ui.css" type="text/css" media="screen" /><link rel="stylesheet" id="current-theme" href="../Public/stylesheets/themes/bec/style.css" type="text/css" media="screen" /><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-1.5.1.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.scrollTo.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.localscroll.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/functions.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.form.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-ui-1.8.11.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.tablePagination.0.5.min.js"></script><script type="text/javascript" src="../Public/js/jquery.easing.1.1.min.js"></script><script type="text/javascript" src="../Public/js/jquery.lavalamp-0.2.min.js"></script><script type="text/javascript" src="../Public/js/neoalchemy.min.js"></script><script src="../Public/js/lib/jquery.cookie.js" type="text/javascript"></script><script src="../Public/js/jquery.treeview.js" type="text/javascript"></script><script src="../Public/js/ajaxFileUpload.js" type="text/javascript"></script></head><body><div id="container"><div id="header"><h1><a href="index.html">Eclipse++ Manage Platform</a></h1><div id="user-navigation"><ul class="wat-cf"><li><a href="#">Paris Ecole centrale d'electronique & UPMC LIB6</a></li><li><a class="logout" href="/Admin/Public/logout">Logout</a></li></ul></div></div></div><div id="sidebar"><div class="block"><h3>Eclipse P2 Space</h3><ul class="navigation"><li><a href="/Admin/Category/index">Eclipse Category</a></li><li><a href="/Admin/Feature/index">Eclipse Feature</a></li></ul><h3>Repository Space</h3><ul class="navigation"><li><a href="/Admin/Repository/index">Repository</a></li><li><a href="/Admin/Repository/add">Add</a></li></ul><h3>Feature Modeling</h3><ul class="navigation"><li><a href="/Admin/Group/index">SPL Feature</a></li><li><a href="/Admin/Group/update">Update a Model</a></li><li><a href="/Admin/Group/add">Import a new Model</a></li></ul><h3>Build Eclipse++</h3><ul class="navigation"><li><a href="/Admin/Debug/index">Build</a></li></ul></div></div><div id="wrapper" class="wat-cf"><div id="main"><div class="block" id="block-tables"><div class="content"><h2 class="title">Eclipse Repository Management</h2><div class="inner"><div style="display:blcok;">&nbsp;
						<div style="width:600px;height:40px;float: right;"><div id="name_con_div" style="display:inline-block;"><input type="text" id="searchName" style="width:315px;height:28px;"><button class="button" onclick="search();" style="float: right;">search</button></div></div></div><p></p><table id="dataTable" class="table"><tr><th class="first">URL</th></tr><tbody id='listBody'></tbody></table><div class="actions-bar wat-cf"><div class="actions"><div id="threaddiv" style="color: red;"></div><div id="testdiv" style="color: red;"></div></div></div></div><div class="actions-bar wat-cf"><div class="actions"><div id="mvn" style="color: red;"></div><!-- <button class="button" type="submit"><img src="../Public/images/icons/cross.png" alt="Delete" />Delete
	                    </button> --></div><div id="pagination" class="pagination"><!-- <span class="disabled prev_page">« Previous</span><span class="current">1</span><a rel="next" href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><a href="#">6</a><a href="#">7</a><a href="#">8</a><a href="#">9</a><a href="#">10</a><a href="#">11</a><a rel="next" class="next_page" href="#">Next »</a> --></div></div></div></div><div class="inner div_hide" id="block-messages"><div class="flash"><div class="message error"><span><p>Error message</p></span></div><div class="message warning"><span><p>Warning message</p></span></div><div class="message notice"><span><p>Notice message</p></span></div></div></div><script>  /**
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
  
  
  </script></div></div><script>var sessionId = "";
		$(document).ready(function() {   
		
				function shows(){
                   $.get('http://127.0.0.1:8078/EppServer/RepositoryAjaxServlet', function(responseText) { 
						$('#threaddiv').text("Currently server runs "+responseText + " threads for updating repository");  
                    });
				};
				
				function send(){
					$.ajax({
						type:"get",
						url:"http://127.0.0.1:8078/EppServer/testServlet",
						dataType: "html",
						timeout: 600000,
						success:function(data) {
							$('#testdiv').text(data);  
						}        
					});
				};
				/*
				function mvn(){
                    $.get('http://192.168.1.9:8078/EclipsePlusPlus/MvnServlet', function(responseText) { 
						sessionId = responseText;
                    });
				};
				function mvnAjax(){
                   $.get('http://192.168.1.9:8078/EclipsePlusPlus/MvnAjaxServlet?session='+sessionId, function(responseText) { 
						$('#mvn').append(responseText+"<br \>");  
                    });
				};
				mvn();
				*/
				setInterval(function(){shows();}, 2000);
				//setInterval(function(){mvnAjax();}, 5000);
         });

var currPageIndex = 1;
var totalPages = 0;
var searchCondition = '';
$(function(){
	loadPage(1);
	
	
	$('#cbox1').removeAttr('checked');
	$('#cbox1').click(function(){
		if ($('#cbox1').attr('checked')) {
			$("input[type='checkbox']").attr('checked', 'true');			
		} else {
			$("input[type='checkbox']").removeAttr('checked');	
		}
	});
	
});

function loadPage(pageIndex) {
	currPageIndex = pageIndex;
	//load data
	$.post('/Admin/Repository/loadRepositoryPage',{p:pageIndex,sName:searchCondition},function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			loadData(obj);
			hideMessage();
		} else if(obj.status == 2) {
			$('#listBody').html('');
			showMessage(obj.msg, 'warning');
		} else if (obj.status == 0) {
			showMessage(obj.msg, 'error');
		}
	});
}

function loadData(obj) {
	var data = obj.data;
	var html = '';
	for(i=0; i<data.length; i++) {
		html = html + '<tr><td>'+data[i].repository+'</td></tr>';
	}
	$('#listBody').html(html);
	
	//biuld pagination
	var pagination = '';
	var page = obj.page;
	var nowPage = parseInt(page.nowPage);
	var prevPage = nowPage - 1;
	totalPages = page.totalPages;
	if (prevPage > 0) {
		pagination = '<span class="disabled prev_page" style="cursor:pointer;" onclick="javascript:loadPage('+prevPage+');">« Prev</span>';	
	} else {
		pagination = '<span class="disabled prev_page">« Prev</span>';
	}
	
	for (i=1; i<= page.totalPages; i++) {
		if (i == nowPage) {
			pagination = pagination + '<span class="current">'+i+'</span>';
		} else {
			pagination = pagination + '<a href="javascript:loadPage('+i+');">'+i+'</a>';					
		}
	}
	var nextPage = nowPage + 1;
	if (nextPage > page.totalPages) {
		pagination = pagination + '<span class="next_page">Next »</span>';	
	} else {
		pagination = pagination + '<span class="next_page" style="cursor:pointer;" onclick="javascript:loadPage('+nextPage+');">Next »</a></span>';
	}
	
	$('#pagination').html(pagination);
}



function search() {
	searchCondition = $('#searchName').val();
	loadPage(1);
}
</script></body></html>