const addSectionState = {
    nameTable: "sections_directory",
    countSymbolsNewCode: 9,
    oldCodesData: [],
    oldCodeIdSelected: null,
    newCodesData: [],
    newCodeIdSelected: null,
    mainSectionsData: [],
    mainSectionIdSelected: null,
}

const modalAddSectionOnClick = () => {
    getOldCodesRequest(addSectionState, () => {
        getMainSectionsRequest(addSectionState, () => {
            fillAddModalSectionOldCodeSelect();
            fillAddModalSectionNewCodeSelect();
            fillAddModalSectionMainSectionSelect()
            const modalWindow = document.getElementById("modal-window-add-section");
            modalWindow.style.display = "block";
        })
    });
}

const fillAddModalSectionOldCodeSelect = () => {
    rerenderSectionOldCodeAddModal();
    const select = document.getElementById("modal-add-section-old-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати старий код";
    selectedOption.innerText = "Обрати старий код";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addSectionState.oldCodesData.length === 0)
        return;
    addSectionState.oldCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.old_code;
        optionSelect.innerText = element.old_code;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderSectionOldCodeAddModal = () => {
    const field = document.getElementsByClassName("modal-add-section-old-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-section-old-code__select' class='old-code-select' title='Старий код'></select>").appendTo(field).select2();
    const select = document.getElementById("modal-add-section-old-code__select");
    select.onchange = addModalSectionOldCodeOnChange.bind(this);
}

const addModalSectionOldCodeOnChange = () => {
    const select = document.getElementById("modal-add-section-old-code__select");
    addSectionState.oldCodeIdSelected = select[select.selectedIndex].getAttribute("data-id");
    getNewCodesRequest(addSectionState, () => {
        fillAddModalSectionNewCodeSelect();
    })
}

const fillAddModalSectionNewCodeSelect = () => {
    rerenderSectionNewCodeAddModal();
    const select = document.getElementById("modal-add-section-new-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати новий код";
    selectedOption.innerText = "Обрати новий код";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addSectionState.newCodesData.length === 0)
        return;
    addSectionState.newCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.new_code;
        optionSelect.innerText = element.new_code;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderSectionNewCodeAddModal = () => {
    const field = document.getElementsByClassName("modal-add-section-new-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-section-new-code__select' class='new-code-select' title='Новий код'></select>").appendTo(field).select2();
}

const fillAddModalSectionMainSectionSelect = () => {
    rerenderSectionMainSectionAddModal();
    const select = document.getElementById("modal-add-section-main-section__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати головний розділ";
    selectedOption.innerText = "Обрати головний розділ";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addSectionState.mainSectionsData.length === 0)
        return;
    addSectionState.mainSectionsData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.name;
        optionSelect.innerText = element.name;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderSectionMainSectionAddModal = () => {
    const field = document.getElementsByClassName("modal-add-section-main-section-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-section-main-section__select' class='main-section-select' title='Новий код'></select>").appendTo(field).select2();
}

const modalAddSectionCloseOnClick = () => {
    const nameSectionInput = document.getElementById("modal-name-section-add");
    nameSectionInput.value = "";
    addSectionState.oldCodesData = [];
    addSectionState.oldCodeIdSelected = null;
    addSectionState.newCodesData = [];
    addSectionState.newCodeIdSelected = null;
    const modalWindow = document.getElementById("modal-window-add-section");
    modalWindow.style.display = "none";
}

const modalAddSectionSaveOnClick = () => {
    const nameSectionInput = document.getElementById("modal-name-section-add");
    let nameSection = nameSectionInput.value;
    const oldCodeSelect = document.getElementById("modal-add-section-old-code__select");
    let oldCode = oldCodeSelect[oldCodeSelect.selectedIndex].value;
    const newCodeSelect = document.getElementById("modal-add-section-new-code__select");
    let newCodeId = newCodeSelect[newCodeSelect.selectedIndex].getAttribute("data-id");
    let newCode = newCodeSelect[newCodeSelect.selectedIndex].value;
    const mainSectionSelect = document.getElementById("modal-add-section-main-section__select");
    let mainSectionId = mainSectionSelect[mainSectionSelect.selectedIndex].getAttribute("data-id");
    let nameMainSection = mainSectionSelect[mainSectionSelect.selectedIndex].value;
    if (nameSection === '' || 
        oldCode === ( '' || null) || 
        newCode === ( '' || null) || 
        nameMainSection === ( '' || null)) {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let sectionData = JSON.stringify({
        typeRequest: "modalSaveAddSectionRequest",
        newCodeId: newCodeId,
        newCode: newCode,
        mainSectionId: mainSectionId,
        name: nameSection,
    });
    modalSaveAddSectionRequest(sectionData);
}


const modalSaveAddSectionRequest = (data) => {
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
        modalAddSectionCloseOnClick();
        const nameMainSectionInput = document.getElementById("modal-name-section-add");
        nameMainSectionInput.value = "";
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

