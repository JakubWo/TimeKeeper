document.addEventListener("keydown", function(event) {
    if(event.key === "Enter"){
        checkLoginParams();
    }
})

function checkLoginParams() {
    const emailError = document.getElementById("error_email");
    const passwordError = document.getElementById("error_password");

    const emailInput = document.getElementById("email_input").value;
    const passwordInput = document.getElementById("password_input").value;

    emailError.innerText = "";
    passwordError.innerText = "";

    if (passwordInput === "") {
        passwordError.innerText = "You must fill this field";
    }
    if (emailInput === "") {
        emailError.innerText = "You must fill this field";
        return;
    }

    const validateEmailResult = validateEmail(emailInput);

    if(validateEmailResult === false) {
        emailError.innerText = "Invalid email given";
    } else if(passwordInput !== "") {
        document.getElementById("login_form").submit();
    }

}

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}