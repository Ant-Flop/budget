let state = {
  serviceData: [],
  serviceIdSelected: null,
  budgetArticleData: [],
  budgetArticleIdSelected: null,
  budgetArticleNameSelected: null,
  counterpartyData: [],
  counterpartyIdSelected: null,
  contractData: [],
  contractIdSelected: null,
  year: new Date().getFullYear(),
};

document.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("writing-off-costs-report")) {
    state = JSON.parse(localStorage.getItem("writing-off-costs-report"));
    if (localStorage.getItem("budgetPlanfilterInfo")) {
      const filterInfo = JSON.parse(
        localStorage.getItem("budgetPlanfilterInfo")
      );
      state.year = filterInfo.year;
    }
    getServiceDataRequest(() => {
      fillServiceSelect();
      fillBudgetArticleSelect();
      fillCounterpartySelect();
      fillContractSelect();
      renderTableRequest();
    });
  } else {
    if (localStorage.getItem("budgetPlanfilterInfo")) {
      const filterInfo = JSON.parse(
        localStorage.getItem("budgetPlanfilterInfo")
      );
      state.year = filterInfo.year;
    }
    getServiceDataRequest(() => {
      fillServiceSelect();
      renderTableRequest();
    });
  }
});

const renderTableRequest = () => {
  // let articles = state.budgetArticleData.filter(
  //   (element) => element.id === state.budgetArticleIdSelected
  // );
  // let article = articles[0].name ? articles[0].name : "";
  console.log(state.budgetArticleNameSelected);
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "writing-off-costs-report.php");
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      plannedIndicatorsId: state.budgetArticleIdSelected,
      articleName: state.budgetArticleNameSelected,
      counterpartyId: state.counterpartyIdSelected,
      contractId: state.contractIdSelected,
    })
  );
  xhr.onload = function () {
    document.querySelector("#main-table").innerHTML = xhr.response;
    setScrollTable();
  };
};

const getServiceDataRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "writing-off-costs-report.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getServiceDataRequest",
    })
  );
  xhr.onload = () => {
    console.log(JSON.parse(xhr.response));
    state.serviceData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const fillServiceSelect = () => {
  rerenderServiceSelect();
  const select = document.getElementById(
    "writing-off-costs-report-service__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (state.serviceData.length === 0) return;
  state.serviceData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    if (element.id == state.serviceIdSelected) optionSelect.selected = true;
    select.appendChild(optionSelect);
  });
};

const rerenderServiceSelect = () => {
  const field = document.getElementsByClassName(
    "writing-off-costs-report-service-wrapper__select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='writing-off-costs-report-service__select' class='service-select' title='Служба'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "writing-off-costs-report-service__select"
  );
  select.onchange = selectServiceOnChange.bind(this);
};

const selectServiceOnChange = () => {
  const select = document.getElementById(
    "writing-off-costs-report-service__select"
  );
  state.serviceIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  state.budgetArticleData = [];
  state.budgetArticleIdSelected = null;
  state.budgetArticleNameSelected = null;
  state.contractData = [];
  state.contractIdSelected = null;
  state.counterpartyData = [];
  state.counterpartyIdSelected = null;
  localStorage.setItem("writing-off-costs-report", JSON.stringify(state));
  renderTableRequest();
  getBudgetArticleDataRequest(() => {
    fillBudgetArticleSelect();
  });
};

const getBudgetArticleDataRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "writing-off-costs-report.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getBudgetArticleDataRequest",
      serviceId: state.serviceIdSelected,
      year: state.year,
    })
  );
  xhr.onload = () => {
    state.budgetArticleData = JSON.parse(xhr.response);
    selectBudgetArticleOnChange();
    callbackFunction();
  };
};

const fillBudgetArticleSelect = () => {
  rerenderBudgetArticleSelect();
  const select = document.getElementById(
    "writing-off-costs-report-budget-article__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (state.budgetArticleData.length === 0) return;
  state.budgetArticleData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    if (element.id == state.budgetArticleIdSelected)
      optionSelect.selected = true;
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleSelect = () => {
  const field = document.getElementsByClassName(
    "writing-off-costs-report-budget-article-wrapper__select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='writing-off-costs-report-budget-article__select' class='budget-article-select' title='Стаття'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "writing-off-costs-report-budget-article__select"
  );
  select.onchange = selectBudgetArticleOnChange.bind(this);
};

const selectBudgetArticleOnChange = () => {
  const select = document.getElementById(
    "writing-off-costs-report-budget-article__select"
  );
  state.budgetArticleIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  state.budgetArticleNameSelected =
    select[select.selectedIndex].value !== (null || "")
      ? select[select.selectedIndex].value
      : null;
  state.counterpartyData = [];
  state.counterpartyIdSelected = null;
  state.contractData = [];
  state.contractIdSelected = null;
  localStorage.setItem("writing-off-costs-report", JSON.stringify(state));
  renderTableRequest();
  getCounterpartyDataRequest(() => {
    fillCounterpartySelect();
    getContractDataRequest(() => {
      fillContractSelect();
    });
  });
};

const getCounterpartyDataRequest = (callbackFunction) => {
  state.budgetArticleData.forEach((element) => {
    if (state.budgetArticleIdSelected === element.id) {
      state.counterpartyData.push({
        id: element.counterparty_id,
        name: element.counterparty_name,
      });
    }
  });
  callbackFunction();
};

const fillCounterpartySelect = () => {
  rerenderCounterpartySelect();
  const select = document.getElementById(
    "writing-off-costs-report-counterparty__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (state.counterpartyData.length === 0) return;
  state.counterpartyData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    if (element.id == state.counterpartyIdSelected)
      optionSelect.selected = true;
    select.appendChild(optionSelect);
  });
};

const rerenderCounterpartySelect = () => {
  const field = document.getElementsByClassName(
    "writing-off-costs-report-counterparty-wrapper__select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='writing-off-costs-report-counterparty__select' class='counterparty-select' title='Контрагент'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "writing-off-costs-report-counterparty__select"
  );
  select.onchange = selectCounterpartyOnChange.bind(this);
};

const selectCounterpartyOnChange = () => {
  const select = document.getElementById(
    "writing-off-costs-report-counterparty__select"
  );
  state.counterpartyIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  state.contractData = [];
  state.contractIdSelected = null;
  localStorage.setItem("writing-off-costs-report", JSON.stringify(state));
  getContractDataRequest(() => {
    fillContractSelect();
  });
  renderTableRequest();
};

const getContractDataRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "writing-off-costs-report.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getContractDataRequest",
      plannedIndicatorsId: state.budgetArticleIdSelected,
      article_name: state.budgetArticleNameSelected,
      counterpartyId: state.counterpartyIdSelected,
    })
  );
  xhr.onload = () => {
    console.log(JSON.parse(xhr.response));
    state.contractData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const fillContractSelect = () => {
  rerenderContractSelect();
  const select = document.getElementById(
    "writing-off-costs-report-contract__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if (state.contractData.length === 0) return;
  state.contractData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.number;
    optionSelect.innerText = element.number;
    optionSelect.setAttribute("data-id", element.id);
    if (element.id == state.contractIdSelected) optionSelect.selected = true;
    select.appendChild(optionSelect);
  });
};

const rerenderContractSelect = () => {
  const field = document.getElementsByClassName(
    "writing-off-costs-report-contract-wrapper__select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='writing-off-costs-report-contract__select' class='budget-contract-select' title='Договір'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "writing-off-costs-report-contract__select"
  );
  select.onchange = selectContractOnChange.bind(this);
};

const selectContractOnChange = () => {
  const select = document.getElementById(
    "writing-off-costs-report-contract__select"
  );
  state.contractIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  localStorage.setItem("writing-off-costs-report", JSON.stringify(state));
  renderTableRequest();
};

const setScrollTable = () => {
  const table = document.getElementById("main-table");
  table.scroll(0, 1);
  table.scroll(0, table.scrollHeight);
};

const clearFiltersReloadOnClick = () => {
  window.localStorage.removeItem("writing-off-costs-report");
  document.location.reload();
};

const clearAllFiltersReloadOnClick = () => {
  window.localStorage.clear();
  document.location.reload();
};
