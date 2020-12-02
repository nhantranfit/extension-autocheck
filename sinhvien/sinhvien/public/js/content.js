// $(document).ready(function(){
// 	chrome.storage.sync.get(["auto-login"], function(checkbox)	{
// 		if(checkbox["auto-login"]){
// 			login();
// 		}
// 	});
// 	chrome.runtime.onMessage.addListener(
// 		function(request,sender, sendResponse){
// 			if(request.message === "start"){
// 				login();
// 			}
// 		}
// 	)
// });
// function login() {
// 	chrome.storage.sync.get(["userselected", "passselected"], function(result) {
// 		$('input[id=input_user]').val(result["userselected"]);
// 		$('input[id=input_password]').val(result["passselected"]);
// 		$('button[id=bnt_summit]').click();
// 	});
	
// }