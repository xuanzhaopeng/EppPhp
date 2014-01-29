<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>Eclipse++ Manage Platform</title><link rel="stylesheet" href="../Public/stylesheets/jquery.treeview.css" /><link rel="stylesheet" href="../Public/stylesheets/base.css" type="text/css" media="screen" /><link rel="stylesheet" href="../Public/stylesheets/jquery-ui.css" type="text/css" media="screen" /><link rel="stylesheet" id="current-theme" href="../Public/stylesheets/themes/bec/style.css" type="text/css" media="screen" /><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-1.5.1.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.scrollTo.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.localscroll.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/functions.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.form.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-ui-1.8.11.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.tablePagination.0.5.min.js"></script><script type="text/javascript" src="../Public/js/jquery.easing.1.1.min.js"></script><script type="text/javascript" src="../Public/js/jquery.lavalamp-0.2.min.js"></script><script type="text/javascript" src="../Public/js/neoalchemy.min.js"></script><script src="../Public/js/lib/jquery.cookie.js" type="text/javascript"></script><script src="../Public/js/jquery.treeview.js" type="text/javascript"></script><script src="../Public/js/ajaxFileUpload.js" type="text/javascript"></script></head><body><div id="container"><div id="header"><h1><a href="index.html">Eclipse++ Manage Platform</a></h1><div id="user-navigation"><ul class="wat-cf"><li><a href="#">Paris Ecole centrale d'electronique & UPMC LIB6</a></li><li><a class="logout" href="/Admin/Public/logout">Logout</a></li></ul></div></div></div><div id="sidebar"><div class="block"><h3>Eclipse P2 Space</h3><ul class="navigation"><li><a href="/Admin/Category/index">Eclipse Category</a></li><li><a href="/Admin/Feature/index">Eclipse Feature</a></li></ul><h3>Repository Space</h3><ul class="navigation"><li><a href="/Admin/Repository/index">Repository</a></li><li><a href="/Admin/Repository/add">Add</a></li></ul><h3>Feature Modeling</h3><ul class="navigation"><li><a href="/Admin/Group/index">SPL Feature</a></li><li><a href="/Admin/Group/update">Update a Model</a></li><li><a href="/Admin/Group/add">Import a new Model</a></li></ul><h3>Build Eclipse++</h3><ul class="navigation"><li><a href="/Admin/Debug/index">Build</a></li></ul></div></div><div id="wrapper" class="wat-cf"><div id="main"><div class="block" id="block-tables"><div class="content"><h2 class="title">Build your eclipse</h2><div class="inner"><div class="row"><div class="col-xs-6 col-md-4"><h2 class="title">Step 1: Select SPL Feature</h2><div id="name_con_div" style="display:inline-block;"><label>Select Feature Model: &nbsp;</label><select id="modelselect" name="modelselect" style="width:315px;height:28px;" onchange="changeModel(this);"></select></div><ul id="red" class="treeview-red"></ul><br /><!--
							<h2 class="title">Step 2: Select feature(Option)</h2><div style="height:40px;"><div id="name_con_div" style="display:inline-block; width:100%;"><input type="text" id="searchName" style="width:75%; height:28px;"><button class="button" onclick="search();" style="float: right;">search</button></div></div><table id="dataTable" class="table" width="100%"><thead><th width="60%">Feature Id</th><th width="40%">Name</th></thead><tbody id='listBody' width="100%"></tbody></table>							--></div><div class="col-xs-6 col-md-4"><h2 class="title">Step 2: Choose Operation Platform</h2><select  id="oplist" name="oplist" style="width:100%;" ><option value="win32">Windows 32bit</option><option value="win64">Windows 64bit</option><option value="linux32">Linux 32bit</option><option value="linux64">Linux 64bit</option><option value="mac64">Mac 64bit</option></select><br /><br /></div><div class="col-xs-6 col-md-4"><h2 class="title">Step 3: Verify SPL Feature</h2><select  id="featureList" multiple="multiple" style="width:100%;height:300px;" ></select><br /><br /><button class="button" onclick="deleteItem();" style="float: left;">Delete</button><button class="button" onclick="clearSession();" style="float: left;">Clear</button><form id="bform" name="bform" action="http://127.0.0.1:8078/EppServer/buildepp.jsp" method="POST"><button class="button" type="button" onclick="message();" style="float: right;">Build</button></form></div></div></div></div></div><div class="block div_hide" id="buildForm"><div class="content"><h2 class="title">Feature detail</h2><div class="inner"><div class="group"><div class="fieldWithErrors"><label class="label" for="post_title" style="color:black;">Name</label></div><input type="text" class="text_field" id='dia_name' name='name' /></div><div class="group"><div class="fieldWithErrors"><label class="label" for="post_title" style="color:black;">Id</label></div><input type="text" class="text_field" id='dia_id' name='id' /></div><div class="group"><div class="fieldWithErrors"><label class="label" for="post_title" style="color:black;">Provider</label></div><input type="text" class="text_field" id='dia_publisher' name='publisher' /></div><div class="group"><div class="fieldWithErrors"><label class="label" for="post_title" style="color:black;">Description</label></div><textarea rows="4" cols="50" id='description' name='dia_description' ></textarea></div><div class="group navform wat-cf"><button id="btnEditSave" class="button" type="button" onclick="javascript:cancel();" ><img src="../Public/images/icons/tick.png" alt="Save" /> Close
                  </button></div><script>        			var thumbPrefix = 'logo_w200_w200_';
        			var thumbW = 200;
        			var thumbH = 200;
			 </script></div></div></div><script type="text/javascript">var featureId = "";


function dia_loadPage() {
	//load data
	$.post('/Admin/Feature/getFeatureInfo',{id:featureId},function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			dia_loadData(obj);
		}
	});
}

function dia_loadData(obj) {
	var data = obj.data;
	var html = '';
	
	if (data != null) {
		$('#dia_name').val(data[0].name);
		$('#dia_id').val(data[0].id);
		$('#dia_publisher').val(data[0].publisher);
	}
}


function cancel() {
	$("#buildForm").dialog("close");
}

function openEditDialog(id) {
  $('#buildForm').dialog({
		width: 800,
           height: 620,
           modal: true,
           title: 'Edit',
           create: function () { },
           open: function () { },
           close: function () { }
	});
	featureId = id;
	dia_loadPage();
}


</script></div></div><script>var current_model_id;
$(function(){
	//loadPage2();
	//clearSession();
	loadModel();
	
	$(function(){
        $("option").bind("dblclick", function(){
            alert($(this).text());
        });
    });
});
function message(){

	$.post('/Admin/Debug/doBuild', {model_id:current_model_id},function(data){
		var obj = $.parseJSON(data);
		var data = obj.data;
		var feature = obj.data["features"];
		var repo = obj.data["repo"];
		var groups = obj.data["groups"];
		var email = obj.data["email"];
		var version = $('#oplist').val();
		
		var strfeature = feature.join(",");
		var strrepo = repo.join(",");
		var strgroup = groups.join(",");
		
		$('<input type="hidden" id="feature" name="feature"/>').val(strfeature).appendTo('#bform');
		$('<input type="hidden" id="repo" name="repo"/>').val(strrepo).appendTo('#bform');
		$('<input type="hidden" id="version" name="version"/>').val(version).appendTo('#bform');
		$('<input type="hidden" id="email" name="email"/>').val(email).appendTo('#bform');
		$('<input type="hidden" id="groups" name="groups"/>').val(strgroup).appendTo('#bform');
		$("#bform").submit();
		
	});
	
}

function loadSession() {
	$.post('/Admin/Spl/printSession',{model_id:current_model_id}, function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			var data = obj.data;
			$('#featureList').find('option').remove();
			for(i=0;i<data.length;i++){
				$('#featureList').append('<option value="'+data[i]+'#group" >SPL Feature:'+data[i]+'</option>');
			}
		} else if(obj.status == 0) {
			$('#featureList').find('option').remove();
		}
	});
}

function loadTree() {
	$('#red').html('');
	$.post('/Admin/Debug/loadTree',{model:current_model_id}, function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			loadData2(obj);
		} else if(obj.status == 2) {
			$('#red').html('');
		} else if (obj.status == 0) {
		}
	});
}

function outputFeature(feature){
	var html = '<ul><li><span><a href="javascript:showDialog(\''+feature.id+'\');" style="cursor:poniter;">'+feature.id+'</a></span></li></ul>';
	
	return html;
}

function outputGroup(group){

	var html = '<li><span><a href="javascript:addItem(\''+group.id+'\',\'group\');" ';
	
	if(group.status == 1) 
		html = html + 'style="cursor:poniter;color: red">';//abstract = black
	else
		html = html + 'style="cursor:poniter; color: black">';

	html = html + group.id+'</a></span><ul>';

	if(group.hasOwnProperty('children')){
		for(var i=0;i<group['children'].length;i++){
			html = html + outputGroup(group['children'][i]);
		}
	}else if(group.hasOwnProperty('features')){
		for(var i=0;i<group['features'].length;i++){
			html = html + outputFeature(group['features'][i]);
		}
	}
	return html+'</ul></li>';
}

function outputModel(model){
	var html = '';
	//var html = '<li><span><label style="cursor:poniter;">'+model.mname+'</label></span><ul>';

	if(model.hasOwnProperty('group'))
		html = html + outputGroup(model['group']);

	return html+'</ul></li>';
}

function loadData2(obj) {
	var data = obj.data;
	var html = '';
	
	for(var i=0;i<data.length;i++)
		html = html + outputModel(data[i]);

	$('#red').append(html); 
	
	$("#red").treeview({
		animated: "fast",
		collapsed: true,
		unique: true,
		persist: "cookie",
		toggle: function() {
			window.console && console.log("%o was toggled", this);
		}
	});
}

var searchNameCondition = '';
function loadFeaturePage() {
	//load data
	$.post('/Admin/Debug/loadFeaturePage',{sName:searchNameCondition},function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			loadFeatureData(obj);
		} else if(obj.status == 2) {
			$('#listBody').html('');
		} else if (obj.status == 0) {
		}
	});
}

function loadFeatureData(obj) {
	var data = obj.data;
	var html = '';
	for(i=0; i<data.length; i++) {
		html = html + '<tr width="100%"><td width="60%"><a href="javascript:addItem(\''+data[i].id+'\',\'feature\');" style="cursor:poniter;">'+data[i].id+'</a></td>'; 
		html = html + '<td width="40%">'+data[i].name+'</td></tr>';
	}
	$('#listBody').html(html);
}

function search() {
	searchNameCondition = $('#searchName').val().trim();
	if(searchNameCondition!=null && searchNameCondition != '' ){
		loadFeaturePage();
	}
}

function addItem(id,type){
	if(type == 'group') {
		$.post('/Admin/Spl/startSelectNode',{model_id:current_model_id,node_id:id},function(data){
			var obj = $.parseJSON(data);
			if (obj.status == 1) {
				var data = obj.data;
				$('#featureList').find('option').remove();
				for(i=0;i<data.length;i++){
					$('#featureList').append('<option value="'+data[i]+'#group" >SPL Feature:'+data[i]+'</option>');
				}
			} else if(obj.status == 0) {
				var res = obj.message.split("+");
				alert(res[0] + " and " + res[1] + " can not be selected in same time based on Feature Model!");
			}
		});
	}else if(type == 'feature'){
		$('#featureList').append('<option value="'+id+'#feature" >Feature:'+id+'</option>');
	}
}

function deleteItem(){
	var value = $('#featureList :selected').val();
	if(value.indexOf("#group") != -1){
		var res = value.split("#");
		var id = res[0];
		$.post('/Admin/Spl/startUnselectNode',{model_id:current_model_id,node_id:id},function(data){
			var obj = $.parseJSON(data);
			if (obj.status == 1) {
				var data = obj.data;
				$('#featureList').find('option').remove();
				for(i=0;i<data.length;i++){
					$('#featureList').append('<option value="'+data[i]+'#group" >SPL Feature:'+data[i]+'</option>');
				}
			} else if(obj.status == 0) {
				var res = obj.message.split("+");
				alert("You cannot delete SPL Feature[" + res[1] + "] cause the SPL Feature[" + res[0] + "] are related with it, please remove SPL Feature[" + res[0] + "] at first!");
			}
		});
	}else{
		$('#featureList :selected').remove(); 
	}
	//$('#featureList :selected').remove(); 
}

function showDialog(id){
	if(id != null && id != ''){
		openEditDialog(id);
	}
}
	
function loadModel() {
	//load model
	$.post('/Admin/Spl/loadSpl',function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			var data = obj.data;
			for(i=0;i<data.length;i++) {
				var model_id = data[i].id;
				var model_name = data[i].name;
				if(i == 0){
					$('#modelselect').append('<option value="'+model_id+'" selected="selected">'+model_name+'</option>');
					current_model_id = model_id;
					loadTree();
					loadSession();
				}else{
					$('#modelselect').append('<option value="'+model_id+'" >'+model_name+'</option>');
				}
			}
			hideMessage();
		} else if(obj.status == 2) {
			$('#modelselect').html('');
			showMessage(obj.msg, 'warning');
		} else if (obj.status == 0) {
			showMessage(obj.msg, 'error');
		}
	});
}

function clearSession() {
	$.post('/Admin/Spl/clearSession',{model_id:current_model_id},function(data){
		
	});
	$('#featureList').find('option').remove();
}

function changeModel(sel) {
	var value = sel.options[sel.selectedIndex].value;  
	current_model_id = value;
	loadTree();
	loadSession();
}
</script></body></html>