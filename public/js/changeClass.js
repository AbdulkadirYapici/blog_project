document.getElementById("form_preview_img").className = "";

var x = document.getElementsByClassName("custom-file-label");
x[0].className= "";

console.log (x[0]);

$(function () {

    $('input[type="file"]').change(function () {
        if ($(this).val() != "") {

            $(this).css('content ', 'kadirr');




        }else{
            $(this).css('color', 'transparent');


        }
    });
})