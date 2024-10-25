document.addEventListener("DOMContentLoaded", () => {
  renderTableRequest();
});

const renderTableRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "counterparties-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
    })
  );
  xhr.onload = function () {
    console.log(xhr.response);
    document.querySelector("#counterparties-directory-table").innerHTML =
      xhr.response;
    stickyTableColumn();
  };
};

const changeStatusRequest = (status, id) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "counterparties-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "changeStatusRequest",
      status: status,
      id: id,
    })
  );
  xhr.onload = function () {
    renderTableRequest();
  };
};

const changeSignRequest = (sign, id) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "counterparties-directory.php";
  xhr.open("POST", requestURL);
  xhr.responseType = "";
  xhr.send(
    JSON.stringify({
      typeRequest: "changeSignRequest",
      sign: sign,
      id: id,
    })
  );
  xhr.onload = function () {
    renderTableRequest();
  };
};

const changeStatusOnClick = (status, id) => {
  changeStatusRequest(status, id);
};

const changeSignOnClick = (sign, id) => {
  changeSignRequest(sign, id);
};

const clearFiltersReloadOnClick = () => {
  //window.localStorage.removeItem('act-card-state');
  document.location.reload();
};

const clearAllFiltersReloadOnClick = () => {
  window.localStorage.clear();
  document.location.reload();
};

const stickyTableColumn = () => {
  const columnArray = [
    {
      name: ".table-column-id",
      direction: "left",
    },
    {
      name: "thead .table-column-id",
      direction: "top",
    },
    {
      name: ".table-column-name",
      direction: "left",
    },
    {
      name: "thead .table-column-name",
      direction: "top",
    },
    {
      name: ".table-column-add",
      direction: "left",
    },
    {
      name: "thead .table-column-add",
      direction: "top",
    },
  ];
  $("#counterparties-directory-table").scroll(function () {
    columnArray.forEach((element) => {
      switch (element.direction) {
        case "left":
          $(element.name).css({
            left: $(this).scrollLeft(),
          });
          break;
        case "top":
          $(element.name).css({
            top: $(this).scrollTop(),
          });
          break;
        default:
          break;
      }
    });
  });
};
