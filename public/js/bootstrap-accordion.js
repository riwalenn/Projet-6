$(".toggle-accordion").on("click", function() {
    var accordionId = $(this).attr("accordion-id"),
        numPanelOpen = $(accordionId + ' .collapse.in').length;

    $(this).toggleClass("active");

    if (numPanelOpen == 0) {
        openAllPanels(accordionId);
    } else {
        closeAllPanels(accordionId);
    }
})