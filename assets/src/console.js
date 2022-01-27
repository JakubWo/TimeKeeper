$(function () {
    const start_button = $("#start");
    const stop_button = $("#stop");
    const break_button = $("#break");

    const custom_time_checkbox = $("#custom_time_checkbox");
    let custom_time_input = $("#custom_time_input");
    custom_time_checkbox.prop("checked", false);
    custom_time_input.prop("disabled", true);

    if (start_button.is(":disabled") && stop_button.is(":disabled") && break_button.is(":disabled")) {
        start_button.prop("disabled", false);
        stop_button.prop("disabled", false);
        break_button.prop("disabled", false);
    }

    custom_time_checkbox.on("change", function () {
        if (this.checked) {
            custom_time_input.prop("disabled", false);
        } else {
            custom_time_input.prop("disabled", true);
        }
    });

    /*
        START EVENT
     */
    start_button.on("click", function () {
        if (custom_time_input.val() === '' && custom_time_checkbox.is(":checked")) {
            alert("Time input box not/partially filled.\nPlease uncheck custom time or enter valid time.");
            custom_time_input.get(0).setCustomValidity("Invalid field.");
            return;
        }

        const data = {
            time_zone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            time_input: custom_time_input.val()
        };

        $.ajax({
            type: "POST",
            data: data,
            dataType: "json",
            url: "/api/start",
            success: function (response) {
                console.log(response);
                if (response['response']['action'] === "Stop break") {
                    loadWorkdays('update');
                } else {
                    loadWorkdays('start');
                }

                start_button.prop("disabled", true);
                stop_button.prop("disabled", false);
                break_button.prop("disabled", false);
            },
            error: function (error) {
                errorHandler(error);
            }
        });
    });

    /*
        BREAK EVENT
    */
    break_button.on("click", function () {
        $.ajax({
            type: "PATCH",
            dataType: "json",
            url: "/api/break",
            success: function (response) {
                console.log(response);
                // loadWorkdays(1, 'update'); ???
                start_button.prop("disabled", false);
                break_button.prop("disabled", true);
            },
            error: function (error) {
                errorHandler(error);
            }
        });
    });

    /*
        STOP EVENT
     */
    stop_button.on("click", function () {
        $.ajax({
            type: "PATCH",
            dataType: "json",
            url: "/api/stop",
            success: function (response) {
                console.log(response);
                loadWorkdays('update');
                start_button.prop("disabled", false);
                stop_button.prop("disabled", true);
                break_button.prop("disabled", true);
            },
            error: function (error) {
                errorHandler(error);
            }
        });
    });


});
