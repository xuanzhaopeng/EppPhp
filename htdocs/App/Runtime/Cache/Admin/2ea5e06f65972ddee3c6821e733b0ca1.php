<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>Eclipse++ Manage Platform</title><link rel="stylesheet" href="../Public/stylesheets/jquery.treeview.css" /><link rel="stylesheet" href="../Public/stylesheets/base.css" type="text/css" media="screen" /><link rel="stylesheet" href="../Public/stylesheets/jquery-ui.css" type="text/css" media="screen" /><link rel="stylesheet" id="current-theme" href="../Public/stylesheets/themes/bec/style.css" type="text/css" media="screen" /><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-1.5.1.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.scrollTo.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.localscroll.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/functions.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.form.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery-ui-1.8.11.min.js"></script><script type="text/javascript" charset="utf-8" src="../Public/js/jquery.tablePagination.0.5.min.js"></script><script type="text/javascript" src="../Public/js/jquery.easing.1.1.min.js"></script><script type="text/javascript" src="../Public/js/jquery.lavalamp-0.2.min.js"></script><script type="text/javascript" src="../Public/js/neoalchemy.min.js"></script><script src="../Public/js/lib/jquery.cookie.js" type="text/javascript"></script><script src="../Public/js/jquery.treeview.js" type="text/javascript"></script><script src="../Public/js/ajaxFileUpload.js" type="text/javascript"></script></head><body><div id="container"><div id="header"><h1><a href="index.html">Eclipse++ Manage Platform</a></h1><div id="user-navigation"><ul class="wat-cf"><li><a href="#">Paris Ecole centrale d'electronique & UPMC LIB6</a></li><li><a class="logout" href="/Admin/Public/logout">Logout</a></li></ul></div></div></div><div id="sidebar"><div class="block"><h3>Eclipse P2 Space</h3><ul class="navigation"><li><a href="/Admin/Category/index">Eclipse Category</a></li><li><a href="/Admin/Feature/index">Eclipse Feature</a></li></ul><h3>Repository Space</h3><ul class="navigation"><li><a href="/Admin/Repository/index">Repository</a></li><li><a href="/Admin/Repository/add">Add</a></li></ul><h3>Feature Modeling</h3><ul class="navigation"><li><a href="/Admin/Group/index">SPL Feature</a></li><li><a href="/Admin/Group/update">Update a Model</a></li><li><a href="/Admin/Group/add">Import a new Model</a></li></ul><h3>Build Eclipse++</h3><ul class="navigation"><li><a href="/Admin/Debug/index">Build</a></li></ul></div></div><div id="wrapper" class="wat-cf"><div id="main"><div class="block" id="block-tables"><div class="content"><h2 class="title">Software Product Line Feature</h2><div class="inner"><div style="display:blcok;">&nbsp;
						<div style="width:600px;height:40px;float: right;"><div id="name_con_div" style="display:inline-block;"><label>Select Feature Model: &nbsp;</label><select id="modelselect" name="modelselect" style="width:315px;height:28px;" onchange="changeModel(this);"></select><button class="button" onclick="showTree();" style="float: right;">Tree</button></div></div></div><div style="display:blcok;">&nbsp;
						<div style="width:600px;height:40px;float: right;"><div id="name_con_div" style="display:inline-block;"><input type="text" id="searchName" style="width:315px;height:28px;"><button class="button" onclick="searchByName();" style="float: right;">search</button></div></div></div><p></p><table id="dataTable" class="table" style="table-layout: fixed;max-width: 600px;word-wrap: break-word;"><tr><th style="width: 3%;">No.</th><th style="width: 10%;">Id</th><th style="width: 10%;">Name</th><th style="width: 32%;">Asset: Feature</th><th style="width: 32%;">Asset: Repository</th><th style="width: 10%;">Type</th><th style="width: 3%;">&nbsp;</th></tr><tbody id='listBody'></tbody></table></div><div class="actions-bar wat-cf"><div class="actions"></div><div id="pagination" class="pagination"></div></div></div></div><div class="inner div_hide" id="block-messages"><div class="flash"><div class="message error"><span><p>Error message</p></span></div><div class="message warning"><span><p>Warning message</p></span></div><div class="message notice"><span><p>Notice message</p></span></div></div></div><script>  /**
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
  
  
  </script><div class="block div_hide" id="groupForm"><div class="content"><h2 class="title">SPL Feature detail</h2><div class="inner"><form id="form1" action="/Admin/Group/doEdit" method="post" class="form"><input type='hidden' id='modelId' name='modelId'><input type='hidden' id='groupId' name='groupId'><div class="group"><div class="fieldWithErrors"><label class="label" for="post_title">Name</label></div><input type="text" class="text_field" id='name' name='name' /><span class="description">Ex: a simple text</span></div><div class="group"><div class="fieldWithErrors"><label class="label" for="post_title">Description</label></div><input type="text" class="text_field" id='description' name='description' /><span class="description">Ex: a simple text</span></div><div class="group"><div style="display:blcok;">&nbsp;
						<div style="width:600px;height:40px;float: right;"><div id="name_con_div" style="display:inline-block;"><input type="text" id="dia_searchName" style="width:315px;height:28px;"><button class="button" type="button" onclick="dia_searchByName();" style="float: right;">search</button></div></div></div><br /><br /><div class="fieldWithErrors"><label class="label" for="post_title">Features</label>				&nbsp; &nbsp;
				Just show selected features:<input id="selectedFeature" type="checkbox" class="checkbox toggle" value='' /></div><table id="diadataTable" class="table"><tr><th class="first"></th><th>number</th><th>id</th><th>name</th></tr><tbody id='dialistBody'></tbody></table><div id="diapagination" class="pagination"></div></br></div><div class="group navform wat-cf"><button id="btnEditSave" class="button" type="submit"><img src="../Public/images/icons/tick.png" alt="Save" /> Save
                  </button><span class="text_button_padding">or</span><a class="text_button_padding link_button" href="javascript:cancel();" >Cancel</a></div><script>        			var thumbPrefix = 'logo_w200_w200_';
        			var thumbW = 200;
        			var thumbH = 200;
			      </script></div></div></form></div></div></div><script type="text/javascript">var groupId = "";
var dia_searchCondition = "";
var dia_model_id;
var dia_currentPageIndex;
$(function(){
	$('#selectedFeature').removeAttr('checked');
	$('#selectedFeature').click(function(){
		if($("#selectedFeature").is(':checked')){
			dia_searchCondition = "";
			$("#dia_searchName").val("");
			dia_loadPageSelectedFeatures(1);
			
		}else{
			dia_loadPage(1);
		}
	});
	

  $('#btnEditSave').click(function(){
    $('#form1').ajaxSubmit({
      success:function(data){
        var obj = $.parseJSON(data);
        if (obj.status > 0) {
          alert(obj.msg);
        } else {
          cancel();
          loadPage(1);
        }
      }
    });
    return false;
  });
});

function dia_loadPageSelectedFeatures(pageIndex) {
	currPageIndex = pageIndex;
	//load data
	$.post('/Admin/Group/getSelectedFeaturesPage',{p:pageIndex,id:groupId,model:dia_model_id},function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			dia_loadData(obj);
		} else if(obj.status == 2) {
			$('#dialistBody').html('');
		} else if (obj.status == 0) {
		}
	});
}

function dia_loadPage(pageIndex) {
	dia_currentPageIndex = pageIndex;
	//load data
	$.post('/Admin/Group/getGroupPage',{p:pageIndex,id:groupId,fId:dia_searchCondition,model:dia_model_id},function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			dia_loadData(obj);
		} else if(obj.status == 2) {
			$('#dialistBody').html('');
		} else if (obj.status == 0) {
		}
	});
}

function dia_loadData(obj) {
	var data = obj.data;
	var html = '';
	
	//biuld pagination
	var pagination = '';
	var page = obj.page;
	var nowPage = parseInt(page.nowPage);
	var prevPage = nowPage - 1;
	totalPages = page.totalPages;
	var startNumber = (nowPage - 1)*15; 
	
	if (data != null) {
		$('#modelId').val(data[0].model_id);
		$('#groupId').val(data[0].id);
		$('#name').val(data[0].name);
		$('#description').val(data[0].description);
	
	if(data[0].status != 1){
		for(i=0; i<data.features.length; i++) {
			if(data.features[i].name != null || data.features[i].id != null ) {
				if (data.features[i].contained) {
						html = html + '<tr><td><input type="checkbox" class="checkbox" onclick="handleCheck(\''+data.features[i].id+'\',this);" name="features[]" value="'+data.features[i].id+'" checked/></td>';
				}else{
						html = html + '<tr><td><input type="checkbox" class="checkbox" onclick="handleCheck(\''+data.features[i].id+'\',this);" name="features[]" value="'+data.features[i].id+'" /></td>';
				}
		  
				html = html + '<td>'+(i+1 + startNumber)+'</td>';
				html = html + '<td>'+data.features[i].id+'</td>';
				html = html + '<td>'+data.features[i].name+'</td></tr>';  
			 }
		}
		$('#dialistBody').html(html);
		
		if (prevPage > 0) {
			pagination = '<span class="disabled prev_page" style="cursor:pointer;" onclick="javascript:dia_loadPage('+prevPage+');">« Prev</span>';	
		} else {
			pagination = '<span class="disabled prev_page">« Prev</span>';
		}
		
		for (i=1; i<= page.totalPages; i++) {
			if (i == nowPage) {
				pagination = pagination + '<span class="current">'+i+'</span>';
			} else {
				pagination = pagination + '<a href="javascript:dia_loadPage('+i+');">'+i+'</a>';					
			}
		}
		var nextPage = nowPage + 1;
		if (nextPage > page.totalPages) {
			pagination = pagination + '<span class="next_page">Next »</span>';	
		} else {
			pagination = pagination + '<span class="next_page" style="cursor:pointer;" onclick="javascript:dia_loadPage('+nextPage+');">Next »</a></span>';
		}
		
		$('#diapagination').html(pagination);
	}

	
	}
}

function cancel() {
	$("#groupForm").dialog("close");
}

function openEditDialog(id,model_id) {
  $('#groupForm').dialog({
		width: 800,
           height: 620,
           modal: true,
           title: 'Edit',
           create: function () { },
           open: function () { },
           close: function () { }
	});
	groupId = id;
	dia_model_id = model_id;
	$('#selectedFeature').removeAttr('checked');
	$('#dia_searchName').val("");
	html = '';
	dia_searchCondition = '';
	$('#dialistBody').html('');
	dia_loadPage(1);
}

function dia_searchByName() {
	dia_searchCondition = $('#dia_searchName').val();
	if($("#selectedFeature").is(':checked')){
		alert("Search function just when you unselect 'Just show selected features'");
	}else{
		dia_loadPage(1);
	}
}

function handleCheck(featureid,cb) {
	if(cb.checked){
		//add
		$.post('/Admin/Group/doAjaxEditFeature',{groupId:groupId,model:dia_model_id,feature:featureid,type:"add"},function(data){
			var obj = $.parseJSON(data);
		});
	}else{
		//delete
		$.post('/Admin/Group/doAjaxEditFeature',{groupId:groupId,model:dia_model_id,feature:featureid,type:"delete"},function(data){
			var obj = $.parseJSON(data);
		});
	}
}

</script><script type="text/javascript" src="../Public/js/d3.v2.js"></script><style>.link {
  fill: none;
  stroke: #ccc;
  stroke-width: 4.5px;
}
</style><div class="block div_hide" id="groupTreeForm"><div class="content"><h2 class="title">SPL Feature Model Tree</h2><div class="inner"><div id="viz"></div><script>        			var thumbPrefix = 'logo_w200_w200_';
        			var thumbW = 200;
        			var thumbH = 200;
			</script></div></div></div><script type="text/javascript">var tree_model_id;

function dia_loadTree() {
	//load data
	var treeData = {};
	$.post('/Admin/Group/getTree',{model:tree_model_id},function(data){
		var obj = $.parseJSON(data);
		treeData = obj[0];
		var vis = d3.select("#viz").append("svg:svg")
			.attr("width", "2000")
			.attr("height", "2500")
			.append("svg:g")
			.attr("transform", "translate(150, 0)");
			
		d3.select("#viz").selectAll("svg text").attr("selected", "false");

		var tree = d3.layout.tree()
			.size([800,500]);

		var diagonal = d3.svg.diagonal()
			.projection(function(d) { return [d.y, d.x]; });

		var nodes = tree.nodes(treeData);
		var link = vis.selectAll("pathlink")
			.data(tree.links(nodes))
			.enter().append("svg:path")
			.attr("class", "link")
			.attr("d", diagonal);

		var node = vis.selectAll("g.node")
			.data(nodes)
			.enter().append("svg:g")
			.attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })

		node.append("svg:circle")
			.attr("r", 4.5)
			.attr("fill",function(n){
				var status = n.status;
				if(n.status == 1)
					return "red";
				return "black";
			});

		node.append("svg:text")
			.attr("dx", function(d) { return d.children ? -8 : 8; })
			.attr("dy", 3)
			.attr("id", function(d) { return d.id; })
			.attr("text-anchor", function(d) { return d.children ? "end" : "start"; })
			.text(function(d) { return d.name; })
			.style("cursor", "hand")
			.on("click", function(d) { 
				var selectId = d.id;
				$.post('/Admin/Group/getDependency',{model:tree_model_id,group:selectId},function(dependency){
					var dp = $.parseJSON(dependency);
					//d3.select("#viz").selectAll("svg text").style("fill", "black");
					//d3.select("#viz").selectAll("svg text").attr("selected", "false");
					if(dp != null){
						for(j = 0; j<dp.length;j++){
							var id = dp[j].id;
							d3.select("#" + id).style("fill", "red"); 
							d3.select("#" + id).attr("selected", "true"); 
						}
					}
					d3.select("#" + d.id).style("fill", "red"); 
					d3.select("#" + d.id).attr("selected", "true"); 
				});
			});
	});

}

function openTreeDialog(model_id) {
  $('#groupTreeForm').dialog({
		   width: 1000,
		   height:600,
           modal: true,
           title: 'SPL Tree',
           create: function () { },
           open: function () { },
           close: function () { }
	});
	tree_model_id = model_id;
	$('#viz').html("");
	dia_loadTree();
}

</script></div></div><script>var currPageIndex = 1;
var totalPages = 0;
var searchNameCondition = '';
var startTime = '';
var endTime = '';
var current_model_id;
$(function(){
	//loadPage(1);
	loadModel();
	//search bar
	$('#cboxSearchName').attr('checked', 'true');
	$('#cboxSearchTime').removeAttr('checked');
	$('#cboxSearchName').click(function(){
		$('#cboxSearchTime').removeAttr('checked');
		$('#time_con_div').css('display', 'none');
		$('#name_con_div').css('display', 'inline-block');
	});
	$('#cboxSearchTime').click(function(){
		$('#cboxSearchName').removeAttr('checked');
		$('#time_con_div').css('display', 'inline-block');
		$('#name_con_div').css('display', 'none');
	});
	
	//search time
	$('#startTime').datepicker({
		'dateFormat' :'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		yearRange: "2013:2050"
	});
	$('#endTime').datepicker({
		'dateFormat' :'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		yearRange: "2013:2050"
	});
	
	
	$('#cbox1').removeAttr('checked');
	$('#cbox1').click(function(){
		if ($('#cbox1').attr('checked')) {
			$("input[type='checkbox']").attr('checked', 'true');			
		} else {
			$("input[type='checkbox']").removeAttr('checked');	
		}
	});
});

function changeModel(sel) {
	var value = sel.options[sel.selectedIndex].value;  
	current_model_id = value;
	loadPage(1);
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
					loadPage(1);
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

function loadPage(pageIndex) {
	currPageIndex = pageIndex;
	//load data
	$.post('/Admin/Group/loadGroupPage',{p:pageIndex,sName:searchNameCondition,model:current_model_id},function(data){
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
		html = html + '<tr><td>'+(i+1)+'</td>';
		html = html + '<td>'+data[i].id+'</td>';
		html = html + '<td>'+data[i].name+'</td>';
		if(data[i].status == 0){
			var featureHtml = '<ul>';
			for(j=0; j<data[i]['feature'].length; j++){
				featureHtml = featureHtml + '<li>'+ data[i]['feature'][j].id + '</li>';
			}
			featureHtml = featureHtml + '</ul>';
			html = html + '<td>'+featureHtml+'</td>';
			var repoHtml = '<ul>';
			for(h=0; h<data[i]['repo'].length ;h++){
				repoHtml = repoHtml + '<li>' +data[i]['repo'][h].repo + '</li>';
			}
			repoHtml = repoHtml + '</ul>';
			html = html + '<td>'+repoHtml+'</td>';
		}else{
			html = html + '<td></td><td></td>';
		}

		var type = '';
		if(data[i].status == 0){
			type = '<label style="color:blue;">Concrete</label>';
		}else if(data[i].status == 1){
			type = '<label style="color:green;">Abstract</label>';
		}else if(data[i].status == 2){
			type = '<label style="color:gray;">Hidden</label>';
		}else{
			type = '<label style="color:red;">Error</label>';
		}
		html = html + '<td>'+type+'</td>';
		html = html + '<td><a href="javascript:editItem(\''+data[i].id+'\');" style="cursor:poniter;"><img src="../Public/images/icons/edit.png"/></a></td></tr>';
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


function editItem(id) {
	
	openEditDialog(id,current_model_id);	
}

function searchByName() {
	searchNameCondition = $('#searchName').val();
	loadPage(1);
}

function showTree() {
	openTreeDialog(current_model_id);
}
</script></body></html>