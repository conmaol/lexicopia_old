/* this is just a helper function */
if (typeof String.prototype.startsWith != 'function') {
    String.prototype.startsWith = function (str) {
        return this.substring(0, str.length) === str;
    }
}

function createIndices() {
    // create target index
    $.getJSON("cache/target-index.json", function(data) {
        var target_index = data.target_index;
        var targetindexdiv = document.getElementById('trg-index');
        for (var i = 0; i < target_index.length; i++) {
            var item = target_index[i];
            //create a div with class indexitem and add it within trg-index
            var newdiv = document.createElement('div');
            newdiv.setAttribute('class', 'indexitem');
            newdiv.setAttribute('id', item.id);
            newdiv.setAttribute('onclick', 'entryhistory=[\'' + item.id + '\'];updateContent(\'' + item.id + '\')');
            newdiv.setAttribute('title', item.en);
            newdiv.innerText = item.word;
            /*
            if (item.word.indexOf(' ') > 0) {
                newdiv.style.display = 'none';
            }
            */
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
            newdiv = document.createElement('div');
            newdiv.setAttribute('class', 'indexitem');
            var str = "";
            for (var j = 0; j < item.gds.length; j++) {
                str = str + '\'' + item.gds[j].id + '\'';
                if (j < (item.gds.length - 1)) {
                    str = str + ',';
                }
            }
            newdiv.setAttribute('onclick', 'entryhistory=[]; filterTrgIndexFromEn([' + str + '],\'' + item.en + '\')');
            newdiv.innerText = item.en;
            englishindexdiv.appendChild(newdiv);
        }
    });
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
}

function filterTrgIndexFromEn(ids, en) {
    /* when an English index item is clicked, this function filters the target language index to suit */
    var indexDiv = document.getElementById('trg-index');
    var divs = indexDiv.childNodes;
    for (var i = 1; i < divs.length; i++) {
        var div = divs[i];
        try {
            var id = div.getAttribute('id');
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
        entryhistory.push(ids[0]);
        updateContent(ids[0]);
    }
    else {
        document.getElementById("content-div-entry").innerHTML = "<p>Look at the index on the left for all the words that mean \'" + en + "\'.</p><p>Click on each of them for further information.</p>";
    }
}

function resetpage() {
    /* when reset link in banner is clicked, this function resets the page back to the default initial state, with unfiltered indexes and the introductory message in the middle */
    document.getElementById('trg-filterbox').value = ''; // reset top left filterbox to empty
    filterIndex('trg'); // reset left index to default state
    document.getElementById('en-filterbox').value = ''; // reset top right filterbox to empty
    filterIndex('en'); // reset right index to default state
    document.getElementById("content-div-entry").innerHTML = defaultContent;
    entryhistory = [];
}

function updateContent(id) {
    // update the content panel when a new lexical entry is selected
    //$('#content-div-entry').load("../../lexicopia-entries/" + lang + "/html/" + id + ".html");
    $('#content-div-entry').load("../code/php/generatelexicalentry.php?lang=" + lang + "&id=" + id);
    if (entryhistory.length > 1) {
        document.getElementById("backbutton").style.display = 'block';
    }
    else {
        document.getElementById("backbutton").style.display = 'none';
    }
}

function getRandom() {
    $.getJSON("cache/target-index.json", function(data) {
        var target_index = data.target_index;
        var randomid = target_index[Math.floor(Math.random()*target_index.length)].id;
        entryhistory=[randomid];
        updateContent(randomid);
    });

}


