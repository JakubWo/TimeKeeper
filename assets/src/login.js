$(function () {
    const error_alert_text = "Couldn't connect to API. Please try again.\n" +
        "If error continues contact server admin.";

    const submit_button = $("#submit_button");
    const error_login = $("#error_login");


    // <noscript>html code for no js in here</noscript>
    // Add block timer on button!

    // if (error_login.innerText !== '') {
    //     submit_button.disabled = true;
    //     setTimeout(function () {
    //         submit_button.disabled = false;
    //     }, 20000)
    // }

    $(document).on("keydown", function (event) {
        if (event.key === "Enter") {
            checkLoginParams();
        }
    });

    submit_button.on("click", function () {
        checkLoginParams();
    });


    function checkLoginParams() {

        const emailError = $("#error_email");
        const passwordError = $("#error_password");

        const emailInput = $("#email_input").val();
        const passwordInput = $("#password_input").val();

        error_login.text("");
        emailError.text("");
        passwordError.text("");

        if (passwordInput === "") {
            passwordError.text("You must fill this field");
        }
        if (emailInput === "") {
            emailError.text("You must fill this field");
            return;
        }

        const validateEmailResult = validateEmail(emailInput);

        if (validateEmailResult === false) {
            emailError.text("Invalid email given");
        } else if (passwordInput !== "") {
            const data = {'email': emailInput, 'password': passwordInput};
            $.ajax({
                type: "POST",
                data: data,
                dataType: "json",
                url: "/api/login",
                success: function (response) {
                    console.log(response);
                    if (response['response']['result'] === 'Success') {
                        $("#login_form").trigger("submit");
                    } else {
                        const apiErrorDetails = response['response']['error']['title'].split('Action failed: ')[1];
                        error_login.text(apiErrorDetails);
                    }
                },
                error: function () {
                    alert(error_alert_text);
                }
            });
        }
    }

    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
});