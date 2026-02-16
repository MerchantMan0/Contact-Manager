// Build API URL dynamically (no hardcoded domain)
const apiUrl = window.location.origin + "/api/login.php";

/* Global variables */
let userId = 0;
let firstName = "";
let lastName = "";

function doLogin() {
    userId = 0;
    firstName = "";
    lastName = "";

    const username = document.getElementById("loginName").value;
    const password = document.getElementById("loginPassword").value;

    document.getElementById("loginResult").innerHTML = "";

    const payload = {
        username: username,
        password: password
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", apiUrl, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                let jsonObject;

                try {
                    jsonObject = JSON.parse(xhr.responseText);
                } catch (err) {
                    document.getElementById("loginResult").innerHTML = "Invalid server response";
                    return;
                }

                userId = jsonObject.id;

                if (userId < 1) {
                    document.getElementById("loginResult").innerHTML = "Username or password incorrect";
                    return;
                }

                firstName = jsonObject.userFirstName;
                lastName = jsonObject.userLastName;

                saveCookie();
                window.location.href = "contacts.html";
            } else {
                document.getElementById("loginResult").innerHTML =
                    "Server error: " + xhr.status;
            }
        }
    };

    xhr.send(JSON.stringify(payload));
}

function saveCookie() {
    const minutes = 20;
    const date = new Date();
    date.setTime(date.getTime() + minutes * 60 * 1000);
    const expires = ";expires=" + date.toUTCString() + ";path=/";

    document.cookie = "firstName=" + encodeURIComponent(firstName) + expires;
    document.cookie = "lastName=" + encodeURIComponent(lastName) + expires;
    document.cookie = "userId=" + userId + expires;
}
