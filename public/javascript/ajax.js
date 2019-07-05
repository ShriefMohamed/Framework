var currentLocation = document.location.origin + '/';

function CallAjax(url, callback = null) {
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (callback != null) {
                if (this.responseText) {
                    callback(JSON.parse(this.responseText));
                } else {
                    callback(null);
                }
            }
        }
    };
    url = currentLocation + url;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function SendForm(url, form_id, button_html, callback = null) {
    $("#" + form_id).submit(function (e) {
        var form_data = $(this).serialize();
        var button = $(this).find('button[type=submit]');
        button.html(button_html + '...');
        url = currentLocation + url;
        jQuery.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: form_data
        }).done(function (data) {
            if (callback !== null) {
                if (data) {
                    callback(data);
                } else {
                    callback(null);
                }
            }
            // button.html();
        });
        e.preventDefault();
    });
}

function UpdateInput(url, input, callback = null) {
    if (input.value) {
        var value = encodeURIComponent(input.value);
        var parameters = "value="+value;
        url = currentLocation + url;

        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                input.value = "";
                input.placeholder = decodeURI(value);
                if (callback != null) {
                    if (this.responseText) {
                        callback(JSON.parse(this.responseText));
                    } else {
                        callback(null);
                    }
                }
            }
        };

        xmlhttp.open("POST",url,true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(parameters);
    }
}

function SendImage(url, input_id, callback = null) {
    var file_data = $('#' + input_id).prop('files')[0];
    var form_data = new FormData();
    form_data.append('file', file_data);
    url = currentLocation + url;

    jQuery.ajax({
        url         : url,
        dataType    : 'text',
        cache       : false,
        contentType : false,
        processData : false,
        data        : form_data,
        type        : 'post',
        success     : function(data) {
            if (callback !== null) {
                if (data) {
                    callback(data);
                } else {
                    callback(null);
                }
            }
        }
    });
    $('#change-image-input').val('');
}