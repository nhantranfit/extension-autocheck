$(document).ready(function(){
    $("#btnDiemDanh").click(function(){
        $("#link").attr("href", "http://localhost:82/extension/php-training/autocheck.php")
        chrome.tabs.getSelected(null, function (tab) {
            var code = 'window.location.reload();';
            chrome.tabs.executeScript(tab.id, { code });
        });
    })
    $("#btnOpen").click(function(){
        chrome.tabs.getSelected(null, function (tab) {
            var newURL = 'http://localhost:82/extension/php-training/list.php';
            chrome.tabs.create({url: newURL });
        });
    })
});