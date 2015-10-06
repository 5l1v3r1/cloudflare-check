
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

    $("#urlform").submit(function(){
        // Form submitted, check if site uses Cloudflare
        var site = $("#urlbox").val();

        // Lock URL box
        $("#urlbox").attr("readonly", true);
        $("#urlbox").addClass("urlbox-readonly");

        var request = $.get("cloudflare-check.php?url=" + encodeURIComponent(site));
        request.done(function(data){
            // Hide existing dialogs
            $(".result").hide();

            if (data.status) {
                if (data.on_cloudflare) {
                    $(".result-yes").fadeIn(250);
                } else {
                    $(".result-no").fadeIn(250);
                }
            } else {
                $(".result-error").fadeIn(250);
            }
        });
        request.fail(function(){
            alert("Error!\n" + "Failed to connect to CloudFlare testing server!")
        });
        request.always(function(){
            // Unlock url box
            $("#urlbox").attr("readonly", false);
            $("#urlbox").removeClass("urlbox-readonly");
        });

        return false;
    });

    // Set result boxes to block objects hidden with jQuery.
    $(".result").css("display", "block");
    $(".result").hide();

});
