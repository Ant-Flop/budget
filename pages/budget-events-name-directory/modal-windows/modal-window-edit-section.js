const editSectionState = {
    sectionId: null,
    sectionData: [],
    newCodeId: null,
}


const modalEditSectionOnClick = (id, newCodeId) => {
    editSectionState.sectionId = id;
    editSectionState.newCodeId = newCodeId;
    getSectionRequest(editSectionState, () => {
        const sectionInput = document.getElementById("modal-section-edit");
        sectionInput.value = editSectionState.sectionData.name;
        const modalWindow = document.getElementById("modal-window-edit-section");
        modalWindow.style.display = "block";
    });
}

const modalEditSectionCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-section");
    modalWindow.style.display = "none";
}

const modalEditSectionSaveOnClick = () => {
    const sectionInput = document.getElementById("modal-section-edit");
    let section = sectionInput.value;
    if (section === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let sectionData = JSON.stringify({
        typeRequest: "modalSaveEditSectionRequest",
        id: editSectionState.sectionId,
        section: section,
    });
    modalSaveEditSectionRequest(sectionData);
}

const modalSaveEditSectionRequest = (data) => {
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
        modalEditSectionCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}


const modalDeleteSectionOnClick = (id, newCodeId) => {   
    if(confirm("Ви дійсно бажаєте видалити розділ?") == true)
        modalDeleteSectionRequest(id, newCodeId);
    else return;
}

const modalDeleteSectionRequest = (id, newCodeId) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-events-names-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteSectionRequest",
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