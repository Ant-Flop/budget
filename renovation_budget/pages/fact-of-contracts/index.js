let state = {
  inputStartMonth: null,
  inputEndMonth: null,
  selectBarData: [],
  selectBarArticleId: "",
  selectBarArticleName: "",
  selectBarCounterpartyId: null,
  selectBarContractId: {},
  selectedContractId: null,
  selectedCounterpartyId: null,
};

let checkStorage = true;
let storage = null;



document.addEventListener("DOMContentLoaded", () => {
  //   const spinner = document.getElementById("spinner-loader-id");
  //   spinner.style.visibility = "visible";
  storage = window.localStorage.getItem("fact-of-contracts-state");
  if (storage) {
    state = JSON.parse(window.localStorage.getItem("fact-of-contracts-state"));
    document.getElementById("start-date-id").value = state.inputStartMonth;
    document.getElementById("end-date-id").value = state.inputEndMonth;
    checkStorage = false;
    const spinner = document.getElementById("spinner-loader-id");
    spinner.style.visibility = "visible";
    renderTableRequest(
      state.selectBarContractId.counterpartyId !== undefined
        ? state.selectBarContractId.counterpartyId
        : null,
      state.selectBarContractId.id !== undefined
        ? state.selectBarContractId.id
        : null
    );
    selectBarDataRequest();
    selectRedefineStyle();
  } else {
    state.inputStartMonth = document.getElementById("start-date-id").value;
    state.inputEndMonth = document.getElementById("end-date-id").value;

    renderTableRequest(null, null);
    selectBarDataRequest();
    selectRedefineStyle();
  }
});

// requests
const renderTableRequest = (counterpartyId, contractId) => {
  //   const spinner = document.getElementById("spinner-loader-id");
  //   spinner.style.visibility = "visible";
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-contracts.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      //   articleId:
      //     state.selectBarArticleId === null || state.selectBarArticleId === ""
      //       ? 0
      //       : state.selectBarArticleId,
      articleName:
        state.selectBarArticleName === null || state.selectBarArticleName === ""
          ? ""
          : state.selectBarArticleName,
      counterpartyId: counterpartyId === undefined ? null : counterpartyId,
      contractId: contractId === undefined ? null : contractId,
      startMonth: state.inputStartMonth === "" ? 1 : state.inputStartMonth,
      endMonth: state.inputEndMonth === "" ? 1 : state.inputEndMonth,
    })
  );
  xhr.onload = function () {
    //spinner.style.visibility = "hidden";

    document.querySelector("#fact-of-contracts-table").innerHTML = xhr.response;
    headerTableRedefineStyle();
    stickyTableColumn();
    setScrollTable();
    selectRowOnClick()
  };
};



const selectRowOnClick = () => {
  const tableRows = [
    ...document.querySelectorAll(
      "#table-id > tbody > tr"
    ),
  ];

  console.log(tableRows)

  tableRows.forEach((row) => {
    row.addEventListener("click", () => {
      [...document.getElementsByClassName("selected-td")].forEach(
        (element) => element.classList.remove("selected-td")
      );
      [...row.getElementsByTagName("td")].forEach((element) =>{
        // element.style.zIndex = 4;
        // element.style.position = "sticky";
        // element.style.top = 0;
        element.classList.add("selected-td")
      }
      );
    });
  });
}

const selectBarDataRequest = () => {
  const spinner = document.getElementById("spinner-loader-id");
  spinner.style.visibility = "visible";
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-contracts.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "selectBarDataRequest",
      startMonth: state.inputStartMonth === "" ? 1 : state.inputStartMonth,
      endMonth: state.inputEndMonth === "" ? 1 : state.inputEndMonth,
      year: state.inputStartMonth.substring(0, 4),
    })
  );
  xhr.onload = function () {
    spinner.style.visibility = "hidden";
    state.selectBarData = JSON.parse(xhr.response);
    console.log(JSON.parse(xhr.response));
    //fillSelectArticleBar();
    fillSelectCounterpartyBar();
    // target.options[target.selectedIndex].getAttribute("get-id")
  };
};

const selectBarDataDateRequest = () => {
  //   const spinner = document.getElementById("spinner-loader-id");
  //   spinner.style.visibility = "visible";
  const xhr = new XMLHttpRequest();
  const requestURL = "fact-of-contracts.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "selectBarDataRequest",
      startMonth: state.inputStartMonth,
      endMonth: state.inputEndMonth,
    })
  );
  xhr.onload = function () {
    state.selectBarData = JSON.parse(xhr.response);
    console.log(state.inputStartMonth);
    if (state.selectBarContractId.id !== undefined) fillInputsSumBar();
    //spinner.style.visibility = "hidden";
  };
};

//events

// const selectArticlesBarOnChange = (target) => {
//   if (checkStorage) {
//     window.localStorage.removeItem("fact-of-contracts-state");
//   }
//   const contractNumberSelect = document.getElementById(
//     "contract-number-select-id"
//   );
//   const counterpartiesSelect = document.getElementById(
//     "counterparty-select-id"
//   );
//   optionRemove(contractNumberSelect, document.createElement("option"));
//   optionRemove(counterpartiesSelect, document.createElement("option"));
//   state.selectBarArticleName = target.options[target.selectedIndex].value;
//   fillSelectCounterpartyBar(
//     target.options[target.selectedIndex].getAttribute("get-id")
//   );
//   if (checkStorage) {
//     renderTableRequest(null, null);
//   }
// };

const selectCounterpartiesBarOnChange = (target) => {
  if (checkStorage) {
    state.selectBarContractId.counterpartyId =
      target.options[target.selectedIndex].getAttribute("get-id");
  }
  const contractNumberSelect = document.getElementById(
    "contract-number-select-id"
  );
  const optionUnselect = document.createElement("option");
  optionRemove(contractNumberSelect, optionUnselect);
  state.selectedCounterpartyId =
    target.options[target.selectedIndex].getAttribute("get-id");
  fillSelectContractNumberBar(
    target.options[target.selectedIndex].getAttribute("get-id")
  );
  if (checkStorage) {
    renderTableRequest(null, null);
  }
};

const selectContractsBarOnChange = (target) => {
  if (checkStorage) {
    state.selectBarContractId.id =
      target.options[target.selectedIndex].getAttribute("get-id");
  }

  state.selectedContractId =
    target.options[target.selectedIndex].getAttribute("get-id");

  selectBarDataDateRequest();

  fillInputsSumBar();
  renderTableRequest(
    state.selectBarContractId.counterpartyId,
    state.selectBarContractId.id
  );
  setTimeout(() => {
    checkStorage = true;
    window.localStorage.setItem(
      "fact-of-contracts-state",
      JSON.stringify(state)
    );
  }, 200);
};

const inputStartMonthChange = (target) => {
  state.inputStartMonth = target.value;
  if (state.selectBarContractId.id === undefined) {
    selectBarDataRequest();
  } else {
    selectBarDataDateRequest();
  }
  renderTableRequest(
    state.selectBarContractId.counterpartyId,
    state.selectBarContractId.id
  );
  window.localStorage.setItem("fact-of-contracts-state", JSON.stringify(state));
};

const inputEndMonthChange = (target) => {
  state.inputEndMonth = target.value;
  if (state.selectBarContractId.id === undefined) {
    selectBarDataRequest();
  } else {
    selectBarDataDateRequest();
  }

  renderTableRequest(
    state.selectBarContractId.counterpartyId,
    state.selectBarContractId.id
  );
  window.localStorage.setItem("fact-of-contracts-state", JSON.stringify(state));
};

const clearFiltersReloadOnClick = () => {
  window.localStorage.removeItem("fact-of-contracts-state");
  document.location.reload();
};

const clearAllFiltersReloadOnClick = () => {
  window.localStorage.clear();
  document.location.reload();
};

//DOM

const optionRemove = (select = null, option = null) => {
  const sumInput = document.getElementById("sum-of-contract-with-pdv-input-id");
  const differenceInput = document.getElementById(
    "balance-of-contract-with-pdv-input-id"
  );
  if (select !== null || option !== null) {
    while (select.firstChild) select.removeChild(select.firstChild);
    option.value = "Обрати";
    option.innerText = "Обрати";
    select.appendChild(option);
  }
  sumInput.value = null;
  differenceInput.value = null;
};

// const fillSelectArticleBar = () => {
//   const articlesSelect = document.getElementById("name-article-select-id");
//   if ([...articlesSelect.options].length > 1) {
//     while ([...articlesSelect.options].length > 1) {
//       articlesSelect.removeChild(articlesSelect.lastChild);
//     }
//   }
//   for (let i = 0; i < state.selectBarData.articles.length; i++) {
//     const option = document.createElement("option");
//     option.innerText = state.selectBarData.articles[i].name;
//     option.value = state.selectBarData.articles[i].name;
//     option.id = state.selectBarData.articles[i].id;
//     option.setAttribute(
//       "get-id",
//       state.selectBarData.articles[i].counterparty_id
//     );
//     articlesSelect.appendChild(option);
//   }

//   let storage = window.localStorage.getItem("fact-of-contracts-state");
//   if (storage) {
//     let selectArticle = document.getElementById("name-article-select-id");
//     for (let i = 0; i < selectArticle.options.length; i++) {
//       if (selectArticle.options[i].value == state.selectBarArticleName) {
//         selectArticle.value = selectArticle.options[i].value;
//       }
//     }
//     selectArticlesBarOnChange(selectArticle);
//   }
// };

const fillSelectCounterpartyBar = (id) => {
  const counterpartySelect = document.getElementById("counterparty-select-id");

  for (let i = 0; i < state.selectBarData.counterparties.length; i++) {
    //if (state.selectBarData.counterparties[i].id === id) {
    const option = document.createElement("option");
    option.innerText = state.selectBarData.counterparties[i].name;
    option.value = state.selectBarData.counterparties[i].name;
    option.id = state.selectBarData.counterparties[i].id;
    option.setAttribute("get-id", state.selectBarData.counterparties[i].id);
    counterpartySelect.appendChild(option);
    //}
  }
  let storage = window.localStorage.getItem("fact-of-contracts-state");
  if (storage) {
    const selectCounterparty = document.getElementById(
      "counterparty-select-id"
    );
    for (let i = 0; i < selectCounterparty.options.length; i++) {
      if (
        selectCounterparty.options[i].id ==
        state.selectBarContractId.counterpartyId
      ) {
        selectCounterparty.value = selectCounterparty.options[i].value;
      }
    }
    selectCounterpartiesBarOnChange(selectCounterparty);
  }
};

const fillSelectContractNumberBar = (id) => {
  const contractNumberSelect = document.getElementById(
    "contract-number-select-id"
  );
  console.log(state);
  state.selectBarData.contracts.forEach((element) => {
    if (element.counterparty_id == id) {
      // element.treaty_articles_array.forEach((item) => {
      //   if (item.article_name == state.selectBarArticleName) {
      const option = document.createElement("option");
      option.innerText = element.contract_number;
      option.value = element.contract_number;
      option.id = element.id;
      option.setAttribute("get-id", element.id);
      contractNumberSelect.appendChild(option);
      //   }
      // });
    }
  });

  let storage = window.localStorage.getItem("fact-of-contracts-state");
  if (storage) {
    const selectTreaty = document.getElementById("contract-number-select-id");
    for (let i = 0; i < selectTreaty.options.length; i++) {
      if (selectTreaty.options[i].id == state.selectBarContractId.id) {
        selectTreaty.value = selectTreaty.options[i].value;
      }
    }
    selectContractsBarOnChange(selectTreaty);
  }
};

const fillInputsSumBar = () => {
  const inputSum = document.getElementById("sum-of-contract-with-pdv-input-id");
  const inputDifference = document.getElementById(
    "balance-of-contract-with-pdv-input-id"
  );
  const sumLabel = document.getElementById("sum-of-contract-with-pdv-label-id");
  const sumDifference = document.getElementById(
    "balance-of-contract-with-pdv-label-id"
  );
  let sum = 0;
  let sign = null;
  console.log(state.selectBarData.renovationTreaty)
  state.selectBarData.renovationTreaty.forEach((element) => {
    if (
      element.contract_id === state.selectBarContractId.id &&
      element.counterparty_id === state.selectBarContractId.counterpartyId
    ) {
      sign = state.selectBarData.contracts.filter(
        (item) => item.id === state.selectBarContractId.id
      )[0]?.sign_of_vat;
      let count_exceptions = element.articles_exceptions.filter(
        (item) => item.article_name === element.article_name
      );

      let cost =
        count_exceptions.length === 0 ? element.cost_of_materials_no_pdv : 0;
      //let cost = element.cost_of_materials_no_pdv;
      sum +=
        element.amount *
        (parseFloat(element.price_of_service_no_pdv) + parseFloat(cost)) *
        (sign === "Без ПДВ" ? 1 : 1.2); // + parseFloat(cost)
    }
  });
  if (sign === "Без ПДВ") {
    sumLabel.innerText = "Сума, договору, грн. без ПДВ";
    sumDifference.innerText = "Залишок по договору, грн. без ПДВ";
  } else {
    sumLabel.innerText = "Сума, договору, грн. з ПДВ";
    sumDifference.innerText = "Залишок по договору, грн. з ПДВ";
  }
  if (
    state.selectBarContractId.id === undefined ||
    state.selectBarContractId.id === null
  ) {
    inputSum.value = "0.00  ";
    inputDifference.value = "0.00";
  } else {
    inputSum.value = sum.toFixed(2);
    inputDifference.value =
      differenceActsTreaty(sum) < 0
        ? "0.00"
        : differenceActsTreaty(sum).toFixed(2);
  }
};

const differenceActsTreaty = (sum) => {
  
  const treatyIdArray = [];
  let sumActs = 0;
  state.selectBarData.renovationTreaty.forEach((element) => {
    if (
      element.contract_id === state.selectBarContractId.id &&
      element.counterparty_id === state.selectBarContractId.counterpartyId
    ) {
      treatyIdArray.push(element.id);
    }
  });
  treatyIdArray.forEach((id) => {

    console.log(state.selectBarData)
    
    state.selectBarData.renovationRactd.forEach((element) => {
      console.log(id, element.treaty_id)
      if (id === element.treaty_id) {
        sumActs +=
        parseFloat(element.amount) *
          (parseFloat(element.cost_of_materials_no_pdv) +
            parseFloat(element.price_of_service_no_pdv)) *
          1.2;
        console.log(sum, sumActs, element.amount, element.cost_of_materials_no_pdv, element.price_of_service_no_pdv)
      }
    });
  });

  return sum - sumActs;
};

const selectRedefineStyle = () => {
  const selectResults = document.getElementsByClassName(
    "select2 select2-container select2-container--default"
  );
  for (let i = 0; i < selectResults.length; i++) {
    selectResults[i].style.width = "";
  }
};

const headerTableRedefineStyle = () => {
  const thAct = document.getElementsByClassName("th-act-card");
  const thCostService = document.getElementsByClassName(
    "table-column-cost-service"
  );
  const thThirdrow = document.getElementsByClassName(
    "table-th-sticky-third-row"
  );
  if (thAct.length > 0) {
    const thFirstHead = document.getElementsByClassName("th-first-head-fact");
    const thSecondHead = document.getElementsByClassName("th-second-head-fact");
    thCostService[0].style.width = "auto";
    if (thAct.length === 5)
      for (let i = 0; i < thThirdrow.length; i++)
        thThirdrow[i].style.top =
          thFirstHead[0].offsetHeight + thSecondHead[0].offsetHeight + "px";
  }
  const thFirstFact = document.getElementsByClassName("th-first-head-fact");
  const thSecondFact = document.getElementsByClassName("th-second-head-fact");
  const thThirdFact = document.getElementsByClassName("th-third-head-fact");
  for (let i = 0; i < thSecondFact.length; i++) {
    thSecondFact[i].style.top = thFirstFact[0].offsetHeight + "px";
  }
  for (let i = 0; i < thThirdFact.length; i++) {
    thThirdFact[i].style.top =
      thFirstFact[0].offsetHeight + thSecondFact[0].offsetHeight + "px";
  }
};

const stickyTableColumn = () => {
  const columnArray = [
    {
      name: ".table-column-id",
      direction: "left",
    },
    {
      name: "thead .table-column-id",
      direction: "top",
    },
    {
      name: ".table-column-article_name",
      direction: "left",
    },
    {
      name: "thead .table-column-article_name",
      direction: "top",
    },
    {
      name: ".table-column-name",
      direction: "left",
    },
    {
      name: "thead .table-column-name",
      direction: "top",
    },
    {
      name: ".table-column-type",
      direction: "left",
    },
    {
      name: "thead .table-column-type",
      direction: "top",
    },
    {
      name: ".table-column-amount-remainder",
      direction: "left",
    },
    {
      name: "thead .table-column-amount-remainder",
      direction: "top",
    },
    {
      name: ".table-column-cost-service",
      direction: "left",
    },
    {
      name: "thead .table-column-cost-service",
      direction: "top",
    },
    {
      name: ".table-column-price",
      direction: "left",
    },
    {
      name: "thead .table-column-price",
      direction: "top",
    },
    {
      name: ".table-column-cost",
      direction: "left",
    },
    {
      name: "thead .table-column-cost",
      direction: "top",
    },
    {
      name: ".table-column-sum",
      direction: "left",
    },
    {
      name: "thead .table-column-sum",
      direction: "top",
    },
    {
      name: ".table-column-remainder",
      direction: "left",
    },
    {
      name: "thead .table-column-remainder",
      direction: "top",
    },
    {
      name: ".table-column-amount",
      direction: "left",
    },
    {
      name: "thead .table-column-amount",
      direction: "top",
    },
    {
      name: ".table-column-all",
      direction: "left",
    },
    {
      name: ".table-column-blank-1",
      direction: "left",
    },
    {
      name: ".table-column-blank-2",
      direction: "left",
    },
    {
      name: ".table-column-blank-3",
      direction: "left",
    },
    {
      name: ".table-column-blank-4",
      direction: "left",
    },
    {
      name: ".table-column-blank-5",
      direction: "left",
    },
    {
      name: ".table-column-blank-6",
      direction: "left",
    },
  ];
  $("#fact-of-contracts-table").scroll(function () {
    columnArray.forEach((element) => {
      switch (element.direction) {
        case "left":
          $(element.name).css({
            left: $(this).scrollLeft(),
          });
          break;
        case "top":
          $(element.name).css({
            top: $(this).scrollTop(),
          });
          break;
        default:
          break;
      }
    });
  });

//   const table = document.querySelector("#fact-of-contracts-table tbody");
// let selectedRow = null;

// table.addEventListener("click", function(e) {
//   const row = e.target.closest("tr");
//   if (!row) return;

//   if (selectedRow) {
//     selectedRow.classList.remove("selected-td", "fixed-row");
//   }

//   row.classList.add("selected-td", "fixed-row");
//   selectedRow = row;
// });

// // Обработка прокрутки
// window.addEventListener("scroll", function() {
//   if (selectedRow) {
//     const rect = selectedRow.getBoundingClientRect();
//     const headerHeight = 2000; // Укажите высоту заголовка таблицы

//     if (rect.top < headerHeight) {
//       selectedRow.classList.add("fixed-row");
//     } else {
//       selectedRow.classList.remove("fixed-row");
//     }
//   }
// });

};

const setScrollTable = () => {
  const table = document.getElementById("fact-of-contracts-table");
  table.scroll(0, 1);
  table.scroll(0, table.scrollHeight);
};
