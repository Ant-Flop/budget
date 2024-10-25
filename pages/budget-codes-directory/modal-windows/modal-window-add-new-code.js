const addNewCodeState = {
    "oldCodesData": null,
}

const getAddOldCodesRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-codes-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getOldCodesRequest",
    }));
    xhr.onload = () => {
        addNewCodeState.oldCodesData = JSON.parse(xhr.response);
        fillAddModalOldCodeSelect();
        const modalWindow = document.getElementById("modal-window-add-new-code");
        modalWindow.style.display = "block";
    }
}

const modalSaveAddNewCodeRequest = (data) => {
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
        modalAddNewCodeCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalAddNewCodeOnClick = () => {
    getAddOldCodesRequest();
}

const modalAddNewCodeCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-new-code");
    modalWindow.style.display = "none";
}

const modalAddNewCodeSaveOnClick = () => {
    const newCodeInput = document.getElementById("modal-new-code-add");
    let newCode = newCodeInput.value;
    const newCodeSelect = document.getElementById("modal-add-old-code__select");
    let oldCodeId = newCodeSelect[newCodeSelect.selectedIndex].getAttribute("data-id");   
    if (newCode === ( '' || 0) ||
        oldCodeId === ( '' || 0)) {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let newCodeData = JSON.stringify({
        typeRequest: "modalSaveAddNewCodeRequest",
        oldCodeId: oldCodeId,
        newCode: newCode,
    });
    modalSaveAddNewCodeRequest(newCodeData);
}

const fillAddModalOldCodeSelect = () => {
    rerenderOldCodeAddModal();
    const select = document.getElementById("modal-add-old-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selecteOption = document.createElement("option");
    selecteOption.value = "Обрати старий код";
    selecteOption.innerText = "Обрати старий код";
    selecteOption.hidden = true;
    selecteOption.selected = true;
    select.appendChild(selecteOption);
    if(addNewCodeState.oldCodesData.length === 0)
        return;
    addNewCodeState.oldCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.old_code;
        optionSelect.innerText = element.old_code;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderOldCodeAddModal = () => {
    const newCodeInput = document.getElementById("modal-new-code-add");
    newCodeInput.value = null;
    const field = document.getElementsByClassName("modal-add-old-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-old-code__select' class='old-code-select' title='Старий код'></select>").appendTo(field).select2();
}

