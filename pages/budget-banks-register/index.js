const state = {
    banksRegisterDate: null,
    newCodesData: [],
    searchBudgetArticles: {
        newCodeIdSelected: null,
        startDate: null,
        endDate: null,
    },
}

document.addEventListener('DOMContentLoaded', () => {
    const spinner = document.getElementById("spinner-loader-id");
    spinner.style.visibility = "visible";
    const banksRegisterDateInput = document.getElementById("banks-register-date-filter");
    if (localStorage.getItem("banksRegisterFilterInfo")) {
        const filterInfo = JSON.parse(localStorage.getItem("banksRegisterFilterInfo"));
        state.banksRegisterDate = filterInfo.banksRegisterDate;
    } else {
        let date = new Date();
        date.setDate(date.getDate());
        let month = date.getMonth() + 1;
        state.banksRegisterDate = date.getFullYear() + '-' + (month < 10 ? "0" + month : month) + '-' + (date.getDate() - 1);
        

    }
    
    banksRegisterDateInput.value = state.banksRegisterDate;
    updateBanksRegisterRequest();
    getNewCodesRequest(state, fillNewCodesSelect.bind());
    rerenderStartDate();
    rerenderEndDate();
})

const updateBanksRegisterRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-banks-register.php";
    xhr.open("POST", requestURL);
    xhr.responseType = "";
    xhr.send(JSON.stringify({
        typeRequest: "updateBanksRegisterRequest",
        date: state.banksRegisterDate,
    }));
    xhr.onload = function () {
        console.log(xhr.response)
        renderTableRequest();
    }
}

const renderTableRequest = () => {
    const spinner = document.getElementById("spinner-loader-id");
    spinner.style.visibility = "visible";
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-banks-register.php";
    xhr.open("POST", requestURL);
    xhr.responseType = "";
    xhr.send(JSON.stringify({
        typeRequest: "renderTableRequest",
        date: state.banksRegisterDate,
    }));
    xhr.onload = function () {
        document.querySelector("#main-table").innerHTML = xhr.response;
        const spinner = document.getElementById("spinner-loader-id");
        spinner.style.visibility = "hidden";
    }
}

const banksRegisterDateInputOnChange = (target) => {
    state.banksRegisterDate = target.value;
    localStorage.setItem("banksRegisterFilterInfo", JSON.stringify({
        banksRegisterDate: state.banksRegisterDate
    }));
    updateBanksRegisterRequest();
}

const bankStatementsProcessing = (iban) => {
    localStorage.setItem("paymentsDirectoryInfo", JSON.stringify({
        iban: iban,
        date: state.banksRegisterDate,
    }));
    window.location.href = "../budget-payments-directory/";
}

const getNewCodesRequest = (paramState, callbackFunction) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-banks-register.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getNewCodesRequest",
    }));
    xhr.onload = () => {
        paramState.newCodesData = JSON.parse(xhr.response);
        callbackFunction();
    }
}

const fillNewCodesSelect = () => {
    rerenderNewCodesSelect();   
    const select = document.getElementById("budget-banks-register-budget-article-search__select");
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати";
    selectedOption.innerText = "Обрати";
    selectedOption.hidden = true;
    selectedOption.selected = state.newCodeIdSelected === null ? true : false;
    select.appendChild(selectedOption);
    if (state.newCodesData.length === 0)
        return;
    state.newCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.new_code;
        optionSelect.innerText = element.new_code;
        optionSelect.setAttribute("data-id", element.id);
        optionSelect.setAttribute("data-new-code", element.new_code);
        if (state.newCodeIdSelected === element.id) {
            optionSelect.selected = true;
            optionSelect.hidden = true;
        }
        select.appendChild(optionSelect);
    })

}

const rerenderNewCodesSelect = () => {
    const field = document.getElementById("budget-banks-register-budget-article-search-wrapper__select");
    while (field.firstChild)
        field.removeChild(field.firstChild);
    $("<select id='budget-banks-register-budget-article-search__select' class='new-code-select' title='Новий код'></select>").appendTo(field).select2();
    [...document.getElementsByClassName("select2-container")].forEach(element => element.style.width = "0");
    const select = document.getElementById("budget-banks-register-budget-article-search__select");
    select.onchange = newCodesSelectOnChange.bind(this, select);
}

const rerenderStartDate = () => {
    const startDateInput = document.getElementById("budget-banks-register-start-date-search-wrapper__input");
    startDateInput.innerHTML = "<input type='date' class='date-filter__input' " +
        " id='budget-banks-register-start-date-search__input' onchange='startDateOnChange(this)' />";
}

const rerenderEndDate = () => {
    const endDateWrapper = document.getElementById("budget-banks-register-end-date-search-wrapper__input");
    endDateWrapper.innerHTML = "<input type='date' class='date-filter__input' " +
        " id='budget-banks-register-end-date-search__input' onchange='endDateOnChange(this)' />";
}

const newCodesSelectOnChange = (target) => {
    state.searchBudgetArticles.newCodeIdSelected = target[target.selectedIndex].getAttribute("data-id");
    searchBudgetArticlesRequest();
}

const startDateOnChange = (target) => {
    state.searchBudgetArticles.startDate = target.value;
    searchBudgetArticlesRequest();
}

const endDateOnChange = (target) => {
    state.searchBudgetArticles.endDate = target.value;
    searchBudgetArticlesRequest();
}

const searchBudgetArticlesRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-banks-register.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "renderSearchTableRequest",
        newCodeId: parseInt(state.searchBudgetArticles.newCodeIdSelected),
        startDate: state.searchBudgetArticles.startDate,
        endDate: state.searchBudgetArticles.endDate,
    }));
    xhr.onload = () => {
        document.querySelector("#footer-table").innerHTML = xhr.response;
    }
}
