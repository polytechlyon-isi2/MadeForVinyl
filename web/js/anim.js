$(function(){

	$(".dropdown-menu li").click(function(){
		$(".container-content").fadeOut(1000);
		$("footer").fadeOut(1000);
		$("h1").hide(1000);
	});

	$(".navbar-brand").click(function(){
		$(".container-content").fadeOut(1000);
		$("footer").fadeOut(1000);
		$("h1").hide(1000);
	});
	$("#admin").click(function(){
		$(".container-content").fadeOut(1000);
		$("footer").fadeOut(1000);
		$("h1").hide(1000);
	});
	$("#basket").click(function(){
		$(".container-content").fadeOut(1000);
		$("footer").fadeOut(1000);
		$("h1").hide(1000);
	});

	$(document).ready(function(){
		$("h1").hide().show(1000);
		$(".container-content").hide().fadeToggle(1000);
		$("footer").hide().fadeToggle(1000);
	});
	
	$(window).load(function() {
     	$('#loading').hide();
  	});

});