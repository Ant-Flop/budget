let state = {
  counterpartiesArray: [],
  treatyArray: [],
  counterpartySelectId: 0,
  treatySelectId: 0,
  numberAct: [],

  selectNumberActId: 0,
  allData: [],
  newActCardData: [],
  editMode: false,
  finalData: {
    sumPriceNoPDV: 0,
    sumCostNoPDV: 0,
    sumPricePDV: 0,
    sumCostPDV: 0,
    sumPriceWithPDV: 0,
    sumCostWithPDV: 0,
    sumAmount: 0,
    sumMain: 0,
  },
  tooltipMode: false,
};

const localState = {
  dataRows: [],
  counterRow: 0,
  limitDataRows: [],
};

let checkStorage = true;

document.addEventListener("DOMContentLoaded", () => {
  let storage = window.localStorage.getItem("act-card-state");
  if (storage) {
    state = JSON.parse(window.localStorage.getItem("act-card-state"));
  }
  selectCounterpartiesValueRequest();
  selectRedefineStyle();
});

document.addEventListener("mouseover", (event) => {
  if (event.target.classList.contains("select2-results__option--highlighted"))
    if (event.target.id.includes("select-name-service")) {
      const searchInput = document.getElementsByClassName(
        "select2-search__field"
      )[0];
      searchInput.value = event.target.title;
    }
});

//state

const finalStateCount = () => {
  if (state.newActCardData.length === 0) return;
  state.finalData.sumPriceNoPDV = 0;
  state.finalData.sumCostNoPDV = 0;
  state.finalData.sumPricePDV = 0;
  state.finalData.sumCostPDV = 0;
  state.finalData.sumPriceWithPDV = 0;
  state.finalData.sumCostWithPDV = 0;
  state.finalData.sumAmount = 0;
  state.finalData.sumMain = 0;
  let sign = state.treatyArray.filter(
    (item) => item.id === state.treatySelectId
  )[0].sign_of_vat;
  state.newActCardData.forEach((element) => {
    state.finalData.sumPriceNoPDV += element.price_of_service_no_pdv;
    state.finalData.sumCostNoPDV += element.cost_of_materials_no_pdv;
    state.finalData.sumAmount += element.amount;
    let count_exceptions = element.articles_exceptions.filter(
      (item) => parseInt(item.article_id) === parseInt(element.article_id)
    );
    //let cost = count_exceptions.length === 0 ? element.cost_of_materials_no_pdv : 0;
    let cost = element.cost_of_materials_no_pdv;
    state.finalData.sumMain +=
      element.amount * (element.price_of_service_no_pdv + cost);
  });
  state.finalData.sumPricePDV =
    sign === "Без ПДВ" ? 0 : state.finalData.sumPriceNoPDV * 0.2;
  state.finalData.sumCostPDV =
    sign === "Без ПДВ" ? 0 : state.finalData.sumCostNoPDV * 0.2;
  state.finalData.sumPriceWithPDV =
    state.finalData.sumPriceNoPDV + state.finalData.sumPricePDV;
  state.finalData.sumCostWithPDV =
    state.finalData.sumCostNoPDV + state.finalData.sumCostPDV;
  finalDataCount();
};

const finalEditStateCount = () => {
  if (state.numberAct.length === 0) return;
  state.finalData.sumPriceNoPDV = 0;
  state.finalData.sumCostNoPDV = 0;
  state.finalData.sumPricePDV = 0;
  state.finalData.sumCostPDV = 0;
  state.finalData.sumPriceWithPDV = 0;
  state.finalData.sumCostWithPDV = 0;
  state.finalData.sumAmount = 0;
  state.finalData.sumMain = 0;
  let sign = state.treatyArray.filter(
    (item) => item.id === state.treatySelectId
  )[0].sign_of_vat;

  state.numberAct.forEach((element) => {
    if (element.id == state.selectNumberActId) {
      element.rtac.forEach((item) => {
        state.finalData.sumPriceNoPDV += parseFloat(
          item.price_of_service_no_pdv
        );
        state.finalData.sumCostNoPDV += parseFloat(
          item.cost_of_materials_no_pdv
        );
        state.finalData.sumAmount += parseFloat(item.amount);
        let cost = parseFloat(item.cost_of_materials_no_pdv);
        state.finalData.sumMain +=
          parseFloat(item.amount) *
          (parseFloat(item.price_of_service_no_pdv) +
            parseFloat(item.cost_of_materials_no_pdv));
      });
    }
  });

  state.finalData.sumPricePDV =
    sign === "Без ПДВ" ? 0 : state.finalData.sumPriceNoPDV * 0.2;
  state.finalData.sumCostPDV =
    sign === "Без ПДВ" ? 0 : state.finalData.sumCostNoPDV * 0.2;
  state.finalData.sumPriceWithPDV =
    state.finalData.sumPriceNoPDV + state.finalData.sumPricePDV;
  state.finalData.sumCostWithPDV =
    state.finalData.sumCostNoPDV + state.finalData.sumCostPDV;
  finalDataCount();
};

// requests

const getAllStateRequest = (counterpartyId, treatyId) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "act-card.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "getAllStateRequest",
      counterpartyId: counterpartyId,
      treatyId: treatyId,
    })
  );
  xhr.onload = function () {
    state.allData = JSON.parse(xhr.response);
    state.allData.forEach((element) => {
      localState.limitDataRows.push({
        id: element.id,
        amount: element.amount,
      });
    });
    createTable();
  };
};

const selectCounterpartiesValueRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "act-card.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "selectValueRequest",
    })
  );
  xhr.onload = function () {
    state.counterpartiesArray = JSON.parse(xhr.response).counterparties;
    state.treatyArray = JSON.parse(xhr.response).treaty;

    fillSelectCounterparties(state.counterpartiesArray);
  };
};

const selectNumberActEditRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "act-card.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      treatyId: state.treatySelectId,
      typeRequest: "selectNumberActEditRequest",
    })
  );
  xhr.onload = () => {
    state.numberAct = JSON.parse(xhr.response) || [];

    fillSelectNumberActEdit();
  };
};

const actCardSaveRequest = (
  actNumberValue,
  actDateValue,
  actAccountValue,
  actNumberAccountValue
) => {
  
  let actInfo = state.numberAct.filter((element) => {
    if (element.act_number == actNumberValue) return element;
  })[0];
  const request = state.editMode
    ? {
        typeRequest: "actCardEditSaveRequest",
        actCardData: {
          id: actInfo.id,
          act_number: actNumberValue,
          act_account: actAccountValue,
          act_number_account: actNumberAccountValue,
          act_date: actDateValue,
          rtac: actInfo.rtac,
        },
      }
    : {
        typeRequest: "actCardSaveRequest",
        actNumber: actNumberValue,
        actAccountValue: actAccountValue,
        actNumberAccountValue: actNumberAccountValue,
        actDate: actDateValue,
        counterpartyId: state.counterpartySelectId,
        contractId: state.treatySelectId,
        actCardData: state.newActCardData,
      };

  const xhr = new XMLHttpRequest();
  const requestURL = "act-card.php";
  xhr.open("POST", requestURL);
  xhr.send(JSON.stringify(request));
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);

    const labelStatus = document.getElementById("label-act-card-save-button");
    labelStatus.innerText = response.text;
    labelStatus.hidden = false;
    labelStatus.style.backgroundColor = response.status ? "#ebfbeb" : "#fbedeb";
    setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
    if (state.editMode) {
      selectNumberActEditRequest();

      //fillSelectCounterparties(state.counterpartiesArray);
      const saveButton = document.getElementById("act-card-save-button");
      saveButton.disabled = false;
      const spend = document.getElementById("act-card-spend-button");
      spend.disabled = false;
    } else {
      const actNumber = document.getElementById("act-card-number-of-act-input");
      const actAccount = document.getElementById(
        "act-card-account-of-act-input"
      );
      const actNumberAccount = document.getElementById(
        "act-card-number-account-of-act-input"
      );
      actNumber.value = "";
      actAccount.value = "";
      actNumberAccount.value = "";
      if (response.status) {
        state.newActCardData = [];
        finalStateCount();
        createTable();
      }
    }
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
  };
};

// events`

const selectCounterpartiesOnChange = (target, selectId) => {
  const numberActSelect = document.getElementById(
    "act-card-edit-number-of-act-select"
  );
  if (checkStorage) window.localStorage.removeItem("act-card-state");
  const counterpartyId =
    target.options[target.options.selectedIndex].getAttribute("data-id");
  const treaty = document.getElementById(selectId);
  state.counterpartySelectId = counterpartyId;
  if (checkStorage) state.treatySelectId = 0;
  checkStorage = true;
  while (treaty.firstChild) treaty.removeChild(treaty.firstChild);
  for (let i = 0; i < state.treatyArray.length; i++) {
    if (i === 0) {
      const optionUnselect = document.createElement("option");
      optionUnselect.value = "Обрати";
      optionUnselect.innerText = "Обрати";
      treaty.appendChild(optionUnselect);
    }
    if (state.treatyArray[i].counterparty_id === counterpartyId) {
      const option = document.createElement("option");
      option.value = state.treatyArray[i].number_contract;
      option.innerText = state.treatyArray[i].number_contract;
      option.id = state.treatyArray[i].id;
      option.setAttribute("data-id", state.treatyArray[i].id);
      treaty.appendChild(option);
    }
  }
  if (state.treatyArray.length === 0) {
    const optionUnselect = document.createElement("option");
    optionUnselect.value = "Обрати";
    optionUnselect.innerText = "Обрати";
    treaty.appendChild(optionUnselect);
  }
  state.allData = [];
  state.newActCardData = [];
  state.numberAct = [];
  while (numberActSelect.firstChild)
    numberActSelect.removeChild(numberActSelect.firstChild);
  selectNumberActEditRequest();
  finalStateCount();
  emptyingTable();
  getAllStateRequest(state.counterpartySelectId, state.treatySelectId);
  window.localStorage.setItem("act-card-state", JSON.stringify(state));
};

const selectTreatyOnChange = (target) => {
  const counterparty = document.getElementById("act-card-counterparty-select");
  const counterpartyId =
    counterparty.options[counterparty.options.selectedIndex].getAttribute(
      "data-id"
    );
  const treatyId = target.options[target.selectedIndex].getAttribute("data-id");
  const numberActSelect = document.getElementById(
    "act-card-edit-number-of-act-select"
  );
  state.counterpartySelectId = counterpartyId;
  state.treatySelectId = treatyId;
  state.allData = [];
  state.newActCardData = [];
  state.numberAct = [];
  while (numberActSelect.firstChild)
    numberActSelect.removeChild(numberActSelect.firstChild);
  selectNumberActEditRequest();
  finalStateCount();
  emptyingTable();

  getAllStateRequest(state.counterpartySelectId, state.treatySelectId);
  window.localStorage.setItem("act-card-state", JSON.stringify(state));
};

const clearFiltersReloadOnClick = () => {
  window.localStorage.removeItem("act-card-state");
  document.location.reload();
};

const actCardSaveOnClick = (target) => {
  const actNumber = document.getElementById("act-card-number-of-act-input");
  const actDate = document.getElementById("act-card-date-of-act-input");
  const actAccount = document.getElementById("act-card-account-of-act-input");
  const actNumberAccount = document.getElementById(
    "act-card-number-account-of-act-input"
  );

  let actNumberValue = actNumber.value;
  let actDateValue = actDate.value;
  let actAccountValue = actAccount.value;
  let actNumberAccountValue = actNumberAccount.value;
  if (!activateSaveButton()) {
    target.disabled = true;
    actCardSaveRequest(
      actNumberValue,
      actDateValue,
      actAccountValue,
      actNumberAccountValue
    );
  }
};

const actCardConductOnClick = (target) => {
  let actId = state.numberAct[0].id;
  const xhr = new XMLHttpRequest();
  const requestURL = "act-card.php";
  xhr.open("POST", requestURL);

  xhr.send(
    JSON.stringify({
      typeRequest: "actCardConductRequest",
      actId: actId,
    })
  );
  xhr.onload = function () {
    const conductButton = document.getElementById("act-card-spend-button");
    conductButton.disabled = true;
    fillSelectCounterparties(state.counterpartiesArray);
  };
};

const inputPriceCostOnChange = (rowId) => {
  const inputCost = document.getElementById("input-cost-" + rowId + "-id");
  const inputPrice = document.getElementById("input-price-" + rowId + "-id");
  const inputAmount = document.getElementById("input-amount-" + rowId + "-id");
  let maxCost = parseFloat(inputCost.getAttribute("data-max"));
  let maxPrice = parseFloat(inputPrice.getAttribute("data-max"));
  let id = parseInt(inputCost.getAttribute("data-id"));
  let exception = 0;
  state.newActCardData.forEach((element) => {
    if (element.rowId === rowId) {
      let count_exceptions = element.articles_exceptions.filter(
        (item) => item.article_id === element.article_id
      );
      exception = count_exceptions.length;
    }
  });
  inputPrice.value = inputPrice.value.replace(",", ".");
  inputCost.value = inputCost.value.replace(",", ".");
  if (/[a-zа-яё]/i.test(inputPrice.value)) inputPrice.value = 0;
  if (/[a-zа-яё]/i.test(inputCost.value)) inputCost.value = 0;
  let price = parseFloat(
    inputPrice.value < 0 || inputPrice.value === ""
      ? 0
      : inputPrice.value > maxPrice
      ? maxPrice
      : inputPrice.value
  );
  if (inputPrice.value > maxPrice)
    alert("Ціна послуги не повинна перевищувати: " + maxPrice);
  if (inputCost.value > maxCost)
    //  && exception === 0
    alert("Вартість матеріалів не повинна перевищувати: " + maxCost);
  let cost = parseFloat(
    inputCost.value < 0 || inputCost.value === ""
      ? 0
      : inputCost.value > maxCost
      ? maxCost
      : inputCost.value
  ); //  && exception === 0
  let amount = parseFloat(inputAmount.value);
  inputCost.value = cost.toFixed(2);
  inputPrice.value = price.toFixed(2);
  state.newActCardData.forEach((element) => {
    if (element.rowId === rowId) {
      element.price_of_service_no_pdv = price;
      element.cost_of_materials_no_pdv = cost;
      element.amount = amount;
      element.sum_treaty_row = element.amount * (price + cost);
      let count_exceptions = element.articles_exceptions.filter(
        (item) => item.article_id === element.article_id
      );
      //cost = count_exceptions.length === 0 ? cost : 0;
      //cost = cost;
      const sumTreatyRow = document.getElementById("td-sum-" + rowId + "-id");
      sumTreatyRow.innerText = (element.amount * (price + cost)).toFixed(2);
    }
  });
  finalStateCount();
};

const inputAmountOnChange = (rowId) => {
  const inputAmount = document.getElementById("input-amount-" + rowId + "-id");
  if (
    parseInt(inputAmount.value) < 0 ||
    inputAmount.value === "" ||
    /[a-zа-яё]/i.test(inputAmount.value)
  )
    inputAmount.value = 0;
  let amount = parseFloat(inputAmount.getAttribute("data-max"));
  let id = parseFloat(inputAmount.getAttribute("data-id"));
  let sumInputAmount = 0;
  let limitAmount = localState.limitDataRows.filter(
    (element) => element.id == id
  )[0].amount;
  localState.dataRows.forEach((element) => {
    if (element.rowId === rowId) {
      element.amount = parseFloat(inputAmount.value);
    }
    if (element.id === id) {
      sumInputAmount += element.amount;
    }
  });
  if (limitAmount < sumInputAmount) {
    alert("Кількість не повинна перевищувати: " + amount);
    localState.dataRows.forEach((element) => {
      if (element.rowId === rowId) {
        element.amount = element.amount - (sumInputAmount - limitAmount);
        inputAmount.value = element.amount;
      }
    });
  }
  inputPriceCostOnChange(rowId);
};

const serviceRowValidate = (id) => {
  let maxSumAmount = 0;
  let limitSumAmount = 0;
  localState.dataRows.forEach((element) => {
    if (element.id === id) maxSumAmount += element.amount;
  });
  localState.limitDataRows.forEach((element) => {
    if (element.id === id) limitSumAmount = element.amount;
  });

  if (maxSumAmount >= limitSumAmount) {
    alert(
      "Неможливо створити запис. Кількість не повинна перевищувати: " +
        limitSumAmount
    );

    return true;
  }
};

const selectNameServiceOnChange = (select) => {
  let id = parseFloat(select[select.selectedIndex].getAttribute("data-id"));
  let rowId = parseFloat(select.getAttribute("data-id"));
  const object = state.allData.filter((item) => item.id === id);
  const calculateAmount = () => {
    let limitAmount = localState.limitDataRows.filter((item) => item.id === id);
    let rowAmount = localState.dataRows.filter((item) => item.id === id);
    let sum = 0;
    rowAmount.forEach((element) => {
      sum += element.amount;
    });
    let result =
      (limitAmount.length !== 0 ? limitAmount[0].amount : 0) -
      (rowAmount.length !== 0 ? sum : 0);
    return result === 0 ? object[0].amount : result;
  };

  if (object.length !== 0) {
    if (rowId === 0) {
      if (serviceRowValidate(id)) {
        deleteTableRow(rowId);
        createTableRow(0);
        return;
      }
      localState.counterRow++;
      let amount = calculateAmount();
      localState.dataRows.push({
        id: object[0].id,
        rowId: localState.counterRow,
        amount: amount,
      });
      fillTableRow(object[0], rowId, amount);
    } else {
      console.log(object[0]);
      if (serviceRowValidate(id)) {
        createSelect(rowId);
        return;
      }
      localState.dataRows = localState.dataRows.filter(
        (item) => item.rowId !== rowId
      );
      state.newActCardData = state.newActCardData.filter(
        (item) => item.rowId !== rowId
      );

      localState.counterRow++;
      let amount = calculateAmount();
      localState.dataRows.push({
        id: object[0].id,
        rowId: localState.counterRow,
        amount: amount,
      });
      fillTableRow(object[0], rowId, amount);
    }
  } else {
    deleteTableRow(rowId);
  }
  if (rowId === 0) createTableRow(0);

  //console.log(state.newActCardData);
};

const selectNameServiceOnClick = () => {};

// modify DOM

const deleteTableRow = (rowId) => {
  const tbody = document.getElementById("table-tbody-id");
  localState.dataRows = localState.dataRows.filter(
    (item) => item.rowId !== rowId
  );
  state.newActCardData = state.newActCardData.filter(
    (item) => item.rowId !== rowId
  );
  finalStateCount();
  tbody.childNodes.forEach((element) => {
    if (element.childNodes[0].id === "td-treaty-" + rowId + "-id")
      tbody.removeChild(element);
  });
};

const createSelect = (id) => {
  const field = document.getElementById("td-type-" + id + "-id");
  let selectedOption = "<option data-id='0' selected>Обрати</option>";
  if (id !== 0) {
    while (field.firstChild) field.removeChild(field.firstChild);
    let buffId = localState.dataRows.filter((item) => item.rowId === id)[0].id;
    state.allData.forEach((element) => {
      if (element.id === buffId)
        selectedOption +=
          "<option data-id='" +
          element.id +
          "' title='" +
          element.type_equipment +
          "' selected>" +
          element.type_equipment +
          "</option>";
      else
        selectedOption +=
          "<option data-id='" +
          element.id +
          "' title='" +
          element.type_equipment +
          "'>" +
          element.type_equipment +
          "</option>";
    });
  }
  $(
    "<select id='table-select-name-service-" +
      id +
      "-id' data-id='" +
      id +
      "' onchange='selectNameServiceOnChange(this)' onclick='selectNameServiceOnClick()'>" +
      selectedOption +
      "</select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "table-select-name-service-" + id + "-id"
  );
  if (id === 0)
    state.allData.forEach((element, index) => {
      const option = document.createElement("option");
      option.innerText = element.type_equipment;
      option.setAttribute("data-id", element.id);
      option.setAttribute("title", element.name_service);
      select.appendChild(option);
    });
};

const createTableRow = (id) => {
  const tbody = document.getElementById("table-tbody-id");
  const tr = document.createElement("tr");
  const tdId = document.createElement("td");
  const tdName = document.createElement("td");
  const tdType = document.createElement("td");
  const tdAmount = document.createElement("td");
  const inputAmount = document.createElement("input");
  const tdPrice = document.createElement("td");
  const inputPrice = document.createElement("input");
  const tdCost = document.createElement("td");
  const inputCost = document.createElement("input");
  const tdSum = document.createElement("td");
  tdId.id = "td-treaty-" + id + "-id";
  tdName.id = "td-name-service-" + id + "-id";
  tdName.classList.add("table-td-name-service");
  tdType.id = "td-type-" + id + "-id";
  tdType.classList.add("table-td-type-equipment");
  tdType.classList.add("td-nowrap");
  tdAmount.id = "td-amount-" + id + "-id";
  tdAmount.classList.add("table-td-colspan-yellow-background");
  inputAmount.classList.add("input-amount");
  inputAmount.id = "input-amount-" + id + "-id";
  tdPrice.id = "td-price-of-service-no-pdv-" + id + "-id";
  tdPrice.classList.add("table-td-colspan-blue-background");
  inputPrice.classList.add("input-price-of-service-no-pdv");
  inputPrice.id = "input-price-" + id + "-id";
  tdCost.id = "td-cost-of-materials-no-pdv-" + id + "-id";
  tdCost.classList.add("table-td-colspan-blue-background");
  inputCost.classList.add("input-cost-of-materials-no-pdv");
  inputCost.id = "input-cost-" + id + "-id";
  tdSum.id = "td-sum-" + id + "-id";
  tdSum.classList.add("td-sum-treaty-row");
  tr.appendChild(tdId);
  tr.appendChild(tdName);
  tr.appendChild(tdType);
  tdAmount.appendChild(inputAmount);
  tr.appendChild(tdAmount);
  tdPrice.appendChild(inputPrice);
  tr.appendChild(tdPrice);
  tdCost.appendChild(inputCost);
  tr.appendChild(tdCost);
  tr.appendChild(tdSum);
  tbody.appendChild(tr);
  createSelect(id);
};

const createTable = () => {
  emptyingTable();
  if (state.allData.length === 0) return;
  localState.dataRows.push({
    id: 0,
    rowId: 0,
    amount: null,
  });
  createTableRow(0);
};

const createEditTable = () => {
  emptyingTable();
};

const inputEditAmountOnChange = (rowId) => {
  const inputAmount = document.getElementById("input-amount-" + rowId + "-id");
  const sumRow = document.getElementById("td-sum-" + rowId + "-id");
  const tr = document.getElementById("tr-" + rowId);
  let actCardId = tr.getAttribute("act-card-id");
  let actCardTreatyId = tr.getAttribute("act-card-treaty-id");
  let differenceAmount = 0;
  state.numberAct.forEach((element) => {
    if (element.id == actCardId) {
      element.rtac.forEach((item) => {
        if (item.id == actCardTreatyId) {
          if (
            parseFloat(item.amount_limit) >= parseFloat(inputAmount.value) &&
            parseFloat(inputAmount.value) != 0
          ) {
            differenceAmount = item.amount - parseFloat(inputAmount.value);
            item.amount = parseFloat(inputAmount.value);
            sumRow.innerText = (
              parseFloat(item.amount) *
              (parseFloat(item.cost_of_materials_no_pdv) +
                parseFloat(item.price_of_service_no_pdv))
            ).toFixed(2);
          } else
            alert("Кількість не повинна перевищувати: " + item.amount_limit);
        }
      });
    }
  });

  if (differenceAmount !== 0) {
    state.numberAct.forEach((element) => {
      element.rtac.forEach((item) => {
        if (item.id != actCardTreatyId) {
          item.amount_limit += differenceAmount;
        }
      });
    });
  }
  finalEditStateCount();
  console.log(state.numberAct);
};

const inputEditPriceOnChange = (rowId) => {
  const inputPrice = document.getElementById("input-price-" + rowId + "-id");
  const sumRow = document.getElementById("td-sum-" + rowId + "-id");
  const tr = document.getElementById("tr-" + rowId);
  let actCardId = tr.getAttribute("act-card-id");
  let actCardTreatyId = tr.getAttribute("act-card-treaty-id");
  state.numberAct.forEach((element) => {
    if (element.id == actCardId) {
      element.rtac.forEach((item) => {
        if (item.id == actCardTreatyId) {
          if (
            item.price_of_service_no_pdv_limit >= parseFloat(inputPrice.value)
          ) {
            item.price_of_service_no_pdv = parseFloat(inputPrice.value);
            sumRow.innerText = (
              parseFloat(item.amount) *
              (parseFloat(item.cost_of_materials_no_pdv) +
                parseFloat(item.price_of_service_no_pdv))
            ).toFixed(2);
          } else
            alert(
              "Ціна послуги, грн. без ПДВ не повинна перевищувати: " +
                item.price_of_service_no_pdv
            );
        }
      });
    }
  });
  finalEditStateCount();
  // selectNumberActEditOnChange(
  //   document.getElementById("act-card-edit-number-of-act-select")
  // );
};

const inputEditCostOnChange = (rowId) => {
  const inputCost = document.getElementById("input-cost-" + rowId + "-id");
  const sumRow = document.getElementById("td-sum-" + rowId + "-id");
  const tr = document.getElementById("tr-" + rowId);
  let actCardId = tr.getAttribute("act-card-id");
  let actCardTreatyId = tr.getAttribute("act-card-treaty-id");
  state.numberAct.forEach((element) => {
    if (element.id == actCardId) {
      element.rtac.forEach((item) => {
        if (item.id == actCardTreatyId) {
          item.cost_of_materials_no_pdv = parseFloat(inputCost.value);
          sumRow.innerText = (
            parseFloat(item.amount) *
            (parseFloat(item.cost_of_materials_no_pdv) +
              parseFloat(item.price_of_service_no_pdv))
          ).toFixed(2);
        }
      });
    }
  });
  finalEditStateCount();
  // selectNumberActEditOnChange(
  //   document.getElementById("act-card-edit-number-of-act-select")
  // );
};

const createEditTableRow = (id, object) => {
  const tbody = document.getElementById("table-tbody-id");
  const tr = document.createElement("tr");
  tr.setAttribute("act-card-id", object.actCardId);
  tr.setAttribute("act-card-treaty-id", object.actCardTreatyId);
  tr.id = "tr-" + id;
  const tdId = document.createElement("td");
  const tdName = document.createElement("td");
  const tdType = document.createElement("td");
  const tdAmount = document.createElement("td");
  const inputAmount = document.createElement("input");
  const tdPrice = document.createElement("td");
  const inputPrice = document.createElement("input");
  const tdCost = document.createElement("td");
  const inputCost = document.createElement("input");
  const tdSum = document.createElement("td");
  tdId.id = "td-treaty-" + id + "-id";
  tdName.id = "td-name-service-" + id + "-id";
  tdName.classList.add("table-td-name-service");
  tdName.innerText = object.name_of_services;
  tdType.id = "td-type-" + id + "-id";
  tdType.classList.add("table-td-type-equipment");
  tdType.style.display = "table-cell";
  tdType.classList.add("td-nowrap");
  tdType.innerText = object.type_of_equipment;
  tdAmount.id = "td-amount-" + id + "-id";
  tdAmount.classList.add("table-td-colspan-yellow-background");
  inputAmount.classList.add("input-amount");
  inputAmount.id = "input-amount-" + id + "-id";
  //inputPrice.setAttribute("data-id", object.id);
  let amountTitle =
    "Максимальна кількість по id запису: " + object.amount_limit;
  inputAmount.setAttribute("title", amountTitle);
  inputAmount.setAttribute("data-max", object.amount_limit);
  inputAmount.onchange = inputEditAmountOnChange.bind(this, id);
  inputAmount.value = object.amount;
  tdPrice.id = "td-price-of-service-no-pdv-" + id + "-id";
  tdPrice.classList.add("table-td-colspan-blue-background");
  inputPrice.classList.add("input-price-of-service-no-pdv");
  inputPrice.id = "input-price-" + id + "-id";
  inputPrice.value = object.price_of_service_no_pdv.toFixed(2);
  inputPrice.setAttribute("data-id", object.id);
  inputPrice.setAttribute("data-max", object.price_of_service_no_pdv);
  inputPrice.setAttribute(
    "title",
    "Максимальна ціна послуги: " + object.price_of_service_no_pdv.toFixed(2)
  );
  inputPrice.onchange = inputEditPriceOnChange.bind(this, id);
  tdCost.id = "td-cost-of-materials-no-pdv-" + id + "-id";
  tdCost.classList.add("table-td-colspan-blue-background");
  inputCost.classList.add("input-cost-of-materials-no-pdv");
  inputCost.id = "input-cost-" + id + "-id";
  inputCost.value = object.cost_of_materials_no_pdv.toFixed(2);
  inputCost.onchange = inputEditCostOnChange.bind(this, id);
  tdSum.id = "td-sum-" + id + "-id";
  tdSum.classList.add("td-sum-treaty-row");
  tdSum.innerText = (
    object.amount *
    (object.price_of_service_no_pdv + object.cost_of_materials_no_pdv)
  ).toFixed(2);
  tr.appendChild(tdId);
  tr.appendChild(tdName);
  tr.appendChild(tdType);
  tdAmount.appendChild(inputAmount);
  tr.appendChild(tdAmount);
  tdPrice.appendChild(inputPrice);
  tr.appendChild(tdPrice);
  tdCost.appendChild(inputCost);
  tr.appendChild(tdCost);
  tr.appendChild(tdSum);
  tbody.appendChild(tr);
};

const emptyingTable = () => {
  const tbody = document.getElementById("table-tbody-id");
  const actNumber = document.getElementById("act-card-number-of-act-input");
  const actAccount = document.getElementById("act-card-account-of-act-input");
  const actNumberAccount = document.getElementById(
    "act-card-number-account-of-act-input"
  );
  const actDate = document.getElementById("act-card-date-of-act-input");
  actNumber.value = null;
  actAccount.value = null;
  actNumberAccount.value = null;
  actDate.value = null;
  state.editMode = false;
  while (tbody.hasChildNodes()) tbody.removeChild(tbody.firstChild);
};

const fillTableRow = (object, rowId, amount) => {
  const tdId = document.getElementById("td-treaty-" + rowId + "-id");
  const tdName = document.getElementById("td-name-service-" + rowId + "-id");
  const selectName = document.getElementById(
    "table-select-name-service-" + rowId + "-id"
  );
  const tdType = document.getElementById("td-type-" + rowId + "-id");
  const tdAmount = document.getElementById("td-amount-" + rowId + "-id");
  const inputAmount = document.getElementById("input-amount-" + rowId + "-id");
  const tdPrice = document.getElementById(
    "td-price-of-service-no-pdv-" + rowId + "-id"
  );
  const inputPrice = document.getElementById("input-price-" + rowId + "-id");
  const tdCost = document.getElementById(
    "td-cost-of-materials-no-pdv-" + rowId + "-id"
  );
  const inputCost = document.getElementById("input-cost-" + rowId + "-id");
  const tdSum = document.getElementById("td-sum-" + rowId + "-id");

  tdId.id = "td-treaty-" + localState.counterRow + "-id";
  tdName.id = "td-name-service-" + localState.counterRow + "-id";
  selectName.id = "table-select-name-service-" + localState.counterRow + "-id";
  selectName.setAttribute("data-id", localState.counterRow);
  tdType.id = "td-type-" + localState.counterRow + "-id";
  tdAmount.id = "td-amount-" + localState.counterRow + "-id";
  inputAmount.id = "input-amount-" + localState.counterRow + "-id";
  tdPrice.id = "td-price-of-service-no-pdv-" + localState.counterRow + "-id";
  inputPrice.id = "input-price-" + localState.counterRow + "-id";
  tdCost.id = "td-cost-of-materials-no-pdv-" + localState.counterRow + "-id";
  inputCost.id = "input-cost-" + localState.counterRow + "-id";
  tdSum.id = "td-sum-" + localState.counterRow + "-id";

  let count_exceptions = object.articles_exceptions.filter(
    (item) => item.article_id === object.article_id
  );
  let cost =
    count_exceptions.length === 0 ? object.cost_of_materials_no_pdv : 0;
  state.newActCardData.push({
    id: object.id,
    rowId: localState.counterRow,
    article_id: object.article_id,
    article_name: object.article_name,
    articles_exceptions: object.articles_exceptions,
    name_service: object.name_service,
    type_equipment: object.type_equipment,
    amount: amount,
    price_of_service_no_pdv: object.price_of_service_no_pdv,
    cost_of_materials_no_pdv: object.cost_of_materials_no_pdv,
    sum_treaty_row: amount * (object.price_of_service_no_pdv + cost),
  });

  inputAmount.setAttribute("data-id", object.id);
  let amountTitle = "Максимальна кількість по id запису: " + object.amount;
  inputAmount.setAttribute("title", amountTitle);
  inputAmount.setAttribute("data-max", object.amount);
  inputAmount.onchange = inputAmountOnChange.bind(this, localState.counterRow);
  inputPrice.setAttribute("data-id", object.id);
  inputPrice.setAttribute("data-max", object.price_of_service_no_pdv);
  inputPrice.setAttribute(
    "title",
    "Максимальна ціна послуги: " + object.price_of_service_no_pdv.toFixed(2)
  );
  inputPrice.onchange = inputPriceCostOnChange.bind(
    this,
    localState.counterRow
  );
  inputCost.setAttribute("data-id", object.id);
  inputCost.setAttribute("data-max", object.cost_of_materials_no_pdv);
  inputCost.onchange = inputPriceCostOnChange.bind(this, localState.counterRow);

  tdId.innerText = localState.counterRow;
  tdName.innerText = object.name_service;
  inputAmount.value = amount;
  inputPrice.value = object.price_of_service_no_pdv.toFixed(2);
  inputCost.value = object.cost_of_materials_no_pdv.toFixed(2);
  tdSum.innerText = ((object.price_of_service_no_pdv + cost) * amount).toFixed(
    2
  );
  inputPriceCostOnChange(localState.counterRow);
  finalStateCount();
  activateSaveButton();
};

const fillSelectCounterparties = (counterpartiesArray) => {
  const counterpartiesMain = document.getElementById(
    "act-card-counterparty-select"
  );
  for (let i = 0; i < counterpartiesArray.length; i++) {
    const option = document.createElement("option");
    option.value = counterpartiesArray[i].name;
    option.innerText = counterpartiesArray[i].name;
    option.id = counterpartiesArray[i].id;
    option.setAttribute("data-id", counterpartiesArray[i].id);
    counterpartiesMain.appendChild(option);
  }

  let storage = window.localStorage.getItem("act-card-state");
  if (storage) {
    let selectCounterparty = document.getElementById(
      "act-card-counterparty-select"
    );
    for (let i = 0; i < selectCounterparty.options.length; i++) {
      if (
        selectCounterparty.options[i].getAttribute("data-id") ==
        state.counterpartySelectId
      )
        selectCounterparty.value = selectCounterparty.options[i].value;
    }
    checkStorage = false;
    selectCounterpartiesOnChange(
      selectCounterparty,
      "act-card-number-contract-select"
    );
    let selectContract = document.getElementById(
      "act-card-number-contract-select"
    );
    for (let i = 0; i < selectContract.options.length; i++) {
      if (
        selectContract.options[i].getAttribute("data-id") ==
        state.treatySelectId
      ) {
        selectContract.value = selectContract.options[i].value;
      }
    }
  }
};

const fillSelectNumberActEdit = () => {
  const numberActSelect = document.getElementById(
    "act-card-edit-number-of-act-select"
  );
  while (numberActSelect.firstChild)
    numberActSelect.removeChild(numberActSelect.firstChild);
  const optionUnselect = document.createElement("option");
  optionUnselect.value = "Обрати";
  optionUnselect.innerText = "Обрати";
  numberActSelect.appendChild(optionUnselect);
  state.numberAct.forEach((element, index) => {
    const option = document.createElement("option");
    option.value = element.act_number;
    option.innerText = element.act_number;
    option.id = element.id;
    option.setAttribute("data-id", element.id);
    numberActSelect.appendChild(option);
  });
  if (state.editMode) {
    for (let i = 0; i < numberActSelect.options.length; i++) {
      if (
        numberActSelect.options[i].getAttribute("data-id") ==
        state.selectNumberActId
      ) {
        numberActSelect.value = numberActSelect.options[i].value;
      }
    }
    finalEditStateCount();
  }
};

const fillAboveTableBar = (object) => {
  const actNumber = document.getElementById("act-card-number-of-act-input");
  actNumber.value = object.actNumber;
  const actAccount = document.getElementById("act-card-account-of-act-input");
  actAccount.value = object.actAccount;
  const actNumberAccount = document.getElementById(
    "act-card-number-account-of-act-input"
  );
  actNumberAccount.value = object.actNumberAccount;
  const actDate = document.getElementById("act-card-date-of-act-input");
  actDate.value = object.actDate;
};

const selectNumberActEditOnChange = (target) => {
  const actNumberId =
    target.options[target.options.selectedIndex].getAttribute("data-id");
  emptyingTable();
  state.editMode = false;

  if (actNumberId !== null) {
    state.selectNumberActId = actNumberId;
    state.numberAct.forEach((element, index) => {
      if (element.id == actNumberId) {
        state.editMode = true;
        fillAboveTableBar({
          actAccount: element.act_account,
          actDate: element.act_date,
          actNumberAccount: element.act_number_account,
          actNumber: element.act_number,
        });
        element.rtac.forEach((item, indexItem) => {
          createEditTableRow(indexItem, {
            actCardId: element.id,
            actCardTreatyId: item.id,
            name_of_services: item.name_of_services,
            type_of_equipment: item.type_of_equipment,
            amount: parseFloat(item.amount),
            amount_limit: parseFloat(item.amount_limit),
            price_of_service_no_pdv: parseFloat(item.price_of_service_no_pdv),
            price_of_service_no_pdv_limit: parseFloat(
              item.price_of_service_no_pdv_limit
            ),
            cost_of_materials_no_pdv: parseFloat(item.cost_of_materials_no_pdv),
          });
        });
      }
    });
    selectNumberActEditRequest();
    const spend = document.getElementById("act-card-spend-button");
    spend.disabled = true;
  } else {
    fillSelectCounterparties(state.counterpartiesArray);
    const spend = document.getElementById("act-card-spend-button");
    spend.disabled = true;
    state.selectNumberActId = actNumberId;
  }

  activateSaveButton();
};

const activateSaveButton = () => {
  const counterparty = document.getElementById("act-card-counterparty-select");
  const treaty = document.getElementById("act-card-number-contract-select");
  const actNumber = document.getElementById("act-card-number-of-act-input");
  const actAccount = document.getElementById("act-card-account-of-act-input");
  const actNumberAccount = document.getElementById(
    "act-card-number-account-of-act-input"
  );
  const actDate = document.getElementById("act-card-date-of-act-input");
  const saveButton = document.getElementById("act-card-save-button");
  let dataLength = state.newActCardData.length || state.numberAct.length;

  if (counterparty.options[counterparty.selectedIndex].id !== "") {
    if (
      treaty.options.length != 0 &&
      treaty.options[treaty.selectedIndex].id !== "" &&
      actNumber.value !== "" &&
      actDate.value !== "" &&
      dataLength > 0
    ) {
      saveButton.disabled = false;
      return false;
    } else {
      saveButton.disabled = true;
      return true;
    }
  }
};

const selectRedefineStyle = () => {
  const selectResults = document.getElementsByClassName(
    "select2 select2-container select2-container--default"
  );
  for (let i = 0; i < selectResults.length; i++) {
    selectResults[i].style.width = "";
  }
};

const createRanks = (number) => {
  let numberArray = number.split(".");
  numberArray[0] = numberArray[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
  return numberArray.join(".");
};

const finalDataCount = () => {
  const sumAmount = document.getElementById("td-lower-amount-sum");
  const sumPriceNoVAT = document.getElementById("td-lower-price-no-vat");
  const sumPriceVAT = document.getElementById("td-lower-price-vat");
  const sumPriceWithVAT = document.getElementById("td-lower-price-with-vat");
  const sumCostNoVAT = document.getElementById("td-lower-cost-no-vat");
  const sumCostVAT = document.getElementById("td-lower-cost-vat");
  const sumCostWithVAT = document.getElementById("td-lower-cost-with-vat");
  const sumMainNoVal = document.getElementById("td-lower-sum-main-no-val");
  const sumVal = document.getElementById("td-lower-sum-val");
  const sumMainWithVal = document.getElementById("td-lower-sum-main-with-val");
  let sign = state.treatyArray.filter(
    (item) => item.id === state.treatySelectId
  )[0].sign_of_vat;
  sumAmount.innerText = state.finalData.sumAmount;
  sumPriceNoVAT.innerText = createRanks(
    state.finalData.sumPriceNoPDV.toFixed(2)
  );
  sumPriceVAT.innerText = createRanks(state.finalData.sumPricePDV.toFixed(2));
  sumPriceWithVAT.innerText = createRanks(
    state.finalData.sumPriceWithPDV.toFixed(2)
  );
  sumCostNoVAT.innerText = createRanks(state.finalData.sumCostNoPDV.toFixed(2));
  sumCostVAT.innerText = createRanks(state.finalData.sumCostPDV.toFixed(2));
  sumCostWithVAT.innerText = createRanks(
    state.finalData.sumCostWithPDV.toFixed(2)
  );
  sumMainNoVal.innerText = createRanks(state.finalData.sumMain.toFixed(2));
  sumVal.innerText = createRanks(
    (sign === "Без ПДВ" ? 0 : state.finalData.sumMain * 0.2).toFixed(2)
  );
  sumMainWithVal.innerText = createRanks(
    (sign === "Без ПДВ"
      ? state.finalData.sumMain
      : state.finalData.sumMain * 1.2
    ).toFixed(2)
  );
};
