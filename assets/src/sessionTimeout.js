const maxIdleTime = 1800000;
let idleTimeout;

// TODO: refresh session on activity!
function resetIdleTimeout() {
    if (idleTimeout) clearTimeout(idleTimeout);
    idleTimeout = setTimeout(() => window.location.replace('/'), maxIdleTime)
}

$(function () {
    resetIdleTimeout();

    ['click', 'touchstart', 'mousemove'].forEach(event =>
        document.addEventListener(event, resetIdleTimeout)
    );
});