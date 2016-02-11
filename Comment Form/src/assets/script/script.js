/**
 * Created by alexandrshumilow on 06/02/16.
 */

/**
 * Validates form fields and sends message
 * @returns {boolean}
 */
function sendFeedback() {

    clearFormMessage();

    if (! validateFeedbackForm()) {
        return false;
    }
    try {
        var formElement = document.getElementById("feedback-form");
        var xhttp;
        if (window.XMLHttpRequest) {
            xhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xhttp.onreadystatechange = function () {

            if (xhttp.readyState === XMLHttpRequest.DONE) {
                if (xhttp.status === 200) {
                    showFormMessage('Message sent successfully', 'success');
                } else {
                    var message = 'Could not send message';
                    if (xhttp.responseText) {
                        var response = JSON.parse(xhttp.responseText);
                        if (response) {
                            message = '';
                            for (var i = 0; i < response.length; i++) {
                                message += response[i] + "<br />";
                            }
                        }
                    }
                    showFormMessage(message, 'error');
                }
            }
        };
        xhttp.open("POST", "/feedback", true);
        xhttp.setRequestHeader('Content-Type', 'application/json');
        xhttp.send(JSON.stringify({
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            comment: document.getElementById('comment').value
        }));
    } catch (e) {
        showFormMessage('Exception: ' + e.message, 'error');
    }

    return false;
}

/**
 * Validates form fields
 * @returns {boolean}
 */
function validateFeedbackForm() {

    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;
    var comment = document.getElementById('comment').value;

    if (name.length == 0 || name.length > 50) {
        showFormMessage('Name is required and length should be max. 50 characters', 'error');
        return false;
    }

    if (email.length == 0 || email.length > 100) {
        showFormMessage('Email is required and length should be max. 100 characters', 'error');
        return false;
    }
    if (! email.match(/^[A-Za-z\._\-[0-9]*[@][A-Za-z]*[\.][a-z]{2,4}$/)) {
        showFormMessage('Email is invalid', 'error');
        return false;
    }

    if (comment.length == 0 || comment.length > 200) {
        showFormMessage('Comment is required and length should be max. 200 characters', 'error');
        return false;
    }

    return true;
}

/**
 * Shows error or success messages in form
 *
 * @param message
 * @param type - error or success
 */
function showFormMessage(message, type) {

    if (type == 'error') {
        document.getElementById('form-message').className = "error";
    } else if (type == 'success') {
        document.getElementById('form-message').className = "success";
    }

    document.getElementById('form-message').innerHTML = "<p>" + message + "</p>";
    document.getElementById('form-message').style.display = 'block';
}

/**
 * Clears and hides error or success messages in form
 */
function clearFormMessage() {
    document.getElementById('form-message').innerHTML = "";
    document.getElementById('form-message').className = "";
    document.getElementById('form-message').style.display = 'none';
}