$(document).ready(function(){
	$('#send').click(function(){
		var mssv = $('#mssv').val();
		if(mssv == ''){
			$("#thongbao").html(
				`<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<span>Không để trống</span>
				</div>`
			)
		}	
		else{
			$.post("http://localhost:82/extension/php-training/checksv.php",{mssv},function (data) {
				if(data == 0){
					$("#thongbao").html(
						`<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<span>Sinh viên này đã điểm danh</span>
						</div>`
					)
				}
				else if(data == 1){
					$("#thongbao").html(
						`<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<span>Không tìm thấy sinh viên</span>
						</div>`
					)
				}
				else if(data == 2){
					$("#thongbao").html(
						`<div class="alert alert-success alert-dismissible fade show" role="alert">
						<span>Điểm danh thành công</span>
						</div>`
					)
				}
				else if(data == 3){
					$("#thongbao").html(
						`<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<span>Điểm danh thất bại</span>
						</div>`
					)
				}
			})
			$('#mssv').val('')
		}
	});
})
