document.addEventListener("DOMContentLoaded", () => {
    let href = window.location.href.split("/");
    if (href[href.length - 2] === "budget-planned-indicators" ||
        href[href.length - 2] === "budget-payments-directory") {
        const headerLogo = document.getElementsByClassName("header-logo")[0];
        headerLogo.style.marginLeft = "23.35%";
    }
    if (href[href.length - 2] !== "budget-planned-indicators" &&
        href[href.length - 2] !== "budget-payments-directory") {
        window.localStorage.setItem("previousUrl", JSON.stringify(window.location.href));
    }

})

const goBack = () => {
    if(window.localStorage.getItem("previousUrl")) {
        location.replace(JSON.parse(window.localStorage.getItem("previousUrl")));
    } else {
        window.history.back();
    }
}