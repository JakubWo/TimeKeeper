$(function () {

    $("#logout").on("click", function () {
        $.ajax({
            type: "DELETE",
            url: "/api/logout",
            success: function (response) {
                if (response['response']['result'] === 'Success') {
                    window.location.replace('/');
                }
            },
            error: function (error) {
                errorHandler(error);
            }
        });

    });
});