/*      GESTION DES VIDEOS DANS LA COLLECTION     */
var videosHolder = $("#videos_list");
videosHolder.data("index", videosHolder.find(".card").length);

//ajouter une vidéo
var addNewVideoBtn = $("<a href='#videos_list' class='btn btn-info' style='margin-top:1rem;'>Ajouter une vidéo</a>");
videosHolder.append(addNewVideoBtn);

//supprimer une vidéo
function addRemoveVideoBtn(card) {
    var removeBtn = $("<a href='#videos_list' class='btn btn-danger'><i class='fas fa-trash'></i></a>");

    var removeDiv = $("<div class='col-auto'></div>").append(removeBtn);

    removeBtn.click(function (e) {
        $(e.target).parents(".card.border-light").slideUp(1000, function () {
            e.preventDefault();
            $(this).remove();
        });
    });

    card.append(removeDiv);
}

videosHolder.find(".card-body").each(function () {
    var ids = $("[id^='trick_videos_']");
    ids.addClass("col-auto mr-auto");
    addRemoveVideoBtn($(this));
});

function addNewFormVideo() {
    var prototype = videosHolder.data("prototype");
    var index = videosHolder.data("index");
    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);

    videosHolder.data("index", index++);

    var card = $("<div class='card border-light'></div>");
    var cardBody = $("<div class='card-body alert alert-secondary row'></div>").append(newForm);

    card.append(cardBody);

    addRemoveVideoBtn(card);

    addNewVideoBtn.before(card);
}

addNewVideoBtn.click(function (e) {
    e.preventDefault();
    addNewFormVideo();
});

/*      GESTION DES IMAGES DANS LA COLLECTION     */
var imgHolder = $("#images_list");

//ajouter une image
var addNewImg = $("<a href='#images_list' class='btn btn-info'>Ajouter une image</a>");

//supprimer une image
function addRemoveImgButton(card) {
    var removeButton = $("<a href='#images_list' class='btn btn-danger'><i class='fas fa-trash'></i></a>");
    var removeDiv = $("<div class='col-1'></div>").append(removeButton);

    removeButton.click(function (e) {

    });

    card.append(removeDiv);
}

imgHolder.find(".card-body").each(function () {
    $("[id^='trick_images_']").addClass("col-10");
    addRemoveImgButton($(this));
});

