var lang = "gd";
var defaultContent = "Welcome to Lexicopia / GD! Get browsing.";
var entryhistory = [];
// create target index
$.getJSON("cache/target-index.json", function(data) {
    var target_index = data.target_index;
    var targetindexdiv = document.getElementById("trg-index");
    for (var i = 0; i < target_index.length; i++) {
        var item = target_index[i];
        //create a div with class indexitem and add it within trg-index
        var newdiv = document.createElement("div");
        newdiv.setAttribute("class", "indexitem");
        var newa = document.createElement("a");
        newa.setAttribute("href", "#");
        newa.setAttribute("data-id", item.id);
        newa.setAttribute("class", "lexicopia-link");
        newa.setAttribute('title', item.en);
        newa.innerText = item.word;
        newdiv.appendChild(newa);
        targetindexdiv.appendChild(newdiv);
    }
});
// create English index
$.getJSON("cache/english-index.json", function(data) {
    var english_index = data.english_index;
    var englishindexdiv = document.getElementById('en-index');
    for (i = 0; i < english_index.length; i++) {
        item = english_index[i];
        //create a div with class indexitem and add it within en-index
        var newdiv = document.createElement('div');
        newdiv.setAttribute('class', 'indexitem');
        var newa = document.createElement("a");
        newa.setAttribute("href", "#");
        newa.setAttribute("class", "en-index-link");
        var str = "";
        for (var j = 0; j < item.gds.length; j++) {
            str = str + item.gds[j].id;
            if (j < (item.gds.length - 1)) {
                str = str + ' ';
            }
        }
        newa.setAttribute("data-id1", str);
        newa.setAttribute("data-id2", item.en);
        //newa.setAttribute('onclick', 'entryhistory=[]; filterTrgIndexFromEn([' + str + '],\'' + item.en + '\')');
        newa.innerText = item.en;
        newdiv.appendChild(newa);
        englishindexdiv.appendChild(newdiv);
    }
});
$("#content-div-entry").html(defaultContent);

/* EVENT HANDLERS */

$("#resetPage").on('click', function() {
    resetIndices();
    entryhistory = [];
    $("#content-div-entry").html(defaultContent);
    $("#backbutton").hide();
    return false;
});

$('#randomEntry').on('click', function() {
    resetIndices();
    $.getJSON("cache/target-index.json", function(data) {
        var target_index = data.target_index;
        var randomid = target_index[Math.floor(Math.random()*target_index.length)].id;
        updateContent(randomid);
    });
    return false;
});

$("#trg-filterbox").on("keyup", function() {
    filterIndex("trg");
    return false;
});

$("#en-filterbox").on("keyup", function() {
    filterIndex("en");
    return false;
});

$(document).on('click', '.en-index-link', function() {
    var ids = $(this).attr('data-id1').split(" ");
    var en = $(this).attr('data-id2');
    entryhistory=[];
    var indexDiv = document.getElementById('trg-index');
    var divs = indexDiv.childNodes;
    for (var i = 1; i < divs.length; i++) {
        var div = divs[i];
        try {
            var a = div.childNodes[0];
            var id = a.getAttribute('data-id');
            if (ids.indexOf(id) > -1) {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
        catch (exc) {
        }
    }
    if (ids.length == 1) {
        updateContent(ids[0]);
    }
    else {
        document.getElementById("content-div-entry").innerHTML = "<p>Look at the index on the left for all the words that mean \'" + en + "\'.</p><p>Click on each of them for further information.</p>";
    }
    return false;
});

$("#backbutton a").on("click", function() {
    entryhistory.pop();
    var newid = entryhistory.pop();
    entryhistory.push(newid);
    updateContent(newid);
    return false;
});

/* HELPER FUNCTIONS */

function resetIndices() {
    document.getElementById("trg-filterbox").value = ""; // reset top left filterbox to empty
    filterIndex("trg"); // reset left index to default state
    document.getElementById("en-filterbox").value = ""; // reset top right filterbox to empty
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
    var str = document.getElementById(type + '-filterbox').value.toLowerCase();
    var indexDiv = document.getElementById(type + '-index');
    var divs = indexDiv.childNodes;
    for (var i = 1; i < divs.length; i++) {
        var e = divs[i];
        try {
            var f = e.firstChild.textContent.toLowerCase();
            if (f.startsWith(str)) {
                e.style.display = 'block';
            } else {
                e.style.display = 'none';
            }
        }
        catch (exc) {
        }
    }
    return false;
}

function updateContent(id) {
    entryhistory.push(id);
    $('#content-div-entry').load("../code/php/generateLexicalEntry.php?lang=" + lang + "&id=" + id);
    if (entryhistory.length > 1) {
        $('#backbutton').show();
    }
    else {
        $('#backbutton').hide();
    }
    return false;
}



