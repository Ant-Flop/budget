
const modalAddCounterpartyOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-counterparty");
    modalWindow.style.display = "block";
}

const modalAddCounterpartyCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-counterparty");
    modalWindow.style.display = "none";
}

const modalAddCounterpartySaveOnClick = () => {
    const nameCounterpartyInput = document.getElementById("modal-name-counterparty-add");
    let nameCounterparty = nameCounterpartyInput.value;
    if (nameCounterparty === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let counterpartyData = JSON.stringify({
        typeRequest: "modalSaveAddCounterpartyRequest",
        name: nameCounterparty,
    });
    modalSaveAddCounterpartyRequest(counterpartyData);
}


const modalSaveAddCounterpartyRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-counterparties-directory.php";
    xhr.open("POST", requestURL);
    xhr.send(data);
    xhr.onload = () => {
        let response = JSON.parse(xhr.response);
        const labelStatus = document.getElementById("label-save-indicator");
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        labelStatus.innerText = response.text;
        labelStatus.hidden = false;
        backgroundStatus.style.backgroundColor = response.status ? "#ebfbeb" : "#fbedeb";
        setTimeout(timeoutLabel.bind(this, labelStatus), 3000);
        renderTableRequest();
        modalAddCounterpartyCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}

