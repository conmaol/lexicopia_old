/* GLOBAL VARIABLES */

var defaultContent = "Welcome to Lexicopia! Get browsing.";
var entryHistory = [];

/* STUFF TO DO ON LOADING THE PAGE */

// create and display target index
$.getJSON("cache/targetIndex.json", function(data) {
    var targetIndex = data.targetIndex;
    for (var i = 0; i < targetIndex.length; i++) {
        var item = targetIndex[i];
        var link = "<a href='#' data-id='" + item.id + "' class='lexicopiaLink' title='" + item.en + "'>" + item.target + "</a>";
        var div = "<div class='indexItem'>" + link + "</div>";
        $("#trgIndex").append(div);
    }
});
// create and display English index
$.getJSON("cache/englishIndex.json", function(data) {
    var englishIndex = data.englishIndex;
    for (i = 0; i < englishIndex.length; i++) {
        item = englishIndex[i];
        var str = "";
        for (var j = 0; j < item.targets.length; j++) {
            str = str + item.targets[j].id;
            if (j < (item.targets.length - 1)) {
                str = str + ' ';
            }
        }
        var link = "<a href='#' data-id1='" + str + "' data-id2='" + item.en + "' class='enIndexLink'>" + item.en + "</a>";
        var div = "<div class='indexItem'>" + link + "</div>";
        $("#enIndex").append(div);
    }
});
$("#contentDivEntry").html(defaultContent);

/* EVENT HANDLERS */

$("#resetPage").on("click", function() {
    resetIndices();
    entryHistory = [];
    $("#contentDivEntry").html(defaultContent);
    $("#backButton").hide();
    return false;
});

$("#randomEntry").on("click", function() {
    resetIndices();
    $.getJSON("cache/targetIndex.json", function(data) {
        var targetIndex = data.targetIndex;
        var randomId = targetIndex[Math.floor(Math.random()*targetIndex.length)].id;
        updateContent(randomId);
    });
    return false;
});

$("#trgFilterBox").on("keyup", function() {
    filterIndex("trg");
    return false;
});

$("#enFilterBox").on("keyup", function() {
    filterIndex("en");
    return false;
});

$(document).on("click", ".enIndexLink", function() {
    var ids = $(this).attr("data-id1").split(" ");
    var en = $(this).attr("data-id2");
    entryHistory=[];
    var indexDiv = document.getElementById("trgIndex"); // $("trgIndex")
    var divs = indexDiv.childNodes;
    for (var i = 1; i < divs.length; i++) {
        var div = divs[i];
        try {
            var a = div.childNodes[0];
            var id = a.getAttribute("data-id");
            if (ids.indexOf(id) > -1) {
                div.style.display = "block";
            } else {
                div.style.display = "none";
            }
        }
        catch (exc) {
        }
    }
    if (ids.length == 1) {
        updateContent(ids[0]);
    }
    else {
        $("#contentDivEntry").html("<p>Look at the index on the left for all the words that mean \'" + en + "\'.</p><p>Click on each of them for further information.</p>");
        //document.getElementById("contentDivEntry").innerHTML = "<p>Look at the index on the left for all the words that mean \'" + en + "\'.</p><p>Click on each of them for further information.</p>";
    }
    return false;
});

$("#backButton a").on("click", function() {
    entryHistory.pop();
    var newId = entryHistory.pop();
    entryHistory.push(newId);
    updateContent(newId);
    return false;
});

/* HELPER FUNCTIONS */

function resetIndices() {
    $("#trgFilterBox").val("");
    filterIndex("trg"); // reset left index to default state
    $("#enFilterBox").val("");
    filterIndex("en"); // reset right index to default state
    return false;
}

if (typeof String.prototype.startsWith != 'function') {
    String.prototype.startsWith = function (str) {
        return this.substring(0, str.length) === str;
    }
}

function filterIndex(type) {
    /* when one of the filterboxes is altered (or when the reset button is clicked, or when the page is first loaded), this function changes the relevant index to suit */
    var str = document.getElementById(type + "FilterBox").value.toLowerCase();
    var indexDiv = document.getElementById(type + "Index");
    var divs = indexDiv.childNodes;
    for (var i = 1; i < divs.length; i++) {
        var e = divs[i];
        try {
            var f = e.firstChild.textContent.toLowerCase();
            if (f.startsWith(str)) {
                e.style.display = "block";
            } else {
                e.style.display = "none";
            }
        }
        catch (exc) {
        }
    }
    return false;
}

function updateContent(id) {
    entryHistory.push(id);
    $("#contentDivEntry").load("../code/php/generateLexicalEntry.php?lang=" + lang + "&id=" + id);
    if (entryHistory.length > 1) {
        $("#backButton").show();
    }
    else {
        $("#backButton").hide();
    }
    return false;
}



