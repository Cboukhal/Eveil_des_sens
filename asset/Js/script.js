$(document).ready(()=>{
    let image = $('#slider img')
    let nbrSlides = image.length;
    let imageActive = 0;
    image.eq(imageActive).show();
    returnSlide();
    function returnSlide()
    {  
        $('#sgauche').click(()=>{
            if(imageActive > 0)
                imageActive--;
            else
                imageActive = nbrSlides;
            image.not(imageActive).fadeOut(500);
            image.eq(imageActive).fadeIn(500);
        });
        $('#sdroite').click(()=>{
            imageActive++;
            if(imageActive == nbrSlides)
                imageActive = 0;
            image.not(imageActive).fadeOut(500);
            image.eq(imageActive).fadeIn(500);
        });
    }
})

/*-------------------------------------BURGER--------------------------------------*/
const burger = document.querySelector('.burger');
const nav = document.querySelector('nav');
burger.addEventListener('click',()=>{
    burger.classList.toggle('croix');
    nav.classList.toggle('active');
})

/*-------------------------------------QSN--------------------------------------*/
$('.image img').mouseenter(()=>{ 
    $('.texte').show();
})
$('.apropos').mouseleave(()=>{ 
    $('.texte').hide();
})

/*-------------------------------------menu--------------------------------------*/
const btn = document.querySelectorAll(".menus .btn");
const popup = document.querySelectorAll(".menus .popup");
const fermer = document.querySelectorAll(".menus .close");


for (let i = 0; i < btn.length; i++) {
    btn[i].addEventListener('click', (event) => {
        event.preventDefault();//est une méthode JavaScript utilisée pour empêcher le comportement par défaut d’un événement dans le navigateur.
        popup[i].style.visibility = 'visible';
    });
    fermer[i].addEventListener('click', () => {
        popup[i].style.visibility = 'hidden';
    });
}


/*-------------------------------------articles--------------------------------------*/
let articles = $('.blog .article');
for(let article of articles){
    $(article).mouseenter(()=>{
        $(article).css('border','2px solid red').css('background-color','white').css('color','black')
    })
    $(article).mouseleave(()=>{
        $(article).css('border','').css('background-color','black').css('color','white')
    })
}

/*-------------------------------------Contact--------------------------------------*/
const champs = document.querySelectorAll('.contact input, .contact textarea');
for(let champ of champs){
    champ.addEventListener("focus",()=>{
        champ.style.backgroundColor = 'orange';
    })
}
for(let champ of champs){
    champ.addEventListener("blur",()=>{
        champ.style.backgroundColor = 'white';
    })
}

// const form = document.querySelector('.contact form');
// const reussi = document.querySelector('.reussite');
// form.addEventListener('submit',(element)=>{
//         element.preventDefault();
//         reussi.style.display = "block";
//         setTimeout(()=>{
//             reussi.style.display = "none";
//         },5000)
//     })

/*-------------------------------------Reservation--------------------------------------*/
// const formr = document.getElementById('form-reservation');
//         const popupr = document.getElementById('popup');

//         formr.addEventListener('submit', function(event) {
//             event.preventDefault();
//             popupr.style.display = 'block';
//             setTimeout(() => {
//                 popupr.style.display = 'none';
//                 formr.reset();
//             }, 4000);
//         });

/*-------------------------Estimation-------------------------*/
let menu_s = document.getElementById('menu_s');
let menu_c = document.getElementById('menu_c');
let menu_b = document.getElementById('menu_b');
let menu_d = document.getElementById('menu_d');
let saveurs = document.getElementById('menus_saveurs');
let carte = document.getElementById('menu_carte');
let boissons = document.getElementById('boissons');
let dessert = document.getElementById('dessert');
let effacer = document.getElementById('effacer');
let final = document.getElementById('final');
let HT = document.getElementById('HT');
let TVA = document.getElementById('TVA');
let TTC = document.getElementById('TTC');
let total_H = 0;

menu_s.addEventListener('click', () => {
    const Menu = saveurs.value;
    const texteMenu = saveurs.options[saveurs.selectedIndex].text;

    total_m.innerHTML += "<li>";
    total_m.innerHTML += texteMenu;
    total_m.innerHTML += "</li>";
    total_H= total_H + parseInt(Menu);
    HT.innerHTML = total_H;
    TVA.innerHTML = total_H * 0.055;

});
menu_c.addEventListener('click', () => {
    const Menu = carte.value;
    const texteMenu = carte.options[carte.selectedIndex].text;

    total_c.innerHTML += "<li>";
    total_c.innerHTML += texteMenu;
    total_c.innerHTML += "</li>";
    total_H = total_H + parseInt(Menu);
    HT.innerHTML = total_H;
    TVA.innerHTML = total_H * 0.055;
});
menu_b.addEventListener('click', () => {
    const Menu = boissons.value;
    const texteMenu = boissons.options[boissons.selectedIndex].text;

    total_b.innerHTML += "<li>";
    total_b.innerHTML += texteMenu;
    total_b.innerHTML += "</li>";;
    total_H = total_H + parseInt(Menu);
    HT.innerHTML = total_H;
    TVA.innerHTML = total_H * 0.055;
});
menu_d.addEventListener('click', () => {
    const Menu = dessert.value;
    const texteMenu = dessert.options[dessert.selectedIndex].text;

    total_d.innerHTML += "<li>";
    total_d.innerHTML += texteMenu;
    total_d.innerHTML += "</li>";
    total_H = total_H + parseInt(Menu);
    HT.innerHTML = total_H;
    TVA.innerHTML = total_H * 0.055;
});

final.addEventListener('click', () => {
    TTC.innerHTML = total_H + total_H * 0.055;
});
effacer.addEventListener('click', () => {

    total_H = 0;
    total_m.innerHTML = "";
    total_c.innerHTML = "";
    total_b.innerHTML = "";
    total_d.innerHTML = "";
    HT.innerHTML = "";
    TVA.innerHTML = "";
    TTC.innerHTML = "";
});
