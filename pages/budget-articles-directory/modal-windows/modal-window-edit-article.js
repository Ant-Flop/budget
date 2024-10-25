const editBudgetArticleState = {
  lowLimitSymbolsNewCode: 11,
  articleData: [],
  id: null,
  name: null,
  fundholderIdSelected: null,
  serviceIdSelected: null,
  servicesData: [],
  oldCodeIdSelected: null,
  oldCodesData: [],
  newCodeIdSelected: null,
  newCodesData: [],
};

const modalEditBudgetArticleOnClick = (id) => {
  editBudgetArticleState.id = id;
  getBudgetArticleRequest(editBudgetArticleState, () => {
    if (state.userInfo.role.fin_dir_role) {
      
      editBudgetArticleState.oldCodeIdSelected =
        editBudgetArticleState.articleData.old_code_id;
      editBudgetArticleState.newCodeIdSelected =
        editBudgetArticleState.articleData.new_code_id;
      getOldCodesRequest(editBudgetArticleState, () => {
        fillEditModalBudgetArticleOldCodeSelect();
        getNewCodesRequest(
          editBudgetArticleState,
          "edit",
          editBudgetArticleState.articleData.new_code_id,
          () => {
            fillEditModalBudgetArticleNewCodeSelect();
            editBudgetArticleState.fundholderIdSelected =
              editBudgetArticleState.articleData.fundholder_id;
            editBudgetArticleState.name =
              editBudgetArticleState.articleData.name;
            editBudgetArticleState.serviceIdSelected =
              editBudgetArticleState.articleData.service_id;
            getServicesRequest(editBudgetArticleState, () => {
              fillEditModalBudgetArticleServiceSelect();
              fillEditModalBudgetArticleNameInput();
              const modalWindow = document.getElementById(
                "modal-window-edit-budget-article"
              );
              modalWindow.style.display = "block";
            });
          }
        );
      });
    } else if (state.userInfo.role.financier_role) {
      editBudgetArticleState.oldCodeIdSelected =
        editBudgetArticleState.articleData.old_code_id;
      editBudgetArticleState.newCodeIdSelected =
        editBudgetArticleState.articleData.new_code_id;
      getOldCodesRequest(editBudgetArticleState, () => {
        fillEditModalBudgetArticleOldCodeSelect();
        getNewCodesRequest(
          editBudgetArticleState,
          "edit",
          editBudgetArticleState.articleData.new_code_id,
          () => {
            fillEditModalBudgetArticleNewCodeSelect();
            const modalWindow = document.getElementById(
              "modal-window-edit-budget-article"
            );
            modalWindow.style.display = "block";
          }
        );
      });
    } else if (state.userInfo.role.director_role) {
      editBudgetArticleState.fundholderIdSelected =
        editBudgetArticleState.articleData.fundholder_id;
      editBudgetArticleState.name = editBudgetArticleState.articleData.name;
      editBudgetArticleState.serviceIdSelected =
        editBudgetArticleState.articleData.service_id;
      getServicesRequest(editBudgetArticleState, () => {
        fillEditModalBudgetArticleServiceSelect();
        fillEditModalBudgetArticleNameInput();
        const modalWindow = document.getElementById(
          "modal-window-edit-budget-article"
        );
        modalWindow.style.display = "block";
      });
    }
  });
};

const fillEditModalBudgetArticleOldCodeSelect = () => {
  rerenderBudgetArticleOldCodeEditModal();
  const select = document.getElementById(
    "modal-edit-budget-article-old-code__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати старий код";
  selectedOption.innerText = "Обрати старий код";
  selectedOption.hidden = true;
  selectedOption.selected =
    editBudgetArticleState.oldCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (editBudgetArticleState.oldCodesData.length === 0) return;
  editBudgetArticleState.oldCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.old_code;
    optionSelect.innerText = element.old_code;
    optionSelect.setAttribute("data-id", element.id);
    if (editBudgetArticleState.oldCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleOldCodeEditModal = () => {
  const field = document.getElementsByClassName(
    "modal-edit-budget-article-old-code-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-edit-budget-article-old-code__select' class='old-code-select' title='Старий код'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-edit-budget-article-old-code__select"
  );
  select.onchange = editModalBudgetArticleOldCodeOnChange.bind(this);
};

const editModalBudgetArticleOldCodeOnChange = () => {
  const select = document.getElementById(
    "modal-edit-budget-article-old-code__select"
  );
  editBudgetArticleState.oldCodeIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
  editBudgetArticleState.newCodeIdSelected = null;
  getNewCodesRequest(
    editBudgetArticleState,
    "edit",
    editBudgetArticleState.articleData.new_code_id,
    () => {
      fillEditModalBudgetArticleNewCodeSelect();
    }
  );
};

const fillEditModalBudgetArticleNewCodeSelect = () => {
  rerenderBudgetArticleNewCodeEditModal();
  const select = document.getElementById(
    "modal-edit-budget-article-new-code__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати новий код";
  selectedOption.innerText = "Обрати новий код";
  selectedOption.hidden = true;
  selectedOption.selected =
    editBudgetArticleState.newCodeIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (editBudgetArticleState.newCodesData.length === 0) return;
  editBudgetArticleState.newCodesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.new_code;
    optionSelect.innerText = element.new_code;
    optionSelect.setAttribute("data-id", element.id);
    if (editBudgetArticleState.newCodeIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleNewCodeEditModal = () => {
  const field = document.getElementsByClassName(
    "modal-edit-budget-article-new-code-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-edit-budget-article-new-code__select' class='new-code-select' title='Новий код'></select>"
  )
    .appendTo(field)
    .select2();
  const select = document.getElementById(
    "modal-edit-budget-article-new-code__select"
  );
  select.onchange = editModalBudgetArticleNewCodeOnChange.bind(this);
};

const editModalBudgetArticleNewCodeOnChange = () => {
  const select = document.getElementById(
    "modal-edit-budget-article-new-code__select"
  );
  editBudgetArticleState.newCodeIdSelected =
    select[select.selectedIndex].getAttribute("data-id");
};

const fillEditModalBudgetArticleServiceSelect = () => {
  rerenderBudgetArticleServiceEditModal();
  const select = document.getElementById(
    "modal-edit-budget-article-service__select"
  );
  while (select.firstChild) select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати службу";
  selectedOption.innerText = "Обрати службу";
  selectedOption.hidden = true;
  selectedOption.selected =
    editBudgetArticleState.serviceIdSelected === null ? true : false;
  select.appendChild(selectedOption);
  if (editBudgetArticleState.servicesData.length === 0) return;
  editBudgetArticleState.servicesData.forEach((element) => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    optionSelect.setAttribute("data-id", element.id);
    if (editBudgetArticleState.serviceIdSelected === element.id) {
      optionSelect.selected = true;
      optionSelect.hidden = true;
    }
    select.appendChild(optionSelect);
  });
};

const rerenderBudgetArticleServiceEditModal = () => {
  const field = document.getElementsByClassName(
    "modal-edit-budget-article-service-wrapper-select"
  );
  while (field.firstChild) field.removeChild(field.firstChild);
  $(
    "<select id='modal-edit-budget-article-service__select' class='service-select' title='Служба'></select>"
  )
    .appendTo(field)
    .select2();
};

const fillEditModalBudgetArticleNameInput = () => {
  const nameInput = document.getElementById("modal-edit-budget-article-name");
  nameInput.value = editBudgetArticleState.name;
};

const modalEditBudgetArticleSaveOnClick = () => {
  let articleData = null;
  if (state.userInfo.role.fin_dir_role) {
    const nameInput = document.getElementById("modal-edit-budget-article-name");
    editBudgetArticleState.name = nameInput.value;
    const select = document.getElementById(
      "modal-edit-budget-article-service__select"
    );
    editBudgetArticleState.serviceIdSelected =
      select[select.selectedIndex].getAttribute("data-id");

    if (editBudgetArticleState.name === null) {
      alert("Не всі обов'язкові поля заповнені!");
      return;
    }
    articleData = JSON.stringify({
      typeRequest: "modalSaveEditBudgetArticleRequest",
      id: editBudgetArticleState.id,
      name: editBudgetArticleState.name,
      serviceId: editBudgetArticleState.serviceIdSelected,
      previousServiceId: editBudgetArticleState.articleData.service_id,
      newCodeId: editBudgetArticleState.newCodeIdSelected,
      previousNewCodeId: editBudgetArticleState.articleData.new_code_id,
    });
  } else if (state.userInfo.role.financier_role) {
    articleData = JSON.stringify({
      typeRequest: "modalSaveEditBudgetArticleRequest",
      id: editBudgetArticleState.id,
      newCodeId: editBudgetArticleState.newCodeIdSelected,
      previousNewCodeId: editBudgetArticleState.articleData.new_code_id,
    });
  } else if (state.userInfo.role.director_role) {
    const nameInput = document.getElementById("modal-edit-budget-article-name");
    editBudgetArticleState.name = nameInput.value;
    const select = document.getElementById(
      "modal-edit-budget-article-service__select"
    );
    editBudgetArticleState.serviceIdSelected =
      select[select.selectedIndex].getAttribute("data-id");

    if (editBudgetArticleState.name === null) {
      alert("Не всі обов'язкові поля заповнені!");
      return;
    }
    articleData = JSON.stringify({
      typeRequest: "modalSaveEditBudgetArticleRequest",
      id: editBudgetArticleState.id,
      name: editBudgetArticleState.name,
      serviceId: editBudgetArticleState.serviceIdSelected,
      previousServiceId: editBudgetArticleState.articleData.service_id,
    });
  }
  modalSaveEditBudgetArticleRequest(articleData);
};

const modalSaveEditBudgetArticleRequest = (data) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
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
    modalEditBudgetArticleCloseOnClick();
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "#fff";
  };
};

const modalEditBudgetArticleCloseOnClick = () => {
  const modalWindow = document.getElementById(
    "modal-window-edit-budget-article"
  );
  modalWindow.style.display = "none";
};

const deleteBudgetArticleOnClick = (id) => {
  editBudgetArticleState.id = id;
  getBudgetArticleRequest(editBudgetArticleState, () => {
    editBudgetArticleState.oldCodeIdSelected =
      editBudgetArticleState.articleData.old_code_id;
    editBudgetArticleState.newCodeIdSelected =
      editBudgetArticleState.articleData.new_code_id;
    editBudgetArticleState.fundholderIdSelected =
      editBudgetArticleState.articleData.fundholder_id;
    if (confirm("Ви дійсно бажаєте видалити статтю бюджета?") == true)
      deleteBudgetArticleRequest(id);
    else return;
  });
};

const deleteBudgetArticleRequest = (id) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "deleteBudgetArticleRequest",
      id: id,
      newCodeId: editBudgetArticleState.newCodeIdSelected,
      fundholderId: editBudgetArticleState.fundholderIdSelected,
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
    backgroundStatus.style.backgroundColor = "#fff";
  };
};
