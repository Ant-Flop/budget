let state = {
  articleSelect: null,
  infoActCard: [],
  infoNumberSelect: [],
  infoNumberId: [],
  planOfBudgetInput: {},
  selectedArticleSelect: {},
  selectedStartDateInput: null,
  selectedEndDateInput: null,
  selectedSubsectionSelect: {
    id: null,
    name: null,
  },
  sumFactOfWritingOffCost: 0,
  sumFactsOfTreaties: [],
  infoNumberSelect: [],
  subsectionSelect: [
    {
      id: 10,
      name: "Служба телекомунікацій",
      shortname: "СТ",
    },
    {
      id: 11,
      name: "Служба технічної підтримки",
      shortname: "СТП",
    },
  ],
};

let checkStorage = true;

document.addEventListener("DOMContentLoaded", () => {
  let storage = window.localStorage.getItem("fact-of-articles-state");
  if (storage) {
    checkStorage = false;
    state = JSON.parse(window.localStorage.getItem("fact-of-articles-state"));
  } else {
    state.selectedArticleSelect = {
      id: null,
      articleId: null,
      articleName: null,
      year: null,
    };
  }
  fillInputDate();
  fillSubsectionSelect();
  selectRedefineStyle();
});

const fillInputDate = () => {
  const startDateInput = document.getElementById(
    "start-date-fact-of-article-input"
  );
  const endDateInput = document.getElementById(
    "end-date-fact-of-article-input"
  );
  if (checkStorage) {
    let currentDate = new Date();
    state.selectedStartDateInput =
      currentDate.toISOString().split("-")[0] +
      "-" +
      currentDate.toISOString().split("-")[1];
    state.selectedEndDateInput =
      currentDate.toISOString().split("-")[0] +
      "-" +
      currentDate.toISOString().split("-")[1];
  }
  startDateInput.value = state.selectedStartDateInput;
  endDateInput.value = state.selectedEndDateInput;
  endDateInput.min = startDateInput.value.substr(0, 4) + "-01";
  endDateInput.max = startDateInput.value.substr(0, 4) + "-12";
};

const fillSubsectionSelect = () => {
  const select = document.getElementById("subsection-fact-of-article-select");
  while (select.firstChild) select.removeChild(select.firstChild);
  const unselectOption = document.createElement("option");
  unselectOption.innerText = "Обрати";
  unselectOption.value = "Обрати";
  select.appendChild(unselectOption);
  for (let i = 0; i < state.subsectionSelect.length; i++) {
    const option = document.createElement("option");
    option.value = state.subsectionSelect[i].name;
    option.innerText = state.subsectionSelect[i].shortname;
    option.id = state.subsectionSelect[i].id;
    option.setAttribute("data-id", state.subsectionSelect[i].id);
    select.appendChild(option);
  }
  let storage = window.localStorage.getItem("fact-of-articles-state");
  if (storage) {
    let subsectionSelect = document.getElementById(
      "subsection-fact-of-article-select"
    );
    for (let i = 0; i < subsectionSelect.options.length; i++) {
      if (
        subsectionSelect.options[i].getAttribute("data-id") ==
        state.selectedSubsectionSelect.id
      ) {
        subsectionSelect.value = subsectionSelect.options[i].value;
      }
    }
    selectSubsectionOnChange(subsectionSelect);
  }
};

const clearTables = () => {
  inputClear(document.getElementById("plane-of-budget-fact-of-article-input"));
  inputClear(
    document.getElementById("plane-write-off-of-budget-fact-of-article-input")
  );
  tdRemove(document.getElementById("info-treaty__table__tbody"));
  tdRemove(document.getElementById("fact-of-articles_table_tbody"));
  tdRemove(document.getElementById("final-table-tbody"));
  document.getElementById("final-table__tfoot__td-sum-month").innerText =
    "0.00000";
  document.getElementById("final-table__tfoot__td-sum-year").innerText =
    "0.00000";
  inputClear(
    document.getElementById("plane-of-budget-fact-of-article-input"),
    "0.00000"
  );
  inputClear(
    document.getElementById("plane-write-off-of-budget-fact-of-article-input"),
    "0.00000"
  );
  inputClear(document.getElementById("deviation-plan-input"), "0.00000");
  inputClear(
    document.getElementById("percentage-of-completion-budget-input"),
    "0"
  );
  inputClear(document.getElementById("all-fact-sum-input"), "0.00000");
};

const inputStartDateOnChange = (target) => {
  const endDateInput = document.getElementById(
    "end-date-fact-of-article-input"
  );
  state.planOfBudgetInput = {};
  state.sumFactOfWritingOffCost = 0;
  state.selectedStartDateInput = target.value;
  if (target.value === "") {
    endDateInput.disabled = true;
    endDateInput.value = null;
  } else endDateInput.disabled = false;
  state.selectedEndDateInput =
    target.value.substr(0, 4) + "-" + endDateInput.value.substr(5, 2);
  endDateInput.min = target.value.substr(0, 4) + "-01";
  endDateInput.max = target.value.substr(0, 4) + "-12";
  endDateInput.value =
    target.value.substr(0, 4) + "-" + endDateInput.value.substr(5, 2);
  window.localStorage.setItem("fact-of-articles-state", JSON.stringify(state));
  clearTables();
  // if (state.selectedArticleSelect.year !== state.selectedEndDateInput.substr(0, 4))
  //     return;
  selectArticleRequest();
  infoNumberContractRequest();
  infoPlanRequest();
  infoFactSum();
};

const inputEndDateOnChange = (target) => {
  state.planOfBudgetInput = {};
  state.sumFactOfWritingOffCost = 0;
  state.selectedEndDateInput = target.value;
  window.localStorage.setItem("fact-of-articles-state", JSON.stringify(state));
  clearTables();
  // if (state.selectedArticleSelect.year === state.selectedEndDateInput.substr(0, 4)) {
  //     return;
  // }
  selectArticleRequest();
  infoNumberContractRequest();
  infoPlanRequest();
  infoFactSum();
};

const selectSubsectionOnChange = (target) => {
  if (checkStorage) {
    window.localStorage.removeItem("fact-of-articles-state");
  }
  // state.planOfBudgetInput = {};
  // state.selectedArticleSelect = {
  //     id: null,
  //     articleId: null,
  //     articleName: null,
  //     year: null
  // };
  // state.sumFactOfWritingOffCost = 0;
  state.selectedSubsectionSelect.id =
    target.options[target.selectedIndex].getAttribute("data-id");
  state.selectedSubsectionSelect.name =
    target.options[target.selectedIndex].value;
  // state.articleSelect = [];
  window.localStorage.setItem("fact-of-articles-state", JSON.stringify(state));
  clearTables();
  selectArticleRequest();
};

const selectArticleRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-articles.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "selectArticleRequest",
      id: state.selectedSubsectionSelect.id,
      name: state.selectedSubsectionSelect.name,
      startDate: state.selectedStartDateInput,
      endDate: state.selectedEndDateInput,
    })
  );
  xhr.onload = function () {
    state.articleSelect = JSON.parse(xhr.response);
    fillArticleSelect();
  };
};

const fillArticleSelect = () => {
  const select = document.getElementById("article-fact-of-article-select");
  while (select.firstChild) select.removeChild(select.firstChild);
  const unselectOption = document.createElement("option");
  unselectOption.value = "Обрати";
  unselectOption.innerText = "Обрати";
  select.appendChild(unselectOption);
  state.articleSelect.forEach((element) => {
    const option = document.createElement("option");
    option.value = element.articleName;
    option.innerText = element.articleName;
    option.id = element.id;
    option.setAttribute("data-id", element.id);
    option.setAttribute("data-article-id", element.articleId);
    option.setAttribute("data-article-year", element.year);
    if (element.articleName === state.selectedArticleSelect.articleName)
      option.selected = true;
    select.appendChild(option);
  });
  let storage = window.localStorage.getItem("fact-of-articles-state");
  if (storage) {
    let articleSelect = document.getElementById(
      "article-fact-of-article-select"
    );
    for (let i = 0; i < articleSelect.options.length; i++) {
      if (
        articleSelect.options[i].getAttribute("data-id") ==
        state.selectedArticleSelect.id
      ) {
        articleSelect.value = articleSelect.options[i].value;
      }
    }
    selectArticleOnChange(articleSelect);
  }
};

const selectArticleOnChange = (target) => {
  if (checkStorage) {
    state.planOfBudgetInput = {};
    state.sumFactOfWritingOffCost = 0;
    state.selectedArticleSelect.id =
      target.options[target.selectedIndex].getAttribute("data-id");
    state.selectedArticleSelect.articleName =
      target.options[target.selectedIndex].value === "Обрати"
        ? null
        : target.options[target.selectedIndex].value;
    state.selectedArticleSelect.articleId =
      target.options[target.selectedIndex].getAttribute("data-article-id");
    state.selectedArticleSelect.year =
      target.options[target.selectedIndex].getAttribute("data-article-year");
  }
  state.planOfBudgetInput = {};
  state.sumFactOfWritingOffCost = 0;
  checkStorage = true;
  window.localStorage.setItem("fact-of-articles-state", JSON.stringify(state));
  tdRemove(document.getElementById("info-treaty__table__tbody"));
  tdRemove(document.getElementById("fact-of-articles_table_tbody"));
  tdRemove(document.getElementById("final-table-tbody"));
  document.getElementById("final-table__tfoot__td-sum-month").innerText =
    "0.00000";
  document.getElementById("final-table__tfoot__td-sum-year").innerText =
    "0.00000";
  inputClear(
    document.getElementById("plane-of-budget-fact-of-article-input"),
    "0.00000"
  );
  inputClear(
    document.getElementById("plane-write-off-of-budget-fact-of-article-input"),
    "0.00000"
  );
  inputClear(document.getElementById("deviation-plan-input"), "0.00000");
  inputClear(
    document.getElementById("percentage-of-completion-budget-input"),
    "0"
  );
  inputClear(document.getElementById("all-fact-sum-input"), "0.00000");
  infoNumberContractRequest();
  infoPlanRequest();
  infoFactSum();
};

const clearFiltersReloadOnClick = () => {
  window.localStorage.removeItem("fact-of-articles-state");
  document.location.reload();
};

const clearAllFiltersReloadOnClick = () => {
  window.localStorage.clear();
  document.location.reload();
};

const infoNumberContractRequest = () => {
  const spinner = document.getElementById("spinner-loader-id");
  spinner.style.visibility = "visible";
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-articles.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "infoNumberContractRequest",
      startDate: state.selectedStartDateInput,
      endDate: state.selectedEndDateInput,
      name: state.selectedArticleSelect.articleName,
      id:
        state.selectedArticleSelect.id === null
          ? 0
          : state.selectedArticleSelect.id,
    })
  );
  xhr.onload = function () {
    console.log(xhr.response);
    let response = JSON.parse(xhr.response);
    if (response.length === 0) {
      spinner.style.visibility = "hidden";
      return;
    }
    state.infoNumberSelect = response.treaty_data;
    state.infoNumberId = response.treaty_id;
    fillInfoNumberContractSelect();
    infoFactFromActCardRequest();
    infoFactByTreatyRequest();
  };
};

const fillInfoNumberContractSelect = () => {
  const tbody = document.getElementById("info-treaty__table__tbody");
  while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
  if (state.infoNumberSelect !== undefined)
    state.infoNumberSelect.forEach((element) => {
      console.log(element);
      const tr = document.createElement("tr");
      const tdContractNumber = document.createElement("td");
      const tdDate = document.createElement("td");
      const tdCounterparty = document.createElement("td");
      const tdSumNoVAT = document.createElement("td");
      const tdSumWithVAT = document.createElement("td");
      tdContractNumber.innerText = element.contractNumber;
      tdDate.innerText = element.date;
      tdCounterparty.innerText = element.counterparty;
      tdSumNoVAT.innerText = element.sumNoVAT;
      tdSumWithVAT.innerText = element.sumWithVAT;
      tr.appendChild(tdContractNumber);
      tr.appendChild(tdDate);
      tr.appendChild(tdCounterparty);
      tr.appendChild(tdSumNoVAT);
      tr.appendChild(tdSumWithVAT);
      tbody.appendChild(tr);
    });
};

const tdRemove = (tbody) => {
  while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
};

const inputClear = (input, value) => {
  input.value = value;
};

const infoPlanRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-articles.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "infoPlanRequest",
      id:
        state.selectedArticleSelect.id === null
          ? 0
          : state.selectedArticleSelect.id,
      startDate: state.selectedStartDateInput,
      endDate: state.selectedEndDateInput,
    })
  );
  xhr.onload = function () {
    console.log(state.selectedStartDateInput, state.selectedEndDateInput);
    fillPlanInputs(JSON.parse(xhr.response));
  };
};

const fillPlanInputs = (data) => {
  state.planOfBudgetInput.planVAT = data.planVAT;
  state.planOfBudgetInput.planNoVAT = data.planNoVAT;
  const inputVAL = document.getElementById(
    "plane-of-budget-fact-of-article-input"
  );
  const inputNoVAL = document.getElementById(
    "plane-write-off-of-budget-fact-of-article-input"
  );
  inputVAL.value = state.planOfBudgetInput.planVAT.toFixed(5);
  inputNoVAL.value = state.planOfBudgetInput.planNoVAT.toFixed(5);
};

const infoFactSum = () => {
  if (state.selectedArticleSelect.id === null ? true : false) return;
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-articles.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "infoFactSum",
      id: state.selectedArticleSelect.id,
      date: state.selectedEndDateInput,
    })
  );
  xhr.onload = function () {
    fillFactSum(JSON.parse(xhr.response));
  };
};

const fillFactSum = (sum) => {
  const input = document.getElementById("all-fact-sum-input");
  input.value = sum;
};

const infoFactFromActCardRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-articles.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "infoFactFromActCardRequest",
      infoTreaty: state.infoNumberId,
      startDate: state.selectedStartDateInput,
      endDate: state.selectedEndDateInput,
      planId: state.selectedArticleSelect.id,
    })
  );
  xhr.onload = function () {
    console.log(state.selectedStartDateInput, state.selectedEndDateInput);
    console.log(xhr.response);
    const spinner = document.getElementById("spinner-loader-id");
    spinner.style.visibility = "hidden";
    state.infoActCard = JSON.parse(xhr.response);
    filterArray();
    state.infoActCard.sort((a, b) =>
      a.act_number > b.act_number ? 1 : b.act_number > a.act_number ? -1 : 0
    );
    state.infoActCard.sort((a, b) =>
      a.act_date > b.act_date ? 1 : b.act_date > a.act_date ? -1 : 0
    );
    fillFactTable();
  };
};

const filterArray = () => {
  const arr = [];
  const reduceActCardData = (element, array) => {
    let buffArr = array.filter(function (item) {
      if (parseInt(element.actCardId) === parseInt(item.actCardId)) return item;
    });
    buffArr[0].sum_act_VAT = buffArr
      .reduce(
        (previous, current) =>
          parseFloat(previous) + parseFloat(current.sum_act_VAT),
        0
      )
      .toFixed(5);
    buffArr[0].sum_act_no_VAT = buffArr
      .reduce(
        (previous, current) =>
          parseFloat(previous) + parseFloat(current.sum_act_no_VAT),
        0
      )
      .toFixed(5);
    arr.push(buffArr[0]);
  };

  state.infoActCard.forEach((element, index, array) => {
    index === 0
      ? reduceActCardData(element, array)
      : element.actCardId !== array[index - 1].actCardId
      ? reduceActCardData(element, array)
      : false;
  });
  state.infoActCard = arr;
};

const fillFactTable = () => {
  const tbody = document.getElementById("fact-of-articles_table_tbody");
  while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
  state.sumFactOfWritingOffCost = 0;
  state.infoActCard.forEach((element) => {
    state.sumFactOfWritingOffCost += parseFloat(element.sum_act_VAT);
    const tr = document.createElement("tr");
    const tdFactDate = document.createElement("td");
    const tdFactOfPayWithVAT = document.createElement("td");
    const tdFactofWriteOffNoVAT = document.createElement("td");
    const tdWriteOffWithVAT = document.createElement("td");
    const tdActNumber = document.createElement("td");
    const tdActDate = document.createElement("td");
    const tdTreatyNumber = document.createElement("td");
    tdFactDate.innerText = element.fact_date === null ? "—" : element.fact_date;
    tdFactOfPayWithVAT.innerText = element.sum_fact_with_VAT;
    tdFactofWriteOffNoVAT.innerText = element.sum_act_no_VAT;
    tdWriteOffWithVAT.innerText = element.sum_act_VAT;
    tdActNumber.innerText = element.act_number;
    tdActDate.innerText = element.act_date;
    tdTreatyNumber.innerText = element.contract_number;
    tr.appendChild(tdFactDate);
    tr.appendChild(tdFactOfPayWithVAT);
    tr.appendChild(tdFactofWriteOffNoVAT);
    tr.appendChild(tdWriteOffWithVAT);
    tr.appendChild(tdActNumber);
    tr.appendChild(tdActDate);
    tr.appendChild(tdTreatyNumber);
    tbody.appendChild(tr);
  });
  if (
    state.selectedArticleSelect.id !== null &&
    state.sumFactOfWritingOffCost !== 0 &&
    state.selectedEndDateInput !== ""
  )
    fillDeviationPlanInputs();
  // дата оплати
};

const fillDeviationPlanInputs = () => {
  console.log(state.planOfBudgetInput.planVAT, state.sumFactOfWritingOffCost);
  const deviationInput = document.getElementById("deviation-plan-input");
  const percentageInput = document.getElementById(
    "percentage-of-completion-budget-input"
  );
  deviationInput.value = (
    state.planOfBudgetInput.planVAT - state.sumFactOfWritingOffCost
  ).toFixed(5);
  let deviation =
    state.planOfBudgetInput.planVAT === 0
      ? 0
      : (
          (state.sumFactOfWritingOffCost * 100) /
          state.planOfBudgetInput.planVAT
        ).toFixed(1);
  percentageInput.value = deviation;
};

const infoFactByTreatyRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-articles.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  const numbersContracts = [];
  state.infoNumberSelect.forEach((element) =>
    numbersContracts.push(element.contractNumber)
  );
  console.log(numbersContracts);
  xhr.send(
    JSON.stringify({
      typeRequest: "infoFactByTreatyRequest",
      numbersContract: [...new Set(numbersContracts)],
      startDate: state.selectedStartDateInput,
      endDate: state.selectedEndDateInput,
      planId: state.selectedArticleSelect.id,
      articleName: state.selectedArticleSelect.articleName,
      articleYear: state.selectedArticleSelect.year,
    })
  );
  xhr.onload = function () {
    console.log(xhr.response);
    state.sumFactsOfTreaties = JSON.parse(xhr.response);
    // state.infoActCard.forEach(element => {
    //   state.sumFactsOfTreaties.push({
    //     number_contract: element.contract_number,

    //   })
    // });

    // state.sumFactsOfTreaties = state.infoActCard;

    fillFinalTreatyTable();
  };
};

const fillFinalTreatyTable = () => {
  const tbody = document.getElementById("final-table-tbody");
  while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
  const tdFinalSumMonth = document.getElementById(
    "final-table__tfoot__td-sum-month"
  );
  const tdFinalSumYear = document.getElementById(
    "final-table__tfoot__td-sum-year"
  );
  let sumMonth = 0;
  let sumYear = 0;
  state.sumFactsOfTreaties.forEach((element) => {
    console.log(element);
    sumMonth += parseFloat(element.sum_month === null ? 0 : element.sum_month);
    sumYear += parseFloat(element.sum_year === null ? 0 : element.sum_year);
    // sumYear += parseFloat(
    //   element.all_sum_year === null ? 0 : element.all_sum_year
    // );
    //arseFloat(element.all_sum_year);
    const tr = document.createElement("tr");
    const tdTreaty = document.createElement("td");
    const tdMonth = document.createElement("td");
    const tdYear = document.createElement("td");
    tdTreaty.innerText =
      "Всього витрат по договору № " +
      element.number_contract +
      ", тис.грн. з ПДВ";
    tdMonth.innerText = element.sum_month;
    tdYear.innerText = element.sum_year;
    tr.appendChild(tdTreaty);
    tr.appendChild(tdMonth);
    tr.appendChild(tdYear);
    tbody.appendChild(tr);
  });
  tdFinalSumMonth.innerText = sumMonth.toFixed(5);
  tdFinalSumYear.innerText = sumYear.toFixed(5);
};

const optionRemove = (select = null, option = null) => {
  if (select !== null && option !== null) {
    while (select.firstChild) select.removeChild(select.firstChild);
    if (
      select !==
      document.getElementById("info-number-contract-fact-of-article-select")
    ) {
      option.value = "Обрати";
      option.innerText = "Обрати";
      select.appendChild(option);
    }
  } else {
    while (select.firstChild) select.removeChild(select.firstChild);
  }
};

// events

// modify DOM
const selectRedefineStyle = () => {
  const selectResults = document.getElementsByClassName(
    "select2 select2-container select2-container--default"
  );
  for (let i = 0; i < selectResults.length; i++) {
    selectResults[i].style.width = "";
  }
};
