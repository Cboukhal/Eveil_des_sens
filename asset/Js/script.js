//$('selecteur').methode() //possibilité d'enchainer les méthodes sauf pour le HTML
$('*').css('margin','0').css('text-align','center');
$('H1').css('font-size','4em').css('text-align','center').css('color','crimson').css('padding','20px');
$('#deux').css('font-size','3em').css('text-align','center').css('color','green').css('padding','10px').css('background-color','whitesmoke');
$('.trois').css('font-size','1.5em').css('text-align','center').css('color','grey').css('padding','10px');
$('ul li:first-child').css('font-size','1.5em').css('text-align','center').css('color','red').css('padding','5px').css('background-color','purple');

//Methode html
$('#un').html("Je suis le H2 modifier depuis le Jquerry");//on peut enchainer en CSS mais pas en HTML.
//Methode text ne prendra que le texte et pas les balises
$('ul li:last-child').text("Je suis le li du Jquery");

//methode de class
// $('body').html("<selection id='align'></section>");
$('#align').html("<div></div><div></div><div></div><div></div>");
$("div").addClass("div");
$('#align').addClass("alignement"); //ajouter une class
$('#align').removeClass("alignement"); //supprime une class
$('#align').toggleClass("alignement"); //ajoute ou supprime une class

//methode des attributs
$('#img1').attr("src","../asset/image/dragon or.webp");
$('#img1').attr("alt","dragon doré");
$('#img1').removeAttr("alt","dragon doré");//sans précision, il supprime tout les att

//methode pour ajouter au début et à la fin d'un élément
//after et before
$('h1').before("****************************************************************************************************************************************");
$('h1').after("<hr>");

//prepend et append
$('body').prepend('<br><br>');//ajoute au début avant le before
$('body').append("<section id='sect2'></section>");
$('#sect2').append("<div></div>");
$('#sect2').addClass("alignement");
$("div").addClass("div");

//methode pour supprimer ou vider un elem
$('body').append('<br><br>');//ajoute au début avant le before
$('body').append("<section id='sect3'></section>");
$('#sect3').append("<div></div><div></div><div></div><div></div>");
$('#sect3').addClass("alignement");
$("div").addClass("div");
$("#sect3 div:first-child").remove()//supprime l'élém cibler
$("#sect3 div:last-child").remove()//supprime l'élém cibler
$("#sect3").empty();//pour vider l'élém

//___________________________________Evénement___________________________________\\
$("#app").css('padding','10px').css('background-color',"green").css('border-radius',"30px");
$("#dis").css('padding','10px').css('background-color',"red").css('border-radius',"30px");
// $("#app").on('click',"",()=>{//ancienne synthaxe ;"" => inutile
//     $('h1').css('visibility',"visible");
// });
// $("#dis").on('click',()=>{
//     $('h1').css('visibility',"hidden");
// });
$("#app").click(()=>{
    $('h1').show();//affich l'élém
});
$("#dis").click(()=>{
    $('h1').hide();//cache l'élém
});
//-----------img-----------\\
$('#img1').hover(()=>{
    $('#img1').css("opacity","1");
    $('.test p').css("visibility","visible");
});
$('#img1').mouseleave(()=>{
    $('#img1').css("opacity","50%");
    $('.test p').css("visibility","hidden");
});
//-------------------------------------------------------------------------------------------\\
// $("#ajout").css('padding','10px').css('background-color',"green").css('border-radius',"30px");
$("#ajout").css({
    padding: '10px',
    backgroundColor: 'fuchsia',
    borderRadius: '30px'
});
$("#ajout").click(()=>{
    $('#sect4').addClass("alignement");
    $('#sect4').append("<div></div>");
    $('#sect4 div').addClass('div');
});
$('#sect4').on('click','div',function(){
    $(this).remove();
});

//___________________________________Animation___________________________________\\

// $('#anim').click(()=>{
//     $('.form').animate({left : '250px'}).animate({top : '500px'}).animate({left : '20px'}).animate({top : '250px'}),3000;
// });
$('#anim').click(() => {
  $('.form')
    .animate({ left: '250px' }, 750)
    .animate({ top: '500px' }, 750)
    .animate({ left: '20px' }, 750)
    .animate({ top: '250px' }, 750);
});
$('.form').click(()=>{
    $('.form').toggleClass("rotation");
})

//-----------Caroussel_slider-----------\\
$(document).ready(()=>{ //atend la fin du chargement de la page
    let image = $('#slider img');
    // console.log (image);
    let nbrSlides = image.lenght; //nbr img
    let activeSlide = 0; //img q'on souhaite faire apparaitre
    image.eq(activeSlide).show(); //montre l'img ciblée
    setInterval (returnSlide,3000);
    function returnSlide(){ //return une nvlle img
        activeSlide++;
        if(activeSlide == nbrSlides){
            activeSlide = 0;
        }
        image.not(activeSlide).fadeOut(2000);
        image.eq(activeSlide).fadeIn(2000);
    }
})