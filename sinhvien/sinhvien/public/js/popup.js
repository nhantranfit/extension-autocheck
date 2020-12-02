$(document).ready(function(){
	$('#send').click(function(){
		var mssv = $('#mssv').val();	
			$.post("http://localhost:82/test3/php-training/autocheck.php",{mssv},function (data) {
			})
	});
})
