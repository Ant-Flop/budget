const editPlannedIndicatorState = {
  lowLimitSymbolsNewCode: 11,
  plannedIndicatorId: null,
  plannedIndicatorData: [],
  counterpartyIdSelected: null,
  counterpartiesData: [],
  counterpartiesAddData: [],
  counterpartiesDeleteData: [],
  oldCodeIdSelected: null,
  oldCodesData: [],
  newCodeIdSelected: null,
  newCodesData: [],
  sumWithVAT: null,
  sumWithVATEdited: null,
  contractsData: [],
  contractsIdSelected: [],
  contractsAddData: [],
  contractsDeleteData: [],
  plannedIndicatorEditData: false,
};

//editPlannedIndicatorState.newCodeIdSelected
const modalEditPlannedIndicatorOnClick = (id) => {
  editPlannedIndicatorState.plannedIndicatorId = id;
  
  getPlannedIndicatorRequest(editPlannedIndicatorState, () => {
    if (state.userInfo.role.fin_dir_role) {
      getCounterpartiesRequest(editPlannedIndicatorState, () => {
        editPlannedIndicatorState.counterpartiesAddData = editPlannedIndicatorState.plannedIndicatorData.counterparties.filter(item => item.id !== null && item.name !== null);
        editPlannedIndicatorState.counterpartyIdSelected =
          editPlannedIndicatorState.plannedIndicatorData.counterparties.filter(item => item.id !== null && item.name !== null);

          
        fillEditModalPlannedIndicatorCounterpartySelect();
        fillEditModalPlannedIndicatorBudgetArticleInput();
        // запрос на договоры
        getContractsRequest(editPlannedIndicatorState, () => {
          editPlannedIndicatorState.contractsAddData =
            editPlannedIndicatorState.contractsIdSelected;
          fillEditModalPlannedIndicatorContractsSelect();
        });
        editPlannedIndicatorState.oldCodeIdSelected =
          editPlannedIndicatorState.plannedIndicatorData.old_code_id;

        getOldCodesRequest(editPlannedIndicatorState, () => {
          fillEditModalPlannedIndicatorOldCodeSelect();
          getNewCodesRequest(editPlannedIndicatorState, () => {
            editPlannedIndicatorState.newCodeIdSelected =
              editPlannedIndicatorState.plannedIndicatorData.new_code_id;
            fillEditModalPlannedIndicatorNewCodeSelect();
            fillEditModalPlannedIndicatorSumWithVATInput();
            const modalWindow = document.getElementById(
              "modal-window-edit-planned-indicator"
            );
            modalWindow.style.display = "block";

          });
        });
      });
    }
    if (state.userInfo.role.director_role) {
      if (editPlannedIndicatorState.plannedIndicatorData.editable) {
        document.getElementById(
          "modal-edit-budget-plan-wrapper-id"
        ).hidden = false;
      }
      getCounterpartiesRequest(editPlannedIndicatorState, () => {

        editPlannedIndicatorState.counterpartiesAddData = editPlannedIndicatorState.plannedIndicatorData.counterparties.filter(item => item.id !== null && item.name !== null);
        editPlannedIndicatorState.counterpartyIdSelected =
          editPlannedIndicatorState.plannedIndicatorData.counterparties.filter(item => item.id !== null && item.name !== null);
        fillEditModalPlannedIndicatorCounterpartySelect();
        fillEditModalPlannedIndicatorBudgetArticleInput();
        //console.log(editPlannedIndicatorState.plannedIndicatorData);
        // запрос на договоры
        getContractsRequest(editPlannedIndicatorState, () => {
          editPlannedIndicatorState.contractsAddData =
            editPlannedIndicatorState.contractsIdSelected;
          fillEditModalPlannedIndicatorContractsSelect();
        });
        const modalWindow = document.getElementById(
          "modal-window-edit-planned-indicator"
        );
        modalWindow.style.display = "block";
      });
    } else if (state.userInfo.role.financier_role) {
      editPlannedIndicatorState.oldCodeIdSelected =
        editPlannedIndicatorState.plannedIndicatorData.old_code_id;

      getOldCodesRequest(editPlannedIndicatorState, () => {
        fillEditModalPlannedIndicatorOldCodeSelect();
        getNewCodesRequest(editPlannedIndicatorState, () => {
          editPlannedIndicatorState.newCodeIdSelected =
            editPlannedIndicatorState.plannedIndicatorData.new_code_id;
          fillEditModalPlannedIndicatorNewCodeSelect();
          fillEditModalPlannedIndicatorSumWithVATInput();
          const modalWindow = document.getElementById(
            "modal-window-edit-planned-indicator"
          );
          modalWindow.style.display = "block";
        });
      });
    }
  });
};

const fillEditModalPlannedIndicatorCounterpartySelect = () => {
  rerenderPlannedIndicatorCounterpartyEditModal();
  const select = document.getElementById(
    "modal-edit-planned-indicator-counterparty__select"
  );

  select.onchange = editModalPlannedIndicatorCounterpartySelectOnChange.bind(this);

  while (select.firstChild) select.removeChild(select.firstChild);
  editPlannedIndicatorState.counterpartiesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    editPlannedIndicatorState.counterpartyIdSelected.forEach((value) => {
      if (value.id === element.id) {
        optionSelect.selected = true;
        optionSelect.hidden = true;
      }
    });
    select.appendChild(optionSelect);
  });
  
  if (editPlannedIndicatorState.contractsAddData.length > 0)
    select.disabled = true;
  else select.disabled = false;

  // while (select.firstChild) select.removeChild(select.firstChild);
  // const selectedOption = document.createElement("option");
  // selectedOption.value = "Обрати контрагента";
  // selectedOption.innerText = "Обрати контрагента";
  // selectedOption.hidden = true;
  // selectedOption.selected =
  //   editPlannedIndicatorState.counterpartyIdSelected === null ? true : false;
  // select.appendChild(selectedOption);
  // if (editPlannedIndicatorState.counterpartiesData.length === 0) return;
  // editPlannedIndicatorState.counterpartiesData.forEach((element) => {
  //   const optionSelect = document.createElement("option");
  //   optionSelect.value = element.name;
  //   optionSelect.innerText = element.name;
  //   optionSelect.setAttribute("data-id", element.id);

  //   editPlannedIndicatorState.counterparties.forEach((value) => {});
  //   if (editPlannedIndicatorState.counterpartyIdSelected === element.id) {
  //     optionSelect.selected = true;
  //     optionSelect.hidden = true;
  //   }
  //   select.appendChild(optionSelect);
  // });
};


const editModalPlannedIndicatorCounterpartySelectOnChange = () => {
  const select = document.getElementById(
    "modal-edit-planned-indicator-counterparty__select"
  );

  editPlannedIndicatorState.counterpartiesAddData = [];
  editPlannedIndicatorState.counterpartiesDeleteData = [];
  [...select.options].forEach((element, index) => {
    
    if (element.selected) {
      const newElement = {
        id: element.getAttribute("data-id"),
        planned_indicators_id: editPlannedIndicatorState.plannedIndicatorId,
      };
      console.log(element)
      editPlannedIndicatorState.counterpartiesAddData.push(newElement);

      const elementExists = editPlannedIndicatorState.counterpartyIdSelected.some(
        (item) => item.id === newElement.id && item.planned_indicators_id === newElement.planned_indicators_id
      );

      if (!elementExists) {
        editPlannedIndicatorState.counterpartyIdSelected.push(newElement);
      }
    } else {
      if (
        editPlannedIndicatorState.counterpartyIdSelected.find(
          (value) => value.id == element.getAttribute("data-id")
        ) !== undefined
      )

      editPlannedIndicatorState.counterpartyIdSelected = editPlannedIndicatorState.counterpartyIdSelected.filter(item => item.id != element.getAttribute("data-id") );
        editPlannedIndicatorState.counterpartiesDeleteData.push({
          id: element.getAttribute("data-id"),
          planned_indicators_id: editPlannedIndicatorState.plannedIndicatorId,
        });


        editPlannedIndicatorState.contractsData = editPlannedIndicatorState.contractsData.filter(item => item.counterparty_id != element.getAttribute("data-id") )

        
    }

    editPlannedIndicatorState.counterpartyIdSelected = Array.from(new Set(editPlannedIndicatorState.counterpartyIdSelected));

    

    getClearContractsRequest(editPlannedIndicatorState, () => { fillEditModalPlannedIndicatorContractsSelect()})
    
  });




};

const rerenderPlannedIndicatorContractsEditModal = () => {
  const field = document.getElementsByClassName(
    "modal-edit-planned-indicator-contracts-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-edit-planned-indicator-contracts__select' class='counterparty-select contracts-select js-example-basic-multiple' title='Договори' name='states[]' multiple='multiple'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-edit-planned-indicator-contracts__select"
  );
  select.onchange = editModalPlannedIndicatorContractsSelectOnChange.bind(this);
};

const fillEditModalPlannedIndicatorContractsSelect = () => {
  const contractWrapper = document.getElementById(
    "modal-edit-contracts-wrapper-id"
  );
  if (editPlannedIndicatorState.plannedIndicatorData.exception_section > 0) {
    contractWrapper.hidden = false;
  } else {
    contractWrapper.hidden = true;
  }

  rerenderPlannedIndicatorContractsEditModal();

  const selectCounterparty = document.getElementById(
    "modal-edit-planned-indicator-counterparty__select"
  );

  const select = document.getElementById(
    "modal-edit-planned-indicator-contracts__select"
  );
  select.onchange = editModalPlannedIndicatorContractsSelectOnChange.bind(this);
  while (select.firstChild) select.removeChild(select.firstChild);
  editPlannedIndicatorState.contractsData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.contract_number;
    optionSelect.innerText = element.contract_number;
    optionSelect.setAttribute("data-id", element.contract_id);
    editPlannedIndicatorState.contractsAddData.forEach((value) => {
      if (value.contract_id === element.contract_id) {
        optionSelect.selected = true;
        optionSelect.hidden = true;
      }
    });
    select.appendChild(optionSelect);
  });
  // if (editPlannedIndicatorState.contractsAddData.length > 0)
  //   selectCounterparty.disabled = true;
  // else selectCounterparty.disabled = false;
};

const editModalPlannedIndicatorContractsSelectOnChange = () => {
  const select = document.getElementById(
    "modal-edit-planned-indicator-contracts__select"
  );

  editPlannedIndicatorState.contractsAddData = [];
  editPlannedIndicatorState.contractsDeleteData = [];
  [...select.options].forEach((element, index) => {
    if (element.selected) {
      editPlannedIndicatorState.contractsAddData.push({
        contract_id: element.getAttribute("data-id"),
        planned_indicators_id: editPlannedIndicatorState.plannedIndicatorId,
      });
    } else {
      if (
        editPlannedIndicatorState.contractsIdSelected.find(
          (value) => value.contract_id == element.getAttribute("data-id")
        ) !== undefined
      )
        editPlannedIndicatorState.contractsDeleteData.push({
          contract_id: element.getAttribute("data-id"),
          planned_indicators_id: editPlannedIndicatorState.plannedIndicatorId,
        });
    }
  });

  editPlannedIndicatorState.contractsIdSelected = Array.from(new Set(editPlannedIndicatorState.contractsIdSelected));

  fillEditModalPlannedIndicatorContractsSelect();

};

const rerenderPlannedIndicatorCounterpartyEditModal = () => {
  const field = document.getElementsByClassName(
    "modal-edit-planned-indicator-counterparty-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-edit-planned-indicator-counterparty__select' class='counterparty-select js-example-basic-multiple' title='Контрагент' name='states[]' multiple='multiple'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-edit-planned-indicator-counterparty__select"
  );
  select.onchange = editModalPlannedIndicatorCounterpartyOnChange.bind(this);
};

const editModalPlannedIndicatorCounterpartyOnChange = () => {
  const select = document.getElementById(
    "modal-edit-planned-indicator-counterparty__select"
  );

  // Создаем массив для хранения выбранных data-id
  const selectedDataIds = [];

  // Проходимся по всем опциям select и выбираем только те, что выбраны
  for (let i = 0; i < select.options.length; i++) {
    const option = select.options[i];
    if (option.selected) {
      const dataId = option.getAttribute("data-id");
      const name = option.value;
      if (dataId) {
        selectedDataIds.push({ id: dataId, name: name});
      }
    }
  }

  // Сохраняем в state массив выбранных counterpartyId
  editPlannedIndicatorState.counterpartyIdSelected = selectedDataIds;

  // Выполняем запрос
  getContractsRequest(editPlannedIndicatorState, () => {
    fillEditModalPlannedIndicatorContractsSelect();
  });
};


const fillEditModalPlannedIndicatorBudgetArticleInput = () => {
  const input = document.getElementById(
    "modal-edit-planned-indicator-article-name"
  );
  input.value = editPlannedIndicatorState.plannedIndicatorData.article_name;
};

const modalSaveEditPlannedIndicatorRequest = (data) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(data);
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
    modalEditPlannedIndicatorCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};

const fillEditModalPlannedIndicatorOldCodeSelect = () => {
  rerenderPlannedIndicatorOldCodeEditModal();
  const select = document.getElementById(
    "modal-edit-planned-indicator-old-code__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати старий код";
  selectedOption.innerText = "Обрати старий код";
  selectedOption.hidden = true;
  selectedOption.selected =
    editPlannedIndicatorState.oldCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (editPlannedIndicatorState.oldCodesData.length === 0) return;
  editPlannedIndicatorState.oldCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.old_code;
    optionSelect.innerText = element.old_code;
    optionSelect.setAttribute("data-id", element.id);
    if (editPlannedIndicatorState.oldCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorOldCodeEditModal = () => {
  const field = document.getElementsByClassName(
    "modal-edit-planned-indicator-old-code-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-edit-planned-indicator-old-code__select' class='old-code-select' title='Старий код'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-edit-planned-indicator-old-code__select"
  );
  select.onchange = editModalPlannedIndicatorOldCodeOnChange.bind(this);
};

const editModalPlannedIndicatorOldCodeOnChange = () => {
  const select = document.getElementById(
    "modal-edit-planned-indicator-old-code__select"
  );
  editPlannedIndicatorState.oldCodeIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  editPlannedIndicatorState.newCodeIdSelected = null;
  getNewCodesRequest(editPlannedIndicatorState, () => {
    fillEditModalPlannedIndicatorNewCodeSelect();
  });
};

const fillEditModalPlannedIndicatorNewCodeSelect = () => {
  rerenderPlannedIndicatorNewCodeEditModal();
  const select = document.getElementById(
    "modal-edit-planned-indicator-new-code__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати новий код";
  selectedOption.innerText = "Обрати новий код";
  selectedOption.hidden = true;
  selectedOption.selected =
    editPlannedIndicatorState.newCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (editPlannedIndicatorState.newCodesData.length === 0) return;
  editPlannedIndicatorState.newCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.new_code;
    optionSelect.innerText = element.new_code;
    optionSelect.setAttribute("data-id", element.id);
    if (editPlannedIndicatorState.newCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const rerenderPlannedIndicatorNewCodeEditModal = () => {
  const field = document.getElementsByClassName(
    "modal-edit-planned-indicator-new-code-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-edit-planned-indicator-new-code__select' class='new-code-select' title='Старий код'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-edit-planned-indicator-new-code__select"
  );
  select.onchange = editModalPlannedIndicatorNewCodeOnChange.bind(this);
};

const editModalPlannedIndicatorNewCodeOnChange = () => {
  const select = document.getElementById(
    "modal-edit-planned-indicator-new-code__select"
  );
  editPlannedIndicatorState.newCodeIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
};

const fillEditModalPlannedIndicatorSumWithVATInput = () => {
  const input = document.getElementById(
    "modal-edit-planned-indicator-sum-with-vat"
  );
  editPlannedIndicatorState.sumWithVATEdited =
    editPlannedIndicatorState.plannedIndicatorData.sum_with_vat_edited;
  input.value =
    editPlannedIndicatorState.plannedIndicatorData.sum_with_vat_edited.toFixed(
      5
    );
};

const editModalPlannedIndicatorSumWithVATOnChange = () => {
  const input = document.getElementById(
    "modal-edit-planned-indicator-sum-with-vat"
  );
  editPlannedIndicatorState.sumWithVATEdited = parseFloat(input.value);
  input.value = editPlannedIndicatorState.sumWithVATEdited.toFixed(5);
};

const modalEditPlannedIndicatorSaveOnClick = () => {
  let plannedIndicatorData = null;
  if (state.userInfo.role.fin_dir_role) {
    const nameInput = document.getElementById(
      "modal-edit-planned-indicator-article-name"
    );
    let name = nameInput.value;
    console.log(editPlannedIndicatorState.newCodeIdSelected);
    if (editPlannedIndicatorState.newCodeIdSelected === null || name === "") {
      alert("Не всі обов'язкові поля заповнені!");
      nameInput.value =
        editPlannedIndicatorState.plannedIndicatorData.article_name;
      editPlannedIndicatorState.oldCodeIdSelected =
        editPlannedIndicatorState.plannedIndicatorData.old_code_id;
      fillEditModalPlannedIndicatorOldCodeSelect();
      editPlannedIndicatorState.newCodeIdSelected =
        editPlannedIndicatorState.plannedIndicatorData.new_code_id;
      getNewCodesRequest(editPlannedIndicatorState, () => {
        fillEditModalPlannedIndicatorNewCodeSelect();
      });
      return;
    }

    plannedIndicatorData = JSON.stringify({
      typeRequest: "modalSaveEditPlannedIndicatorRequest",
      id: editPlannedIndicatorState.plannedIndicatorId,
      counterpartyId: editPlannedIndicatorState.counterpartyIdSelected ? editPlannedIndicatorState.counterpartyIdSelected : 0,
      previousCounterpartyId:
        editPlannedIndicatorState.plannedIndicatorData.counterparty_id,
      name: name,
      contracts: {
        deleted: editPlannedIndicatorState.contractsDeleteData,
        deletedAll:
          editPlannedIndicatorState.plannedIndicatorData.counterparty_id !==
          editPlannedIndicatorState.counterpartyIdSelected
            ? true
            : false,
        added: editPlannedIndicatorState.contractsAddData,
      },
      newCodeId: editPlannedIndicatorState.newCodeIdSelected,
      sumWithVATEdited: editPlannedIndicatorState.sumWithVATEdited,
      sumWithVATDifference:
        editPlannedIndicatorState.sumWithVATEdited !== 0
          ? editPlannedIndicatorState.sumWithVATEdited -
            editPlannedIndicatorState.plannedIndicatorData.sum_with_vat
          : 0,
      plannedIndicatorsEditData:
        editPlannedIndicatorState.plannedIndicatorEditData,
    });
  } else if (state.userInfo.role.financier_role) {
    if (editPlannedIndicatorState.newCodeIdSelected === null) {
      alert("Не всі обов'язкові поля заповнені!");
      return;
    }
    plannedIndicatorData = JSON.stringify({
      typeRequest: "modalSaveEditPlannedIndicatorRequest",
      id: editPlannedIndicatorState.plannedIndicatorId,
      newCodeId: editPlannedIndicatorState.newCodeIdSelected,
      sumWithVATEdited: editPlannedIndicatorState.sumWithVATEdited,
      sumWithVATDifference:
        editPlannedIndicatorState.sumWithVATEdited !== 0
          ? editPlannedIndicatorState.sumWithVATEdited -
            editPlannedIndicatorState.plannedIndicatorData.sum_with_vat
          : 0,
    });
  } else if (state.userInfo.role.director_role) {
    const nameInput = document.getElementById(
      "modal-edit-planned-indicator-article-name"
    );
    let name = nameInput.value;
    if (name === "") {
      alert("Не всі обов'язкові поля заповнені!");
      return;
    }
    // editPlannedIndicatorState.counterpartyIdSelected
    plannedIndicatorData = JSON.stringify({
      typeRequest: "modalSaveEditPlannedIndicatorRequest",
      id: editPlannedIndicatorState.plannedIndicatorId,
      counterpartyId: {
        deleted: editPlannedIndicatorState.counterpartiesDeleteData, 
        deletedAll: editPlannedIndicatorState.counterpartiesAddData.length === 0,
        added: editPlannedIndicatorState.counterpartiesAddData
      },
      previousCounterpartyId:
        editPlannedIndicatorState.plannedIndicatorData.counterparty_id,
      name: name,
      contracts: {
        deleted: editPlannedIndicatorState.contractsDeleteData,
        deletedAll:
          editPlannedIndicatorState.plannedIndicatorData.counterparty_id !==
          editPlannedIndicatorState.counterpartyIdSelected
            ? true
            : false,
        added: editPlannedIndicatorState.contractsAddData,
      },
      plannedIndicatorsEditData:
        editPlannedIndicatorState.plannedIndicatorEditData,
    });
  }


  modalSaveEditPlannedIndicatorRequest(plannedIndicatorData);
};

const modalEditPlannedIndicatorCloseOnClick = () => {
  editPlannedIndicatorState.lowLimitSymbolsNewCode = 11;
  editPlannedIndicatorState.plannedIndicatorId = null;
  editPlannedIndicatorState.plannedIndicatorData = [];
  editPlannedIndicatorState.counterpartyIdSelected = null;
  editPlannedIndicatorState.counterpartiesData = [];
  editPlannedIndicatorState.oldCodeIdSelected = null;
  editPlannedIndicatorState.oldCodesData = [];
  editPlannedIndicatorState.newCodeIdSelected = null;
  editPlannedIndicatorState.newCodesData = [];
  editPlannedIndicatorState.contractsAddData = [];
  editPlannedIndicatorState.contractsData = [];
  editPlannedIndicatorState.contractsDeleteData = [];
  editPlannedIndicatorState.contractsIdSelected = [];
  editPlannedIndicatorState.plannedIndicatorEditData = false;
  const modalWindow = document.getElementById(
    "modal-window-edit-planned-indicator"
  );
  modalWindow.style.display = "none";
  document.getElementById("modal-edit-budget-plan-wrapper-id").hidden = true;
};

const modalDeletePlannedIndicatorOnClick = (id) => {
  if (confirm("Ви дійсно бажаєте видалити запис?") == true)
    modalDeletePlannedIndicatorRequest(id);
  else return;
};

const modalDeletePlannedIndicatorRequest = (id) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-planned-indicators.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "deletePlannedIndicatorRequest",
      id: id,
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
    backgroundStatus.style.backgroundColor = "#0000";
  };
};
