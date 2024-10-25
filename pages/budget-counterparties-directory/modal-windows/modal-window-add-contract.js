const addContractState = {
  id: 0,
  counterpartiesData: []
};

const modalAddContractOnClick = () => {
  
  modalGetCounterpartiesRequest();
  const modalWindow = document.getElementById("modal-window-add-contract");
  modalWindow.style.display = "block";
};

const modalAddContractCloseOnClick = () => {
  const modalWindow = document.getElementById("modal-window-add-contract");
  modalWindow.style.display = "none";
};

const modalGetCounterpartiesRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-counterparties-directory.php";
  xhr.open("POST", requestURL);
  console.log(xhr)
  xhr.send(
    JSON.stringify({
      typeRequest: "modalGetCounterpartiesRequest",
    })
  );
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);
    addContractState.counterpartiesData = response;
    fillCounterpartiesSelect()
  };
}

const fillCounterpartiesSelect = () => {
  const select = document.getElementById("modal-counterparty-select");
  while (select.firstChild) 
    select.removeChild(select.firstChild);
  const selectedOption = document.createElement("option");
  selectedOption.value = "Обрати контрагента";
  selectedOption.innerText = "Обрати контрагента";
  selectedOption.hidden = true;
  selectedOption.selected = true;
  select.appendChild(selectedOption);
  if(addContractState.counterpartiesData.length === 0) return;
  addContractState.counterpartiesData.forEach(element => {
    const optionSelect = document.createElement("option");
    optionSelect.value = element.name;
    optionSelect.innerText = element.name;
    console.log(element.id)
    optionSelect.setAttribute("data-id", element.id);
    select.appendChild(optionSelect);
  })
}

const modalAddContractSaveOnClick = () => {
  const numberCounterpartyInput = document.getElementById(
    "modal-counterparty-select"
  );
  let counterpartyId = numberCounterpartyInput[numberCounterpartyInput.selectedIndex].getAttribute('data-id')
  const numberContractInput = document.getElementById(
    "modal-number-contract-add"
  );
  let numberContract = numberContractInput.value;
  const statusContractSelect = document.getElementById(
    "modal-status-contract-select"
  );
  let statusContract =
    statusContractSelect[statusContractSelect.selectedIndex].value;
  console.log(statusContract);
  const signVATContractSelect = document.getElementById(
    "modal-sign-vat-contract-select"
  );
  let signVATContract =
    signVATContractSelect[signVATContractSelect.selectedIndex].value;
  const nameContractInput = document.getElementById("modal-name-contract-add");
  let nameContract = nameContractInput.value;
  const termContractInput = document.getElementById("modal-term-contract-add");
  let termContract = termContractInput.value;
  if (
    numberContract === "" ||
    statusContract === "" ||
    nameContract === "" ||
    termContract === "" 
  ) {
    alert("Не всі обов'язкові поля заповнені!");
    return;
  }
  let contractData = JSON.stringify({
    typeRequest: "modalSaveAddContractRequest",
    counterpartyId: counterpartyId,
    number: numberContract,
    status: statusContract,
    vatSign: signVATContract === "" ? null : signVATContract,
    name: nameContract,
    term: termContract,
  });
  modalSaveAddContractRequest(contractData);
};


const modalClearContractFields = () => {
  const numberContractInput = document.getElementById(
    "modal-number-contract-add"
  );
  numberContractInput.value = "";
  const statusContractSelect = document.getElementById(
    "modal-status-contract-select"
  );
  statusContractSelect.innerHTML =
    '<option value="Відкритий" selected hidden>Обрати статус договору</option><option value="Відкритий">Відкритий</option><option value="Закритий">Закритий</option>';
  const signVATContractSelect = document.getElementById(
    "modal-sign-vat-contract-select"
  );
  signVATContractSelect.innerHTML =
    '<option selected hidden>Обрати ознаку ПДВ</option><option value="З ПДВ">З ПДВ</option><option value="Без ПДВ">Без ПДВ</option>';
  const nameContractInput = document.getElementById("modal-name-contract-add");
  nameContractInput.value = "";
  const termContractInput = document.getElementById("modal-term-contract-add");
  termContractInput.value = "";
};

const modalSaveAddContractRequest = (contractData) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-counterparties-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(contractData);
  xhr.onload = function () {
    let response = JSON.parse(xhr.response);
    const labelStatus = document.getElementById("label-save-indicator");
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    labelStatus.innerText = response.text;
    labelStatus.hidden = false;
    backgroundStatus.style.backgroundColor = response.status
      ? "#ebfbeb"
      : "#ffd3cd";
    console.log(response);
    setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
    if (response.status) {
      renderTableRequest();
      modalAddContractCloseOnClick();
      modalClearContractFields();
    }
  };
  const timeoutLabel = (labelStatus) => {
    labelStatus.hidden = true;
    const backgroundStatus =
      document.getElementsByClassName("upper-save-panel")[0];
    backgroundStatus.style.backgroundColor = "";
  };
};
