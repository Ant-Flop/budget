let state = {
  counterpartiesArray: [],
  treatyArray: [],
  counterpartySelectId: 0,
  treatySelectId: 0,
  yearSelected: null,
  contractEntriesArray: [],
  editeEntryData: null,
  editedEntryStatus: false,
  scrollAfterEdit: null,
  articlesArray: [],
  articleEditSelectedId: null,
};

let checkStorage = true;

document.addEventListener("DOMContentLoaded", () => {
  let storage = window.localStorage.getItem("treaty-directory-state");
  if (window.localStorage.getItem("treaty-directory-state"))
    state = JSON.parse(storage);
  contractEntriesArrayRequest(state.counterpartySelectId, state.treatySelectId);
  renderTableRequest(state.counterpartySelectId, state.treatySelectId);
  selectCounterpartiesValueRequest();
  selectRedefineStyle();
});

// Requests

const contractEntriesArrayRequest = (counterpartyId, treatyId) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "contractEntriesArrayRequest",
      counterpartyId: counterpartyId,
      treatyId: treatyId,
    })
  );
  xhr.onload = function () {
    state.contractEntriesArray = JSON.parse(xhr.response);
    contractEditStatusRequest(treatyId);
  };
};

const contractEditStatusRequest = (treatyId) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "contractEditStatusRequest",
      treatyId: treatyId,
    })
  );
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);
    state.editedEntryStatus = response.edit_status;
    displayEditLock(state.editedEntryStatus);
  };
};

const renderTableRequest = (counterpartyId, treatyId) => {
  getArticlesRequest();
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      counterpartyId: counterpartyId,
      treatyId: treatyId,
    })
  );
  xhr.onload = function () {
    document.querySelector("#treaty-directory-table").innerHTML = xhr.response;
    setScrollTable();
    selectRowOnClick()
  };
};

const selectRowOnClick = () => {
  const tableRows = [
    ...document.querySelectorAll(
      "#treaty-directory-table > table > tbody > tr"
    ),
  ];

  console.log(tableRows)

  tableRows.forEach((row) => {
    row.addEventListener("click", () => {
      [...document.getElementsByClassName("selected-td")].forEach(
        (element) => element.classList.remove("selected-td")
      );
      [...row.getElementsByTagName("td")].forEach((element) =>
        element.classList.add("selected-td")
      );
    });
  });
}

const selectCounterpartiesValueRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "selectValueRequest",
    })
  );
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);
    state.counterpartiesArray = response.counterparties;
    state.treatyArray = response.treaties;
    fillSelectCounterparties(state.counterpartiesArray);
    checkOnEditModeTreaty();
  };
};

const saveEditedEntryRequest = () => {
  console.log(state.editeEntryData);
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "saveEditedEntryRequest",
      entryData: state.editeEntryData,
    })
  );
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);
    const labelStatus = document.getElementById(
      "label-treaty-directory-save-indicator"
    );
    const backgroundStatus = document.getElementsByClassName(
      "upper-treaty-directory-save-panel"
    )[0];
    labelStatus.innerText = response.text;
    labelStatus.hidden = false;
    backgroundStatus.style.backgroundColor = response.status
      ? "#ebfbeb"
      : "#fbedeb";
    setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
    contractEntriesArrayRequest(
      state.counterpartySelectId,
      state.treatySelectId
    );
    renderTableRequest(state.counterpartySelectId, state.treatySelectId);
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus = document.getElementsByClassName(
      "upper-treaty-directory-save-panel"
    )[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};

const blockingEditModeRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "blockingEditModeRequest",
      treatyId: state.treatySelectId,
    })
  );
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);
    const labelStatus = document.getElementById(
      "label-treaty-directory-save-indicator"
    );
    const backgroundStatus = document.getElementsByClassName(
      "upper-treaty-directory-save-panel"
    )[0];
    labelStatus.innerText = response.text;
    labelStatus.hidden = false;
    backgroundStatus.style.backgroundColor = response.status
      ? "#ebfbeb"
      : "#fbedeb";
    setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
    selectCounterpartiesValueRequest();
    contractEntriesArrayRequest(
      state.counterpartySelectId,
      state.treatySelectId
    );
    renderTableRequest(state.counterpartySelectId, state.treatySelectId);
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus = document.getElementsByClassName(
      "upper-treaty-directory-save-panel"
    )[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};

const getArticlesRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getArticlesRequest",
      counterpartyId: state.counterpartySelectId,
    })
  );
  xhr.onload = function () {
    state.articlesArray = JSON.parse(xhr.response);
  };
};

// Events

const selectCounterpartiesOnChange = (selectId) => {
  if (checkStorage) window.localStorage.removeItem("treaty-directory-state");
  const target = document.getElementById("treaty-counterparty-select");
  const counterpartyId =
    target.options[target.options.selectedIndex].getAttribute("data-id");
  selectId =
    selectId === null ? (selectId = "treaty-number-contract-select") : selectId;
  console.log(counterpartyId);
  if (!counterpartyId) state.yearSelected = null;
  //console.log(state.treatyArray);

  fillTermContract(counterpartyId);

  fillTreatySelect(selectId, counterpartyId);
};

const fillTermContract = (counterpartyId) => {
  const termContractSelect = document.getElementById("term-of-contract-select");
  const yearArray = [];

  while (termContractSelect.firstChild)
    termContractSelect.removeChild(termContractSelect.firstChild);

  const optionUnselect = document.createElement("option");

  optionUnselect.value = "Обрати";
  optionUnselect.innerText = "Обрати";

  termContractSelect.appendChild(optionUnselect);
  console.log(state.treatyArray);
  state.treatyArray.forEach((element) => {
    if (element.counterparty_id === counterpartyId) {
      console.log(
        yearArray.filter(
          (item) => item === element.term_contract.substring(0, 4)
        )
      );
      if (!yearArray.includes(element.term_contract.substring(0, 4))) {
        yearArray.push(element.term_contract.substring(0, 4));
      }
    }
  });
  console.log(yearArray);
  let storageStatus = window.localStorage.getItem("treaty-directory-state");
  let storage = JSON.parse(
    window.localStorage.getItem("treaty-directory-state")
  );

  yearArray.forEach((element) => {
    const option = document.createElement("option");
    option.value = element;
    option.innerText = element;
    if (storageStatus)
      if (element === storage.yearSelected) option.selected = true;

    console.log(option.selected);
    termContractSelect.appendChild(option);
  });

  termContractSelect.onchange = termContractOnChange.bind(
    this,
    termContractSelect
  );
};

//  Доделать !!! _--------------------------------_

const termContractOnChange = (target) => {
  console.log(state.counterpartySelectId);
  state.yearSelected = target.options[target.selectedIndex].value;
  fillTreatySelect("treaty-number-contract-select", state.counterpartySelectId);
  window.localStorage.setItem("treaty-directory-state", JSON.stringify(state));
};

const fillTreatySelect = (selectId, counterpartyId) => {
  const treaty = document.getElementById(selectId);
  state.counterpartySelectId = counterpartyId;
  if (checkStorage) state.treatySelectId = 0;
  checkStorage = true;
  while (treaty.firstChild) treaty.removeChild(treaty.firstChild);
  for (let i = 0; i < state.treatyArray.length; i++) {
    if (i === 0) {
      const optionUnselect = document.createElement("option");
      if (selectId === "modal-treaty-number-contract-select") {
        optionUnselect.value = "Обрати номер договору";
        optionUnselect.innerText = "Обрати номер договору";
      } else {
        optionUnselect.value = "Обрати";
        optionUnselect.innerText = "Обрати";
        rerenderModalCounterpatySelect(counterpartyId);
        if (state.treatySelectId !== null)
          rerenderModalTreatySelect(state.treatySelectId);
      }
      treaty.appendChild(optionUnselect);
    }

    if (
      state.treatyArray[i].counterparty_id === counterpartyId &&
      state.treatyArray[i].term_contract.substring(0, 4) === state.yearSelected
    ) {
      console.log(state.treatyArray[i].term_contract.substring(0, 4));
      const option = document.createElement("option");
      option.value = state.treatyArray[i].number_contract;
      option.innerText = state.treatyArray[i].number_contract;
      option.id = state.treatyArray[i].id;
      option.setAttribute("data-id", state.treatyArray[i].id);
      treaty.appendChild(option);
    }
  }
  if (selectId === "treaty-number-contract-select") {
    contractEntriesArrayRequest(
      state.counterpartySelectId,
      state.treatySelectId
    );
    renderTableRequest(state.counterpartySelectId, state.treatySelectId);
  }
};

const selectTreatyOnChange = () => {
  const counterparty = document.getElementById("treaty-counterparty-select");
  const counterpartyId =
    counterparty.options[counterparty.options.selectedIndex].getAttribute(
      "data-id"
    );
  const target = document.getElementById("treaty-number-contract-select");
  const treatyId = target.options[target.selectedIndex].getAttribute("data-id");
  state.counterpartySelectId = counterpartyId;
  state.treatySelectId = treatyId;
  rerenderModalCounterpatySelect(counterpartyId);
  rerenderModalTreatySelect(treatyId);
  contractEntriesArrayRequest(state.counterpartySelectId, state.treatySelectId);
  renderTableRequest(state.counterpartySelectId, state.treatySelectId);
  window.localStorage.setItem("treaty-directory-state", JSON.stringify(state));
};

const editEntryOnClick = (target) => {
  let id = parseInt(target.getAttribute("data-id"));
  let indexRow = parseInt(target.getAttribute("data-index-row"));
  const contractNameTd =
    document.getElementsByClassName("contract-name__td")[indexRow];
  const contractNameInput = document.createElement("input");
  const contractTermTd =
    document.getElementsByClassName("contract-term__td")[indexRow];
  const contractTermInput = document.createElement("input");
  const articleNameTd =
    document.getElementsByClassName("kind-service__td")[indexRow];
  const articleNameSelect = document.createElement("select");

  const serviceNameTd =
    document.getElementsByClassName("name-service__td")[indexRow];
  const serviceNameInput = document.createElement("input");
  const equipmentTypeTd =
    document.getElementsByClassName("type-equipment__td")[indexRow];
  const equipmentTypeInput = document.createElement("input");
  const amountTd = document.getElementsByClassName("amount__td")[indexRow];
  const amountInput = document.createElement("input");
  const priceTd = document.getElementsByClassName("price__td")[indexRow];
  const priceInput = document.createElement("input");
  const costTd = document.getElementsByClassName("cost__td")[indexRow];
  const costInput = document.createElement("input");
  state.contractEntriesArray.forEach((element) => {
    if (parseInt(element.id) === id) {
      state.editeEntryData = element;
      contractNameTd.innerText = "";
      contractNameInput.value = element.contract_name;
      contractNameInput.onchange = editFieldOnChange.bind(
        this,
        contractNameInput,
        "contract_name",
        id
      );
      contractNameInput.classList.add("edit__input");
      contractNameTd.appendChild(contractNameInput);

      contractTermTd.innerText = "";
      contractTermInput.type = "date";
      contractTermInput.value = element.contract_term;
      contractTermInput.onchange = editFieldOnChange.bind(
        this,
        contractTermInput,
        "contract_term",
        id
      );
      contractTermInput.classList.add("edit__input");
      contractTermTd.appendChild(contractTermInput);

      articleNameTd.innerText = "";
      articleNameSelect;

      serviceNameTd.innerText = "";
      serviceNameInput.value = element.name_service;
      serviceNameInput.onchange = editFieldOnChange.bind(
        this,
        serviceNameInput,
        "name_service",
        id
      );
      serviceNameInput.classList.add("edit__input");
      serviceNameTd.appendChild(serviceNameInput);

      articleNameTd.appendChild(createArticleSelect());

      equipmentTypeTd.innerText = "";
      equipmentTypeInput.value = element.type_equipment;
      equipmentTypeInput.onchange = editFieldOnChange.bind(
        this,
        equipmentTypeInput,
        "type_equipment",
        id
      );
      equipmentTypeInput.classList.add("edit__input");
      equipmentTypeTd.appendChild(equipmentTypeInput);

      amountTd.innerText = "";
      amountInput.type = "number";
      amountInput.value = element.amount;
      amountInput.onchange = editFieldOnChange.bind(
        this,
        amountInput,
        "amount",
        id
      );
      amountInput.classList.add("edit__input");
      amountInput.classList.add("edit-amount__input");
      amountTd.appendChild(amountInput);
      priceTd.innerText = "";
      priceInput.value = parseFloat(element.price_of_service_no_pdv).toFixed(2);
      priceInput.onchange = editFieldOnChange.bind(
        this,
        priceInput,
        "price",
        id
      );
      priceInput.classList.add("edit__input");
      priceInput.classList.add("edit-price__input");
      priceTd.appendChild(priceInput);
      costTd.innerText = "";
      costInput.value = parseFloat(element.cost_of_materials_no_pdv).toFixed(2);
      costInput.onchange = editFieldOnChange.bind(this, costInput, "cost", id);
      costInput.classList.add("edit__input");
      costInput.classList.add("edit-cost__input");
      costTd.appendChild(costInput);
    }
  });
  activateEditMode(indexRow, id);
};

const createArticleSelect = () => {
  const select = document.createElement("select");
  select.classList.add("edit__input");
  state.articlesArray.forEach((element, index) => {
    const option = document.createElement("option");
    option.value = element.name;
    option.innerText = element.name;
    option.setAttribute("id", element.id);
    if (state.editeEntryData.kind_service === element.name)
      option.selected = true;

    select.appendChild(option);
  });
  select.onchange = function () {
    state.editeEntryData.kind_service_id =
      select[select.selectedIndex].getAttribute("id");
    state.editeEntryData.kind_service = select[select.selectedIndex].value;
  };
  return select;
};

const editFieldOnChange = (target, type, id) => {
  state.editedEntryStatus = true;
  switch (type) {
    case "contract_name":
      state.editeEntryData.contract_name = target.value;
      break;
    case "contract_term":
      state.editeEntryData.contract_term = target.value;
      break;
    case "name_service":
      state.editeEntryData.name_service = target.value;
      break;
    case "type_equipment":
      state.editeEntryData.type_equipment = target.value;
      break;
    case "amount":
      state.editeEntryData.amount = target.value;
      break;
    case "price":
      state.editeEntryData.price_of_service_no_pdv = target.value.replace(
        ",",
        "."
      );
      break;
    case "cost":
      state.editeEntryData.cost_of_materials_no_pdv = target.value.replace(
        ",",
        "."
      );
      break;
    default:
      break;
  }
};

const saveEditedEntryOnClick = () => {
  state.scrollAfterEdit = 1;
  saveEditedEntryRequest();
  contractEntriesArrayRequest(state.counterpartySelectId, state.treatySelectId);
  addButton = document.getElementById("treaty-service-add");
  counterpartySelect = document.getElementById("treaty-counterparty-select");
  contractSelect = document.getElementById("treaty-number-contract-select");
  blokEditButton = document.getElementById("treaty-edit-mode");
  addButton.disabled = false;
  counterpartySelect.disabled = false;
  contractSelect.disabled = false;
  blokEditButton.disabled = false;
};

const deactivateEditModeOnClick = () => {
  state.scrollAfterEdit = 1;
  contractEntriesArrayRequest(state.counterpartySelectId, state.treatySelectId);
  renderTableRequest(state.counterpartySelectId, state.treatySelectId);
  addButton = document.getElementById("treaty-service-add");
  counterpartySelect = document.getElementById("treaty-counterparty-select");
  contractSelect = document.getElementById("treaty-number-contract-select");
  blokEditButton = document.getElementById("treaty-edit-mode");
  addButton.disabled = false;
  counterpartySelect.disabled = false;
  contractSelect.disabled = false;
  blokEditButton.disabled = false;
};

const blokingEditModeOnClick = () => {
  blockingEditModeRequest();
};

const clearFiltersReloadOnClick = () => {
  window.localStorage.removeItem("treaty-directory-state");
  document.location.reload();
};

const clearAllFiltersReloadOnClick = () => {
  window.localStorage.clear();
  document.location.reload();
};

// DOM

const selectRedefineStyle = () => {
  const selectResults = document.getElementsByClassName(
    "select2 select2-container select2-container--default"
  );
  for (let i = 0; i < selectResults.length; i++) {
    selectResults[i].style.width = "";
  }
};

const fillSelectCounterparties = (counterpartiesArray) => {
  const counterpartiesMain = document.getElementById(
    "treaty-counterparty-select"
  );
  while (counterpartiesMain.lastChild) {
    if (counterpartiesMain.lastChild.innerText === "Обрати") break;
    counterpartiesMain.removeChild(counterpartiesMain.lastChild);
  }
  const counterpartiesModal = document.getElementById(
    "modal-treaty-counterparty-select"
  );
  while (counterpartiesModal.lastChild) {
    if (counterpartiesModal.lastChild.innerText === "Обрати контрагента") break;
    counterpartiesModal.removeChild(counterpartiesModal.lastChild);
  }
  for (let i = 0; i < counterpartiesArray.length; i++) {
    const option = document.createElement("option");
    const optionModal = document.createElement("option");
    option.value = counterpartiesArray[i].name;
    optionModal.value = counterpartiesArray[i].name;
    option.innerText = counterpartiesArray[i].name;
    optionModal.innerText = counterpartiesArray[i].name;
    option.id = counterpartiesArray[i].id;
    optionModal.id = counterpartiesArray[i].id + "-modal";
    option.setAttribute("data-id", counterpartiesArray[i].id);
    optionModal.setAttribute("data-id", counterpartiesArray[i].id);
    counterpartiesMain.appendChild(option);
    counterpartiesModal.appendChild(optionModal);
  }
  let storage = window.localStorage.getItem("treaty-directory-state");
  if (storage) {
    let selectCounterparty = document.getElementById(
      "treaty-counterparty-select"
    );
    for (let i = 0; i < selectCounterparty.options.length; i++) {
      if (
        selectCounterparty.options[i].getAttribute("data-id") ==
        state.counterpartySelectId
      )
        selectCounterparty.value = selectCounterparty.options[i].value;
    }
    checkStorage = false;
    selectCounterpartiesOnChange("treaty-number-contract-select");
    let selectContract = document.getElementById(
      "treaty-number-contract-select"
    );
    for (let i = 0; i < selectContract.options.length; i++) {
      if (
        selectContract.options[i].getAttribute("data-id") ==
        state.treatySelectId
      ) {
        selectContract.value = selectContract.options[i].value;
      }
    }
    renderTableRequest(state.counterpartySelectId, state.treatySelectId);
  }
};

const rerenderModalCounterpatySelect = (counterpartyId) => {
  const fields = document.getElementsByClassName("modal-input");
  const counterpartyField = fields[0];
  const span = document.createElement("span");
  span.classList.add("modal-tooltip");
  span.innerText = "Контрагент";
  let selectedOption = "<option>Обрати контрагента</option>";
  while (counterpartyField.firstChild)
    counterpartyField.removeChild(counterpartyField.firstChild);
  fields[0].appendChild(span);
  state.counterpartiesArray.forEach((element) => {
    if (element.id === counterpartyId) {
      selectedOption +=
        "<option data-id='" +
        element.id +
        "' selected>" +
        element.name +
        "</option>";
    } else
      selectedOption +=
        "<option data-id='" + element.id + "' >" + element.name + "</option>";
  });
  $(
    "<select id='modal-treaty-counterparty-select' >" +
      selectedOption +
      "</select>"
  )
    .appendTo(fields[0])
    .select2();
  const select = document.getElementById("modal-treaty-counterparty-select");
  select.onchange = modalSelectCounterpartiesOnChange.bind(
    this,
    select,
    "modal-treaty-number-contract-select"
  );
  modalSelectCounterpartiesOnChange(
    select,
    "modal-treaty-number-contract-select"
  );
};

const rerenderModalTreatySelect = (treatyId) => {
  const fields = document.getElementsByClassName("modal-input");
  const contractField = fields[1];
  const span = document.createElement("span");
  span.classList.add("modal-tooltip");
  span.innerText = "Номер договору";
  let selectedOption = "<option>Обрати номер договору</option>";
  while (contractField.firstChild)
    contractField.removeChild(contractField.firstChild);
  fields[1].appendChild(span);
  state.treatyArray.forEach((element) => {
    if (element.counterparty_id === state.counterpartySelectId) {
      if (element.id === treatyId)
        selectedOption +=
          "<option data-id='" +
          element.id +
          "' selected>" +
          element.number_contract +
          "</option>";
      else
        selectedOption +=
          "<option data-id='" +
          element.id +
          "' >" +
          element.number_contract +
          "</option>";
    }
  });
  $(
    "<select id='modal-treaty-number-contract-select'>" +
      selectedOption +
      "</select>"
  )
    .appendTo(fields[1])
    .select2();
};

const activateEditMode = (index, id) => {
  editButtons = document.getElementsByClassName("td-edit__image");
  addButton = document.getElementById("treaty-service-add");
  counterpartySelect = document.getElementById("treaty-counterparty-select");
  contractSelect = document.getElementById("treaty-number-contract-select");
  blokEditButton = document.getElementById("treaty-edit-mode");
  addButton.disabled = true;
  counterpartySelect.disabled = true;
  contractSelect.disabled = true;
  blokEditButton.disabled = true;
  for (let i = 0; i < editButtons.length; i++) {
    if (i === index) {
      createEditModeButtons(index, id);
      i++;
    } else {
      editButtons[i].disabled = true;
    }
  }
};

const createEditModeButtons = (index, id) => {
  const editTd = document.getElementsByClassName("edit__td")[index];
  const saveButton = document.createElement("input");
  const cancelButton = document.createElement("input");
  editTd.innerText = "";
  saveButton.type = "image";
  saveButton.classList.add("td-edit__image");
  saveButton.src = "../../templates/images/save.png";
  saveButton.onclick = saveEditedEntryOnClick.bind(this);
  cancelButton.type = "image";
  cancelButton.classList.add("td-edit__image");
  cancelButton.src = "../../templates/images/cancel.png";
  cancelButton.onclick = deactivateEditModeOnClick.bind(this);
  editTd.appendChild(saveButton);
  editTd.appendChild(cancelButton);
};

const displayEditLock = (status) => {
  const editLockBar = document.getElementById("edit-mode-id");
  const underTableBar = document.getElementsByClassName("under-table-bar")[0];
  editLockBar.hidden = !status;
  underTableBar.style.backgroundColor = status ? "#e5f7e5" : "";
};
