const editContractState = {
    "id": null,
    'number': null,
    'name': null,
    'term': null,
    'status': null,
    'vatSign': null,
    'editMode': null,
}


const modalEditContractOnClick = (id) => {
    editContractState.id = id;
    getEditContractInfoRequest();
}

const getEditContractInfoRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-counterparties-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getEditContractInfoRequest",
        id: editContractState.id,
    }));
    xhr.onload = () => {
        
        let response = JSON.parse(xhr.response);
        editContractState.number = response.contract_number;
        editContractState.name = response.contract_name;
        editContractState.term = response.contract_term;
        editContractState.status = response.contract_status;
        editContractState.vatSign = response.contract_vat_sign;
        editContractState.editMode = response.contract_edit_mode;
        fillEditContractModalWindow();
        const modalWindow = document.getElementById("modal-window-edit-contract");
        modalWindow.style.display = "block";
    }
}

const modalEditContractCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-contract");
    modalWindow.style.display = "none";
}


const fillEditContractModalWindow = () => {
    const numberContractInput = document.getElementById("modal-number-contract-edit");
    numberContractInput.value = editContractState.number;
    createEditModalSelect("status", [
        "Обрати статус договору",
        "Відкритий",
        "Закритий",
    ], editContractState.status, "Статус договору");
    createEditModalSelect("sign-vat", [
        "Обрати ознаку ПДВ",
        "З ПДВ",
        "Без ПДВ",
    ], editContractState.vatSign, "Ознака ПДВ");
    const nameContractInput = document.getElementById("modal-name-contract-edit");
    nameContractInput.value = editContractState.name;
    const termContractInput = document.getElementById("modal-term-contract-edit");
    termContractInput.value = editContractState.term;
}

const createEditModalSelect = (type, arrayOptions, selectedOption, title) => {
    const select = document.getElementById("modal-" + type + "-contract-edit");
    while (select.firstChild)
        select.removeChild(select.firstChild);
    let options = "";
    arrayOptions.forEach(element => {
        if (element === selectedOption)
            options += "<option selected hidden>" + element + "</option>";
        else options += "<option>" + element + "</option>";
    });
    $("<select id='modal-" + type + "-contract-edit-select' class='contract-select' title='" + title + "'>" + options + "</select>").appendTo(select).select2();
}

const modalEditContractSaveOnClick = () => {
    const numberContractInput = document.getElementById("modal-number-contract-edit");
    let numberContract = numberContractInput.value;
    const statusContractSelect = document.getElementById("modal-status-contract-edit-select");
    let statusContract = statusContractSelect[statusContractSelect.selectedIndex].value;
    const signVATContractSelect = document.getElementById("modal-sign-vat-contract-edit-select");
    let signVATContract = signVATContractSelect[signVATContractSelect.selectedIndex].value;
    const nameContractInput = document.getElementById("modal-name-contract-edit");
    let nameContract = nameContractInput.value;
    const termContractInput = document.getElementById("modal-term-contract-edit");
    let termContract = termContractInput.value;
    if (numberContract === '' ||
        statusContract === '' ||
        nameContract === '' ||
        termContract === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let contractData = JSON.stringify({
        typeRequest: "modalSaveEditContractRequest",
        id: editContractState.id,
        number: numberContract,
        status: statusContract,
        vatSign: signVATContract === '' ? null : signVATContract,
        name: nameContract,
        term: termContract,
        editMode: editContractState.editMode,
    });
    modalSaveEditContractRequest(contractData);
}

const modalSaveEditContractRequest = (contractData) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-counterparties-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(contractData);
    xhr.onload = function () {
        console.log(xhr.response)
        let response = JSON.parse(xhr.response);
        const labelStatus = document.getElementById("label-save-indicator");
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        labelStatus.innerText = response.text;
        labelStatus.hidden = false;
        backgroundStatus.style.backgroundColor = response.status ? "#ebfbeb" : "#fbedeb";
        setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
        renderTableRequest();
        modalEditContractCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteContractOnClick = (id, editMode) => {   
    if(confirm("Ви дійсно бажаєте видалити договір?") == true)
        modalDeleteContractRequest(id, editMode);
    else return;
}

const modalDeleteContractRequest = (id, editMode) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-counterparties-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteContractRequest",
        editMode: editMode,
        id: id
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        const labelStatus = document.getElementById("label-save-indicator");
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        labelStatus.innerText = response.text;
        labelStatus.hidden = false;
        backgroundStatus.style.backgroundColor = response.status ? "#ebfbeb" : "#fbedeb";
        setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
        renderTableRequest();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}


