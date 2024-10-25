const transferPlannedIndicatorState = {
    startYearSelected: null,
    endYearSelected: null,
    startYearsData: [
        "2023",
        "2024",
        "2025",
        "2026",
        "2027",
    ],
    endYearsData: [
        "2023",
        "2024",
        "2025",
        "2026",
        "2027",
    ],
}

const modalTransferPlannedIndicatorOnClick = () => {
    
    
    modalGetTransferYearRequest(transferPlannedIndicatorState, () => {
        console.log(transferPlannedIndicatorState)
        fillTransferModalPlannedIndicatorStartYearSelect();
        fillTransferModalPlannedIndicatorEndYearSelect();
        const modalWindow = document.getElementById("modal-window-transfer-planned-indicator");
        modalWindow.style.display = "block";
    })
    
}

const modalGetTransferYearRequest = (paramState, callbackFunction) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-planned-indicators.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "modalGetTransferYearRequest",
        fundholderId: state.fundholderIdSelected,
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        paramState.startYearsData = response;
        paramState.endYearsData = paramState.endYearsData.filter(element => {
            if(!response.includes(element))
                return element;
        });
        callbackFunction();
    }
    
}

const fillTransferModalPlannedIndicatorStartYearSelect = () => {
    rerenderPlannedIndicatorStartYearTransferModal();
    const select = document.getElementById("modal-transfer-planned-indicator-start-year__select");
    while (select.firstChild)
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати рік";
    selectedOption.innerText = "Обрати рік";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if (transferPlannedIndicatorState.startYearsData.length === 0)
        return;
        transferPlannedIndicatorState.startYearsData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element;
        optionSelect.innerText = element;
        optionSelect.setAttribute("data-year", element);
        select.appendChild(optionSelect);
    })
}

const rerenderPlannedIndicatorStartYearTransferModal = () => {
    const field = document.getElementsByClassName("modal-transfer-planned-indicator-start-year-wrapper-select");
    while (field.firstChild)
        field.removeChild(field.firstChild);
    $("<select id='modal-transfer-planned-indicator-start-year__select' class='year-select' title='Рік'></select>").appendTo(field).select2();
    const select = document.getElementById("modal-transfer-planned-indicator-start-year__select");
    select.onchange = transferModalPlannedIndicatorStartYearOnChange.bind(this);
}

const transferModalPlannedIndicatorStartYearOnChange = () => {
    const select = document.getElementById("modal-transfer-planned-indicator-start-year__select");
    transferPlannedIndicatorState.startYearSelected = select[select.selectedIndex].getAttribute("data-year");
    console.log(transferPlannedIndicatorState.startYearSelected)
}

const fillTransferModalPlannedIndicatorEndYearSelect = () => {
    rerenderPlannedIndicatorEndYearTransferModal();
    const select = document.getElementById("modal-transfer-planned-indicator-end-year__select");
    while (select.firstChild)
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати рік";
    selectedOption.innerText = "Обрати рік";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if (transferPlannedIndicatorState.endYearsData.length === 0)
        return;
        transferPlannedIndicatorState.endYearsData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element;
        optionSelect.innerText = element;
        optionSelect.setAttribute("data-year", element);
        select.appendChild(optionSelect);
    })
}

const rerenderPlannedIndicatorEndYearTransferModal = () => {
    const field = document.getElementsByClassName("modal-transfer-planned-indicator-end-year-wrapper-select");
    while (field.firstChild)
        field.removeChild(field.firstChild);
    $("<select id='modal-transfer-planned-indicator-end-year__select' class='year-select' title='Рік'></select>").appendTo(field).select2();
    const select = document.getElementById("modal-transfer-planned-indicator-end-year__select");
    select.onchange = transferModalPlannedIndicatorEndYearOnChange.bind(this);
}

const transferModalPlannedIndicatorEndYearOnChange = () => {
    const select = document.getElementById("modal-transfer-planned-indicator-end-year__select");
    transferPlannedIndicatorState.endYearSelected = select[select.selectedIndex].getAttribute("data-year");
    console.log(transferPlannedIndicatorState.endYearSelected)
}

const modalTransferPlannedIndicatorSaveOnClick = () => {
    if(transferPlannedIndicatorState.startYearSelected !== null && transferPlannedIndicatorState.endYearSelected !== null)
        modalSaveTransferPlannedIndicatorRequest();
    else alert("Не всі необхідні поля заповнені!");
}

const modalSaveTransferPlannedIndicatorRequest = () => {
    const spinner = document.getElementById("spinner-loader-id");
    spinner.style.visibility = "visible";
    if(localStorage.getItem("budgetPlanfilterInfo")) {
        let filterInfo = JSON.parse(localStorage.getItem("budgetPlanfilterInfo"));
        localStorage.setItem("budgetPlanfilterInfo", JSON.stringify({
            fundholderId: filterInfo.fundholderId,
            year: transferPlannedIndicatorState.endYearSelected,
        }))
    }
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-planned-indicators.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "modalSaveTransferPlannedIndicatorRequest",
        fundholderId: state.fundholderIdSelected,
        fromYear: transferPlannedIndicatorState.startYearSelected,
        toYear: transferPlannedIndicatorState.endYearSelected,
    }));
    modalTransferPlannedIndicatorCloseOnClick();
    xhr.onload = () => {
        console.log(xhr.response)
        let response = JSON.parse(xhr.response);
        const labelStatus = document.getElementById("label-save-indicator");
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        labelStatus.innerText = response.text;
        labelStatus.hidden = false;
        backgroundStatus.style.backgroundColor = response.status ? "#ebfbeb" : "#fbedeb";
        setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
        spinner.style.visibility = "hidden";
        renderTableRequest();
        
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalTransferPlannedIndicatorCloseOnClick = () => {
    transferPlannedIndicatorState.startYearSelected = null;
    transferPlannedIndicatorState.endYearSelected = null;
    const modalWindow = document.getElementById("modal-window-transfer-planned-indicator");
    modalWindow.style.display = "none";
}