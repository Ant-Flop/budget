const addBudgetArticleState = {
  lowLimitSymbolsNewCode: 11,
  oldCodeIdSelected: null,
  newCodeIdSelected: null,
  mainSectionIdSelected: null,
  sectionIdSelected: null,
  subsectionIdSelected: null,
  fundholderIdSelected: null,
  serviceIdSelected: null,
  mainSectionsData: [],
  sectionsData: [],
  subsectionsData: [],
  fundholdersData: [],
  servicesData: [],
  oldCodesData: [],
  newCodesData: [],
  articleData: [],
};

const modalAddBudgetArticleOnClick = () => {
  getMainSectionsRequest(addBudgetArticleState, () => {
    getFundholdersRequest(addBudgetArticleState, () => {
      getOldCodesRequest(addBudgetArticleState, () => {
        fillAddModalBudgetArticleMainSectionSelect();
        fillAddModalBudgetArticleSectionSelect();
        fillAddModalBudgetArticleSubsectionSelect();
        if (
          state.userInfo.role.admin_role ||
          state.userInfo.role.financier_role
        ) {
          fillAddModalBudgetArticleFundholderSelect();
          fillAddModalBudgetArticleServiceSelect();
          fillAddModalBudgetArticleOldCodeSelect();
          fillAddModalBudgetArticleNewCodeSelect();
        } else if (state.userInfo.role.director_role) {
          addBudgetArticleState.fundholderIdSelected =
            state.userInfo.fundholder_id;
          getServicesRequest(addBudgetArticleState, () => {
            fillAddModalBudgetArticleServiceSelect();
          });
        }
        const modalWindow = document.getElementById(
          "modal-window-add-budget-article"
        );
        modalWindow.style.display = "block";
      });
    });
  });
};

const fillAddModalBudgetArticleMainSectionSelect = () => {
  rerenderBudgetArticleMainSectionAddModal();
  const select = document.getElementById(
    "modal-add-budget-article-main-section__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати головний розділ";
  selectedOption.innerText = "Обрати головний розділ";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addBudgetArticleState.mainSectionsData.length === 0) return;
  addBudgetArticleState.mainSectionsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleMainSectionAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-budget-article-main-section-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-budget-article-main-section__select' class='main-section-select' title='Головний розділ'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-budget-article-main-section__select"
  );
  select.onchange = addModalBudgetArticleMainSectionOnChange.bind(this);
};

const addModalBudgetArticleMainSectionOnChange = () => {
  const select = document.getElementById(
    "modal-add-budget-article-main-section__select"
  );
  addBudgetArticleState.mainSectionIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  getSectionsRequest(addBudgetArticleState, () => {
    fillAddModalBudgetArticleSectionSelect();
    fillAddModalBudgetArticleSubsectionSelect();
  });
};

const fillAddModalBudgetArticleSectionSelect = () => {
  rerenderBudgetArticleSectionAddModal();
  const select = document.getElementById(
    "modal-add-budget-article-section__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати розділ";
  selectedOption.innerText = "Обрати розділ";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addBudgetArticleState.sectionsData.length === 0) return;
  addBudgetArticleState.sectionsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleSectionAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-budget-article-section-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-budget-article-section__select' class='section-select' title='Розділ'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-budget-article-section__select"
  );
  select.onchange = addModalBudgetArticleSectionOnChange.bind(this);
};

const addModalBudgetArticleSectionOnChange = () => {
  const select = document.getElementById(
    "modal-add-budget-article-section__select"
  );
  addBudgetArticleState.sectionIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  getSubsectionsRequest(addBudgetArticleState, () => {
    fillAddModalBudgetArticleSubsectionSelect();
  });
};

const fillAddModalBudgetArticleSubsectionSelect = () => {
  rerenderBudgetArticleSubsectionAddModal();
  const select = document.getElementById(
    "modal-add-budget-article-subsection__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати підрозділ";
  selectedOption.innerText = "Обрати підрозділ";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addBudgetArticleState.subsectionsData.length === 0) return;
  addBudgetArticleState.subsectionsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleSubsectionAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-budget-article-subsection-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-budget-article-subsection__select' class='subsection-select' title='Підрозділ'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-budget-article-subsection__select"
  );
  select.onchange = addModalBudgetArticleSubsectionOnChange.bind(this);
};

const addModalBudgetArticleSubsectionOnChange = () => {
  const select = document.getElementById(
    "modal-add-budget-article-subsection__select"
  );
  addBudgetArticleState.subsectionIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
};

const fillAddModalBudgetArticleFundholderSelect = () => {
  rerenderBudgetArticleFundholderAddModal();
  const select = document.getElementById(
    "modal-add-budget-article-fundholder__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати фондоутримувача";
  selectedOption.innerText = "Обрати фондоутримувача";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addBudgetArticleState.fundholdersData.length === 0) return;
  addBudgetArticleState.fundholdersData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleFundholderAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-budget-article-fundholder-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-budget-article-fundholder__select' class='fundholder-select' title='Фондоутримувач'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-budget-article-fundholder__select"
  );
  select.onchange = addModalBudgetArticleFundholderOnChange.bind(this);
};

const addModalBudgetArticleFundholderOnChange = () => {
  const select = document.getElementById(
    "modal-add-budget-article-fundholder__select"
  );
  addBudgetArticleState.fundholderIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  getServicesRequest(addBudgetArticleState, () => {
    fillAddModalBudgetArticleServiceSelect();
  });
};

const fillAddModalBudgetArticleServiceSelect = () => {
  rerenderBudgetArticleServiceAddModal();
  const select = document.getElementById(
    "modal-add-budget-article-service__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати службу";
  selectedOption.innerText = "Обрати службу";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addBudgetArticleState.servicesData.length === 0) return;
  addBudgetArticleState.servicesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleServiceAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-budget-article-service-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-budget-article-service__select' class='service-select' title='Служба'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-budget-article-service__select"
  );
  select.onchange = addModalBudgetArticleServiceOnChange.bind(this);
};

const addModalBudgetArticleServiceOnChange = () => {
  const select = document.getElementById(
    "modal-add-budget-article-service__select"
  );
  addBudgetArticleState.serviceIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
};

const fillAddModalBudgetArticleOldCodeSelect = () => {
  rerenderBudgetArticleOldCodeAddModal();
  const select = document.getElementById(
    "modal-add-budget-article-old-code__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати старий код";
  selectedOption.innerText = "Обрати старий код";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addBudgetArticleState.oldCodesData.length === 0) return;
  addBudgetArticleState.oldCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.old_code;
    optionSelect.innerText = element.old_code;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleOldCodeAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-budget-article-old-code-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-budget-article-old-code__select' class='old-code-select' title='Старий код'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-budget-article-old-code__select"
  );
  select.onchange = addModalBudgetArticleOldCodeOnChange.bind(this);
};

const addModalBudgetArticleOldCodeOnChange = () => {
  const select = document.getElementById(
    "modal-add-budget-article-old-code__select"
  );
  addBudgetArticleState.oldCodeIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  getNewCodesRequest(addBudgetArticleState, "add", null, () => {
    fillAddModalBudgetArticleNewCodeSelect();
  });
};

const fillAddModalBudgetArticleNewCodeSelect = () => {
  rerenderBudgetArticleNewCodeAddModal();
  const select = document.getElementById(
    "modal-add-budget-article-new-code__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати новий код";
  selectedOption.innerText = "Обрати новий код";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addBudgetArticleState.newCodesData.length === 0) return;
  addBudgetArticleState.newCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.new_code;
    optionSelect.innerText = element.new_code;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleNewCodeAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-budget-article-new-code-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-budget-article-new-code__select' class='new-code-select' title='Новий код'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-budget-article-new-code__select"
  );
  select.onchange = addModalBudgetArticleNewCodeOnChange.bind(this);
};

const addModalBudgetArticleNewCodeOnChange = () => {
  const select = document.getElementById(
    "modal-add-budget-article-new-code__select"
  );
  addBudgetArticleState.newCodeIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
};

const modalAddBudgetArticleCloseOnClick = () => {
  const nameArticleInput = document.getElementById(
    "modal-add-budget-article-name"
  );
  nameArticleInput.value = "";
  addBudgetArticleState.oldCodesData = [];
  addBudgetArticleState.oldCodeIdSelected = null;
  addBudgetArticleState.newCodesData = [];
  addBudgetArticleState.newCodeIdSelected = null;
  addBudgetArticleState.mainSectionsData = [];
  addBudgetArticleState.mainSectionIdSelected = null;
  addBudgetArticleState.sectionsData = [];
  addBudgetArticleState.sectionIdSelected = null;
  addBudgetArticleState.subsectionsData = [];
  addBudgetArticleState.subsectionIdSelected = null;
  const modalWindow = document.getElementById(
    "modal-window-add-budget-article"
  );
  modalWindow.style.display = "none";
};

const modalSaveAddBudgetArticleRequest = (data) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(data);
  xhr.onload = () => {
    let response = JSON.parse(xhr.response);
    const labelStatus = document.getElementById("label-save-indicator");
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    labelStatus.innerText = response.text;
    labelStatus.hidden = false;
    backgroundStatus.style.backgroundColor = response.status
      ? "#ebfbeb"
      : "#fbedeb";
    setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
    renderTableRequest();
    modalAddBudgetArticleCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};

const modalAddBudgetArticleSaveOnClick = () => {
  const nameInput = document.getElementById("modal-add-budget-article-name");
  let name = nameInput.value;

  let articleData = null;
  if (state.userInfo.role.admin_role || state.userInfo.role.financier_role) {
    if (
      addBudgetArticleState.mainSectionIdSelected === null ||
      addBudgetArticleState.sectionIdSelected === null ||
      addBudgetArticleState.subsectionIdSelected === null ||
      addBudgetArticleState.fundholderIdSelected === null ||
      addBudgetArticleState.oldCodeIdSelected === null ||
      addBudgetArticleState.newCodeIdSelected === null ||
      name === ""
    ) {
      alert("Не всі обов'язкові поля заповнені!");
      return;
    }
    articleData = JSON.stringify({
      typeRequest: "modalSaveAddBudgetArticleRequest",
      subsectionId: addBudgetArticleState.subsectionIdSelected,
      fundholderId: addBudgetArticleState.fundholderIdSelected,
      serviceId: addBudgetArticleState.serviceIdSelected,
      newCodeId: addBudgetArticleState.newCodeIdSelected,
      name: name,
    });
  } else if (state.userInfo.role.director_role) {
    if (
      addBudgetArticleState.mainSectionIdSelected === null ||
      addBudgetArticleState.sectionIdSelected === null ||
      addBudgetArticleState.subsectionIdSelected === null ||
      addBudgetArticleState.fundholderIdSelected === null ||
      name === ""
    ) {
      alert("Не всі обов'язкові поля заповнені!");
      return;
    }
    articleData = JSON.stringify({
      typeRequest: "modalSaveAddBudgetArticleRequest",
      subsectionId: addBudgetArticleState.subsectionIdSelected,
      fundholderId: addBudgetArticleState.fundholderIdSelected,
      serviceId: addBudgetArticleState.serviceIdSelected,
      name: name,
    });
  }
  modalSaveAddBudgetArticleRequest(articleData);
};
