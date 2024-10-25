const state = {
    userInfo: [],
    sideBarItemSelected: null,
    reportsData: [{
            id: 1,
            name: "register_of_costs_under_the_contract_report",
            title: "Реєстр витрат по договору",
            method: "createROCUCReport",
        },
        {
            id: 2,
            name: "register_of_expenditures_by_budget_article_report",
            title: "Реєстр витрат по статті бюджету",
            method: "createROEBBAReport",
        },
        {
            id: 3,
            name: "daily_report",
            title: "Щоденка",
            method: "createDailyReport",
        },
    ],
    ROCUCReport: {
        contractData: [],
        contractIdSelected: null,
    },
    ROEBBAReport: {
        budgetArticleData: [],
        budgetArticleIdSelected: null,
        budgetPlannnedIndicatorsIdSelected: null,
    },
}

document.addEventListener("DOMContentLoaded", () => {
    sideBarItemAddEventListener();
    getUserInfoRequest(() => {
        const ul = [...document.getElementsByClassName("side-bar-item")];
        /// state.userInfo.role.director_role || state.userInfo.role.report_viewer_role
        if(state.userInfo.fundholder_id == 7){
            state.sideBarItemSelected = 1;
        }
        else {
            state.sideBarItemSelected = 3;
        }
        ul[0].classList.add("side-bar-item-selected");
        loadReportSelected();
    })
    
})

const sideBarItemAddEventListener = () => {
    const sideBarUl = [...document.getElementById("side-bar-ul").children];
    sideBarUl.forEach((element, index, array) => {
        element.onclick = sideBarItemOnClick.bind(this, element, array);
    });
}

const getUserInfoRequest = (callbackFunction) => {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'budget-reports.php');
    xhr.send(JSON.stringify({
        typeRequest: "getUserInfoRequest"
    }));
    xhr.onload = function () {
        state.userInfo = JSON.parse(xhr.response);
        callbackFunction();
    }
}

const sideBarItemOnClick = (target, itemArray) => {
    itemArray.forEach(element => {
        if (parseInt(element.getAttribute("data-id")) === state.sideBarItemSelected)
            element.classList.remove("side-bar-item-selected");
    })
    state.sideBarItemSelected = parseInt(target.getAttribute("data-id"));
    target.classList.add("side-bar-item-selected");
    loadReportSelected();
}

const loadReportSelected = () => {
    const mainContentBlock = document.getElementsByClassName("main-content")[0];
    let reportInfo = state.reportsData.filter(element => element.id === state.sideBarItemSelected)[0];
    switch (reportInfo.method) {
        case "createROCUCReport":
            createROCUCReport(reportInfo, mainContentBlock);
            break;
        case "createROEBBAReport":
            createROEBBAReport(reportInfo, mainContentBlock);
            break;
        case "createDailyReport":
            createDailyReport(reportInfo, mainContentBlock);
            break;
        default:
            break;
    }
}

const renderContentRequest = (reportInfo, conditionInfo) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "budget-reports.php");
    xhr.send(JSON.stringify({
        typeRequest: reportInfo.name,
        conditionInfo: conditionInfo,
    }));
    xhr.onload = () => {
        const wrapperTable = document.getElementsByClassName("main-table")[0];
        wrapperTable.innerHTML = xhr.response;
    }
}

const rerenderROCUCReportContractSelect = (reportInfo) => {
    const wrapperSelect = document.getElementsByClassName("budget-reports-contract-wrapper__select")[0];
    while (wrapperSelect.firstChild)
        wrapperSelect.removeChild(wrapperSelect.firstChild);
    $("<select id='budget-reports-contract__select' class='contract-select' title='Договір'></select>").appendTo(wrapperSelect).select2();
    const select = document.getElementById("budget-reports-contract__select");
    select.onchange = createROCUCReportSelectOnChange.bind(this, select, reportInfo);
}

const createROCUCReportSelectOnChange = (target, reportInfo) => {
    state.ROCUCReport.contractIdSelected = target[target.selectedIndex].getAttribute("data-id");
    renderContentRequest(reportInfo, state.ROCUCReport.contractIdSelected);
}

const fillROCUCReportContractSelect = (reportInfo) => {
    rerenderROCUCReportContractSelect(reportInfo);
    const select = document.getElementById("budget-reports-contract__select");
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати";
    selectedOption.innerText = "Обрати";
    selectedOption.hidden = true;
    selectedOption.selected = state.ROCUCReport.contractIdSelected === null ? true : false;
    select.appendChild(selectedOption);
    if (state.ROCUCReport.contractData.length === 0)
        return;
    state.ROCUCReport.contractData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.number;
        optionSelect.innerText = element.number;
        optionSelect.setAttribute("data-id", element.id);
        optionSelect.setAttribute("data-number", element.number);
        if (state.ROCUCReport.contractIdSelected == element.id) {
            optionSelect.selected = true;
            optionSelect.hidden = true;
        }
        select.appendChild(optionSelect);
    })
    if(state.ROCUCReport.contractIdSelected !== null)
        renderContentRequest(reportInfo, state.ROCUCReport.contractIdSelected);
    else 
    if(state.ROCUCReport.contractIdSelected === null)
        renderContentRequest(reportInfo, null);
}

const getROCUCReportContractDataRequest = (callbackFunction) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "budget-reports.php");
    xhr.send(JSON.stringify({
        typeRequest: "getROCUCReportContractDataRequest",
    }));
    xhr.onload = () => {
        state.ROCUCReport.contractData = JSON.parse(xhr.response);
        callbackFunction();
    }
}

const createROCUCReport = (reportInfo, mainContentBlock) => {
    const subTitle = document.getElementsByClassName("sub-title")[0];
    subTitle.innerText = reportInfo.title;
    while(mainContentBlock.firstChild)
        mainContentBlock.removeChild(mainContentBlock.firstChild);
    const aboveTableBar = document.createElement("div");
    aboveTableBar.classList.add("above-table-bar");
    aboveTableBar.innerHTML = "<div class='add-select-table-bar'>" +
                                "<label>Договір</label><br>" +
                                "<div class='above-table-bar-element above-table-bar-select budget-reports-contract-wrapper__select'>" +
                                "</div>" +
                              "</div>";
    const wrapperTable = document.createElement("div");
    wrapperTable.classList.add("main-table");
    mainContentBlock.appendChild(aboveTableBar); 
    mainContentBlock.appendChild(wrapperTable); 
    getROCUCReportContractDataRequest(() => {
        fillROCUCReportContractSelect(reportInfo);
    })
}

const ROCUCReportContractNumberButtonOnClick = (contractId) => {
    const sideBarUl = [...document.getElementById("side-bar-ul").children];
    state.ROCUCReport.contractIdSelected = contractId;
    sideBarItemOnClick(sideBarUl[0], sideBarUl);
}



const createROEBBAReportBudgetArticleSelectOnChange = (target, reportInfo) => {
    state.ROEBBAReport.budgetPlannnedIndicatorsIdSelected = target[target.selectedIndex].getAttribute("data-planned-indicators-id");
    state.ROEBBAReport.budgetArticleIdSelected = target[target.selectedIndex].getAttribute("data-id");
    renderContentRequest(reportInfo, state.ROEBBAReport.budgetPlannnedIndicatorsIdSelected);
}

const rerenderROEBBAReportBudgetArticleSelect = (reportInfo) => {
    const wrapperSelect = document.getElementsByClassName("budget-reports-budget-article-wrapper__select")[0];
    while (wrapperSelect.firstChild)
        wrapperSelect.removeChild(wrapperSelect.firstChild);
    $("<select id='budget-reports-budget-article__select' class='budget-article-select' title='Стаття бюджету'></select>").appendTo(wrapperSelect).select2();
    const select = document.getElementById("budget-reports-budget-article__select");
    select.onchange = createROEBBAReportBudgetArticleSelectOnChange.bind(this, select, reportInfo);
}

const fillROEBBAReportBudgetArticleSelect = (reportInfo) => {
    rerenderROEBBAReportBudgetArticleSelect(reportInfo);
    const select = document.getElementById("budget-reports-budget-article__select");
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати";
    selectedOption.innerText = "Обрати";
    selectedOption.hidden = true;
    selectedOption.selected = state.ROEBBAReport.budgetArticleIdSelected === null ? true : false;
    select.appendChild(selectedOption);
    if (state.ROEBBAReport.budgetArticleData.length === 0)
        return;
    state.ROEBBAReport.budgetArticleData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.name;
        optionSelect.innerText = element.name;
        optionSelect.setAttribute("data-id", element.id);
        optionSelect.setAttribute("data-name", element.number);
        optionSelect.setAttribute("data-planned-indicators-id", element.planned_indicators_id);
        if (state.ROEBBAReport.budgetArticleIdSelected == element.id) {
            optionSelect.selected = true;
            optionSelect.hidden = true;
        }
        select.appendChild(optionSelect);
    })
    
    if(state.ROEBBAReport.budgetArticleIdSelected !== null)
        renderContentRequest(reportInfo, state.ROEBBAReport.budgetPlannnedIndicatorsIdSelected);
    else 
    if(state.ROEBBAReport.budgetArticleIdSelected === null)
        renderContentRequest(reportInfo, null);
}

const getROEBBAReportBudgetArticleDataRequest = (callbackFunction) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "budget-reports.php");
    xhr.send(JSON.stringify({
        typeRequest: "getROEBBAReportBudgetArticleDataRequest",
        year: JSON.parse(localStorage.getItem("budgetPlanfilterInfo")).year,
    }));
    xhr.onload = () => {
        state.ROEBBAReport.budgetArticleData = JSON.parse(xhr.response);
        callbackFunction();
    }
}

const createROEBBAReport = (reportInfo, mainContentBlock) => {
    const subTitle = document.getElementsByClassName("sub-title")[0];
    subTitle.innerText = reportInfo.title;
    while(mainContentBlock.firstChild)
        mainContentBlock.removeChild(mainContentBlock.firstChild);
    const aboveTableBar = document.createElement("div");
    aboveTableBar.classList.add("above-table-bar");
    aboveTableBar.innerHTML = "<div class='add-select-table-bar'>" +
                                "<label>Стаття бюджету</label><br>" +
                                "<div class='above-table-bar-element above-table-bar-select budget-reports-budget-article-wrapper__select'>" +
                                "</div>" +
                              "</div>";
    const wrapperTable = document.createElement("div");
    wrapperTable.classList.add("main-table");
    mainContentBlock.appendChild(aboveTableBar); 
    mainContentBlock.appendChild(wrapperTable); 
    getROEBBAReportBudgetArticleDataRequest(() => {
        fillROEBBAReportBudgetArticleSelect(reportInfo);
    })
}

const createDailyReport = (reportInfo, mainContentBlock) => {
    const subTitle = document.getElementsByClassName("sub-title")[0];
    subTitle.innerText = reportInfo.title;
    while(mainContentBlock.firstChild)
        mainContentBlock.removeChild(mainContentBlock.firstChild);
    getDailyReportRequest(() => {})
}

const getDailyReportRequest = () => {
    window.open('../../templates/classes/ParentExcelReport.php', '_blank');
    // const xhr = new XMLHttpRequest();
    // xhr.open("POST", "budget-reports.php");
    // xhr.send(JSON.stringify({
    //     typeRequest: "daily_report",
    // }));
    // xhr.onload = () => {
    //     const wrapperTable = document.getElementsByClassName("main-content")[0];
    //     wrapperTable.innerHTML = xhr.response;
    // }
}