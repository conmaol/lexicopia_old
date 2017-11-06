$(document).on('click', '#en-plus', function() {
    $('#en-minus').show();
    $(this).hide();
    $('#en-text').show();
    return false;
});

$(document).on('click', '#en-minus', function() {
    $('#en-plus').show();
    $(this).hide();
    $('#en-text').hide();
    return false;
});

$(document).on('click', '#pos-plus', function() {
    $('#pos-minus').show();
    $(this).hide();
    $('#pos-text').show();
    return false;
});

$(document).on('click', '#pos-minus', function() {
    $('#pos-plus').show();
    $(this).hide();
    $('#pos-text').hide();
    return false;
});

function goBack() {
    entryhistory.pop();
    var newid = entryhistory.pop();
    entryhistory.push(newid);
    updateContent(newid);
}





