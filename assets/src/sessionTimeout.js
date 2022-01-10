const maxIdleTime = 1800000; // in milliseconds
let idleTimeout;

function resetIdleTimeout() {
    if (idleTimeout) clearTimeout(idleTimeout);
    idleTimeout = setTimeout(() => window.location.replace('/'), maxIdleTime)
}

$(document).ready(function () {
    resetIdleTimeout();

    // Reset the idle timeout on any of the events listed below
    ['click', 'touchstart', 'mousemove'].forEach(event =>
        document.addEventListener(event, resetIdleTimeout)
    );
});