let editBudgetPlanState = {
  id: null,
  budgetPlanId: null,
  monthPlannedIndicatorWithVAT: [
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
  monthPlannedIndicatorNoVAT: [
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

const modalBudgetPlan = document.getElementById(
  "modal-window-edit-budget-plan"
);
const modalBudgetPlanInput = modalBudgetPlan.getElementsByClassName(
  "modal__table__input"
);

const modalEditBudgetPlanOnClick = (id) => {
  editBudgetPlanState.id = id;

  modalGetBudgetPlanRequest();
  const modalWindow = document.getElementById("modal-window-edit-budget-plan");
  modalWindow.style.display = "block";
};

const modalGetBudgetPlanRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalGetBudgetPlanRequest",
      id: editBudgetPlanState.id,
    })
  );
  xhr.onload = function () {
    console.log(editBudgetPlanState.id);
    console.log(xhr.response);
    let response = JSON.parse(xhr.response);
    editBudgetPlanState.budgetPlanId = response.id;
    console.log(editBudgetPlanState.budgetPlanId);
    editBudgetPlanState.monthPlannedIndicatorWithVAT =
      response.month_array_with_vat;
    editBudgetPlanState.monthPlannedIndicatorNoVAT =
      response.month_array_no_vat;
    editBudgetPlanState.sumMonthWithVAT = response.sum_with_vat;
    editBudgetPlanState.sumMonthNoVAT = response.sum_no_vat;
    modalEditFillTableMonthInput();
  };
};

const modalEditSumOnKeyDown = (keyCode, indexClassElement) => {
  switch (keyCode) {
    case 13:
      if ([...modalBudgetPlanInput].length !== indexClassElement + 1)
        modalBudgetPlanInput[indexClassElement + 1].focus();
      break;
    case 38:
      if (indexClassElement > 12)
        modalBudgetPlanInput[indexClassElement - 13].focus();
      break;
    case 40:
      if (indexClassElement < 13)
        modalBudgetPlanInput[indexClassElement + 13].focus();
      break;
    case 39:
      if (indexClassElement < 25)
        modalBudgetPlanInput[indexClassElement + 1].focus();
      break;
    case 37:
      if (indexClassElement > 0)
        modalBudgetPlanInput[indexClassElement - 1].focus();
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
    editBudgetPlanState.monthPlannedIndicatorWithVAT[numberMonth]
  );
  switch (signVAT) {
    case true:
      if (numberMonth === "sum") {
        editBudgetPlanState.sumMonthWithVAT = value;
        modalEditFillTableMonthInput();
      } else {
        editBudgetPlanState.monthPlannedIndicatorWithVAT[numberMonth] = value;
        editBudgetPlanState.monthPlannedIndicatorNoVAT[numberMonth] =
          value === null ? null : value / 1.2;
        editBudgetPlanState.sumMonthWithVAT =
          editBudgetPlanState.monthPlannedIndicatorWithVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        editBudgetPlanState.sumMonthNoVAT =
          editBudgetPlanState.monthPlannedIndicatorNoVAT.reduce(
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
        editBudgetPlanState.monthPlannedIndicatorNoVAT[numberMonth] = value;
        editBudgetPlanState.sumMonthNoVAT =
          editBudgetPlanState.monthPlannedIndicatorNoVAT.reduce(
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
  if (value === null) modalBudgetPlanInput[indexClassElement].focus();
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
  [...modalBudgetPlanInput].forEach((element, index) => {
    if (index < 12) {
      element.value =
        editBudgetPlanState.monthPlannedIndicatorWithVAT[i] === null
          ? null
          : editBudgetPlanState.monthPlannedIndicatorWithVAT[i].toFixed(5);
      i++;
    }
    if (index === 12) {
      element.value =
        editBudgetPlanState.sumMonthWithVAT === null
          ? null
          : editBudgetPlanState.sumMonthWithVAT.toFixed(5);
      i = 0;
    }
    if (index > 12 && index < 25) {
      element.value =
        editBudgetPlanState.monthPlannedIndicatorNoVAT[i] === null
          ? null
          : editBudgetPlanState.monthPlannedIndicatorNoVAT[i].toFixed(5);
      i++;
    }
    if (index === 25) {
      element.value =
        editBudgetPlanState.sumMonthNoVAT === null
          ? null
          : editBudgetPlanState.sumMonthNoVAT.toFixed(5);
    }
  });
};

const modalEditBudgetPlanCloseOnClick = () => {
  const modalWindow = document.getElementById("modal-window-edit-budget-plan");
  modalWindow.style.display = "none";
};

const modalEditBudgetPlanSaveOnClick = () => {
  modalEditBudgetPlanSaveRequest();
  modalEditBudgetPlanCloseOnClick();
};

const modalEditBudgetPlanSaveRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalEditBudgetPlanSaveRequest",
      id: editBudgetPlanState.id,
      budgetPlanId: editBudgetPlanState.budgetPlanId,
      monthPlannedIndicatorWithVAT:
        editBudgetPlanState.monthPlannedIndicatorWithVAT.map((element) =>
          element === null ? 0 : element
        ),
      sumMonthWithVAT:
        editBudgetPlanState.sumMonthWithVAT === null
          ? 0
          : editBudgetPlanState.sumMonthWithVAT,
      monthPlannedIndicatorNoVAT:
        editBudgetPlanState.monthPlannedIndicatorNoVAT.map((element) =>
          element === null ? 0 : element
        ),
      sumMonthNoVAT:
        editBudgetPlanState.sumMonthNoVAT === null
          ? 0
          : editBudgetPlanState.sumMonthNoVAT,
      action: "edit",
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
    modalEditBudgetPlanCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};
