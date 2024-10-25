let setSumPlannedIndicatorState = {
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
  monthPlannedIndicatorVatSign: [
    true,
    true,
    true,
    true,
    true,
    true,
    true,
    true,
    true,
    true,
    true,
    true,
    true,
  ],
};

const modalPlannedIndicator = document.getElementById(
  "modal-window-set-sum-planned-indicator"
);
const modalPlannedIndicatorInput = modalPlannedIndicator.getElementsByClassName(
  "modal__table__input"
);
const monthPlannedIndicatorVatSignInput = document.getElementsByClassName(
  "modal-vat-sign__input"
);

const modalSetSumPlannedIndicatorOnClick = () => {
  if (window.localStorage.getItem("modalSumPlannedIndicatorState"))
    setSumPlannedIndicatorState = JSON.parse(
      window.localStorage.getItem("modalSumPlannedIndicatorState")
    );
  else {
    window.localStorage.setItem(
      "modalSumPlannedIndicatorState",
      JSON.stringify({
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
        monthPlannedIndicatorVatSign: [
          true,
          true,
          true,
          true,
          true,
          true,
          true,
          true,
          true,
          true,
          true,
          true,
        ],
      })
    );
  }
  modalFillTableMonthInput();
  const modalWindow = document.getElementById(
    "modal-window-set-sum-planned-indicator"
  );
  modalWindow.style.display = "block";
  modalPlannedIndicatorInput[0].focus();
};

const modalSetSumPlannedIndicatorOnKeyDown = (keyCode, indexClassElement) => {
  switch (keyCode) {
    case 13:
      if ([...modalPlannedIndicatorInput].length !== indexClassElement + 1)
        modalPlannedIndicatorInput[indexClassElement + 1].focus();
      break;
    case 38:
      if (indexClassElement > 12)
        modalPlannedIndicatorInput[indexClassElement - 13].focus();
      break;
    case 40:
      if (indexClassElement < 13)
        modalPlannedIndicatorInput[indexClassElement + 13].focus();
      break;
    case 39:
      if (indexClassElement < 25)
        modalPlannedIndicatorInput[indexClassElement + 1].focus();
      break;
    case 37:
      if (indexClassElement > 0)
        modalPlannedIndicatorInput[indexClassElement - 1].focus();
      break;
    default:
      break;
  }
};

const modalTableMonthInputOnChange = (
  target,
  numberMonth,
  signVAT,
  indexClassElement
) => {
  let value = modalTableValidationInput(target, indexClassElement);

  switch (signVAT) {
    case true:
      if (numberMonth === "sum") {
        setSumPlannedIndicatorState.sumMonthWithVAT = value;
        modalFillTableMonthInput();
      } else {
        setSumPlannedIndicatorState.monthPlannedIndicatorWithVAT[numberMonth] =
          value;
        setSumPlannedIndicatorState.monthPlannedIndicatorNoVAT[numberMonth] =
          value === null ? null : value / 1.2;
        setSumPlannedIndicatorState.sumMonthWithVAT =
          setSumPlannedIndicatorState.monthPlannedIndicatorWithVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        setSumPlannedIndicatorState.sumMonthNoVAT =
          setSumPlannedIndicatorState.monthPlannedIndicatorNoVAT.reduce(
            (previousElement, currentElement) => {
              return previousElement + currentElement;
            },
            0
          );
        modalFillTableMonthInput();
      }
      break;
    case false:
      if (numberMonth === "sum") {
        setSumPlannedIndicatorState.sumMonthNoVAT = value;
      } else {
        setSumPlannedIndicatorState.monthPlannedIndicatorNoVAT[numberMonth] =
          value;
        setSumPlannedIndicatorState.sumMonthNoVAT =
          setSumPlannedIndicatorState.monthPlannedIndicatorNoVAT.reduce(
            (previousElement = 0, currentElement = 0) => {
              return previousElement + currentElement;
            },
            0
          );
        modalFillTableMonthInput();
      }
      break;
    default:
      break;
  }
  if (value === null) modalPlannedIndicatorInput[indexClassElement].focus();
};

const modalTableValidationInput = (target) => {
  let value =
    target.value === "" ? null : parseFloat(target.value.replace(",", "."));
  if (isNaN(value)) {
    alert("Введено недопустиме значення.");
    target.value = null;
    return null;
  }
  target.value = value === null ? null : value.toFixed(5);
  return value;
};

const modalFillTableMonthInput = () => {
  let i = 0;
  [...modalPlannedIndicatorInput].forEach((element, index) => {
    if (index < 12) {
      element.value =
        setSumPlannedIndicatorState.monthPlannedIndicatorWithVAT[i] === null
          ? null
          : setSumPlannedIndicatorState.monthPlannedIndicatorWithVAT[i].toFixed(
              5
            );
      i++;
    }
    if (index === 12) {
      element.value =
        setSumPlannedIndicatorState.sumMonthWithVAT === null
          ? null
          : setSumPlannedIndicatorState.sumMonthWithVAT.toFixed(5);
      i = 0;
    }
    if (index > 12 && index < 25) {
      element.value =
        setSumPlannedIndicatorState.monthPlannedIndicatorNoVAT[i] === null
          ? null
          : setSumPlannedIndicatorState.monthPlannedIndicatorNoVAT[i].toFixed(
              5
            );
      i++;
    }
    if (index === 25) {
      element.value =
        setSumPlannedIndicatorState.sumMonthNoVAT === null
          ? null
          : setSumPlannedIndicatorState.sumMonthNoVAT.toFixed(5);
    }
  });
  [...monthPlannedIndicatorVatSignInput].forEach((element, index) => {
    element.checked =
      setSumPlannedIndicatorState.monthPlannedIndicatorVatSign[index];
  });
};

const modalTableCheckBoxInputOnChange = (target, index) => {
  setSumPlannedIndicatorState.monthPlannedIndicatorVatSign[index] =
    target.checked;
  console.log(setSumPlannedIndicatorState.monthPlannedIndicatorVatSign[index])
  if (index === 12) {
    const checkBoxes = [
      ...document.getElementsByClassName("modal-vat-sign__input"),
    ];

    checkBoxes.forEach((element, indexEl) => {
      if (indexEl !== 12) {
        element.checked = target.checked;
        setSumPlannedIndicatorState.monthPlannedIndicatorVatSign[indexEl] =
          target.checked;
      }
    });
  }
};

const modalSetSumPlannedIndicatorSaveOnClick = () => {
  console.log(setSumPlannedIndicatorState)
  window.localStorage.setItem(
    "modalSumPlannedIndicatorState",
    JSON.stringify(setSumPlannedIndicatorState)
  );
  const signPlannedIndicator = document.getElementById(
    "sign-planned-indicator"
  );
  signPlannedIndicator.innerHTML =
    "<div class='check-mark-element-1'></div>" +
    "<div class='check-mark-element-2'></div>";
  modalSetSumPlannedIndicatorCloseOnClick();
};

const modalSetSumPlannedIndicatorCloseOnClick = () => {
  setSumPlannedIndicatorState = {
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
    monthPlannedIndicatorVatSign: [
      true,
      true,
      true,
      true,
      true,
      true,
      true,
      true,
      true,
      true,
      true,
      true,
      true,
    ],
  };
  const modalWindow = document.getElementById(
    "modal-window-set-sum-planned-indicator"
  );
  modalWindow.style.display = "none";
};
