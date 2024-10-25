const editSubsectionState = {
    subsectionId: null,
    subsectionData: [],
    newCodeId: null,
}

const modalEditSubsectionOnClick = (id, newCodeId) => {
    editSubsectionState.subsectionId = id;
    editSubsectionState.newCodeId = newCodeId;
    getSubsectionRequest(editSubsectionState, () => {
        const subsectionInput = document.getElementById("modal-subsection-edit");
        subsectionInput.value = editSubsectionState.subsectionData.name;
        const modalWindow = document.getElementById("modal-window-edit-subsection");
        modalWindow.style.display = "block";
    });
}

const modalEditSubsectionCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-edit-subsection");
    modalWindow.style.display = "none";
}

const modalEditSubsectionSaveOnClick = () => {
    const subsectionInput = document.getElementById("modal-subsection-edit");
    let subsection = subsectionInput.value;
    if (subsection === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let subsectionData = JSON.stringify({
        typeRequest: "modalSaveEditSubsectionRequest",
        id: editSubsectionState.subsectionId,
        subsection: subsection,
    });
    modalSaveEditSubsectionRequest(subsectionData);
}

const modalSaveEditSubsectionRequest = (data) => {
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
        modalEditSubsectionCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

const modalDeleteSubsectionOnClick = (id, newCodeId) => {   
    if(confirm("Ви дійсно бажаєте видалити підрозділ?") == true)
        modalDeleteSubsectionRequest(id, newCodeId);
    else return;
}

const modalDeleteSubsectionRequest = (id, newCodeId) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-events-names-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "deleteSubsectionRequest",
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