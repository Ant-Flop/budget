const editBankState = {
    "id": null,
    "code": null,
    "currentAccount": null,
    "name": null,
    "mfo": null,
    "iban": null,
}

const getEditBankInfoRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-banks-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getEditBankInfoRequest",
        id: editBankState.id,
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        editBankState.id = response.id;
        editBankState.code = response.code;
        editBankState.currentAccount = response.current_account;
        editBankState.name = response.name;
        editBankState.mfo = response.mfo;
        editBankState.iban = response.iban;
        const codeInput = document.getElementById("modal-code-edit");
        codeInput.value = editBankState.code;
        const currentAccountInput = document.getElementById("modal-current-account-edit");
        currentAccountInput.value = editBankState.currentAccount;
        const nameInput = document.getElementById("modal-name-edit");
        nameInput.value = editBankState.name;
        const mfoInput = document.getElementById("modal-mfo-edit");
        mfoInput.value = editBankState.mfo;
        const ibanInput = document.getElementById("modal-iban-edit");
        ibanInput.value = editBankState.iban;
        const modalWindow = document.getElementById("modal-window-edit-bank");
        modalWindow.style.display = "block";
    }
}

const modalSaveEditBankRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-banks-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(data);
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        const labelStatus = document.getElementById("label-save-indicator");
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        labelStatus.innerText = response.text;
        labelStatus.hidden = false;
        backgroundStatus.style.backgroundColor = response.status ? "#ebfbeb" : "#fbedeb";
        setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
        renderTableRequest();
        modalEditBankCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteBankRequest = (id) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-banks-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteBankRequest",
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

const modalEditBankOnClick = (id) => {
    editBankState.id = id;
    getEditBankInfoRequest();
}

const modalEditBankCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-bank");
    modalWindow.style.display = "none";
}

const modalEditBankSaveOnClick = () => {
    const codeInput = document.getElementById("modal-code-edit");
    let code = codeInput.value;
    const currentAccountInput = document.getElementById("modal-current-account-edit");
    let currentAccount = currentAccountInput.value;
    const nameInput = document.getElementById("modal-name-edit");
    let name = nameInput.value;
    const mfoInput = document.getElementById("modal-mfo-edit");
    let mfo = mfoInput.value;
    const ibanInput = document.getElementById("modal-iban-edit");
    let iban = ibanInput.value;  
    if (code === ( '' || 0) ||
        currentAccount === ( '' || 0) ||
        name === ( '' || 0) ||
        mfo === ( '' || 0) ||
        iban === ( '' || 0)) {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let bankData = JSON.stringify({
        typeRequest: "modalSaveEditBankRequest",
        id: editBankState.id,
        code: code,
        currentAccount: currentAccount,
        name: name,
        mfo: mfo,
        iban: iban,
    });
    modalSaveEditBankRequest(bankData);
}

const modalDeleteBankOnClick = (id) => {   
    if(confirm("Ви дійсно бажаєте видалити банк?") == true)
        modalDeleteBankRequest(id);
    else return;
}