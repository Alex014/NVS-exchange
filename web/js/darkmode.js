$(document).ready(function () {
    // Toggle dark mode
    $(".toggle-icon").click(function () {
        $("body").toggleClass("dark-mode");
        $(".btn-primary").toggleClass("dark-mode");
        $(".container").toggleClass("dark-mode");
        $(".form-text").toggleClass("dark-mode");
        $("h1").toggleClass("dark-mode");
        $("h3").toggleClass("dark-mode");
        $(".form-label").toggleClass("dark-mode");
        $(".faucet").toggleClass("dark-mode");
        $(".text-line").toggleClass("dark-mode");
        $(".name").toggleClass("dark-mode");
        $(".value").toggleClass("dark-mode");
        // Change icon based on mode
        var iconPath = $("body").hasClass("dark-mode") ? "/img/light-mode.png" : "/img/dark-mode.png";
        $(".toggle-icon").attr("src", iconPath);

        if ($("body").hasClass("dark-mode")) {
            $.get("/cookies.php?darkmode=1")
        } else {
            $.get("/cookies.php?darkmode=0")
        }
    });
});