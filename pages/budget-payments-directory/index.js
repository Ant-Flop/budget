const state = {
  paymentsDirectoryInfo: {
    iban: null,
    date: null,
  },
  sortInfo: {},
  paginationInfo: {
    amountPayments: null,
    startLimitPayments: 0,
    limitPaymentsPagin: 14,
    amountPagins: null,
    currentPagin: 1,
  },
  oldCodesData: [],
  oldCodeIdSelected: null,
  oldCodeSelected: null,
  newCodesData: [],
  newCodeIdSelected: null,
  newCodeSelected: null,
  paymentsBatchProcessingInfo: {
    paymentsArray: [],
    signChecker: false,
    amountCheckedElement: null,
  },
};

const DATE = new Date();

document.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("paymentsDirectoryInfo"))
    state.paymentsDirectoryInfo = JSON.parse(
      localStorage.getItem("paymentsDirectoryInfo")
    );
  getOldCodesRequest(state, () => {
    fillOldCodesBatchProcessingSelect();
  });
  getNewCodesRequest(state, () => {
    fillNewCodesBatchProcessingSelect();
  });
  renderTableRequest();
  createPagination();
});

const renderTableRequest = () => {
  const spinner = document.getElementById("spinner-loader-id");
  spinner.style.visibility = "visible";
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      iban: state.paymentsDirectoryInfo.iban,
      date: state.paymentsDirectoryInfo.date,
      limit:
        "LIMIT " +
        state.paginationInfo.startLimitPayments +
        ", " +
        state.paginationInfo.limitPaymentsPagin,
    })
  );
  xhr.onload = function () {
    disabledBatchProcessing();
    document.querySelector("#main-table").innerHTML = xhr.response;
    $("select").select2();
    [...document.getElementsByClassName("select2 select2-container")].forEach(
      (element) => {
        element.style.width = "0";
      }
    );
    const spinner = document.getElementById("spinner-loader-id");
    spinner.style.visibility = "hidden";
  };
};

const createPagination = () => {
  getAmountRecordsPaymentsRequest(() => {
    const paginator = document.getElementsByClassName("paginator")[0];
    for (let i = 0; i < state.paginationInfo.amountPagins + 2; i++) {
      const paginatorItem = document.createElement("div");
      paginatorItem.classList.add("paginator-item");
      const button = document.createElement("button");
      if (i === state.paginationInfo.currentPagin)
        button.classList.add("paginator-item-active");
      if (i === 0) {
        button.innerText = "«";
        button.onclick = () => {
          if (state.paginationInfo.currentPagin > 1) {
            const previousActiveItem = document.getElementsByClassName(
              "paginator-item-active"
            )[0];
            previousActiveItem.classList.remove("paginator-item-active");
            state.paginationInfo.currentPagin -= 1;
            previousActiveItem.parentNode.previousElementSibling.lastElementChild.classList.add(
              "paginator-item-active"
            );
            state.paginationInfo.startLimitPayments =
              state.paginationInfo.currentPagin *
                state.paginationInfo.limitPaymentsPagin -
              state.paginationInfo.limitPaymentsPagin;
            disabledBatchProcessing();
            renderTableRequest();
          }
        };
      } else if (i > state.paginationInfo.amountPagins) {
        button.innerText = "»";
        button.onclick = () => {
          if (
            state.paginationInfo.currentPagin <
            state.paginationInfo.amountPagins
          ) {
            const previousActiveItem = document.getElementsByClassName(
              "paginator-item-active"
            )[0];
            previousActiveItem.classList.remove("paginator-item-active");
            state.paginationInfo.currentPagin += 1;
            previousActiveItem.parentNode.nextElementSibling.lastElementChild.classList.add(
              "paginator-item-active"
            );
            state.paginationInfo.startLimitPayments =
              state.paginationInfo.currentPagin *
                state.paginationInfo.limitPaymentsPagin -
              state.paginationInfo.limitPaymentsPagin;
            disabledBatchProcessing();
            renderTableRequest();
          }
        };
      } else {
        button.innerText = i;
        button.onclick = () => {
          const previousActiveItem = document.getElementsByClassName(
            "paginator-item-active"
          )[0];
          previousActiveItem.classList.remove("paginator-item-active");
          state.paginationInfo.currentPagin = i;
          button.classList.add("paginator-item-active");
          state.paginationInfo.startLimitPayments =
            state.paginationInfo.currentPagin *
              state.paginationInfo.limitPaymentsPagin -
            state.paginationInfo.limitPaymentsPagin;
          disabledBatchProcessing();
          renderTableRequest();
        };
      }
      paginatorItem.appendChild(button);
      paginator.appendChild(paginatorItem);
    }
  });
};

const getAmountRecordsPaymentsRequest = (callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getAmountRecordsPaymentsRequest",
      iban: state.paymentsDirectoryInfo.iban,
      date: state.paymentsDirectoryInfo.date,
      limit: null,
    })
  );
  xhr.onload = () => {
    state.paginationInfo.amountPayments = JSON.parse(xhr.response);
    state.paginationInfo.amountPagins = Math.ceil(
      state.paginationInfo.amountPayments /
        state.paginationInfo.limitPaymentsPagin
    );
    callbackFunction();
  };
};

const oldCodeSelectOnChange = (target, previousNew) => {
  let paymentId = target.getAttribute("data-payment-id");
  let previousNewCodeId = target.getAttribute("data-previous-new-code-id");
  let oldCodeId = target[target.selectedIndex].getAttribute("data-old-code-id");
  let oldCode = target[target.selectedIndex].getAttribute("data-old-code");
  console.log(previousNew);
  oldCodeSaveRequest(paymentId, previousNewCodeId, oldCodeId, oldCode);
};

const oldCodeSaveRequest = (
  paymentId,
  previousNewCodeId,
  oldCodeId,
  oldCode
) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "oldCodesSaveRequest",
      paymentId: paymentId,
      previousNewCodeId: previousNewCodeId,
      oldCodeId: oldCodeId,
      oldCode: oldCode,
      iban: state.paymentsDirectoryInfo.iban,
      date: state.paymentsDirectoryInfo.date,
    })
  );
  xhr.onload = () => {
    console.log(xhr.response);
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

const newCodeSelectOnChange = (target, previousNew) => {
  let paymentId = target.getAttribute("data-payment-id");
  let previousNewCodeId = target.getAttribute("data-previous-new-code-id");
  let newCodeId = target[target.selectedIndex].getAttribute("data-new-code-id");
  let newCode = target[target.selectedIndex].getAttribute("data-new-code");
  let oldCode = target[target.selectedIndex].getAttribute("data-old-code");
  console.log(newCodeId, previousNew);

  newCodeSaveRequest(paymentId, previousNewCodeId, newCodeId, newCode, oldCode);
};

const newCodeSaveRequest = (
  paymentId,
  previousNewCodeId,
  newCodeId,
  newCode,
  oldCode
) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "newCodesSaveRequest",
      paymentId: paymentId,
      previousNewCodeId: previousNewCodeId,
      oldCode: oldCode,
      newCodeId: newCodeId,
      newCode: newCode,
      iban: state.paymentsDirectoryInfo.iban,
      date: state.paymentsDirectoryInfo.date,
    })
  );
  xhr.onload = () => {
    console.log("new - " + newCodeId, " previous - " + previousNewCodeId);
    console.log(xhr.response);
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

const getOldCodesRequest = (paramState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getOldCodesRequest",
    })
  );
  xhr.onload = () => {
    console.log(JSON.parse(xhr.response));
    paramState.oldCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const fillOldCodesBatchProcessingSelect = () => {
  const select = document.getElementById(
    "budget-payment-directory-old-code__select"
  );
  //select.disabled = state.paymentsDirectoryInfo.date.slice(0, 4) !== DATE.getFullYear().toString() ? true : false;
  select.onchange = oldCodesBatchProcessingSelectOnChange.bind(this, select);
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected = state.oldCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (state.oldCodesData.length === 0) return;
  console.log(state.oldCodesData);
  state.oldCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.old_code;
    optionSelect.innerText = element.old_code;
    optionSelect.setAttribute("data-id", element.id);
    optionSelect.setAttribute("data-old-code", element.old_code);
    if (state.oldCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const oldCodesBatchProcessingSelectOnChange = (target) => {
  const batchProcessingButton = document.getElementById(
    "payments-batch-processing__button"
  );
  batchProcessingButton.classList.add("disabled");
  batchProcessingButton.disabled = true;
  state.oldCodeIdSelected =
    target[target.selectedIndex].getAttribute("data-id");
  state.oldCodeSelected =
    target[target.selectedIndex].getAttribute("data-old-code");
  state.newCodeIdSelected = null;
  state.newCodeSelected = null;
  if (state.paymentsBatchProcessingInfo.signChecker) {
    batchProcessingButton.classList.remove("disabled");
    batchProcessingButton.disabled = false;
  } else {
    batchProcessingButton.classList.add("disabled");
    batchProcessingButton.disabled = true;
  }
  getNewCodesRequest(state, () => {
    fillNewCodesBatchProcessingSelect();
  });
};

const getNewCodesRequest = (paramState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getNewCodesRequest",
      oldCodeId: paramState.oldCodeIdSelected,
    })
  );
  xhr.onload = () => {
    paramState.newCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const fillNewCodesBatchProcessingSelect = () => {
  rerenderNewCodesBatchProcessingSelect();
  const select = document.getElementById(
    "budget-payment-directory-new-code__select"
  );
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected = state.newCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (state.newCodesData.length === 0) return;
  state.newCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.new_code;
    optionSelect.innerText = element.new_code;
    optionSelect.setAttribute("data-id", element.id);
    optionSelect.setAttribute("data-new-code", element.new_code);
    if (state.newCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const rerenderNewCodesBatchProcessingSelect = () => {
  const field = document.getElementsByClassName(
    "budget-payment-directory-new-code-wrapper__select"
  )[0];
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='budget-payment-directory-new-code__select' class='new-code-select' title='Новий код'></select>"
  )
    .appendTo(field)
    .select2();
  [...document.getElementsByClassName("select2-container")].forEach(
    (element) => (element.style.width = "0")
  );
  const select = document.getElementById(
    "budget-payment-directory-new-code__select"
  );
  //select.disabled = state.paymentsDirectoryInfo.date.slice(0, 4) !== DATE.getFullYear().toString() ? true : false;
  select.onchange = newCodesBatchProcessingSelectOnChange.bind(this, select);
};

const newCodesBatchProcessingSelectOnChange = (target) => {
  state.newCodeIdSelected =
    target[target.selectedIndex].getAttribute("data-id");
  state.newCodeSelected =
    target[target.selectedIndex].getAttribute("data-new-code");
  const batchProcessingButton = document.getElementById(
    "payments-batch-processing__button"
  );
  if (state.paymentsBatchProcessingInfo.signChecker) {
    batchProcessingButton.classList.remove("disabled");
    batchProcessingButton.disabled = false;
  } else {
    batchProcessingButton.classList.add("disabled");
    batchProcessingButton.disabled = true;
  }
};

const batchProcessingCheckboxOnChange = (target, all) => {
  const checkboxes = document.getElementsByClassName(
    "batch-processing-checkbox"
  );
  const batchProcessingButton = document.getElementById(
    "payments-batch-processing__button"
  );
  state.paymentsBatchProcessingInfo.amountCheckedElement = null;
  [...checkboxes].forEach((element) => {
    element.checked =
      all && !element.disabled ? target.checked : element.checked;
    state.paymentsBatchProcessingInfo.amountCheckedElement += element.checked
      ? 1
      : 0;
  });

  state.paymentsBatchProcessingInfo.signChecker =
    state.paymentsBatchProcessingInfo.amountCheckedElement > 0 ? true : false;
  if (state.paymentsBatchProcessingInfo.signChecker) {
    batchProcessingButton.classList.remove("disabled");
    batchProcessingButton.disabled = false;
  } else {
    batchProcessingButton.classList.add("disabled");
    batchProcessingButton.disabled = true;
  }
};

const disabledBatchProcessing = () => {
  state.oldCodeSelected = null;
  state.oldCodeIdSelected = null;
  state.newCodeSelected = null;
  state.newCodeIdSelected = null;
  state.paymentsBatchProcessingInfo.paymentsArray = [];
  state.oldCodesData = [];
  state.newCodesData = [];
  state.paymentsBatchProcessingInfo.signChecker = false;
  const checkboxes = document.getElementsByClassName(
    "batch-processing-checkbox"
  );
  const batchProcessingButton = document.getElementById(
    "payments-batch-processing__button"
  );
  batchProcessingButton.classList.add("disabled");
  batchProcessingButton.disabled = true;
  [...checkboxes].forEach((element) => {
    element.checked = false;
  });
  getOldCodesRequest(state, () => {
    fillOldCodesBatchProcessingSelect();
  });
  getNewCodesRequest(state, () => {
    fillNewCodesBatchProcessingSelect();
  });
};

const paymentsBatchProcessingOnClick = () => {
  const checkboxes = document.getElementsByClassName(
    "batch-processing-checkbox"
  );
  [...checkboxes].forEach((element) => {
    if (element.checked) {
      state.paymentsBatchProcessingInfo.paymentsArray.push({
        paymentId: element.getAttribute("data-payment-id"),
        previousNewCodeId: element.getAttribute("data-previous-new-code-id"),
        previousNewCode: element.getAttribute("data-previous-new-code"),
        previousOldCode: element.getAttribute("data-previous-old-code"),
      });
    }
  });
  paymentsBatchProcessingRequest();
};

const paymentsBatchProcessingRequest = () => {
  const spinner = document.getElementById("spinner-loader-id");
  spinner.style.visibility = "visible";
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "paymentsBatchProcessingRequest",
      paymentArray: state.paymentsBatchProcessingInfo.paymentsArray,
      oldCodeId: state.oldCodeIdSelected,
      oldCode: state.oldCodeSelected,
      newCodeId: state.newCodeIdSelected,
      newCode: state.newCodeSelected,
    })
  );
  xhr.onload = () => {
    console.log(xhr.response);
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
    const batchProcessingButton = document.getElementById(
      "payments-batch-processing__button"
    );
    batchProcessingButton.classList.add("disabled");
    batchProcessingButton.disabled = true;
    state.paymentsBatchProcessingInfo.signChecker = false;
    state.paymentsBatchProcessingInfo;
    renderTableRequest();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};
