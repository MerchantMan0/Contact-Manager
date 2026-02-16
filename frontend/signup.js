// Build API URL dynamically
const apiUrl = window.location.origin + "/api/registration.php";

/* Global variables */
let userId = 0;
let firstName = "";
let lastName = "";
let username = "";

function doSignup() {
    userId = 0;

    username = document.getElementById("signupName").value;
    const password = document.getElementById("signupPassword").value;
    firstName = document.getElementById("signupFirstName").value;
    lastName = document.getElementById("signupLastName").value;

    document.getElementById("signupResult").innerHTML = "";

    const payload = {
        firstName: firstName,
        lastName: lastName,
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
                    document.getElementById("signupResult").innerHTML = "Invalid server response";
                    return;
                }

                userId = jsonObject.id;

                if (userId < 1) {
                    document.getElementById("signupResult").innerHTML = "Signup failed. Please try again.";
                    return;
                }

                firstName = jsonObject.userFirstName;
                lastName = jsonObject.userLastName;

                saveCookie();
                window.location.href = "contacts.html";
            } else {
                document.getElementById("signupResult").innerHTML =
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
    document.cookie = "username=" + encodeURIComponent(username) + expires;
}
