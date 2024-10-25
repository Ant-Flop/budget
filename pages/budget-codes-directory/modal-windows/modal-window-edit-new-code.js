const editNewCodeState = {
    "id": null,
    "newCode": null,
    "oldCodeId": null,
    "oldCode": null,
    "oldCodesData": null,
}

const getEditInfoNewCodeInfoRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-codes-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getEditInfoNewCodeInfoRequest",
        id: editNewCodeState.id,
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        editNewCodeState.id = response.id;
        editNewCodeState.newCode = response.new_code;
        editNewCodeState.oldCodeId = response.old_code_id;
        getEditOldCodesRequest();
        
    }
}

const getEditOldCodesRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-codes-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getOldCodesRequest",
    }));
    xhr.onload = () => {
        editNewCodeState.oldCodesData = JSON.parse(xhr.response);
        fillEditNewCodeModalWindow();
        const modalWindow = document.getElementById("modal-window-edit-new-code");
        modalWindow.style.display = "block";
    }
}

const modalSaveEditNewCodeRequest = (data) => {
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
        modalEditNewCodeCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteNewCodeRequest = (id) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-codes-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteNewCodeRequest",
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

const modalEditNewCodeOnClick = (id) => {
    editNewCodeState.id = id;
    getEditInfoNewCodeInfoRequest();
}

const modalEditNewCodeCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-new-code");
    modalWindow.style.display = "none";
}

const modalEditNewCodeSaveOnClick = () => {
    const newCodeInput = document.getElementById("modal-new-code-edit");
    let newCode = newCodeInput.value;
    const select = document.getElementById("modal-edit-old-code__select");
    let oldCodeId = select[select.selectedIndex].getAttribute("data-id");
    if (newCode === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let newCodeData = JSON.stringify({
        typeRequest: "modalSaveEditNewCodeRequest",
        id: editNewCodeState.id,
        newCode: newCode,
        oldCodeId: oldCodeId,
    });
    modalSaveEditNewCodeRequest(newCodeData);
}

const modalDeleteNewCodeOnClick = (id) => {   
    if(confirm("Ви дійсно бажаєте видалити новий код?") == true)
        modalDeleteNewCodeRequest(id);
    else return;
}

const fillEditNewCodeModalWindow = () => {
    rerenderOldCodeEditModal();
    const select = document.getElementById("modal-edit-old-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    if(editNewCodeState.oldCodesData.length === 0)
        return;
    editNewCodeState.oldCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.old_code;
        optionSelect.innerText = element.old_code;
        optionSelect.setAttribute("data-id", element.id);
        if(element.id === editNewCodeState.oldCodeId) {
            optionSelect.selected = true;
            optionSelect.hidden = true;
        }
        select.appendChild(optionSelect);
    })
    const newCodeInput = document.getElementById("modal-new-code-edit");
    newCodeInput.value = editNewCodeState.newCode;
}


const rerenderOldCodeEditModal = () => {
    const newCodeInput = document.getElementById("modal-new-code-edit");
    newCodeInput.value = null;
    const field = document.getElementsByClassName("modal-edit-old-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-edit-old-code__select' class='old-code-select' title='Старий код'></select>").appendTo(field).select2();
}