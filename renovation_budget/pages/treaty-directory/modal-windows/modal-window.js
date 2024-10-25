const stateModal = {
  counterpartyId: null,
  numberContractId: null,
  kindService: null,
  nameService: null,
  typeEquipment: null,
  amount: null,
  priceService: null,
  costMaterials: null,
};

// Request

const modalArticleRequest = (counterpartyId) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "modalArticleRequest",
      counterpartyId: counterpartyId,
    })
  );
  xhr.onload = function () {
    fillModalArticleSelect(JSON.parse(xhr.response));
  };
};

const modalSaveRequest = (
  serviceData,
  counterpartyId,
  counterpartyName,
  numberContractId,
  numberContract
) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "treaty-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(serviceData);
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
    rerenderCounterpartiesBar(counterpartyId);
    rerenderCountractBar(numberContractId);
    //contractEntriesArrayRequest(state.counterpartySelectId, state.treatySelectId);
    setLocalStorage(counterpartyId, numberContractId);
    renderTableRequest(counterpartyId, numberContractId);
    contractEntriesArrayRequest(counterpartyId, numberContractId);
    modalCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus = document.getElementsByClassName(
      "upper-treaty-directory-save-panel"
    )[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};

// Events

const addServiceOnClick = () => {
  const modalWindow = document.getElementById("modal-window");
  checkOnEditModeTreaty();
  modalWindow.style.display = "block";
};

const modalSelectCounterpartiesOnChange = (target, selectId) => {
  const counterpartyId =
    target.options[target.options.selectedIndex].getAttribute("data-id");
  const treaty = document.getElementById(selectId);
  const article = document.getElementById("modal-kind-service-select");
  while (treaty.firstChild) treaty.removeChild(treaty.firstChild);
  while (article.firstChild) article.removeChild(article.firstChild);
  const optionArticleUnselect = document.createElement("option");
  optionArticleUnselect.value = "Обрати статтю бюджету";
  optionArticleUnselect.innerText = "Обрати статтю бюджету";
  article.appendChild(optionArticleUnselect);
  if (counterpartyId !== null) {
    modalArticleRequest(counterpartyId);
  }
  for (let i = 0; i < state.treatyArray.length; i++) {
    if (i === 0) {
      const optionUnselect = document.createElement("option");
      if (selectId === "modal-treaty-number-contract-select") {
        optionUnselect.value = "Обрати номер договору";
        optionUnselect.innerText = "Обрати номер договору";
      } else {
        optionUnselect.value = "Обрати";
        optionUnselect.innerText = "Обрати";
      }
      treaty.appendChild(optionUnselect);
    }

    if (state.treatyArray[i].counterparty_id === counterpartyId) {
      const option = document.createElement("option");
      option.value = state.treatyArray[i].number_contract;
      option.innerText = state.treatyArray[i].number_contract;
      option.id = state.treatyArray[i].id;
      option.setAttribute("data-id", parseInt(state.treatyArray[i].id));
      treaty.appendChild(option);
    }
  }
  checkOnEditModeTreaty();
};

const modalSelectTreatyOnChange = () => {
  checkOnEditModeTreaty();
};

const modalSaveOnClick = () => {
  const counterpartySelect = document.getElementById(
    "modal-treaty-counterparty-select"
  );
  let counterpartyId =
    counterpartySelect.options[counterpartySelect.selectedIndex].getAttribute(
      "data-id"
    );
  let conterpartyName = counterpartySelect.value;
  const numberContractSelect = document.getElementById(
    "modal-treaty-number-contract-select"
  );
  let numberContractId =
    numberContractSelect.options[
      numberContractSelect.selectedIndex
    ].getAttribute("data-id");
  let numberContract = numberContractSelect.value;
  const articleSelect = document.getElementById("modal-kind-service-select");
  let articleName = articleSelect.value;
  let articleId =
    articleSelect.options[articleSelect.selectedIndex].getAttribute("data-id");
  let articleYear =
    articleSelect.options[articleSelect.selectedIndex].getAttribute(
      "data-year"
    );
  let nameContract = null;
  let termContract = null;
  state.treatyArray.forEach((element) => {
    if (element.id === numberContractId) {
      nameContract = element.name_contract;
      termContract = element.term_contract;
    }
  });
  let nameService = document.getElementById("modal-name-service-add").value;
  let typeEquipment = document.getElementById("modal-type-equipment-add").value;
  let amount = document.getElementById("modal-amount-add").value;
  let priceService = document.getElementById("modal-price-service-add").value;
  let costMaterials = document.getElementById("modal-cost-materials-add").value;
  if (
    nameService === "" ||
    typeEquipment === "" ||
    amount === "" ||
    priceService === "" ||
    costMaterials === "" ||
    numberContractId === "" ||
    counterpartyId === "" ||
    articleId === null
  ) {
    alert("Не всі обов'язкові поля заповнені!");
    return;
  }
  let serviceData = JSON.stringify({
    typeRequest: "modalSaveRequest",
    counterpartyId: counterpartyId,
    counterpartyName: conterpartyName,
    numberContractId: numberContractId,
    numberContract: numberContract,
    nameContract: nameContract,
    termContract: termContract,
    kindServiceId: articleId,
    kindServiceName: articleName,
    kindServiceYear: articleYear,
    nameService: nameService,
    typeEquipment: typeEquipment,
    amount: amount,
    priceService: priceService,
    costMaterials: costMaterials,
  });
  modalSaveRequest(
    serviceData,
    counterpartyId,
    conterpartyName,
    numberContractId,
    numberContract
  );
};

const modalCloseOnClick = () => {
  const modalWindow = document.getElementById("modal-window");
  modalWindow.style.display = "none";
  //modalFormClear();
};

// DOM

const fillModalArticleSelect = (articles) => {
  if (articles.length === 0) return;
  const select = document.getElementById("modal-kind-service-select");
  articles.forEach((article) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = article.name;
    optionSelect.innerText = article.name;
    optionSelect.setAttribute("data-id", article.id);
    optionSelect.setAttribute("data-year", article.year);
    select.appendChild(optionSelect);
  });
};

const checkOnEditModeTreaty = () => {
  const contractSelect = document.getElementById(
    "modal-treaty-number-contract-select"
  );
  const saveButton = document.getElementById("modal-button-save");
  const modalTreatyId =
    contractSelect.options[contractSelect.selectedIndex].getAttribute(
      "data-id"
    );
  let status = true;
  state.treatyArray.forEach((element) => {
    if (parseInt(element.id) === parseInt(modalTreatyId)) {
      status = element.edit_mode;
    }
  });
  //saveButton.disabled = !status;
};

const setLocalStorage = (counterpartyId, numberContractId) => {
  let stateStorage = JSON.parse(
    window.localStorage.getItem("treaty-directory-state")
  );
  if (stateStorage === null) {
    state.counterpartySelectId = counterpartyId;
    state.treatySelectId = numberContractId;
    return;
  }
  stateStorage.counterpartySelectId = counterpartyId;
  stateStorage.treatySelectId = numberContractId;
  window.localStorage.setItem(
    "treaty-directory-state",
    JSON.stringify(stateStorage)
  );
};

const rerenderCounterpartiesBar = (counterpartyId) => {
  const fields = document.getElementsByClassName("above-table-bar-element");
  const counterpartyField = fields[1];
  let selectedOption = "<option>Обрати</option>";
  while (counterpartyField.firstChild)
    counterpartyField.removeChild(counterpartyField.firstChild);
  state.counterpartiesArray.forEach((element) => {
    if (element.id === counterpartyId)
      selectedOption +=
        "<option data-id='" +
        element.id +
        "' selected>" +
        element.name +
        "</option>";
    else
      selectedOption +=
        "<option data-id='" + element.id + "' >" + element.name + "</option>";
  });
  $("<select id='treaty-counterparty-select' >" + selectedOption + "</select>")
    .appendTo(fields[1])
    .select2();
  const select = document.getElementById("treaty-counterparty-select");
  select.onchange = selectCounterpartiesOnChange.bind(
    this,
    "treaty-number-contract-select"
  );
  //select.disabled = true;
};

const rerenderCountractBar = (numberContractId) => {
  const fields = document.getElementsByClassName("above-table-bar-element");
  const contractField = fields[2];
  let selectedOption = "<option>Обрати</option>";
  while (contractField.firstChild)
    contractField.removeChild(contractField.firstChild);
  state.treatyArray.forEach((element) => {
    if (element.id === numberContractId)
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
  });
  $(
    "<select id='treaty-number-contract-select' >" +
      selectedOption +
      "</select>"
  )
    .appendTo(fields[2])
    .select2();
  const select = document.getElementById("treaty-number-contract-select");
  select.onchange = selectTreatyOnChange.bind(this);
};

const setScrollTable = () => {
  const table = document.getElementById("treaty-directory-table");
  if (state.scrollAfterEdit !== 1) table.scroll(0, table.scrollHeight);
  state.scrollAfterEdit = null;
};
