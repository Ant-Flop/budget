
const modalAddOldCodeOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-old-code");
    modalWindow.style.display = "block";
}

const modalAddOldCodeCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-old-code");
    modalWindow.style.display = "none";
}

const modalAddOldCodeSaveOnClick = () => {
    const nameOldCodeInput = document.getElementById("modal-name-old-code-add");
    let nameOldCode = nameOldCodeInput.value;
    if (nameOldCode === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let oldCodeData = JSON.stringify({
        typeRequest: "modalSaveAddOldCodeRequest",
        oldCode: nameOldCode,
    });
    modalSaveAddOldCodeRequest(oldCodeData);
}


const modalSaveAddOldCodeRequest = (data) => {
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
        modalAddOldCodeCloseOnClick();
        const nameOldCodeInput = document.getElementById("modal-name-old-code-add");
        nameOldCodeInput.value = "";
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

