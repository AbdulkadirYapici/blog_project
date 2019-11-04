// Automatic Slideshow - change image every 3 seconds
var myIndex = 0;
carousel();

function carousel() {
    var i;
    var x = document.getElementsByClassName("mySlides");
    console.log(x) ;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }

    myIndex++;
    console.log(myIndex) ;

    if (myIndex > x.length) {myIndex = 1}
    x[myIndex-1].style.display = "block";
    setTimeout(carousel, 3000);
}

