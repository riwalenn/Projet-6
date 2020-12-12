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

function addMedia(id) {
    $("#add_trick_media"+id).modal();
}

function confirmMediaDelete(id){
    $("#delete_media_modal"+id).modal();
}

function confirmTrickDelete(id){
    $("#delete_trick_modal"+id).modal();
}

function modifyRoleUser(id){
    $("#change_role_user"+id).modal();
}
function toggleInput() {
    var inputLink  = $("input[name='lien']");
    var inputFile = $("input[name='file']");
    var checkboxImgUp = $("#typeImageUpload");
    var checkboxImg = $("#typeImage");
    var checkboxVid = $("#typeVideo");
    if (inputLink.attr("disabled")) {
        //inputs
        inputLink.prop({disabled: false});
        inputFile.prop({disabled: true});
        $("input[name='type']").prop({disabled: false});
        $("#imageDownload").css("opacity",".5");
        $("#imageLink").css("opacity","1");
        //checkbox
        checkboxImgUp.prop({checked: false});
        checkboxImg.prop({checked: true});
        checkboxImgUp.prop({disabled: true});
        checkboxImg.prop({disabled: false});
        checkboxVid.prop({disabled: false});
    } else {
        //inputs
        inputLink.prop({disabled: true});
        inputFile.prop({disabled: false});
        $("#imageLink").css("opacity",".5");
        $("#imageDownload").css("opacity","1");
        //checkbox
        checkboxImgUp.prop({checked: true});
        checkboxImg.prop({checked: false});
        checkboxVid.prop({checked: false});
        checkboxImgUp.prop({disabled: false});
        checkboxImg.prop({disabled: true});
        checkboxVid.prop({disabled: true});
    }
}