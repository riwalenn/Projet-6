//formulaires : modifie la div qui englobe les inputs
$('.form-control').parent().addClass('form-div');

//si la page est tricks_detail alors la barre de menu devient bleu de base
var urlcourante = document.location.pathname.split('/');
if (urlcourante.indexOf('tricks_detail') === 1) {
    document.querySelector("#header").classList.remove("header-transparent");
    document.querySelector("#header").classList.add("header-scrolled");
} else {
    false;
}