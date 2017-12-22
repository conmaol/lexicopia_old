$(document).on('click', '#enPlus', function() {
    $('#enMinus').show();
    $(this).hide();
    $('#enText').show();
    return false;
});

$(document).on('click', '#enMinus', function() {
    $('#enPlus').show();
    $(this).hide();
    $('#enText').hide();
    return false;
});

$(document).on('click', '#posPlus', function() {
    $('#posMinus').show();
    $(this).hide();
    $('#posText').show();
    return false;
});

$(document).on('click', '#posMinus', function() {
    $('#posPlus').show();
    $(this).hide();
    $('#posText').hide();
    return false;
});

$(document).on("click", ".lexicopiaLink", function() {
    var id = $(this).attr("data-id");
    updateContent(id);
    return false;
});






