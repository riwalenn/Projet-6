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

function ConfirmDeconnexion() {
    if (confirm("Etes-vous sûr de vouloir vous déconnecter ?")) {
        return true;
    } else {
        return false;
    }
}

function confirmTrickDelete(id){
    $("#delete_trick_modal"+id).modal();
}

var nextTricksSlug = [

];

function getPenPath() {
    var slug = nextTricksSlug[ this.loadCount ];
    if ( slug ) {
        return 'https://s.codepen.io/desandro/debug/' + slug;
    }
}

var infScroll = new InfiniteScroll( '.container', {
    path: getPenPath,
append: '.post',
    button: '.view-more-button',
    // using button, disable loading on scroll
    scrollThreshold: false,
    status: '.page-load-status',
});