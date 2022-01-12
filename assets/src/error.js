$(function () {
    $("#redirection_button").on("click", function () {
        document.cookie = "error=;expires= Thu, 01 Jan 1970 00:00:00 GMT;SameSite=Strict";
        window.location.replace('/');
    });
});