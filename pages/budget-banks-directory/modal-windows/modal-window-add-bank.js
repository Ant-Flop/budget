

const modalSaveAddBankRequest = (data) => {
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
        modalAddBankCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalAddBankOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-bank");
    modalWindow.style.display = "block";
}

const modalAddBankCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-bank");
    modalWindow.style.display = "none";
}

const modalAddBankSaveOnClick = () => {
    const codeInput = document.getElementById("modal-code-add");
    let code = codeInput.value;
    const currentAccountInput = document.getElementById("modal-current-account-add");
    let currentAccount = currentAccountInput.value;
    const nameInput = document.getElementById("modal-name-add");
    let name = nameInput.value;
    const mfoInput = document.getElementById("modal-mfo-add");
    let mfo = mfoInput.value;
    const ibanInput = document.getElementById("modal-iban-add");
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
        typeRequest: "modalSaveAddBankRequest",
        code: code,
        currentAccount: currentAccount,
        name: name,
        mfo: mfo,
        iban: iban,
    });
    modalSaveAddBankRequest(bankData);
}


