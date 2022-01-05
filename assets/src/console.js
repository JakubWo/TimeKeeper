$(document).ready(function () {
    var start_button = $("#start");
    var stop_button = $("#stop");
    var break_button = $("#break");

    if (start_button.is(":disabled") && stop_button.is(":disabled") && break_button.is(":disabled")) {
        start_button.prop("disabled", false);
    }

    start_button.click(function () {
        $.ajax({
            url: "/api/start",
            success: function () {
                start_button.prop("disabled", true);
                stop_button.prop("disabled", false);
                break_button.prop("disabled", false);
            }
        });

    });

    stop_button.click(function () {
        $.ajax({
            url: "/api/stop",
            success: function () {
                start_button.prop("disabled", false);
                stop_button.prop("disabled", true);
                break_button.prop("disabled", true);
            }
        });
    });

    break_button.click(function () {
        $.ajax({
            url: "/api/break",
            success: function () {
                start_button.prop("disabled", false);
                break_button.prop("disabled", true);
            }
        });
    });
});
