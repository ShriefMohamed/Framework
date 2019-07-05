var currentLocation = document.location.origin + '/';

function Show(id) {
    // document.getElementById(id).style.display = 'block';
    // document.getElementById(id).style.visibility = 'visible';
    // document.getElementById(id).style.opacity = '1';
    // document.getElementById(id).style.transitionDelay = '0s'
    $('#' + id).show(600);
}

function Hide(id) {
    // document.getElementById(id).style.display = 'none';
    // document.getElementById(id).style.visibility = 'hidden';
    // document.getElementById(id).style.opacity = '0';
    // document.getElementById(id).style.transition = 'visibility 0s linear 0.33s, opacity 0.33s linear';
    $('#' + id).hide(600);
}

function RemoveAll(className) {
    var elements = document.getElementsByClassName(className);
    if (elements) {
        for (var i = 0; i < elements.length; i++) {
            elements[i].remove(elements[i].selectedIndex);
        }
    }
}

function InsertHTML(id, html) {
    document.getElementById(id).appendChild(html);
}

function ClearMessage() {
    var message = document.getElementById('message-text');
    message.innerHTML = '';
}

function InsertMessage(message, type) {
    Show('message');

    var strong = document.createElement('strong');
    var span = document.createElement('span');

    span.innerHTML = message;
    if (type == 1) {
        strong.innerHTML = "Success!";
    } else {
        strong.innerHTML = "Error!";
    }

    InsertHTML('message-text',strong);
    InsertHTML('message-text',span);
}

function ShowFeedback() {
    var feedback = getCookie('feedback');
    if (feedback) {
        Show('message');
        feedback = JSON.parse(feedback);
        for (var i = 0; i < feedback.length; i++) {
            var span = document.createElement('span');
            var string = feedback[i].replace(/[+]/g,' ');
            span.innerHTML = string;
            InsertHTML('message-text', span);
        }
        removeCookie('feedback');
    }
}

function setCookie(cname, cvalue, exhourse = 24) {
    var d = new Date();
    d.setTime(d.getTime() + (exhourse*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function removeCookie(name) {
    setCookie(name, 'none', -1);
}