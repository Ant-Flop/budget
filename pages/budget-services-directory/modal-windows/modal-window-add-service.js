const addServiceState = {
    "fundholdersData": null,
}

const getAddFundholdersRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-services-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getAddFundholdersRequest",
    }));
    xhr.onload = () => {
        addServiceState.fundholdersData = JSON.parse(xhr.response);
        fillAddModalServiceSelect();
        const modalWindow = document.getElementById("modal-window-add-service");
        modalWindow.style.display = "block";
    }
}

const modalSaveAddServiceRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-services-directory.php";
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
        modalAddServiceCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalAddServiceOnClick = () => {
    getAddFundholdersRequest();
}

const modalAddServiceCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-service");
    modalWindow.style.display = "none";
}

const modalAddServiceSaveOnClick = () => {
    const serviceInput = document.getElementById("modal-service-add");
    let service = serviceInput.value;
    const fundholderSelect = document.getElementById("modal-add-fundholder__select");
    let fundholderId = fundholderSelect[fundholderSelect.selectedIndex].getAttribute("data-id");   
    if (service == ( '' || 0) ||
        fundholderId == ( '' || 0)) {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let serviceData = JSON.stringify({
        typeRequest: "modalSaveAddServiceRequest",
        service: service,
        fundholderId: fundholderId,
    });
    modalSaveAddServiceRequest(serviceData);
}

const fillAddModalServiceSelect = () => {
    rerenderServiceAddModal();
    const select = document.getElementById("modal-add-fundholder__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selecteOption = document.createElement("option");
    selecteOption.value = "Обрати фондоутримувача";
    selecteOption.innerText = "Обрати фондоутримувача";
    selecteOption.hidden = true;
    selecteOption.selected = true;
    select.appendChild(selecteOption);
    if(addServiceState.fundholdersData.length === 0)
        return;
        addServiceState.fundholdersData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.name;
        optionSelect.innerText = element.name;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderServiceAddModal = () => {
    const serviceInput = document.getElementById("modal-service-add");
    serviceInput.value = null;
    const field = document.getElementsByClassName("modal-add-fundholder-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-fundholder__select' class='fundholder-select' title='Фондоутримувач'></select>").appendTo(field).select2();
}

