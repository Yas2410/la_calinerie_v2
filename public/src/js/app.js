
// je m'assure que la "page" soit bien chargée avant d'executer mon code
$(document).ready(function() {

    $('.menuResp a').click(function(){

        $('.trigger').toggle();
        $('.menuResp').toggleClass('round');
        $('.close').toggle();
        $('.drop-down').toggleClass('down');
    });

});