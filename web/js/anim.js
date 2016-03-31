$(function(){

	$(".dropdown-menu li").click(function(){
		$(".container-content").fadeOut(1000);
		$("h1").hide("slide");
	});

	$(".navbar-brand").click(function(){
		$(".container-content").fadeOut(1000);
		$("h1").hide("slide");
	});

	$(document).ready(function(){
		$("h1").hide().show("slide");
		$(".container-content").hide().fadeToggle(1000);
	});
	
	$(window).load(function() {
     	$('#loading').hide();
  	});

});