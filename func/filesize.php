<?php
function FormateFileSize($size) {
	if ($size < 1024) {
		return $size." B";
	} elseif ($size < 1024*1024) {
		return round($size/1024)." KB";
	} elseif ($size < 1024*1024*1024) {
		return round($size/1024/1024, 1)." MB";
	} else {
		return round($size/1024/1024/1024, 1)." GB";
	}
}
