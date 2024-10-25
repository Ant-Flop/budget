const modalAddFundholderOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-fundholder");
    modalWindow.style.display = "block";
}

const modalAddFundholderCloseOnClick = () => {
    const modalWindow = document.getElementById("modal-window-add-fundholder");
    modalWindow.style.display = "none";
}

const modalAddFundholderSaveOnClick = () => {
    const nameFundholderInput = document.getElementById("modal-name-fundholder-add");
    const shortNameFundholderInput = document.getElementById("modal-short-name-fundholder-add");
    let nameFundholder = nameFundholderInput.value;
    let shortNameFundholder = shortNameFundholderInput.value;
    if (nameFundholder === '' || shortNameFundholder === '') {
        alert("Не всі обов'язкові поля заповнені!");
        return;
    }
    let fundholderData = JSON.stringify({
        typeRequest: "modalSaveAddFundholderRequest",
        name: nameFundholder,
        shortname: shortNameFundholder,
    });
    modalSaveAddFundholderRequest(fundholderData);
}

const modalSaveAddFundholderRequest = (data) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-fundholders-directory.php";
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
        modalAddFundholderCloseOnClick();
    }
    const timeoutLabel = (labelStatus) => {
        labelStatus.hidden = true;
        const backgroundStatus = document.getElementsByClassName("upper-save-panel")[0];
        backgroundStatus.style.backgroundColor = "#fff";
    }
}