$(document).ready(function (){
	$('#hide_button a').click(function (){
		$('.toolbox').fadeOut('fast');
		$('#show_toolbox').fadeIn('fast');
		$.post(toolbox_toggle_link, {show: 0});
	});
	$('#show_toolbox a').click(function (){
		$('#show_toolbox').fadeOut('fast');
		$('.toolbox').fadeIn('fast');
		$.post(toolbox_toggle_link, {show: 1});
	});
});