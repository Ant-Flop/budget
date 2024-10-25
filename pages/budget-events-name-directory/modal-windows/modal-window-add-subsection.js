const addSubsectionState = {
    nameTable: "subsections_directory",
    countSymbolsNewCode: 10,
    oldCodesData: [],
    oldCodeIdSelected: null,
    newCodesData: [],
    newCodeIdSelected: null,
    mainSectionsData: [],
    mainSectionIdSelected: null,
    sectionsData: [],
    sectionIdSelected: null,
}

const modalAddSubsectionOnClick = () => {
    getOldCodesRequest(addSubsectionState, () => {
        getMainSectionsRequest(addSubsectionState, () => {
            fillAddModalSubsectionOldCodeSelect();
            fillAddModalSubsectionNewCodeSelect();
            fillAddModalSubsectionMainSectionSelect();
            fillAddModalSubsectionSectionSelect();
            const modalWindow = document.getElementById("modal-window-add-subsection");
            modalWindow.style.display = "block";
        })
    });
}

const fillAddModalSubsectionOldCodeSelect = () => {
    rerenderSubsectionOldCodeAddModal();
    const select = document.getElementById("modal-add-subsection-old-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати старий код";
    selectedOption.innerText = "Обрати старий код";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addSubsectionState.oldCodesData.length === 0)
        return;
    addSubsectionState.oldCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.old_code;
        optionSelect.innerText = element.old_code;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderSubsectionOldCodeAddModal = () => {
    const field = document.getElementsByClassName("modal-add-subsection-old-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-subsection-old-code__select' class='old-code-select' title='Старий код'></select>").appendTo(field).select2();
    const select = document.getElementById("modal-add-subsection-old-code__select");
    select.onchange = addModalSubsectionOldCodeOnChange.bind(this);
}

const addModalSubsectionOldCodeOnChange = () => {
    const select = document.getElementById("modal-add-subsection-old-code__select");
    addSubsectionState.oldCodeIdSelected = select[select.selectedIndex].getAttribute("data-id");
    getNewCodesRequest(addSubsectionState, () => {
        fillAddModalSubsectionNewCodeSelect();
    })
}

const fillAddModalSubsectionNewCodeSelect = () => {
    rerenderSubsectionNewCodeAddModal();
    const select = document.getElementById("modal-add-subsection-new-code__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати новий код";
    selectedOption.innerText = "Обрати новий код";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addSubsectionState.newCodesData.length === 0)
        return;
        addSubsectionState.newCodesData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.new_code;
        optionSelect.innerText = element.new_code;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderSubsectionNewCodeAddModal = () => {
    const field = document.getElementsByClassName("modal-add-subsection-new-code-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-subsection-new-code__select' class='new-code-select' title='Новий код'></select>").appendTo(field).select2();
}

const fillAddModalSubsectionMainSectionSelect = () => {
    rerenderSubsectionMainSectionAddModal();
    const select = document.getElementById("modal-add-subsection-main-section__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати головний розділ";
    selectedOption.innerText = "Обрати головний розділ";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addSubsectionState.mainSectionsData.length === 0)
        return;
    addSubsectionState.mainSectionsData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.name;
        optionSelect.innerText = element.name;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderSubsectionMainSectionAddModal = () => {
    const field = document.getElementsByClassName("modal-add-subsection-main-section-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-subsection-main-section__select' class='main-section-select' title='Новий код'></select>").appendTo(field).select2();
    const select = document.getElementById("modal-add-subsection-main-section__select");
    select.onchange = addModalSubsectionMainSectionOnChange.bind(this);
}

const addModalSubsectionMainSectionOnChange = () => {
    const select = document.getElementById("modal-add-subsection-main-section__select");
    addSubsectionState.mainSectionIdSelected = select[select.selectedIndex].getAttribute("data-id");
    getSectionsRequest(addSubsectionState, () => {
        fillAddModalSubsectionSectionSelect();
    })
}

const fillAddModalSubsectionSectionSelect = () => {
    rerenderSubsectionSectionAddModal();
    const select = document.getElementById("modal-add-subsection-section__select");
    while (select.firstChild) 
        select.removeChild(select.firstChild);
    const selectedOption = document.createElement("option");
    selectedOption.value = "Обрати розділ";
    selectedOption.innerText = "Обрати розділ";
    selectedOption.hidden = true;
    selectedOption.selected = true;
    select.appendChild(selectedOption);
    if(addSubsectionState.sectionsData.length === 0)
        return;
    addSubsectionState.sectionsData.forEach(element => {
        const optionSelect = document.createElement("option");
        optionSelect.value = element.name;
        optionSelect.innerText = element.name;
        optionSelect.setAttribute("data-id", element.id);
        select.appendChild(optionSelect);
    })
}

const rerenderSubsectionSectionAddModal = () => {
    const field = document.getElementsByClassName("modal-add-subsection-section-wrapper-select");
    while (field.firstChild) 
        field.removeChild(field.firstChild);
    $("<select id='modal-add-subsection-section__select' class='main-section-select' title='Новий код'></select>").appendTo(field).select2();
}

const modalAddSubsectionCloseOnClick = () => {
    const nameSubsectionInput = document.getElementById("modal-name-subsection-add");
    nameSubsectionInput.value = "";
    addSubsectionState.oldCodesData = [];
    addSubsectionState.oldCodeIdSelected = null;
    addSubsectionState.newCodesData = [];
    addSubsectionState.newCodeIdSelected = null;
    addSubsectionState.mainSectionsData = [];
    addSubsectionState.mainSectionIdSelected = null;
    addSubsectionState.sectionsData = [];
    addSubsectionState.sectionIdSelected = null;
    const modalWindow = document.getElementById("modal-window-add-subsection");
    modalWindow.style.display = "none";
}

const modalAddSubsectionSaveOnClick = () => {
    const nameSubsectionInput = document.getElementById("modal-name-subsection-add");
    let nameSubsection = nameSubsectionInput.value;
    const oldCodeSelect = document.getElementById("modal-add-subsection-old-code__select");
    let oldCode = oldCodeSelect[oldCodeSelect.selectedIndex].value;
    const newCodeSelect = document.getElementById("modal-add-subsection-new-code__select");
    let newCodeId = newCodeSelect[newCodeSelect.selectedIndex].getAttribute("data-id");
    let newCode = newCodeSelect[newCodeSelect.selectedIndex].value;
    const mainSectionSelect = document.getElementById("modal-add-subsection-main-section__select");
    let mainSectionId = mainSectionSelect[mainSectionSelect.selectedIndex].getAttribute("data-id");
    let nameMainSection = mainSectionSelect[mainSectionSelect.selectedIndex].value;
    const sectionSelect = document.getElementById("modal-add-subsection-section__select");
    let sectionId = sectionSelect[sectionSelect.selectedIndex].getAttribute("data-id");
    let nameSection = sectionSelect[sectionSelect.selectedIndex].value;
    if (nameSubsection === '' || 
        oldCode === ( '' || null) || 
        newCode === ( '' || null) || 
        nameMainSection === ( '' || null) || 
        nameSection === ( '' || null)) {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let sectionData = JSON.stringify({
        typeRequest: "modalSaveAddSubsectionRequest",
        newCodeId: newCodeId,
        sectionId: sectionId,
        name: nameSubsection,
    });
    modalSaveAddSubsectionRequest(sectionData);
}


const modalSaveAddSubsectionRequest = (data) => {
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
        modalAddSubsectionCloseOnClick();
        const nameMainSectionInput = document.getElementById("modal-name-section-add");
        nameMainSectionInput.value = "";
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

