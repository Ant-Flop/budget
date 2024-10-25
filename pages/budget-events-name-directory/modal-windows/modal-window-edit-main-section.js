const editMainSectionState = {
    mainSectionId: null,
    mainSectionData: [],
    newCodeId: null,
}

const modalEditMainSectionOnClick = (id, newCodeId) => {
    editMainSectionState.mainSectionId = id;
    editMainSectionState.newCodeId = newCodeId;
    getMainSectionRequest(editMainSectionState, () => {
        const mainSectionInput = document.getElementById("modal-main-section-edit");
        mainSectionInput.value = editMainSectionState.mainSectionData.name;
        const modalWindow = document.getElementById("modal-window-edit-main-section");
        modalWindow.style.display = "block";
    });
}

const modalEditMainSectionCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-main-section");
    modalWindow.style.display = "none";
}

const modalEditMainSectionSaveOnClick = () => {
    const mainSectionInput = document.getElementById("modal-main-section-edit");
    let mainSection = mainSectionInput.value;
    if (mainSection === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let mainSectionData = JSON.stringify({
        typeRequest: "modalSaveEditMainSectionRequest",
        id: editMainSectionState.mainSectionId,
        mainSection: mainSection,
    });
    modalSaveEditMainSectionRequest(mainSectionData);
}

const modalSaveEditMainSectionRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-events-names-directory.php";
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
        modalEditMainSectionCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteMainSectionOnClick = (id, newCodeId) => {   
    if(confirm("Ви дійсно бажаєте видалити головний розділ?") == true)
        modalDeleteMainSectionRequest(id, newCodeId);
    else return;
}

const modalDeleteMainSectionRequest = (id, newCodeId) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-events-names-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteMainSectionRequest",
        id: id,
        newCodeId: newCodeId,
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