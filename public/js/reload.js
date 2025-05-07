//loading
document.addEventListener("DOMContentLoaded", function() {
    let loadingOverlay = document.getElementById("loading-overlay");

    setTimeout(() => {
        loadingOverlay.style.display = "flex";
    }, 100);

    window.onload = function() {
        setTimeout(() => {
            loadingOverlay.style.opacity = "0";
            setTimeout(() => {
                loadingOverlay.style.display = "none";
                document.getElementById("content").style.display = "block";
            }, 500);
        }, 300);
    };
});