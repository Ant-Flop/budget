const editFundholderState = {
    "id": null,
    "name": null,
    "shortName": null,
}

const modalEditFundholderOnClick = (id) => {
    editFundholderState.id = id;
    getEditInfoFundholderInfoRequest();
}

const modalEditFundholderCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-fundholder");
    modalWindow.style.display = "none";
}

const getEditInfoFundholderInfoRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-fundholders-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getEditInfoFundholderInfoRequest",
        id: editFundholderState.id,
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        editFundholderState.name = response.name;
        editFundholderState.shortName = response.short_name;
        fillEditFundholderModalWindow();
        const modalWindow = document.getElementById("modal-window-edit-fundholder");
        modalWindow.style.display = "block";
    }
}

const fillEditFundholderModalWindow = () => {
    const nameFundholderInput = document.getElementById("modal-name-fundholder-edit");
    nameFundholderInput.value = editFundholderState.name;
    const shortNameFundholderInput = document.getElementById("modal-short-name-fundholder-edit");
    shortNameFundholderInput.value = editFundholderState.shortName;
}

const modalEditFundholderSaveOnClick = () => {
    const nameFundholderInput = document.getElementById("modal-name-fundholder-edit");
    const shortNameFundholderInput = document.getElementById("modal-short-name-fundholder-edit");
    let nameFundholder = nameFundholderInput.value;
    let shortNameFundholder = shortNameFundholderInput.value;
    if (nameFundholder === '' || shortNameFundholder === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let fundholderData = JSON.stringify({
        typeRequest: "modalSaveEditFundholderRequest",
        id: editFundholderState.id,
        name: nameFundholder,
        shortname: shortNameFundholder,
    });
    modalSaveEditFundholderRequest(fundholderData);
}

const modalSaveEditFundholderRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-fundholders-directory.php";
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
        modalEditFundholderCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteFundholderOnClick = (id) => {   
    if(confirm("Ви дійсно бажаєте видалити фондоутримувача?") == true)
        modalDeleteFundholderRequest(id);
    else return;
}

const modalDeleteFundholderRequest = (id) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-fundholders-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteFundholderRequest",
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