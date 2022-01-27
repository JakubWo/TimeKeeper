let timesAppended = 0;

function loadWorkdays(action = "append") {
    let rowStyle = $(".workday details:nth-child(2) table").attr("class");
    let amountOfWorkdays = 1;

    if (action === "start") {
        rowStyle = rowStyle === "row0" ? 1 : 0;
    } else {
        rowStyle = rowStyle === "row0" ? 0 : 1;
    }

    if (action === "append") {
        amountOfWorkdays = 14;
    }

    $.ajax({
        type: "GET",
        data: {
            "amountOfWorkdays": amountOfWorkdays,
            "timesAppended": timesAppended,
            "action": action,
            "rowStyle": rowStyle,
        },
        url: "/view/workday",
        success: function (response) {
            switch (action) {
                case "append":
                    timesAppended += 1;
                    $(".workday").append(response);
                    break;
                case "start":
                    $(".headers").after(response);
                    break;
                case "update":
                    $(".workday details:nth-child(2)").replaceWith(response);
                    break;
            }
        },
        error: function (error) {
            if (error.responseText === "No more records") {
                $(".load_more").hide();
                $(".bottom").hide();
                console.log(error.responseText);
            }
        }
    });
}


$(function () {
    loadWorkdays();

    $("#logout").on("click", function () {
        $.ajax({
            type: "DELETE",
            url: "/api/logout",
            success: function (response) {
                if (response["response"]["result"] === "Success") {
                    window.location.replace("/");
                }
            },
            error: function (error) {
                errorHandler(error);
            }
        });

    });
    $(".bottom").on("click", function () {
        loadWorkdays();
    });

});