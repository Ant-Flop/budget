const state = {
  scrollInfo: {
    width: null,
    height: null,
  },
  withArchiveContracts: false
};

document.addEventListener("DOMContentLoaded", () => {
  const table = document.getElementById("main-table");
  table.addEventListener("scroll", (event) => {
    getScrollTable();
  });
  renderTableRequest();
});

const archiveContractOnClick = () => {
  const archiveCheckbox = document.getElementById("archive-contract");
  state.withArchiveContracts = archiveCheckbox.checked
  renderTableRequest()
}

const renderTableRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-counterparties-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      withArchiveContracts: state.withArchiveContracts
    })
  );
  xhr.onload = function () {
    document.querySelector("#main-table").innerHTML = xhr.response; 
    setScrollTable();
    stickyTableColumn();
  };
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
      name: ".table-column-actions",
      direction: "left",
    },
    {
      name: "thead .table-column-actions",
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
  $("#main-table").scroll(function () {
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

const getScrollTable = () => {
  const table = document.getElementById("main-table");
  state.scrollInfo.height = table.scrollTop;
};

const setScrollTable = () => {
  const table = document.getElementById("main-table");
  const th = table
    .querySelector("thead")
    .getElementsByClassName("sticky-table-column");
  table.scrollTop = state.scrollInfo.height;
  [...th].map((element) => {
    element.style.top = state.scrollInfo.height + "px";
  });
};
