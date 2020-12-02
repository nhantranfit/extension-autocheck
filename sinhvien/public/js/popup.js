var send = document.querySelector('#content__send');
var notify = document.querySelector('.header__notify--success');
send.onclick = function () {
	getMSSV();
}

function getMSSV() {
	var mssv = document.querySelector('.content__mssv-input').value;
	$.get('http://localhost:80/nnnnn/login/checkhs.php', {mssv}, function (data) {
		if(data == 1) {
			$(notify).html("<div class='alert alert-failed'>Điểm danh không thành công!</div>")
		}
		else {
			$(notify).html("<div class='alert alert-success'>Điểm danh thành công!</div>")
		}
	});
	
}