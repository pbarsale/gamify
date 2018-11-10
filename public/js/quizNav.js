$(document).ready(function(){
    $(".questiondivs div").each(function(e) {

        if (e != 0)
            $(this).hide();
    });


    $("#next").click(function(){
        if ($(".questiondivs div:visible").next().length != 0)
            $(".questiondivs div:visible").next().show().prev().hide();
        else {
            $(".questiondivs div:visible").hide();
            $(".questiondivs div:first").show();
        }
        return false;
    });

    $("#prev").click(function(){
        if ($(".questiondivs div:visible").prev().length != 0)
            $(".questiondivs div:visible").prev().show().next().hide();
        else {
            $(".questiondivs div:visible").hide();
            $(".questiondivs div:last").show();
        }
        return false;
    });
});