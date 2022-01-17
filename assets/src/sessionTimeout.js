const maxIdleTime = 1800000;
let idleTimeout;

function resetIdleTimeout() {
    if (idleTimeout) clearTimeout(idleTimeout);
    idleTimeout = setTimeout(() => window.location.replace('/'), maxIdleTime)
}

$(document).on("ready", function () {
    resetIdleTimeout();

    ['click', 'touchstart', 'mousemove'].forEach(event =>
        document.addEventListener(event, resetIdleTimeout)
    );
});