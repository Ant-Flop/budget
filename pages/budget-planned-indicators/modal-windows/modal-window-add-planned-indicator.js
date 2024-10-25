const addPlannedIndicatorState = {
  mainSectionIdSelected: null,
  sectionIdSelected: null,
  subsectionIdSelected: null,
  serviceIdSelected: null,
  articleIdSelected: null,
  counterpartyIdSelected: [],
  mainSectionsData: [],
  sectionsData: [],
  subsectionsData: [],
  servicesData: [],
  articlesData: [],
  counterpartiesData: [],
  contractsData: [],
  contractsIdSelected: [],
  exceptionsSections: [34],
};

const modalAddPlannedIndicatorOnClick = () => {
  
  getMainSectionsRequest(addPlannedIndicatorState, () => {
    getCounterpartiesRequest(addPlannedIndicatorState, () => {
      fillAddModalPlannedIndicatorMainSectionSelect();
      fillAddModalPlannedIndicatorCounterpartySelect();
      fillAddModalPlannedIndicatorSectionSelect();
      fillAddModalPlannedIndicatorSubsectionSelect();
      fillAddModalPlannedIndicatorServiceSelect();
      fillAddModalPlannedIndicatorArticleSelect();
      getClearContractsRequest(addPlannedIndicatorState, () => {
        fillAddModalPlannedIndicatorContractsSelect();
      });
      const modalWindow = document.getElementById(
        "modal-window-add-planned-indicator"
      );
      modalWindow.style.display = "block";
    });
  });
};

const fillAddModalPlannedIndicatorMainSectionSelect = () => {
  rerenderPlannedIndicatorMainSectionAddModal();
  const select = document.getElementById(
    "modal-add-planned-indicator-main-section__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати головний розділ";
  selectedOption.innerText = "Обрати головний розділ";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addPlannedIndicatorState.mainSectionsData.length === 0) return;
  addPlannedIndicatorState.mainSectionsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorMainSectionAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-planned-indicator-main-section-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-planned-indicator-main-section__select' class='main-section-select' title='Головний розділ'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-planned-indicator-main-section__select"
  );
  select.onchange = addModalPlannedIndicatorMainSectionOnChange.bind(this);
};

const addModalPlannedIndicatorMainSectionOnChange = () => {
  const select = document.getElementById(
    "modal-add-planned-indicator-main-section__select"
  );
  addPlannedIndicatorState.mainSectionIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  getSectionsRequest(addPlannedIndicatorState, () => {
    fillAddModalPlannedIndicatorSectionSelect();
    fillAddModalPlannedIndicatorSubsectionSelect();
    fillAddModalPlannedIndicatorServiceSelect();
    fillAddModalPlannedIndicatorArticleSelect();
  });
};

const fillAddModalPlannedIndicatorSectionSelect = () => {
  rerenderPlannedIndicatorSectionAddModal();
  const select = document.getElementById(
    "modal-add-planned-indicator-section__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати розділ";
  selectedOption.innerText = "Обрати розділ";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addPlannedIndicatorState.sectionsData.length === 0) return;
  addPlannedIndicatorState.sectionsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorSectionAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-planned-indicator-section-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-planned-indicator-section__select' class='section-select' title='Розділ'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-planned-indicator-section__select"
  );
  select.onchange = addModalPlannedIndicatorSectionOnChange.bind(this);
};

const addModalPlannedIndicatorSectionOnChange = () => {
  const select = document.getElementById(
    "modal-add-planned-indicator-section__select"
  );
  addPlannedIndicatorState.sectionIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  const contractWrapper = document.getElementById(
    "modal-add-contracts-wrapper-id"
  );
  if (
    addPlannedIndicatorState.exceptionsSections.find(
      (element) => element == addPlannedIndicatorState.sectionIdSelected
    )
  ) {
    contractWrapper.hidden = false;
  } else {
    contractWrapper.hidden = true;
  }
  getSubsectionsRequest(addPlannedIndicatorState, () => {
    fillAddModalPlannedIndicatorSubsectionSelect();
  });
};

const fillAddModalPlannedIndicatorSubsectionSelect = () => {
  rerenderPlannedIndicatorSubsectionAddModal();
  const select = document.getElementById(
    "modal-add-planned-indicator-subsection__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати підрозділ";
  selectedOption.innerText = "Обрати підрозділ";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addPlannedIndicatorState.subsectionsData.length === 0) return;
  addPlannedIndicatorState.subsectionsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorSubsectionAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-planned-indicator-subsection-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-planned-indicator-subsection__select' class='subsection-select' title='Підрозділ'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-planned-indicator-subsection__select"
  );
  select.onchange = addModalPlannedIndicatorSubsectionOnChange.bind(this);
};

const addModalPlannedIndicatorSubsectionOnChange = () => {
  const select = document.getElementById(
    "modal-add-planned-indicator-subsection__select"
  );
  addPlannedIndicatorState.subsectionIdSelected =
    select[select.selectedIndex].getAttribute("data-id");

  getArticlesRequest(addPlannedIndicatorState, () => {
    fillAddModalPlannedIndicatorArticleSelect();
  });
  getServicesRequest(addPlannedIndicatorState, () => {
    fillAddModalPlannedIndicatorServiceSelect();
  });
};

const fillAddModalPlannedIndicatorServiceSelect = () => {
  rerenderPlannedIndicatorServiceAddModal();
  const select = document.getElementById(
    "modal-add-planned-indicator-service__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати службу";
  selectedOption.innerText = "Обрати службу";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addPlannedIndicatorState.servicesData.length === 0) return;
  addPlannedIndicatorState.servicesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorServiceAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-planned-indicator-service-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-planned-indicator-service__select' class='service-select' title='Служба'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-planned-indicator-service__select"
  );
  select.onchange = addModalPlannedIndicatorServiceOnChange.bind(this);
};

const addModalPlannedIndicatorServiceOnChange = () => {
  const select = document.getElementById(
    "modal-add-planned-indicator-service__select"
  );
  addPlannedIndicatorState.serviceIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  getArticlesRequest(addPlannedIndicatorState, () => {
    fillAddModalPlannedIndicatorArticleSelect();
  });
};

const fillAddModalPlannedIndicatorArticleSelect = () => {
  rerenderPlannedIndicatorArticleAddModal();
  const select = document.getElementById(
    "modal-add-planned-indicator-article__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати назва статті";
  selectedOption.innerText = "Обрати назву статті";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (addPlannedIndicatorState.articlesData.length === 0) return;
  addPlannedIndicatorState.articlesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorArticleAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-planned-indicator-article-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-planned-indicator-article__select' class='article-select' title='Назва статті'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-planned-indicator-article__select"
  );
  select.onchange = addModalPlannedIndicatorArticleOnChange.bind(this);
};

const addModalPlannedIndicatorArticleOnChange = () => {
  const select = document.getElementById(
    "modal-add-planned-indicator-article__select"
  );
  addPlannedIndicatorState.articleIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
};

const fillAddModalPlannedIndicatorCounterpartySelect = () => {
  rerenderPlannedIndicatorCounterpartyAddModal();
  const select = document.getElementById(
    "modal-add-planned-indicator-counterparty__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  if (addPlannedIndicatorState.counterpartiesData.length === 0) return;
  addPlannedIndicatorState.counterpartiesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorCounterpartyAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-planned-indicator-counterparty-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-planned-indicator-counterparty__select' class='counterparty-select js-example-basic-multiple' title='Головний розділ' multiple='multiple'  name='states[]'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-planned-indicator-counterparty-wrapper-select"
  );
  select.onchange = addModalPlannedIndicatorCounterpartyOnChange.bind(this);
};

const addModalPlannedIndicatorCounterpartyOnChange = () => {
  const select = document.getElementById(
    "modal-add-planned-indicator-counterparty__select"
  );

    addPlannedIndicatorState.counterpartyIdSelected = [];
  [...select.options].forEach((element, index) => {
    if (element.selected) {
      addPlannedIndicatorState.counterpartyIdSelected.push({
        counterparty_id: element.getAttribute("data-id"),
        planned_indicators_id: addPlannedIndicatorState.plannedIndicatorId,
      });
    }
  });
  console.log(addPlannedIndicatorState)
  
  getClearContractsRequest(addPlannedIndicatorState, () => {
    fillAddModalPlannedIndicatorContractsSelect();
  });
};



// add contract

const rerenderPlannedIndicatorContractsAddModal = () => {
  const field = document.getElementsByClassName(
    "modal-add-planned-indicator-contracts-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-add-planned-indicator-contracts__select' class='contract-select contracts-select js-example-basic-multiple' title='Договори' name='states[]' multiple='multiple'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-add-planned-indicator-contracts__select"
  );
  select.onchange = addModalPlannedIndicatorContractsSelectOnChange.bind(this);
};

const fillAddModalPlannedIndicatorContractsSelect = () => {
  rerenderPlannedIndicatorContractsAddModal();

  const selectCounterparty = document.getElementById(
    "modal-add-planned-indicator-counterparty__select"
  );

  const select = document.getElementById(
    "modal-add-planned-indicator-contracts__select"
  );
  select.onchange = addModalPlannedIndicatorContractsSelectOnChange.bind(this);
  while (select.firstChild) select.removeChild(select.firstChild);
  addPlannedIndicatorState.contractsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.contract_number;
    optionSelect.innerText = element.contract_number;
    optionSelect.setAttribute("data-id", element.contract_id);
    addPlannedIndicatorState.contractsIdSelected.forEach((value) => {
      if (value.contract_id === element.contract_id) {
        optionSelect.selected = true;
        optionSelect.hidden = true;
      }
    });
    select.appendChild(optionSelect);
  });
};

const addModalPlannedIndicatorContractsSelectOnChange = () => {
  const select = document.getElementById(
    "modal-add-planned-indicator-contracts__select"
  );
  addPlannedIndicatorState.contractsIdSelected = [];
  [...select.options].forEach((element, index) => {
    if (element.selected) {
      addPlannedIndicatorState.contractsIdSelected.push({
        contract_id: element.getAttribute("data-id"),
        planned_indicators_id: addPlannedIndicatorState.plannedIndicatorId,
      });
    }
  });

  fillAddModalPlannedIndicatorContractsSelect();
};

const modalSaveAddPlannedIndicatorRequest = (data) => {
  console.log(data)
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(data);
  xhr.onload = () => {
    console.log(xhr.response)
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
    modalAddPlannedIndicatorCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "";
  };
};

const modalAddPlannedIndicatorSaveOnClick = () => {
  if (
    addPlannedIndicatorState.mainSectionIdSelected === null ||
    addPlannedIndicatorState.sectionIdSelected === null ||
    addPlannedIndicatorState.subsectionIdSelected === null ||
    addPlannedIndicatorState.articleIdSelected === null ||
    !window.localStorage.getItem("modalSumPlannedIndicatorState")
  ) {
    alert("Не всі обов'язкові поля заповнені!");
    return;
  }
  const plannedIndicatorsStorage = JSON.parse(
    window.localStorage.getItem("modalSumPlannedIndicatorState")
  );
  let plannedIndicatorsData = JSON.stringify({
    typeRequest: "modalSaveAddPlannedIndicatorRequest",
    budgetArticleId: addPlannedIndicatorState.articleIdSelected,
    counterparties: addPlannedIndicatorState.counterpartyIdSelected,
    monthPlannedIndicatorWithVAT:
      plannedIndicatorsStorage.monthPlannedIndicatorWithVAT.map((element) =>
        element === null ? 0 : element
      ),
    sumMonthWithVAT:
      plannedIndicatorsStorage.sumMonthWithVAT === null
        ? 0
        : plannedIndicatorsStorage.sumMonthWithVAT,
    monthPlannedIndicatorNoVAT:
      plannedIndicatorsStorage.monthPlannedIndicatorNoVAT.map((element) =>
        element === null ? 0 : element
      ),
    sumMonthNoVAT:
      plannedIndicatorsStorage.sumMonthNoVAT === null
        ? 0
        : plannedIndicatorsStorage.sumMonthNoVAT,
    plannedIndicatorVatSign:
      plannedIndicatorsStorage.monthPlannedIndicatorVatSign,
    sectionId: addPlannedIndicatorState.sectionIdSelected,
    contracts: addPlannedIndicatorState.contractsIdSelected,
  });
  modalSaveAddPlannedIndicatorRequest(plannedIndicatorsData);
};

const modalAddPlannedIndicatorCloseOnClick = () => {
  const signPlannedIndicator = document.getElementById(
    "sign-planned-indicator"
  );
  signPlannedIndicator.innerHTML =
    "<div class='cross-element-1'>" +
    "<div class='cross-element-2'></div>" +
    "</div>";
  addPlannedIndicatorState.mainSectionsData = [];
  addPlannedIndicatorState.mainSectionIdSelected = null;
  addPlannedIndicatorState.sectionsData = [];
  addPlannedIndicatorState.sectionIdSelected = null;
  addPlannedIndicatorState.subsectionsData = [];
  addPlannedIndicatorState.subsectionIdSelected = null;
  addPlannedIndicatorState.articlesData = [];
  addPlannedIndicatorState.articleIdSelected = null;
  addPlannedIndicatorState.counterpartiesData = [];
  addPlannedIndicatorState.counterpartyIdSelected = null;
  addPlannedIndicatorState.servicesData = [];
  addPlannedIndicatorState.serviceIdSelected = null;
  addPlannedIndicatorState.contractsData = [];
  addPlannedIndicatorState.contractsIdSelected = [];
  console.log(addPlannedIndicatorState)
  const contractWrapper = document.getElementById(
    "modal-add-contracts-wrapper-id"
  );
  if (
    addPlannedIndicatorState.exceptionsSections.find(
      (element) => element == addPlannedIndicatorState.sectionIdSelected
    )
  ) {
    contractWrapper.hidden = false;
  } else {
    contractWrapper.hidden = true;
  }
  window.localStorage.removeItem("modalSumPlannedIndicatorState");
  const modalWindow = document.getElementById(
    "modal-window-add-planned-indicator"
  );
  modalWindow.style.display = "none";
};
