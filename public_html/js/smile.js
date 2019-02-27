//charset "utf-8";
// JavaScript Document

function write_smile(code) {
	var currentmessage = document.comment.text.value;
	document.comment.text.value=currentmessage+code;
	document.comment.text.focus();
}