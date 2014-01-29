<?php
//upload image
function uploadImgShowThumb($thumbPrefix,$thumbW,$thumbH) {
	import('@.ORG.Util.UploadFile');
	$upload = new UploadFile();
	$upload->maxSize            = 3292200;
	$upload->allowExts          = explode(',', 'jpg,gif,png,jpeg');
	$upload->savePath           = './Uploads/';
	$upload->thumb              = true;
	$upload->imageClassPath     = '@.ORG.Util.Image';
	$upload->thumbPrefix        = $thumbPrefix;
	$upload->thumbMaxWidth      = $thumbW;
	$upload->thumbMaxHeight     = $thumbH;
	$upload->saveRule           = 'uniqid';
	$upload->thumbRemoveOrigin  = false; //delete the original image
	if (!$upload->upload()) {
		return false;
	} else {
		$uploadList = $upload->getUploadFileInfo();
	}
	if ($list !== false) {
		return $uploadList[0]['savename'];
	} else {
		return false;
	}
}

/**
 *
 * @param $originalImgName 原图名
 */
function thumbImg($orgImageName, $type,$width, $height) {
	if (!empty($orgImageName)) {
		import("ORG.Util.Image");
		$thumbImaName = '';
		$tmpArray = explode('/', $orgImageName);
		$count = count($tmpArray);
		$tmpArray[$count -1] = 'thumb_'.$tmpArray[$count -1];
		$thumbImaName = implode('/', $tmpArray);
		$Image = new Image();
		$thumbImaName = $Image->thumb($orgImageName, $thumbImaName, $type,$width, $height);

		return $thumbImaName;
	}
}

function encryptUid($text) {
	$key = '656cd9b0af9435b0fc692268364e2ff7';   //key的长度必须16，32位,这里直接MD5一个长度为32位的key
	$iv='2801003954373300'; //加密的随机数
	$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv);
	$crypttext = urlencode($crypttext);
	return base64_encode($crypttext);
}
function decryptUid($str) {
	$str = base64_decode($str);
	$str = urldecode($str);
	
	$key = '656cd9b0af9435b0fc692268364e2ff7';   //key的长度必须16，32位,这里直接MD5一个长度为32位的key
	$iv='2801003954373300'; //加密的随机数
	$decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC, $iv);
	
	return $decode;
}

function clear_html_tag($str) {
	$str = htmlspecialchars_decode($str);
	$str = preg_replace( "@<script(.*?)</script>@is", "", $str );
	$str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str );
	$str = preg_replace( "@<style(.*?)</style>@is", "", $str );
	$str = preg_replace( "@<(.*?)>@is", "", $str );
	return $str;
}

?>