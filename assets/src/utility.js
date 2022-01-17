function errorHandler(errorResponse) {
    const error_alert_text = "Couldn't connect to API. Please try again.\n" +
        "If error continues contact server admin.";
    console.log(JSON.parse(errorResponse.responseText)["response"]["error"]["title"]);
}