const editCounterpartyState = {
    "id": null,
    "name": null,
}

const modalEditCounterpartyOnClick = (id) => {
    editCounterpartyState.id = id;
    getEditInfoCounterpartyInfoRequest();
}

const modalEditCounterpartyCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-counterparty");
    modalWindow.style.display = "none";
}

const getEditInfoCounterpartyInfoRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-counterparties-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getEditInfoCounterpartyInfoRequest",
        id: editCounterpartyState.id,
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        editCounterpartyState.name = response.counterparty_name;
        fillEditCounterpartyModalWindow();
        const modalWindow = document.getElementById("modal-window-edit-counterparty");
        modalWindow.style.display = "block";
    }
}

const fillEditCounterpartyModalWindow = () => {
    const nameCounterpartyInput = document.getElementById("modal-name-counterparty-edit");
    nameCounterpartyInput.value = editCounterpartyState.name;
}

const modalEditCounterpartySaveOnClick = () => {
    const nameCounterpartyInput = document.getElementById("modal-name-counterparty-edit");
    let nameCounterparty = nameCounterpartyInput.value;
    if (nameCounterparty === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let counterpartyData = JSON.stringify({
        typeRequest: "modalSaveEditCounterpartyRequest",
        id: editCounterpartyState.id,
        name: nameCounterparty,
    });
    modalSaveEditCounterpartyRequest(counterpartyData);
}


const modalSaveEditCounterpartyRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-counterparties-directory.php";
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
        modalEditCounterpartyCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteCounterpartyOnClick = (id, editMode) => {   
    if(confirm("Ви дійсно бажаєте видалити контрагента?") == true)
        modalDeleteCounterpartyRequest(id, editMode);
    else return;
}

const modalDeleteCounterpartyRequest = (id) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-counterparties-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteCounterpartyRequest",
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