const editOldCodeState = {
    "id": null,
    "oldCode": null,
}

const modalEditOldCodeOnClick = (id) => {
    editOldCodeState.id = id;
    getEditInfoOldCodeInfoRequest();
}

const modalEditOldCodeCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-old-code");
    modalWindow.style.display = "none";
}

const getEditInfoOldCodeInfoRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-codes-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getEditInfoOldCodeInfoRequest",
        id: editOldCodeState.id,
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        editOldCodeState.oldCode = response.old_code;
        fillEditOldCodeModalWindow();
        const modalWindow = document.getElementById("modal-window-edit-old-code");
        modalWindow.style.display = "block";
    }
}

const fillEditOldCodeModalWindow = () => {
    const oldCodeInput = document.getElementById("modal-old-code-edit");
    oldCodeInput.value = editOldCodeState.oldCode;
}

const modalEditOldCodeSaveOnClick = () => {
    const oldCodeInput = document.getElementById("modal-old-code-edit");
    let oldCode = oldCodeInput.value;
    if (oldCode === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let oldCodeData = JSON.stringify({
        typeRequest: "modalSaveEditOldCodeRequest",
        id: editOldCodeState.id,
        oldCode: oldCode,
    });
    modalSaveEditOldCodeRequest(oldCodeData);
}


const modalSaveEditOldCodeRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-codes-directory.php";
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
        modalEditOldCodeCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteOldCodeOnClick = (id) => {   
    if(confirm("Ви дійсно бажаєте видалити старий код?") == true)
        modalDeleteOldCodeRequest(id);
    else return;
}

const modalDeleteOldCodeRequest = (id) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-codes-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteOldCodeRequest",
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