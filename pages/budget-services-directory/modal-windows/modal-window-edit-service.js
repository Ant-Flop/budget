const editServiceState = {
    "id": null,
    "name": null,
}

const getEditServiceInfoRequest = () => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-services-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getEditServiceInfoRequest",
        id: editServiceState.id,
    }));
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        editServiceState.id = response.id;
        editServiceState.name = response.name;
        const serviceInput = document.getElementById("modal-service-edit");
        serviceInput.value = editServiceState.name;
        const modalWindow = document.getElementById("modal-window-edit-service");
        modalWindow.style.display = "block";
    }
}

const modalSaveEditServiceRequest = (data) => {
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
        modalEditServiceCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteServiceRequest = (id) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-services-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteServiceRequest",
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

const modalEditServiceOnClick = (id) => {
    editServiceState.id = id;
    getEditServiceInfoRequest(id);
}

const modalEditServiceCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-service");
    modalWindow.style.display = "none";
}

const modalEditServiceSaveOnClick = () => {
    const serviceInput = document.getElementById("modal-service-edit");
    let service = serviceInput.value;
    if (service === '') {
        alert("Не всі обов'язкові поля заповнені!");
        serviceInput.value = editServiceState.name;
        return;
    }
    let serviceData = JSON.stringify({
        typeRequest: "modalSaveEditServiceRequest",
        id: editServiceState.id,
        name: service,
    });
    modalSaveEditServiceRequest(serviceData);
}

const modalDeleteServiceOnClick = (id) => {   
    if(confirm("Ви дійсно бажаєте видалити службу?") == true)
        modalDeleteServiceRequest(id);
    else return;
}