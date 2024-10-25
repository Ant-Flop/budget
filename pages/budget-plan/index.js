const state = {
  fundholdersData: [],
  fundholderIdSelected: null,
  yearsData: [],
  yearSelected: new Date().getFullYear(),
  userInfo: [],
  scrollInfo: {
    width: null,
    height: null,
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
        } else {
          localStorage.setItem(
            "budgetPlanfilterInfo",
            JSON.stringify({
              year: state.yearSelected,
              fundholderId: state.fundholderIdSelected,
            })
          );
        }
        fillBudgetPlanYearSelect();
        if (state.userInfo.role.financier_role)
          fillBudgetPlanFundholderSelect();
        switchPanelAddListener();
        renderTableRequest();
      });
    });
  });
});

const getPlanIndicatorsYearsInfoRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getPlanIndicatorsYearsInfoRequest",
      fundholderId: state.userInfo.fundholder_id,
    })
  );
  xhr.onload = function () {
    state.yearsData = JSON.parse(xhr.response);
    console.log(state.yearsData);
    state.yearsData = state.yearsData.map((element) => parseInt(element));
    if (state.yearsData.length === 0)
      state.yearsData = [new Date().getFullYear()];
    callbackFunction();
  };
};

const getUserInfoRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan.php";
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

const fillBudgetPlanFundholderSelect = () => {
  const select = document.getElementById("budget-plan-fundholders__select");
  while (select.firstChild) select.removeChild(select.firstChild);
  if (state.fundholdersData.length === 0) return;
  state.fundholdersData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.additional_name;
    optionSelect.innerText = element.additional_name;
    optionSelect.setAttribute("data-id", element.id);
    if (state.fundholderIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const budgetPlanFundholderSelectOnChange = () => {
  const select = document.getElementById("budget-plan-fundholders__select");
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

const fillBudgetPlanYearSelect = () => {
  const select = document.getElementById("budget-plan-years__select");
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

const budgetPlanYearSelectOnChange = () => {
  const select = document.getElementById("budget-plan-years__select");
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
  const requestURL = "budget-plan.php";
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
  const requestURL = "budget-plan.php";
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
    });
  });
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

const switchPanelAddListener = () => {
  const switchPanel = document.getElementById("switch-panel");
  switchPanel.addEventListener("change", (event) => {
    switchColumnOnChange(event.target.dataset.columnClass);
  });
};

const switchColumnOnChange = (target) => {
  const columnClass = document.getElementsByClassName(target);
  console.log(target);
  [...columnClass].forEach((element) => {
    element.hidden = !element.hidden;
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
