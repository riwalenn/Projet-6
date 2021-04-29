//formulaires : modifie la div qui englobe les inputs
$(".form-control").parent().addClass("form-div");

$(".list-group>nav").attr("id", "nav-pagination");

var pagination = $(".pagination");
pagination.addClass("pagination-sm");

//si la page est tricks_detail alors la barre de menu devient bleu de base
var urlcourante = document.location.pathname.split("/");
if (urlcourante.indexOf("tricks_detail") === 1) {
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

function seeMedia() {
    $("#seeMedias").on("click", function () {
        $(".tricks_medias .container").removeClass("d-none d-lg d-lg-block");
    });
}

function confirmTrickDelete(id) {
    $("#delete_trick_modal" + id).modal();
}

function confirmMediaDelete(id) {
    $("#delete_media_modal" + id).modal();
}

function modifyRoleUser(id) {
    $("#change_role_user" + id).modal();
}