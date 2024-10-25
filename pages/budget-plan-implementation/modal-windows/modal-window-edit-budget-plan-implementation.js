let editBudgetPlanImplementationState = {
  id: null,
  budgetPlanImplementationId: null,
  monthPlanImplementationIndicatorWithVAT: [
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
  ],
  sumMonthWithVAT: null,
  monthPlanImplementationIndicatorNoVAT: [
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
  ],
  sumMonthNoVAT: null,
};

const modalBudgetPlanImplementation = document.getElementById(
  "modal-window-edit-budget-plan-implementation"
);
const modalBudgetPlanImplementationInput = document.getElementsByClassName(
  "modal__table__input"
);

const modalEditBudgetPlanImplementationOnClick = (id) => {
  editBudgetPlanImplementationState.id = id;
  modalGetBudgetPlanImplementationRequest();
  const modalWindow = document.getElementById(
    "modal-window-edit-budget-plan-implementation"
  );
  modalWindow.style.display = "block";
};

const modalGetBudgetPlanImplementationRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalGetBudgetPlanImplementationRequest",
      id: editBudgetPlanImplementationState.id,
    })
  );
  xhr.onload = function () {
    console.log(xhr.response);
    let response = JSON.parse(xhr.response);
    editBudgetPlanImplementationState.budgetPlanImplementationId = response.id;
    editBudgetPlanImplementationState.monthPlanImplementationIndicatorWithVAT =
      response.month_array_with_vat;
    editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT =
      response.month_array_no_vat;
    editBudgetPlanImplementationState.sumMonthWithVAT = response.sum_with_vat;
    editBudgetPlanImplementationState.sumMonthNoVAT = response.sum_no_vat;
    modalEditFillTableMonthInput();
  };
};

const modalEditSumOnKeyDown = (keyCode, indexClassElement) => {
  switch (keyCode) {
    case 13:
      if (
        [...modalBudgetPlanImplementationInput].length !==
        indexClassElement + 1
      )
        modalBudgetPlanImplementationInput[indexClassElement + 1].focus();
      break;
    case 38:
      if (indexClassElement > 12)
        modalBudgetPlanImplementationInput[indexClassElement - 13].focus();
      break;
    case 40:
      if (indexClassElement < 13)
        modalBudgetPlanImplementationInput[indexClassElement + 13].focus();
      break;
    case 39:
      if (indexClassElement < 25)
        modalBudgetPlanImplementationInput[indexClassElement + 1].focus();
      break;
    case 37:
      if (indexClassElement > 0)
        modalBudgetPlanImplementationInput[indexClassElement - 1].focus();
      break;
    default:
      break;
  }
};

const modalEditTableMonthInputOnChange = (
  target,
  numberMonth,
  signVAT,
  indexClassElement
) => {
  let value = modalEditTableValidationInput(
    target,
    editBudgetPlanImplementationState.monthPlanImplementationIndicatorWithVAT[
      numberMonth
    ]
  );
  switch (signVAT) {
    case true:
      if (numberMonth === "sum") {
        editBudgetPlanImplementationState.sumMonthWithVAT = value;
        modalEditFillTableMonthInput();
      } else {
        editBudgetPlanImplementationState.monthPlanImplementationIndicatorWithVAT[
          numberMonth
        ] = value;
        editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT[
          numberMonth
        ] = value === null ? null : value / 1.2;
        editBudgetPlanImplementationState.sumMonthWithVAT =
          editBudgetPlanImplementationState.monthPlanImplementationIndicatorWithVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        editBudgetPlanImplementationState.sumMonthNoVAT =
          editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        modalEditFillTableMonthInput();
      }
      break;
    case false:
      if (numberMonth === "sum") {
        setSumPlannedIndicatorState.sumMonthNoVAT = value;
      } else {
        editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT[
          numberMonth
        ] = value;
        editBudgetPlanImplementationState.sumMonthNoVAT =
          editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        modalEditFillTableMonthInput();
      }
      break;
    default:
      break;
  }
  if (value === null)
    modalBudgetPlanImplementationInput[indexClassElement].focus();
};

const modalEditTableValidationInput = (target, stateValue) => {
  let value =
    target.value === "" ? null : parseFloat(target.value.replace(",", "."));
  if (isNaN(value)) {
    alert("Введено недопустиме значення.");
    target.value = stateValue;
    return stateValue;
  }
  target.value = value === null ? null : value.toFixed(5);
  return value;
};

const modalEditFillTableMonthInput = () => {
  let i = 0;
  [...modalBudgetPlanImplementationInput].forEach((element, index) => {
    if (index < 12) {
      element.value =
        editBudgetPlanImplementationState
          .monthPlanImplementationIndicatorWithVAT[i] === null
          ? null
          : editBudgetPlanImplementationState.monthPlanImplementationIndicatorWithVAT[
              i
            ].toFixed(5);
      i++;
    }
    if (index === 12) {
      element.value =
        editBudgetPlanImplementationState.sumMonthWithVAT === null
          ? null
          : editBudgetPlanImplementationState.sumMonthWithVAT.toFixed(5);
      i = 0;
    }
    if (index > 12 && index < 25) {
      element.value =
        editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT[
          i
        ] === null
          ? null
          : editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT[
              i
            ].toFixed(5);
      i++;
    }
    if (index === 25) {
      element.value =
        editBudgetPlanImplementationState.sumMonthNoVAT === null
          ? null
          : editBudgetPlanImplementationState.sumMonthNoVAT.toFixed(5);
    }
  });
};

const modalEditBudgetPlanImplementationCloseOnClick = () => {
  const modalWindow = document.getElementById(
    "modal-window-edit-budget-plan-implementation"
  );
  modalWindow.style.display = "none";
};

const modalEditBudgetPlanImplementationSaveOnClick = () => {
  modalEditBudgetPlanImplementationSaveRequest();
  modalEditBudgetPlanImplementationCloseOnClick();
};

const modalEditBudgetPlanImplementationSaveRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan-implementation.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalEditBudgetPlanImplementationSaveRequest",
      id: editBudgetPlanImplementationState.id,
      budgetPlanImplementationId:
        editBudgetPlanImplementationState.budgetPlanImplementationId,
      monthPlannedImplementationIndicatorWithVAT:
        editBudgetPlanImplementationState.monthPlanImplementationIndicatorWithVAT.map(
          (element) => (element === null ? 0 : element)
        ),
      sumMonthWithVAT:
        editBudgetPlanImplementationState.sumMonthWithVAT === null
          ? 0
          : editBudgetPlanImplementationState.sumMonthWithVAT,
      monthPlannedImplementationIndicatorNoVAT:
        editBudgetPlanImplementationState.monthPlanImplementationIndicatorNoVAT.map(
          (element) => (element === null ? 0 : element)
        ),
      sumMonthNoVAT:
        editBudgetPlanImplementationState.sumMonthNoVAT === null
          ? 0
          : editBudgetPlanImplementationState.sumMonthNoVAT,
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
    modalEditBudgetPlanImplementationCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};
