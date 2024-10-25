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
  const requestURL = "budget-events-names-directory.php";
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

const getOldCodesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-events-names-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getOldCodesRequest",
    })
  );
  xhr.onload = () => {
    modalState.oldCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getNewCodesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-events-names-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getNewCodesRequest",
      countSymbolsNewCode: modalState.countSymbolsNewCode,
      nameTable: modalState.nameTable,
      id: modalState.oldCodeIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.newCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getMainSectionsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-events-names-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getMainSectionsRequest",
    })
  );
  xhr.onload = () => {
    modalState.mainSectionsData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getSectionsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-events-names-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getSectionsRequest",
      mainSectionId: modalState.mainSectionIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.sectionsData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getMainSectionRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-events-names-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getMainSectionRequest",
      mainSectionId: modalState.mainSectionId,
    })
  );
  xhr.onload = () => {
    modalState.mainSectionData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getSectionRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-events-names-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getSectionRequest",
      sectionId: modalState.sectionId,
    })
  );
  xhr.onload = () => {
    modalState.sectionData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getSubsectionRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-events-names-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getSubsectionRequest",
      subsectionId: modalState.subsectionId,
    })
  );
  xhr.onload = () => {
    modalState.subsectionData = JSON.parse(xhr.response);
    callbackFunction();
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
