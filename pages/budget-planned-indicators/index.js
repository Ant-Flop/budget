const state = {
  userInfo: [],
  fundholderIdSelected: null,
  yearSelected: new Date().getFullYear(),
  sortInfo: {
    orderColumn: "a.id",
    orderStatus: "ASC",
    target: null,
    switcher: false,
  },
};

window.localStorage.removeItem("modalSumPlannedIndicatorState");

document.addEventListener("DOMContentLoaded", () => {
  getUserInfoRequest(() => {
    state.fundholderIdSelected = state.userInfo.fundholder_id;
    if (localStorage.getItem("budgetPlanfilterInfo")) {
      const filterInfo = JSON.parse(
        localStorage.getItem("budgetPlanfilterInfo")
      );
      state.yearSelected = parseInt(filterInfo.year);
      state.fundholderIdSelected = filterInfo.fundholderId;
    }

    renderTableRequest();
  });
});

const getSortTableByColumn = (target) => {
  const thead = document.getElementById("main-table-thead");
  let sortData = thead.getAttribute("data-sort").split(" ");
  let orderColumn = sortData[0];
  let orderStatus = sortData[1];
  if (orderColumn === target.getAttribute("data-column")) {
    orderColumn = target.getAttribute("data-column");
    orderStatus = orderStatus === "ASC" ? "DESC" : "ASC";
  } else {
    orderColumn = target.getAttribute("data-column");
    orderStatus = "ASC";
  }
  if (target.classList.contains("sort-th")) {
    state.sortInfo.orderColumn = orderColumn;
    state.sortInfo.orderStatus = orderStatus;
    state.sortInfo.target = target;
    renderTableRequest();
  }
};

const getUserInfoRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
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

const disabledAddButton = () => {
  const addButton = document.getElementsByClassName("above-table-add__button");
  if (addButton.length > 0) {
    if (state.yearSelected !== new Date().getFullYear()) {
      addButton[0].disabled = true;
      addButton[0].classList.add("disabled");
    }
  }
};

const renderTableRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      year: state.yearSelected,
      fundholderId: state.fundholderIdSelected,
      orderColumn: state.sortInfo.orderColumn,
      orderStatus: state.sortInfo.orderStatus,
    })
  );
  xhr.onload = function () {
    disabledAddButton();
    document.querySelector("#main-table").innerHTML = xhr.response;
    [...document.getElementsByClassName("sort-th")].forEach((element) =>
      element.addEventListener("click", (event) =>
        getSortTableByColumn(event.target)
      )
    );
    if (state.sortInfo.target !== null) {
      const th = document.getElementById(state.sortInfo.target.id);
      th.style.background = "#c0cce9";
      th.innerHTML =
        state.sortInfo.orderStatus === "ASC"
          ? th.innerText + " <i class='fa fa-caret-up'></i>"
          : th.innerText + " <i class='fa fa-caret-down'></i>";
    }
    if (state.userInfo.role.director_role) {
      document.getElementById("switch").checked = state.sortInfo.switcher;
      [...document.getElementsByClassName("add-column")].forEach((element) => {
        element.hidden = !state.sortInfo.switcher;
      });
    }
    setScrollTable();
  };
};

const getMainSectionsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getMainSectionsRequest",
    })
  );
  xhr.onload = () => {
    modalState.mainSectionsData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getSectionsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getSectionsRequest",
      mainSectionId: modalState.mainSectionIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.sectionsData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getSubsectionsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getSubsectionsRequest",
      sectionId: modalState.sectionIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.subsectionsData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getServicesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getServicesRequest",
      subsectionId: modalState.subsectionIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.servicesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getArticlesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getArticlesRequest",
      subsectionId: modalState.subsectionIdSelected,
      serviceId: modalState.serviceIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.articlesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getCounterpartiesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getCounterpartiesRequest",
    })
  );
  xhr.onload = () => {
    modalState.counterpartiesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getPlannedIndicatorRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getPlannedIndicatorRequest",
      id: modalState.plannedIndicatorId,
    })
  );
  xhr.onload = () => {
    modalState.plannedIndicatorData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getOldCodesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getOldCodesRequest",
    })
  );
  xhr.onload = () => {
    modalState.oldCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getClearContractsRequest = (modalState, callbackFunction) => {
  //console.log(modalState.counterpartyIdSelected)
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getClearContractsRequest",
      counterpartyId: modalState.counterpartyIdSelected,
    })
  );
  xhr.onload = () => {
    const response = JSON.parse(xhr.response);
    
    modalState.contractsData = response;
    callbackFunction();
  };
};

const getContractsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getContractsRequest",
      counterpartyId: modalState.counterpartyIdSelected,
      plannedIndicatorId: modalState.plannedIndicatorId,
    })
  );
  xhr.onload = () => {
    const response = JSON.parse(xhr.response);
    modalState.contractsData = response.contract_array.length === 0 ? [] : response.contract_array;
    modalState.contractsIdSelected = response.exceptions_array.length === 0 ? [] : response.exceptions_array;

    //modalState.newCodeIdSelected = response.new_code_id;
    callbackFunction();
  };
};

const getNewCodesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getNewCodesRequest",
      lowLimitSymbolsNewCode: modalState.lowLimitSymbolsNewCode,
      oldCodeId: modalState.oldCodeIdSelected,
      newCodeId: modalState.plannedIndicatorData.new_code_id,
      id: modalState.plannedIndicatorId,
    })
  );
  xhr.onload = () => {
    modalState.newCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const displayAdditionalColumn = () => {
  const addColumns = document.getElementsByClassName("add-column");
  state.sortInfo.switcher = !state.sortInfo.switcher;
  [...addColumns].forEach((element) => {
    element.hidden = !element.hidden;
  });
};

const setScrollTable = () => {
  const table = document.getElementById("main-table");
  table.scroll(0, 1);
  table.scroll(0, table.scrollHeight);
};
