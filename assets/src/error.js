$(document).ready(function () {
    $("#redirection_button").click(function () {
        document.cookie = "error=;expires= Thu, 01 Jan 1970 00:00:00 GMT;SameSite=Lax";
        window.location.replace('/');
    });
});