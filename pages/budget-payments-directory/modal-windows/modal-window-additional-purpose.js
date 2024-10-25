const modalState = {
  id: null,
  indexRow: null,
  paymentInfo: {},
  paymentBalance: null,
  oldCodesData: [],
  oldCodeIdSelected: null,
  oldCodeSelected: null,
  newCodesData: [],
  newCodeIdSelected: null,
  newCodeSelected: null,
  sum: null,
  createdPaymentsData: [],
  deletedPaymentsData: [],
};

const modalAdditionalPurposeOnClick = (id) => {
  modalState.id = id;
  modalGetPaymentInfoRequest();
};

const modalGetPaymentInfoRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalGetPaymentInfoRequest",
      id: modalState.id,
    })
  );
  xhr.onload = () => {
    modalState.paymentInfo = JSON.parse(xhr.response);
    getOldCodesRequest(modalState, () => {
      modalFillOldCodesSelect();
      getNewCodesRequest(modalState, () => {
        modalFillNewCodesSelect();
        modalGetAdditionalPurposeRequest(modalState, () => {
          let curSum =
            parseFloat(modalState.paymentInfo.sum) -
            modalState.createdPaymentsData.reduce(
              (previousElement, currentElement) =>
                previousElement + parseFloat(currentElement.sum),
              0
            );
          modalState.paymentBalance = (curSum < 0 ? 0 : curSum).toFixed(2);
          modalFillContent();
          const modalWindow = document.getElementById(
            "modal-window-additional-purpose"
          );
          modalWindow.style.display = "block";
        });
      });
    });
  };
};

const modalGetAdditionalPurposeRequest = (paramState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalGetAdditionalPurposeRequest",
      id: paramState.id,
    })
  );
  xhr.onload = () => {
    paramState.createdPaymentsData = JSON.parse(xhr.response);
    paramState.indexRow = paramState.createdPaymentsData.length;
    modalRerenderMainContent();
    callbackFunction();
  };
};

const modalRerenderMainContent = () => {
  const saveButton = document.getElementById("modal-button-save");
  if (
    modalState.createdPaymentsData.filter((element) => element.id === null)
      .length > 0 ||
    modalState.deletedPaymentsData.length > 0
  ) {
    saveButton.disabled = false;
    saveButton.classList.remove("disabled");
  } else {
    saveButton.disabled = true;
    saveButton.classList.add("disabled");
  }
  const modalMainContent =
    document.getElementsByClassName("modal-main-content")[0];
  while (modalMainContent.firstChild)
    modalMainContent.removeChild(modalMainContent.firstChild);

  modalState.createdPaymentsData.forEach((element) => {
    console.log(element);
    const modalMainContentItem = document.createElement("div");
    modalMainContentItem.classList.add("modal-main-content-item");
    modalMainContentItem.classList.add("modal-input");
    const deleteButtonWrapper = document.createElement("div");
    deleteButtonWrapper.classList.add("modal-tooltip-wrapper");
    const sumWrapper = document.createElement("div");
    sumWrapper.classList.add("modal-tooltip-wrapper");
    const paymentTypeWrapper = document.createElement("div");
    paymentTypeWrapper.classList.add("modal-tooltip-wrapper");
    const oldCodeWrapper = document.createElement("div");
    oldCodeWrapper.classList.add("modal-tooltip-wrapper");
    const newCodeWrapper = document.createElement("div");
    newCodeWrapper.classList.add("modal-tooltip-wrapper");
    const deleteButton = document.createElement("input");
    deleteButton.type = "image";
    deleteButton.src = "../../templates/images/delete.png";
    deleteButton.classList.add("action__td__input");
    deleteButton.onclick = modalDeleteButtonOnClick.bind(this, element);
    const sumInput = document.createElement("input");
    sumInput.value = element.sum;
    sumInput.classList.add("modal-main-content-sum__input");
    sumInput.classList.add("disabled");
    sumInput.disabled = true;
    const paymentTypeInput = document.createElement("input");
    paymentTypeInput.value = element.purpose;
    paymentTypeInput.classList.add("modal-main-content-purpose__input");
    paymentTypeInput.classList.add("disabled");
    paymentTypeInput.disabled = true;
    const oldCodeInput = document.createElement("input");
    oldCodeInput.value = element.old_code;
    oldCodeInput.classList.add("modal-main-content-old-code__input");
    oldCodeInput.classList.add("disabled");
    oldCodeInput.disabled = true;
    const newCodeInput = document.createElement("input");
    newCodeInput.value = element.new_code;
    newCodeInput.classList.add("modal-main-content-new-code__input");
    newCodeInput.classList.add("disabled");
    newCodeInput.disabled = true;
    deleteButtonWrapper.appendChild(deleteButton);
    sumWrapper.appendChild(sumInput);
    paymentTypeWrapper.appendChild(paymentTypeInput);
    oldCodeWrapper.appendChild(oldCodeInput);
    newCodeWrapper.appendChild(newCodeInput);
    modalMainContentItem.appendChild(deleteButtonWrapper);
    modalMainContentItem.appendChild(sumWrapper);
    modalMainContentItem.appendChild(paymentTypeWrapper);
    modalMainContentItem.appendChild(oldCodeWrapper);
    modalMainContentItem.appendChild(newCodeWrapper);
    modalMainContent.appendChild(modalMainContentItem);
  });
};

const modalDeleteButtonOnClick = (target) => {
  if (target.id !== null) modalState.deletedPaymentsData.push(target);
  modalState.paymentBalance = (
    parseFloat(modalState.paymentBalance) + parseFloat(target.sum)
  ).toFixed(2);
  modalFillContent();
  modalState.createdPaymentsData = modalState.createdPaymentsData.filter(
    (element) => {
      return element.id_row !== target.id_row;
    }
  );
  modalRerenderMainContent();
};

const modalSaveAdditionalPurposeOnClick = () => {
  modalSaveAdditionalPurposeRequest();
};

const modalSaveAdditionalPurposeRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-payments-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalSaveAdditionalPurposeRequest",
      deletedData: modalState.deletedPaymentsData,
      createdData: modalState.createdPaymentsData.filter((element) => {
        return element.id === null;
      }),
    })
  );
  xhr.onload = () => {
    console.log(xhr.response);
    let response = JSON.parse(xhr.response);
    const labelStatus = document.getElementById("modal-label-save-indicator");
    const backgroundStatus = document.getElementsByClassName(
      "modal-upper-save-panel"
    )[0];
    labelStatus.innerText = response.text;
    labelStatus.hidden = false;
    backgroundStatus.style.backgroundColor = response.status
      ? "#ebfbeb"
      : "#fbedeb";
    setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
    modalState.deletedPaymentsData = [];
    modalState.createdPaymentsData = [];
    modalGetAdditionalPurposeRequest(modalState, () => {});
    renderTableRequest();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus = document.getElementsByClassName(
      "modal-upper-save-panel"
    )[0];
    backgroundStatus.style.backgroundColor = "#e1eaf7";
  };
};

const modalFillOldCodesSelect = () => {
  modalRerenderOldCodesSelect();
  const select = document.getElementById("modal-old-code__select");
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected =
    modalState.oldCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (modalState.oldCodesData.length === 0) return;
  modalState.oldCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.old_code;
    optionSelect.innerText = element.old_code;
    optionSelect.setAttribute("data-id", element.id);
    optionSelect.setAttribute("data-old-code", element.old_code);
    if (modalState.oldCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const modalRerenderOldCodesSelect = () => {
  const field = document.getElementsByClassName(
    "modal-old-code-wrapper__select"
  )[0];
  while (field.firstChild) field.removeChild(field.firstChild);
  $("<select id='modal-old-code__select' title='Старий код'></select>")
    .appendTo(field)
    .select2();
  [...document.getElementsByClassName("select2-container")].forEach(
    (element) => (element.style.width = "0")
  );
  const select = document.getElementById("modal-old-code__select");
  select.onchange = modalOldCodesSelectOnChange.bind(this, select);
};

const modalOldCodesSelectOnChange = (target) => {
  modalState.oldCodeIdSelected =
    target[target.selectedIndex].getAttribute("data-id");
  modalState.oldCodeSelected =
    target[target.selectedIndex].getAttribute("data-old-code");
  modalState.newCodeIdSelected = null;
  modalState.newCodeSelected = null;
  getNewCodesRequest(modalState, () => {
    modalFillNewCodesSelect();
  });
};

const modalFillNewCodesSelect = () => {
  modalRerenderNewCodesSelect();
  const select = document.getElementById("modal-new-code__select");
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати";
  selectedOption.innerText = "Обрати";
  selectedOption.hidden = true;
  selectedOption.selected =
    modalState.newCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (modalState.newCodesData.length === 0) return;
  modalState.newCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.new_code;
    optionSelect.innerText = element.new_code;
    optionSelect.setAttribute("data-id", element.id);
    optionSelect.setAttribute("data-new-code", element.new_code);
    if (modalState.newCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const modalRerenderNewCodesSelect = () => {
  const field = document.getElementsByClassName(
    "modal-new-code-wrapper__select"
  )[0];
  while (field.firstChild) field.removeChild(field.firstChild);
  $("<select id='modal-new-code__select' title='Новий код'></select>")
    .appendTo(field)
    .select2();
  [...document.getElementsByClassName("select2-container")].forEach(
    (element) => (element.style.width = "0")
  );
  const select = document.getElementById("modal-new-code__select");
  select.onchange = modalNewCodesSelectOnChange.bind(this, select);
};

const modalRerenderSumInput = () => {
  const sumInput = document.getElementById("modal-sum__input");
  sumInput.value = modalState.sum;
};

const modalNewCodesSelectOnChange = (target) => {
  modalState.newCodeIdSelected =
    target[target.selectedIndex].getAttribute("data-id");
  modalState.newCodeSelected =
    target[target.selectedIndex].getAttribute("data-new-code");
};

const modalFillContent = () => {
  const modalHeader = document.getElementsByClassName(
    "modal-header-content"
  )[0];
  modalHeader.innerText =
    "Сума: " +
    modalState.paymentInfo.sum +
    "; Залишок: " +
    modalState.paymentBalance +
    "; Номер доручення: " +
    modalState.paymentInfo.oper_number +
    ";";
  const purposeInput = document.getElementById("modal-purpose__input");
  purposeInput.value = modalState.paymentInfo.payment_type;
  const modalPurposeSpan = document.getElementById("modal-purpose__span");
  modalPurposeSpan.innerText = modalState.paymentInfo.payment_type;
  const sumInput = document.getElementById("modal-sum__input");
  sumInput.onchange = modalSumInputOnChange.bind(this, sumInput);
  const modalAddButtton = document.getElementById("modal-add__button");
  modalAddButtton.onclick = modalAddAdditionalPurposeOnClick.bind();
};

const modalAddAdditionalPurposeOnClick = () => {
  if (
    modalState.newCodeIdSelected === null ||
    modalState.oldCodeIdSelected === null ||
    modalState.sum === null ||
    parseFloat(modalState.paymentBalance) - parseFloat(modalState.sum) < 0
  )
    return;

  modalState.createdPaymentsData.push({
    id_row: modalState.indexRow,
    id: null,
    banks_register_id: modalState.paymentInfo.id,
    date: modalState.paymentInfo.date,
    purpose: modalState.paymentInfo.payment_type,
    sum: modalState.sum,
    old_code_id: modalState.oldCodeIdSelected,
    old_code: modalState.oldCodeSelected,
    new_code_id: modalState.newCodeIdSelected,
    new_code: modalState.newCodeSelected,
  });
  modalState.paymentBalance = (
    parseFloat(modalState.paymentBalance) - parseFloat(modalState.sum)
  ).toFixed(2);
  modalState.indexRow++;
  modalFillContent();
  modalRerenderMainContent();
  modalState.oldCodeSelected = null;
  modalState.oldCodeIdSelected = null;
  modalState.newCodeSelected = null;
  modalState.newCodeIdSelected = null;
  modalState.sum = null;
  modalState.newCodesData = [];
  modalRerenderSumInput();
  modalFillOldCodesSelect();
  modalFillNewCodesSelect();
};

const modalSumInputOnChange = (target) => {
  modalState.sum =
    target.value === ""
      ? null
      : parseFloat(target.value) > 0
      ? parseFloat(target.value)
      : null;
};

const modalAdditionalPurposeCloseOnClick = () => {
  modalState.oldCodeSelected = null;
  modalState.oldCodeIdSelected = null;
  modalState.newCodeSelected = null;
  modalState.newCodeIdSelected = null;
  const modalWindow = document.getElementById(
    "modal-window-additional-purpose"
  );
  modalWindow.style.display = "none";
};
