function showEnglish() {
    document.getElementById("en-minus").style.display = "inline"; // display the [-eng]
    document.getElementById("en-plus").style.display = "none"; // hide the [+eng]
    document.getElementById("en-text").style.display = "inline"; // display the English text
}

function hideEnglish() {
    document.getElementById("en-minus").style.display = "none"; // hide the [-eng]
    document.getElementById("en-plus").style.display = "inline"; // show the [+eng]
    document.getElementById("en-text").style.display = "none"; // hide the English text
}

function showPOS() {
    document.getElementById("pos-minus").style.display = "inline"; // display the [-eng]
    document.getElementById("pos-plus").style.display = "none"; // hide the [+eng]
    document.getElementById("pos-text").style.display = "inline"; // display the English text
}

function hidePOS() {
    document.getElementById("pos-minus").style.display = "none"; // hide the [-eng]
    document.getElementById("pos-plus").style.display = "inline"; // show the [+eng]
    document.getElementById("pos-text").style.display = "none"; // hide the English text
}

function goBack() {
    entryhistory.pop();
    var newid = entryhistory.pop();
    entryhistory.push(newid);
    updateContent(newid);
}





