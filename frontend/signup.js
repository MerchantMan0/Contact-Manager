const urlBase = 'http://locallyhosted.software'; /* replace with actual backend URL */
const extension = 'php';

/* Global variables so cookie has access */
let userId = 0;
let firstName = "";
let lastName = "";
let username = "";
let password = "";


function doSignup() {
    userId = 0;
    username = document.getElementById("signupName").value;
    password = document.getElementById("signupPassword").value;

    firstName = document.getElementById("signupFirstName").value;
    lastName = document.getElementById("signupLastName").value;

    document.getElementById("signupResult").innerHTML = "";

    let url = urlBase + '/Signup.' + extension; /* replace with actual signup endpoint */

    let tmp = { firstName: firstName, lastName: lastName, username: username, password: password };
    let jsonPayload = JSON.stringify(tmp);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                userId = jsonObject.id;

                if (userId < 1) {
                    document.getElementById("signupResult").innerHTML = "Signup failed. Please try again.";
                    return;
                }
                document.getElementById("signupResult").innerHTML = "";
                
                firstName = jsonObject.userFirstName;
                lastName = jsonObject.userLastName;
                saveCookie();
                window.location.href = "contacts.html";
            }

        };
        xhr.send(jsonPayload);
    } catch (err) {
        document.getElementById("signupResult").innerHTML = err.message;
    }
}

function saveCookie() {
    let minutes = 20;
    let date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000));
    document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ",username=" + username + ";expires=" + date.toGMTString();
}