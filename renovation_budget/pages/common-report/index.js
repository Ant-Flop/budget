let state = {
  inputStartMonth: null,
  inputEndMonth: null,
  subsections: [
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
  subsectionData: [],
  mainTableData: [],
};

document.addEventListener("DOMContentLoaded", () => {
  let storage = window.localStorage.getItem("common-report-state");
  if (storage) {
    let storageState = JSON.parse(storage);
    state.inputStartMonth = storageState.startMonth;
    document.getElementById("start-date-id").value = storageState.startMonth;
    state.inputEndMonth = storageState.endMonth;
    document.getElementById("end-date-id").value = storageState.endMonth;
  } else {
    state.inputStartMonth = document.getElementById("start-date-id").value;
    state.inputEndMonth = document.getElementById("end-date-id").value;
  }
  subsectionDataRequest();
  selectRedefineStyle();
});

const subsectionDataRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "common-report.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "subsectionsDataRequest",
      startMonth: state.inputStartMonth,
      endMonth: state.inputEndMonth,
      subsectionData: state.subsections,
      monthRange: getMonthNumbersInRange(
        state.inputStartMonth.substr(5, 7),
        state.inputEndMonth.substr(5, 7)
      ),
    })
  );
  xhr.onload = function () {
    state.subsectionData = JSON.parse(xhr.response);
    fillSubsectionTable();
    mainTableDataRequest();
  };
};

function getMonthNumbersInRange(start, end) {
  let startMonth = start.toString().padStart(2, "0"); // Преобразование начального значения в строку с ведущим нулем
  let endMonth = end.toString().padStart(2, "0"); // Преобразование конечного значения в строку с ведущим нулем

  let months_with_vat = "";
  let months_no_vat = "";

  for (let i = parseInt(startMonth); i <= parseInt(endMonth); i++) {
    months_with_vat +=
      i.toString().padStart(2, "0") +
      "_with_vat" +
      (i !== parseInt(endMonth) ? " + " : "");
    months_no_vat +=
      i.toString().padStart(2, "0") +
      "_no_vat" +
      (i !== parseInt(endMonth) ? " + " : "");
  }

  return [months_with_vat, months_no_vat];
}

const mainTableDataRequest = () => {
  const spinner = document.getElementById("spinner-loader-id");
  spinner.style.visibility = "visible";
  const xhr = new XMLHttpRequest();
  const requestURL = "common-report.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "mainTableDataRequest",
      startMonth: state.inputStartMonth,
      endMonth: state.inputEndMonth,
      subsectionData: state.subsectionData,
    })
  );
  xhr.onload = function () {
    console.log(xhr.response)
    spinner.style.visibility = "hidden";
    OptimizedMainData(JSON.parse(xhr.response));
    state.mainTableData = JSON.parse(xhr.response);

    fillMainTable();
  };
};

const OptimizedMainData = (data) => {
  data.forEach((element, index) => {
    let bufArr = data.filter(
      (item) =>
        item.article_id === element.article_id &&
        item.contract_id === element.contract_id
        
    );
    console.log(bufArr)
    state.mainTableData.push(bufArr);
  });
};

const fillSubsectionTable = () => {
  const tbody = document.getElementById("subsection-info__table__tbody");
  //console.log(state.subsectionData)
  state.subsectionData.forEach((element) => {
    const tr = document.createElement("tr");
    const tdTitle = document.createElement("td");
    const tdSubsection = document.createElement("td");
    const tdSumNoVAT = document.createElement("td");
    const tdSumWithVAT = document.createElement("td");
    tdTitle.innerText = "План бюджету ремонт підрядним способом річний";
    tdSubsection.innerText = element.shortname;
    tdSumNoVAT.innerText = element.sum_no_vat;
    tdSumWithVAT.innerText = element.sum_with_vat;
    tr.appendChild(tdTitle);
    tr.appendChild(tdSubsection);
    tr.appendChild(tdSumNoVAT);
    tr.appendChild(tdSumWithVAT);
    tbody.appendChild(tr);
  });
};

const fillMainTable = () => {
  const tbody = document.getElementById("main-report__table__tbody");
  state.mainTableData.forEach((element, index, array) => {
    const tr = document.createElement("tr");
    const tdArticle = document.createElement("td");
    const tdContract = document.createElement("td");
    const tdDetailReport = document.createElement("td");
    const tdFactNoVAT = document.createElement("td");
    const tdFactWithVAT = document.createElement("td");
    const tdAmount = document.createElement("td");
    const tdSumActNoVAT = document.createElement("td");
    const tdSumActWithVAT = document.createElement("td");
    const tdAllOnTreatyNoVAT = document.createElement("td");
    const tdAllOnTreatyWithVAT = document.createElement("td");
    const tdPlanNoVAT = document.createElement("td");
    const tdPlanWithVAT = document.createElement("td");
    const tdBalanceNoVAT = document.createElement("td");
    const tdBalanceWithVAT = document.createElement("td");
    let rowspanSign =
      index === 0
        ? array.filter((elem) => elem.article_id === element.article_id).length
        : array[index - 1].article_id === element.article_id
        ? 0
        : array.filter((elem) => elem.article_id === element.article_id).length;
    tdArticle.innerText =
      index === 0
        ? element.article_name
        : array[index - 1].article_id === element.article_id
        ? ""
        : element.article_name;
    tdContract.innerText = element.contract_number;
    tdFactNoVAT.innerText = (element.sum_facts_no_vat / 1000).toFixed(5);
    tdFactWithVAT.innerText = (element.sum_facts_with_vat / 1000).toFixed(5);
    tdAmount.innerText = Number.isInteger(element.sum_amount) ? element.sum_amount : element.sum_amount.toFixed(1);
    tdSumActNoVAT.innerText = (element.sum_acts / 1000).toFixed(5);
    tdSumActWithVAT.innerText = ((element.sum_acts * 1.2) / 1000).toFixed(5);
    // let allSumTreatyNoVAT = element.sum_treaty / 1000;
    // let allSumTreatyWithVAT = (element.sum_treaty * 1.2) / 1000;
    console.log("kek");
    let allSumTreatyNoVAT =
      array
        .filter((elem) => elem.article_id === element.article_id)
        .reduce(function (sum, element) {
          return sum + element.sum_acts;
        }, 0) / 1000;
    let allSumTreatyWithVAT =
      (array
        .filter((elem) => elem.article_id === element.article_id)
        .reduce(function (sum, element) {
          return sum + element.sum_acts;
        }, 0) *
        1.2) /
      1000;
    tdAllOnTreatyNoVAT.innerText = allSumTreatyNoVAT.toFixed(5);
    tdAllOnTreatyWithVAT.innerText = allSumTreatyWithVAT.toFixed(5);
    tdPlanNoVAT.innerText = parseFloat(element.plan_no_val).toFixed(5);
    tdPlanWithVAT.innerText = parseFloat(element.plan_with_val).toFixed(5);
    tdBalanceNoVAT.innerText = (
      parseFloat(element.plan_no_val) - allSumTreatyNoVAT
    ).toFixed(5);
    tdBalanceWithVAT.innerText = (
      parseFloat(element.plan_with_val) -
      1.2 * allSumTreatyNoVAT
    ).toFixed(5);

    if (rowspanSign > 1) {
      tdFactNoVAT.rowSpan = rowspanSign;
      tdFactWithVAT.rowSpan = rowspanSign;
      tdAllOnTreatyNoVAT.rowSpan = rowspanSign;
      tdAllOnTreatyWithVAT.rowSpan = rowspanSign;
      tdPlanNoVAT.rowSpan = rowspanSign;
      tdPlanWithVAT.rowSpan = rowspanSign;
      tdBalanceNoVAT.rowSpan = rowspanSign;
      tdBalanceWithVAT.rowSpan = rowspanSign;
      if (array.length === index + rowspanSign) {
        tdFactNoVAT.classList.add("common-border__td");
        tdFactWithVAT.classList.add("common-border__td");
        tdAllOnTreatyNoVAT.classList.add("common-border__td");
        tdAllOnTreatyWithVAT.classList.add("common-border__td");
        tdPlanNoVAT.classList.add("common-border__td");
        tdPlanWithVAT.classList.add("common-border__td");
        tdBalanceNoVAT.classList.add("common-border__td");
        tdBalanceWithVAT.classList.add("common-border__td");
      }
    }

    tr.appendChild(tdArticle);
    tr.appendChild(tdContract);
    tr.appendChild(tdDetailReport);
    if (rowspanSign !== 0) {
      tr.appendChild(tdFactNoVAT);
      tr.appendChild(tdFactWithVAT);
    }
    tr.appendChild(tdAmount);
    tr.appendChild(tdSumActNoVAT);
    tr.appendChild(tdSumActWithVAT);
    if (rowspanSign !== 0) {
      tr.appendChild(tdAllOnTreatyNoVAT);
      tr.appendChild(tdAllOnTreatyWithVAT);
      tr.appendChild(tdPlanNoVAT);
      tr.appendChild(tdPlanWithVAT);
      tr.appendChild(tdBalanceNoVAT);
      tr.appendChild(tdBalanceWithVAT);
    }
    tbody.appendChild(tr);
  });
};

const selectRedefineStyle = () => {
  const selectResults = document.getElementsByClassName(
    "select2 select2-container select2-container--default"
  );
  for (let i = 0; i < selectResults.length; i++) {
    selectResults[i].style.width = "";
  }
};

const clearFiltersReloadOnClick = () => {
  window.localStorage.removeItem("common-report-state");
  document.location.reload();
};

const clearAllFiltersReloadOnClick = () => {
  window.localStorage.clear();
  document.location.reload();
};

const arrowBackOnClick = () => {
  window.location.replace("../fact-of-articles/");
};

const inputStartMonthChange = (target) => {
  const endDateInput = document.getElementById("end-date-id");
  deleteTableContent(document.getElementById("subsection-info__table__tbody"));
  deleteTableContent(document.getElementById("main-report__table__tbody"));
  state.inputStartMonth = document.getElementById("start-date-id").value;
  window.localStorage.setItem(
    "common-report-state",
    JSON.stringify({
      startMonth: state.inputStartMonth,
      endMonth: state.inputEndMonth,
    })
  );
  if (target.value === "") {
    endDateInput.disabled = true;
    endDateInput.value = null;
  } else endDateInput.disabled = false;
  endDateInput.min = target.value.substr(0, 4) + "-01";
  endDateInput.max = target.value.substr(0, 4) + "-12";
  endDateInput.value =
    target.value.substr(0, 4) + "-" + endDateInput.value.substr(5, 2);
  subsectionDataRequest();
};

const inputEndMonthChange = (target) => {
  deleteTableContent(document.getElementById("subsection-info__table__tbody"));
  deleteTableContent(document.getElementById("main-report__table__tbody"));
  state.inputEndMonth = document.getElementById("end-date-id").value;
  window.localStorage.setItem(
    "common-report-state",
    JSON.stringify({
      startMonth: state.inputStartMonth,
      endMonth: state.inputEndMonth,
    })
  );
  subsectionDataRequest();
};

const deleteTableContent = (table) => {
  while (table.firstChild) table.removeChild(table.firstChild);
  state.subsectionData = [];
  state.mainTableData = [];
};
