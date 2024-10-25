const state = {
  fundholdersData: [],
  fundholderIdSelected: null,
  yearsData: [],
  yearSelected: new Date().getFullYear(),
  userInfo: [],
  scrollInfo: [
    {
      width: null,
      height: null,
    },
  ],
  infoTableData: [],
  infoEditWritingOfCost: {
    id: null,
    value: null,
    nameColumn: null,
  },
};

document.addEventListener("DOMContentLoaded", () => {
  const table = document.getElementById("main-table");
  table.addEventListener("scroll", (event) => {
    getScrollTable();
  });
  getUserInfoRequest(() => {
    getPlanIndicatorsYearsInfoRequest(() => {
      getFundholdersRequest(() => {
        state.fundholderIdSelected = state.userInfo.fundholder_id;
        state.yearSelected = new Date().getFullYear();
        if (localStorage.getItem("budgetPlanfilterInfo")) {
          const filterInfo = JSON.parse(
            localStorage.getItem("budgetPlanfilterInfo")
          );
          state.fundholderIdSelected = filterInfo.fundholderId;
          state.yearSelected = state.yearsData.includes(
            parseInt(filterInfo.year)
          )
            ? parseInt(filterInfo.year)
            : parseInt(state.yearsData[0]);
          localStorage.setItem(
            "budgetPlanfilterInfo",
            JSON.stringify({
              year: state.yearSelected,
              fundholderId: state.fundholderIdSelected,
            })
          );
        }
        fillBudgetPlanImplementationYearSelect();
        if (state.userInfo.role.financier_role)
          fillBudgetPlanImplementationFundholderSelect();
        switchPanelAddListener();
        renderTableRequest();
      });
    });
  });
});

const getPlanIndicatorsYearsInfoRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getPlanIndicatorsYearsInfoRequest",
      fundholderId: state.userInfo.fundholder_id,
    })
  );
  xhr.onload = function () {
    state.yearsData = JSON.parse(xhr.response);
    state.yearsData = state.yearsData.map((element) => parseInt(element));
    if (state.yearsData.length === 0)
      state.yearsData = [new Date().getFullYear()];
    callbackFunction();
  };
};

const getUserInfoRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getUserInfoRequest",
    })
  );
  xhr.onload = function () {
    state.userInfo = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const fillBudgetPlanImplementationFundholderSelect = () => {
  const select = document.getElementById(
    "budget-plan-implementation-fundholders__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  if (state.fundholdersData.length === 0) return;
  state.fundholdersData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.additional_name;
    optionSelect.innerText = element.additional_name;
    optionSelect.setAttribute("data-id", element.id);
    if (state.fundholderIdSelected == element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const budgetPlanImplementationFundholderSelectOnChange = () => {
  const select = document.getElementById(
    "budget-plan-implementation-fundholders__select"
  );
  state.fundholderIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  localStorage.setItem(
    "budgetPlanfilterInfo",
    JSON.stringify({
      year: state.yearSelected,
      fundholderId: state.fundholderIdSelected,
    })
  );
  renderTableRequest();
};

const fillBudgetPlanImplementationYearSelect = () => {
  const select = document.getElementById(
    "budget-plan-implementation-years__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  if (state.yearsData.length === 0) return;
  state.yearsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element;
    optionSelect.innerText = element;
    optionSelect.setAttribute("data-year", element);
    if (state.yearSelected == element) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const budgetPlanImplementationYearSelectOnChange = () => {
  const select = document.getElementById(
    "budget-plan-implementation-years__select"
  );
  state.yearSelected = select[select.selectedIndex].getAttribute("data-year");
  localStorage.setItem(
    "budgetPlanfilterInfo",
    JSON.stringify({
      year: state.yearSelected,
      fundholderId: state.fundholderIdSelected,
    })
  );
  localStorage.removeItem("budget-writing-off-costs");
  renderTableRequest();
};

const renderTableRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      year: state.yearSelected,
      fundholderId: state.fundholderIdSelected,
    })
  );
  xhr.onload = function () {
    document.querySelector("#main-table").innerHTML = xhr.response;
    setScrollTable();
    stickyTableColumn();
    hideColumnBySwitch();
  };
};

const getFundholdersRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getFundholdersRequest",
    })
  );
  xhr.onload = () => {
    state.fundholdersData = JSON.parse(xhr.response);
    callbackFunction();
  };
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
      name: ".table-column-actions",
      direction: "left",
    },
    {
      name: "thead .table-column-actions",
      direction: "top",
    },
    {
      name: ".table-column-new-code",
      direction: "left",
    },
    {
      name: "thead .table-column-new-code",
      direction: "top",
    },
    {
      name: ".table-column-fundholder",
      direction: "left",
    },
    {
      name: "thead .table-column-fundholder",
      direction: "top",
    },
    {
      name: ".table-column-service",
      direction: "left",
    },
    {
      name: "thead .table-column-service",
      direction: "top",
    },
    {
      name: ".table-column-article-name",
      direction: "left",
    },
    {
      name: "thead .table-column-article-name",
      direction: "top",
    },
    {
      name: ".table-column-counterparty",
      direction: "left",
    },
    {
      name: "thead .table-column-counterparty",
      direction: "top",
    },
    {
      name: ".main-section__table",
      direction: "left",
    },
    {
      name: ".section__table",
      direction: "left",
    },
    {
      name: ".subsection__table",
      direction: "left",
    },
    {
      name: ".id__table",
      direction: "left",
    },
    {
      name: ".edit__table",
      direction: "left",
    },
    {
      name: ".article-code__table",
      direction: "left",
    },
    {
      name: ".fundholder__table",
      direction: "left",
    },
    {
      name: ".service__table",
      direction: "left",
    },
    {
      name: ".article-name__table",
      direction: "left",
    },
    {
      name: ".counterparty__table",
      direction: "left",
    },
  ];
  const table = document.getElementById("main-table");
  $("#main-table").scroll(function () {
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
      state.scrollInfo.width = table.scrollLeft;
      state.scrollInfo.heght = table.scrollHeight;
    });
  });
};

const switchPanelAddListener = () => {
  const switchPanel = document.getElementById("switch-panel");
  switchPanel.addEventListener("change", (event) => {
    switchColumnOnChange(event.target.dataset.columnClass);
  });
};

const switchColumnOnChange = (target) => {
  const columnClass = document.getElementsByClassName(target);
  [...columnClass].forEach((element) => {
    element.hidden = !element.hidden;
  });
};

const editWritingOffCostsInputOnClick = (target) => {
  state.infoEditWritingOfCost.id = target.getAttribute("data-id");
  state.infoEditWritingOfCost.value = target.value;
  state.infoEditWritingOfCost.nameColumn =
    target.getAttribute("data-month-info");
  editWritingOffCostsSaveRequest();
};

const editWritingOffCostsSaveRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "editWritingOffCostsSaveRequest",
      id: state.infoEditWritingOfCost.id,
      value: state.infoEditWritingOfCost.value,
      nameColumn: state.infoEditWritingOfCost.nameColumn,
    })
  );
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
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};

const getScrollTable = () => {
  const table = document.getElementById("main-table");
  state.scrollInfo.height = table.scrollTop;
};

const setScrollTable = () => {
  const table = document.getElementById("main-table");
  const th = table
    .querySelector("thead")
    .getElementsByClassName("sticky-table-column");
  table.scrollTop = state.scrollInfo.height;
  [...th].map((element) => {
    element.style.top = state.scrollInfo.height + "px";
  });
};

const hideColumnBySwitch = () => {
  const switchInput = [
    ...document.getElementsByClassName("switch-element__input"),
  ];
  switchInput.forEach((elementSwitch) => {
    const className =
      elementSwitch.attributes.getNamedItem("data-column-class").value;
    console.log(elementSwitch.checked);
    [...document.getElementsByClassName(className)].forEach((elementColumn) => {
      elementColumn.hidden = !elementSwitch.checked;
    });
  });
};
