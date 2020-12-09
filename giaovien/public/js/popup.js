
// function winOpen(url){
//     return window.open(url,getWinName(url), 'width=1200,height=800px,toolbar=yes,location=yes,menubar=yes');
//   }

// function getWinName(url){
//     return "win" + url.replace(/[^A-Za-z0-9\-\_]*/g,"");
// }

$(document).ready(function () {
    var childWindow = "";
    var newTabUrl = "http://localhost:82/extension/php-training/list_diemdanh.php";

    $("#btnOpenList").click(function(){
        childWindow = window.open(newTabUrl, "_blank",'width=1200px,height=800px,toolbar=yes,location=yes,menubar=yes');
    })
  
    $("#btnDiemDanh").click(function () {
        $("#link").attr("href", "http://localhost:82/extension/php-training/autocheck.php") 
        // chrome.tabs.getSelected(null, function (tab) {
        //     var code = 'window.location.reload();';
        //     console.log(tab.id)
        //     // chrome.tabs.executeScript(tab.id, { code });
        // });
    
        // chrome.storage.sync.get(["list"], function(result) {
		// 	var get_list = result['list'];
        //     console.log(get_list);
    		
        // });
        // winOpen('http://localhost:82/extension/php-training/list_diemdanh.php');
        childWindow.location = newTabUrl;
    })


  
    // $("#btnOpen").click(function () {
    //     // chrome.tabs.getSelected(null, function (tab) {
    //     //     var newURL = 'http://localhost:82/extension/php-training/list.php';
    //     //     chrome.tabs.create({url: newURL });
    //     // });
    //     window.open('http://localhost:82/extension/php-training/list_diemdanh.php', '_blank', 'width=1200px,height=800px,toolbar=yes,location=yes,menubar=yes');
    // }) 
});

