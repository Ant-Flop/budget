const modalSearchInRegistatBanksState = {
  newCodeIdSelected: null,
  newCodesData: [],
  startDate: null,
  endDate: null,
  yearSelected: null,
  sortInfo: {
    orderColumn: "id",
    orderStatus: "ASC",
    target: null,
    switcher: false,
  },
  sum: 0,
};

const modalSearchInRegistatBanksOnClick = () => {
  const startDateInput = document.getElementById(
    "modal-search-bank-register-start-date__input"
  );
  const endDateInput = document.getElementById(
    "modal-search-bank-register-end-date__input"
  );
  modalSearchInRegistatBanksState.yearSelected = parseInt(state.yearSelected);
  startDateInput.min = modalSearchInRegistatBanksState.yearSelected + "-01-01";
  startDateInput.max = modalSearchInRegistatBanksState.yearSelected + "-12-31";
  endDateInput.min = modalSearchInRegistatBanksState.yearSelected + "-01-01";
  endDateInput.max = modalSearchInRegistatBanksState.yearSelected + "-12-31";
  startDateInput.value =
    modalSearchInRegistatBanksState.yearSelected + "-01-01";
  endDateInput.value = modalSearchInRegistatBanksState.yearSelected + "-12-31";
  getNewCodesRequest(modalSearchInRegistatBanksState, () => {
    modalFillNewCodesSelect();
    const modalWindow = document.getElementById(
      "modal-window-search-bank-register"
    );
    modalWindow.style.display = "block";
    modalSearchBankRegisterRequest();
  });
};

const getNewCodesRequest = (paramState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getNewCodesRequest",
    })
  );
  xhr.onload = () => {
    paramState.newCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const modalFillNewCodesSelect = () => {
  modalRerenderNewCodesSelect();
  const select = document.getElementById(
    "modal-search-bank-register-new-code__select"
  );
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected =
    modalSearchInRegistatBanksState.newCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (modalSearchInRegistatBanksState.newCodesData.length === 0) return;
  modalSearchInRegistatBanksState.newCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.new_code;
    optionSelect.innerText = element.new_code;
    optionSelect.setAttribute("data-id", element.id);
    optionSelect.setAttribute("data-new-code", element.new_code);
    if (modalSearchInRegistatBanksState.newCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const modalRerenderNewCodesSelect = () => {
  const field = document.getElementById(
    "modal-search-bank-register-new-code-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-search-bank-register-new-code__select' class='new-code-select' title='Новий код'></select>"
  )
    .appendTo(field)
    .select2();
  [...document.getElementsByClassName("select2-container")].forEach(
    (element) => (element.style.width = "0")
  );
  const select = document.getElementById(
    "modal-search-bank-register-new-code__select"
  );
  select.onchange = modalNewCodesSelectOnChange.bind(this, select);
};

const modalNewCodesSelectOnChange = (target) => {
  modalSearchInRegistatBanksState.newCodeIdSelected =
    target[target.selectedIndex].getAttribute("data-id");
  modalSearchBankRegisterRequest();
};

const modalStartDateOnChange = (target) => {
  modalSearchInRegistatBanksState.startDate = target.value;
  modalSearchBankRegisterRequest();
};

const modalEndDateOnChange = (target) => {
  modalSearchInRegistatBanksState.endDate = target.value;
  modalSearchBankRegisterRequest();
};

const modalSearchBankRegisterRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);

  xhr.send(
    JSON.stringify({
      typeRequest: "modalSearchBankRegisterRequest",
      newCodeId: parseInt(modalSearchInRegistatBanksState.newCodeIdSelected),
      startDate:
        modalSearchInRegistatBanksState.startDate === null
          ? modalSearchInRegistatBanksState.yearSelected + "-01-" + "01"
          : modalSearchInRegistatBanksState.startDate,
      endDate:
        modalSearchInRegistatBanksState.endDate === null
          ? modalSearchInRegistatBanksState.yearSelected + "-12-" + "31"
          : modalSearchInRegistatBanksState.endDate,
      orderColumn: modalSearchInRegistatBanksState.sortInfo.orderColumn,
      orderStatus: modalSearchInRegistatBanksState.sortInfo.orderStatus,
    })
  );
  xhr.onload = () => {
    document.querySelector("#modal-window-search__table").innerHTML =
      xhr.response;

    [...document.getElementsByClassName("modal-sort-th")].forEach((element) =>
      element.addEventListener("click", (event) =>
        modalGetSortTableByColumn(event.target)
      )
    );

    if (modalSearchInRegistatBanksState.sortInfo.target !== null) {
      const th = document.getElementById(
        modalSearchInRegistatBanksState.sortInfo.target.id
      );
      th.style.background = "#c0cce9";
      th.innerHTML =
        modalSearchInRegistatBanksState.sortInfo.orderStatus === "ASC"
          ? th.innerText + " <i class='fa fa-caret-up'></i>"
          : th.innerText + " <i class='fa fa-caret-down'></i>";
    }

    const tableRows = [
      ...document.querySelectorAll(
        "#modal-window-search-banks-register-table > tbody > tr"
      ),
    ];

    tableRows.forEach((row) => {
      row.addEventListener("click", () => {
        [...document.getElementsByClassName("modal-selected-td")].forEach(
          (element) => element.classList.remove("modal-selected-td")
        );
        [...row.getElementsByTagName("td")].forEach((element) =>
          element.classList.add("modal-selected-td")
        );
      });
    });

    const sumOfPay = [
      ...document.getElementsByClassName("modal-table-column-sum"),
    ];
    modalSearchInRegistatBanksState.sum = 0;
    sumOfPay.forEach((element, index) => {
      if (index > 0) {
        modalSearchInRegistatBanksState.sum += parseFloat(
          element.innerText.replace(/ /g, "")
        );
      }
    });
    const sumInput = document.getElementById(
      "modal-search-bank-register-sum__input"
    );

    sumInput.value = (modalSearchInRegistatBanksState.sum / 1000).toFixed(5);
  };
};

const modalSearchBankRegisterCloseOnClick = () => {
  const modalWindow = document.getElementById(
    "modal-window-search-bank-register"
  );
  modalWindow.style.display = "none";
};

const modalGetSortTableByColumn = (target) => {
  const thead = document.getElementById("modal-main-table-thead");
  let sortData = thead.getAttribute("data-sort").split(" ");
  let orderColumn = "";
  let orderStatus = "";

  if (
    modalSearchInRegistatBanksState.sortInfo.orderColumn ===
    target.getAttribute("data-column")
  ) {
    orderColumn = target.getAttribute("data-column");
    console.log(orderStatus);
    orderStatus =
      modalSearchInRegistatBanksState.sortInfo.orderStatus === "ASC"
        ? "DESC"
        : "ASC";
    console.log(orderStatus);
  } else {
    orderColumn = target.getAttribute("data-column");
    orderStatus = "ASC";
  }
  if (target.classList.contains("modal-sort-th")) {
    console.log(orderColumn, orderStatus);
    modalSearchInRegistatBanksState.sortInfo.orderColumn = orderColumn;
    modalSearchInRegistatBanksState.sortInfo.orderStatus = orderStatus;
    modalSearchInRegistatBanksState.sortInfo.target = target;
    modalSearchBankRegisterRequest();
  }
};
