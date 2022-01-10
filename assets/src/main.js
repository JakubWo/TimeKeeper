$(document).ready(function () {
    $("#logout").click(function () {
        $.ajax({
            url: "/api/logout",
            success: function () {
                window.location.replace("/");
            }
        });

    });
});