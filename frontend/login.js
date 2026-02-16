const urlBase = 'http://locallyhosted.software'; /* replace with actual backend URL */
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function doLogin() {
    userId = 0;
    firstName = "";
    lastName = "";

    let username = document.getElementById("loginName").value;
    let password = document.getElementById("loginPassword").value;

    document.getElementById("loginResult").innerHTML = "";

    // JSON payload to send to backend
    let tmp = { username: username, password: password };
    let jsonPayload = JSON.stringify(tmp);

    let url = urlBase + '/Login.' + extension; /* replace with actual login endpoint */

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                userId = jsonObject.id;
                
                if (userId < 1) {
                    document.getElementById("loginResult").innerHTML = "Username/Password incorrect";
                    return;
                }

                firstName = jsonObject.userFirstName;
                lastName = jsonObject.userLastName;

                saveCookie();
                window.location.href = "contacts.html";
            }
        };
        xhr.send(jsonPayload);
    } catch (err) {
        document.getElementById("loginResult").innerHTML = err.message;
    }
}

function saveCookie() {
    let minutes = 20;
    let date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000));
    document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}


