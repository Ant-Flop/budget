let setBudgetPlanState = {
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

const modalSetBudgetPlan = document.getElementById(
  "modal-window-set-budget-plan"
);
const modalSetBudgetPlanInput = modalSetBudgetPlan.getElementsByClassName(
  "modal__table__input"
);

const modalSetBudgetPlanOnClick = (id) => {
  setBudgetPlanState.id = id;
  modalSetGetBudgetPlanRequest();
  const modalWindow = document.getElementById("modal-window-set-budget-plan");
  modalWindow.style.display = "block";
};

const modalSetGetBudgetPlanRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "modalGetBudgetPlanRequest",
      id: setBudgetPlanState.id,
    })
  );
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);
    setBudgetPlanState.budgetPlanId = response.id;
    setBudgetPlanState.monthPlannedIndicatorWithVAT =
      response.month_array_with_vat;
    setBudgetPlanState.monthPlannedIndicatorNoVAT = response.month_array_no_vat;
    setBudgetPlanState.sumMonthWithVAT = response.sum_with_vat;
    setBudgetPlanState.sumMonthNoVAT = response.sum_no_vat;
    modalSetFillTableMonthInput();
  };
};

const modalSetSumOnKeyDown = (keyCode, indexClassElement) => {
  switch (keyCode) {
    case 13:
      if ([...modalSetBudgetPlanInput].length !== indexClassElement + 1)
        modalSetBudgetPlanInput[indexClassElement + 1].focus();
      break;
    case 38:
      if (indexClassElement > 12)
        modalSetBudgetPlanInput[indexClassElement - 13].focus();
      break;
    case 40:
      if (indexClassElement < 13)
        modalSetBudgetPlanInput[indexClassElement + 13].focus();
      break;
    case 39:
      if (indexClassElement < 25)
        modalSetBudgetPlanInput[indexClassElement + 1].focus();
      break;
    case 37:
      if (indexClassElement > 0)
        modalSetBudgetPlanInput[indexClassElement - 1].focus();
      break;
    default:
      break;
  }
};

const modalSetTableMonthInputOnChange = (
  target,
  numberMonth,
  signVAT,
  indexClassElement
) => {
  let value = modalSetTableValidationInput(
    target,
    setBudgetPlanState.monthPlannedIndicatorWithVAT[numberMonth]
  );
  switch (signVAT) {
    case true:
      if (numberMonth === "sum") {
        setBudgetPlanState.sumMonthWithVAT = value;
        console.log(setBudgetPlanState.sumMonthWithVAT);
        modalSetFillTableMonthInput();
      } else {
        setBudgetPlanState.monthPlannedIndicatorWithVAT[numberMonth] = value;
        setBudgetPlanState.monthPlannedIndicatorNoVAT[numberMonth] =
          value === null ? null : value / 1.2;
        setBudgetPlanState.sumMonthWithVAT =
          setBudgetPlanState.monthPlannedIndicatorWithVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        setBudgetPlanState.sumMonthNoVAT =
          setBudgetPlanState.monthPlannedIndicatorNoVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        modalSetFillTableMonthInput();
      }
      break;
    case false:
      if (numberMonth === "sum") {
        setSumPlannedIndicatorState.sumMonthNoVAT = value;
      } else {
        setBudgetPlanState.monthPlannedIndicatorNoVAT[numberMonth] = value;
        setBudgetPlanState.sumMonthNoVAT =
          setBudgetPlanState.monthPlannedIndicatorNoVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        modalSetFillTableMonthInput();
      }
      break;
    default:
      break;
  }
  if (value === null) modalSetBudgetPlanInput[indexClassElement].focus();
};

const modalSetTableValidationInput = (target, stateValue) => {
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

const modalSetFillTableMonthInput = () => {
  let i = 0;
  [...modalSetBudgetPlanInput].forEach((element, index) => {
    if (index < 12) {
      element.value =
        setBudgetPlanState.monthPlannedIndicatorWithVAT[i] === null
          ? null
          : setBudgetPlanState.monthPlannedIndicatorWithVAT[i].toFixed(5);
      i++;
    }
    if (index === 12) {
      element.value =
        setBudgetPlanState.sumMonthWithVAT === null
          ? null
          : setBudgetPlanState.sumMonthWithVAT.toFixed(5);
      i = 0;
    }
    if (index > 12 && index < 25) {
      element.value =
        setBudgetPlanState.monthPlannedIndicatorNoVAT[i] === null
          ? null
          : setBudgetPlanState.monthPlannedIndicatorNoVAT[i].toFixed(5);
      i++;
    }
    if (index === 25) {
      element.value =
        setBudgetPlanState.sumMonthNoVAT === null
          ? null
          : setBudgetPlanState.sumMonthNoVAT.toFixed(5);
    }
  });
};

const modalSetBudgetPlanCloseOnClick = () => {
  const modalWindow = document.getElementById("modal-window-set-budget-plan");
  modalWindow.style.display = "none";
};

const modalSetBudgetPlanSaveOnClick = () => {
  modalSetBudgetPlanSaveRequest();
  modalSetBudgetPlanCloseOnClick();
};

const modalSetBudgetPlanSaveRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-plan.php";
  xhr.open("POST", requestURL);
  console.log({
    typeRequest: "modalSetBudgetPlanSaveRequest",
    id: setBudgetPlanState.id,
    budgetPlanId: setBudgetPlanState.budgetPlanId,
    monthPlannedIndicatorWithVAT:
      setBudgetPlanState.monthPlannedIndicatorWithVAT.map((element) =>
        element === null ? 0 : element
      ),
    sumMonthWithVAT:
      setBudgetPlanState.sumMonthWithVAT === null
        ? 0
        : setBudgetPlanState.sumMonthWithVAT,
    monthPlannedIndicatorNoVAT:
      setBudgetPlanState.monthPlannedIndicatorNoVAT.map((element) =>
        element === null ? 0 : element
      ),
    sumMonthNoVAT:
      setBudgetPlanState.sumMonthNoVAT === null
        ? 0
        : setBudgetPlanState.sumMonthNoVAT,
  });
  xhr.send(
    JSON.stringify({
      typeRequest: "modalEditBudgetPlanSaveRequest",
      id: setBudgetPlanState.id,
      budgetPlanId: setBudgetPlanState.budgetPlanId,
      monthPlannedIndicatorWithVAT:
        setBudgetPlanState.monthPlannedIndicatorWithVAT.map((element) =>
          element === null ? 0 : element
        ),
      sumMonthWithVAT:
        setBudgetPlanState.sumMonthWithVAT === null
          ? 0
          : setBudgetPlanState.sumMonthWithVAT,
      monthPlannedIndicatorNoVAT:
        setBudgetPlanState.monthPlannedIndicatorNoVAT.map((element) =>
          element === null ? 0 : element
        ),
      sumMonthNoVAT:
        setBudgetPlanState.sumMonthNoVAT === null
          ? 0
          : setBudgetPlanState.sumMonthNoVAT,
      action: "set",
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
    modalSetBudgetPlanCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};
