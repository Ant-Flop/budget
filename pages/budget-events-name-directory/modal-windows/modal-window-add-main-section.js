const addMainSectionState = {
    nameTable: "main_sections_directory",
    countSymbolsNewCode: 8,
    oldCodesData: [],
    oldCodeIdSelected: null,
    newCodesData: [],
    newCodeIdSelected: null,
}

const modalAddMainSectionOnClick = () => {
    getOldCodesRequest(addMainSectionState, () => {
        fillAddModalMainSectionOldCodeSelect();
        fillAddModalMainSectionNewCodeSelect();
        const modalWindow = document.getElementById("modal-window-add-main-section");
        modalWindow.style.display = "block";
        
    });
}

const fillAddModalMainSectionOldCodeSelect = () => {
    rerenderMainSectionOldCodeAddModal();
    const select = document.getElementById("modal-add-main-section-old-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати старий код";
    selectedOption.innerText = "Обрати старий код";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addMainSectionState.oldCodesData.length === 0)
        return;
    addMainSectionState.oldCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.old_code;
        optionSelect.innerText = element.old_code;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderMainSectionOldCodeAddModal = () => {
    const field = document.getElementsByClassName("modal-add-main-section-old-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-main-section-old-code__select' class='old-code-select' title='Старий код' onchange='addModalMainSectionOldCodeOnChange()'></select>").appendTo(field).select2();
    const select = document.getElementById("modal-add-main-section-old-code__select");
    select.onchange = addModalMainSectionOldCodeOnChange.bind(this);
}

const addModalMainSectionOldCodeOnChange = () => {
    const select = document.getElementById("modal-add-main-section-old-code__select");
    addMainSectionState.oldCodeIdSelected = select[select.selectedIndex].getAttribute("data-id");
    getNewCodesRequest(addMainSectionState, () => {
        fillAddModalMainSectionNewCodeSelect();
    })
}

const fillAddModalMainSectionNewCodeSelect = () => {
    rerenderMainSectionNewCodeAddModal();
    const select = document.getElementById("modal-add-main-section-new-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати новий код";
    selectedOption.innerText = "Обрати новий код";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addMainSectionState.newCodesData.length === 0)
        return;
    addMainSectionState.newCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.new_code;
        optionSelect.innerText = element.new_code;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderMainSectionNewCodeAddModal = () => {
    const field = document.getElementsByClassName("modal-add-main-section-new-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-main-section-new-code__select' class='new-code-select' title='Новий код' onchange='addModalMainSectionNewCodeOnChange()'></select>").appendTo(field).select2();
}


const modalAddMainSectionCloseOnClick = () => {
    addMainSectionState.oldCodesData = [];
    addMainSectionState.oldCodeIdSelected = null;
    addMainSectionState.newCodesData = [];
    addMainSectionState.newCodeIdSelected = null;
    const modalWindow = document.getElementById("modal-window-add-main-section");
    modalWindow.style.display = "none";
}

const modalAddMainSectionSaveOnClick = () => {
    const nameMainSectionInput = document.getElementById("modal-name-main-section-add");
    let nameMainSection = nameMainSectionInput.value;
    const oldCodeSelect = document.getElementById("modal-add-main-section-old-code__select");
    let oldCode = oldCodeSelect[oldCodeSelect.selectedIndex].value;
    const newCodeSelect = document.getElementById("modal-add-main-section-new-code__select");
    let newCodeId = newCodeSelect[newCodeSelect.selectedIndex].getAttribute("data-id");
    let newCode = newCodeSelect[newCodeSelect.selectedIndex].value;
    if (nameMainSection === '' || 
        oldCode === ( '' || null) || 
        newCode === ( '' || null)) {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let mainSectionData = JSON.stringify({
        typeRequest: "modalSaveAddMainSectionRequest",
        newCodeId: newCodeId,
        newCode: newCode,
        name: nameMainSection,
    });
    modalSaveAddMainSectionRequest(mainSectionData);
}


const modalSaveAddMainSectionRequest = (data) => {
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
        modalAddMainSectionCloseOnClick();
        const nameMainSectionInput = document.getElementById("modal-name-main-section-add");
        nameMainSectionInput.value = "";
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

