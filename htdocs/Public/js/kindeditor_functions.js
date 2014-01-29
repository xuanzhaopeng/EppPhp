var editor;
var tempContent = '';
var tempId = '';

KindEditor.ready(function(K) {
	editor = K.create('textarea[name="content"]', {
		resizeType : 1,
		allowPreviewEmoticons : false,
		allowImageUpload : false,
		allowFlashUpload:false,
		items : [
			'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
			]
	});
});

function loaddata() {
	var uid = $('#uid').val();
	if (uid == '') {
		alert('{$Think.lang.pls_enter_basic_info}');
		return false;
	}
	var html = '';	
	$.post('/Users/loadCatchword', {uid:uid}, function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 1) {
			$('#words').html('');	
			var words = obj.data;
			if (words != undefined) {
				for(i = 0; i < words.length; i++) {
					j = i+1;
					var content = HtmlDecode(words[i].content);
					content = content.split('\\').join('');
					html = html + "<li id='span_"+words[i].id+"'>"+"<a id='"+words[i].id+"' href='javascript:editContent("+words[i].id+");'>" + content +"</a></li><br />";
					//html = words[0].content;
				}
				//var tmp = $('#words').html(html);
				$('#words').html(html);	
			}
		} else if (obj.status == 0) {
			var msg = HtmlDecode('{$Think.lang.load_data_failed}');
			alert(msg);
		} 		
	});
}

function saveEditorContent(uid) {
	if (uid == '') {
		alert('{$Think.lang.pls_enter_basic_info}');
		return false;
	}
	
	var content = editor.text();
	if (tempId == '') {
		doAddEditorContent(uid, content);
	} else {
		if (content == '') {
			doDelEditorContent(tempId);
		} else {
			doUpdateEditorContent(tempId, content);
		}
	}
	
}


function editContent(id) {
	var aid = '#'+id;
	var itemContent = $(aid).html();
	tempId = id;
	tempContent = itemContent;
	editor.html(itemContent);
}

function doAddEditorContent(uid, content) {
	$.post('/Users/addCatchword', {uid:uid,content:content},function(data){
		loaddata();
		editor.html('');
	});
}

function doDelEditorContent(id) {
	$.post('/Users/delCatchword',{id:id}, function(data){
		var obj = $.parseJSON(data);
		if (obj.status == 0) {
			alert(data.msg);
		} else {
			loaddata();
		}
	});
	clearTempData();
}

function doUpdateEditorContent(id) {
	var content = editor.html();
	if (content != tempContent) {
		$.post('/Users/updateCatchword', {id:id, content:content}, function(data){
			var obj = $.parseJSON(data);
			if (obj.status == 0) {
				alert(data.msg);
			} else {
				loaddata();
			}
			editor.html('');
		});
	}
	clearTempData();
}

function clearTempData() {
	tempId = '';
	tempContent = '';
}

function doEditorFormSubmit() {
	editor.sync();
	$('#form1').submit();
}