const state = {
  scrollInfo: {
    width: null,
    height: null,
  },
};

document.addEventListener("DOMContentLoaded", () => {
  const table = document.getElementById("main-table");
  table.addEventListener("scroll", (event) => {
    getScrollTable();
  });
  renderTableRequest();
});

const renderTableRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-fundholders-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
    })
  );
  xhr.onload = function () {
    document.querySelector("#main-table").innerHTML = xhr.response;
    setScrollTable();
  };
};

const getScrollTable = () => {
  const table = document.getElementById("main-table");
  state.scrollInfo.height = table.scrollTop;
};

const setScrollTable = () => {
  const table = document.getElementById("main-table");
  table.scrollTop = state.scrollInfo.height;
};
