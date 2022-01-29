let timesAppended = 0;
let selectedUser = null;

const acceptedImage = 'assets/Image/accepted.png';

function loadWorkdays(action = "append") {
    let rowStyle = $(".workday details:nth-child(2)").attr("class");
    let amountOfWorkdays = 1;

    if (rowStyle !== undefined) {
        rowStyle = rowStyle.substring(0, 4);
    }

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
                "user": selectedUser,
                "rowStyle": rowStyle,
            },
            url: "/view/workday",
            success: function (response) {
                switch (action) {
                    case "append":
                        timesAppended += 1;
                        $(".workday").append(response);
                        $(".load_more").show();
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
                if (action === "append") {
                    if (error.responseText === "No more records to show.") {
                        $(".load_more").hide();
                    } else if (error.responseText === "Empty workday history, or some unexpected error occurred.") {
                        $(".headers").after("<p class='workday_row'>" + error.responseText + "</p>");
                        $(".load_more").hide();
                    }
                }
            }
        }
    );

}


$(function () {
    let userSelection = $(".user_selection");

    userSelectionChange();

    userSelection.on("change", function () {
        userSelectionChange();
    });

    function userSelectionChange() {
        selectedUser = userSelection.find(":selected").text();
        $(".workday").children(".workday_row").remove();

        selectedUser === "YOU" ? selectedUser = null : undefined;
        timesAppended = 0;
        loadWorkdays()
    }

    $(".load_more").show();

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
                let errorText = JSON.parse(error.responseText);
                let errorTitle = errorText["response"]["error"]["title"];
                alert(errorTitle);
            }
        });
    });


    $(document).on("click", ".acceptation_button", function (event) {
        let eventTarget = $(event.target);
        let eventTargetParent = eventTarget.parents("details");
        let workdayId = eventTargetParent.attr("class").slice(25);

        let button_td = $(this).parent("td");

        $.ajax({
            type: "POST",
            data: {
                "workday_id": workdayId
            },
            url: "/api/acceptWorkday",
            success: function () {
                button_td.remove();
                let img = eventTargetParent.find("img");
                img.attr("src", acceptedImage);
                img.parent("td").attr("class", "is_accepted");
            },
            error: function (error) {
                let errorText = JSON.parse(error.responseText);
                let errorTitle = errorText["response"]["error"]["title"];
                alert(errorTitle);
            }
        })

    });

    $(document).on("click", ".workday_row", function (event) {
        let eventTarget = $(event.target);
        if (eventTarget.text() === "Accept" || eventTarget.prop("tagName") === "DETAILS") {
            return;
        }

        let targetParent = eventTarget.parents("details");
        let workdayId = targetParent.attr("class").slice(25);

        let target = $(".workday_" + workdayId + " span");
        if (target.text() !== "") {
            return;
        }

        $.ajax({
            type: "GET",
            data: {
                "workday_id": workdayId
            },
            url: "/view/event",
            success: function (response) {
                target.replaceWith("<span>" + response + "</span>");
            },
            error: function (error) {
                target.replaceWith("<span>" + error.responseText + "</span>");
            }
        })
    })

    $(".bottom").on("click", function () {
        loadWorkdays();
    });

});