const state = {
    "counterpartyId": 0
}

const addEntryOnClick = (id) => {
    const modalWindow = document.getElementById("modal-window");
    state.counterpartyId = id;
    modalWindow.style.display = "block";
}


const modalCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window");
    modalWindow.style.display = "none";
}

const modalSaveOnClick = () => {
    const numberContractInput = document.getElementById("modal-number-contract-add");
    let numberContract = numberContractInput.value;
    const statusContractSelect = document.getElementById("modal-status-contract-select");
    let statusContract = statusContractSelect[statusContractSelect.selectedIndex].value;
    const signVATContractSelect = document.getElementById("modal-sign-vat-contract-select");
    let signVATContract = signVATContractSelect[signVATContractSelect.selectedIndex].value;
    const nameContractInput = document.getElementById("modal-name-contract-add");
    let nameContract = nameContractInput.value;
    const termContractInput = document.getElementById("modal-term-contract-add");
    let termContract = termContractInput.value;
    if (numberContract === '' || 
        statusContract === '' || 
        nameContract === '' || 
        termContract === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let contractData = JSON.stringify({
        typeRequest: "modalSaveAddContractRequest",
        counterpartyId: state.counterpartyId,
        numberContract: numberContract,
        statusContract: statusContract,
        signVATContract: signVATContract === '' ? null : signVATContract,
        nameContract: nameContract,
        termContract: termContract
    });
    modalSaveAddContractRequest(contractData);
}

const modalSaveAddContractRequest = (contractData) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "counterparties-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(contractData);
    xhr.onload = function () {
        let response = JSON.parse(xhr.response);
        const labelStatus = document.getElementById("label-counterparties-save-indicator");
        const backgroundStatus = document.getElementsByClassName("upper-counterparties-save-panel")[0];
        labelStatus.innerText = response.text;
        labelStatus.hidden = false;
        backgroundStatus.style.backgroundColor = response.status ? "#ebfbeb" : "#fbedeb";
        setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
        renderTableRequest();
        modalCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-counterparties-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}