
/**
 * Slow prints an example URL into the URL box.
 */
function printExampleSite(text) {
    var slowprinter = window.setInterval(function(){
        var progress = $("#urlbox").attr("placeholder").length; // number of characters printed so far

        if (progress == text.length) {
            // Finished printing
            window.clearInterval(slowprinter);
        }

        $("#urlbox").attr("placeholder", text.substr(0, progress + 1));
        console.log("print...");
    }, 80);
}

$(document).ready(function(){

    // Fetch a random site using CloudFlare for an example
    $.get("cloudflare-site.php", function(data){
        printExampleSite(data);
    });

});
