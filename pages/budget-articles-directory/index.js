const state = {
  userInfo: [],
  scrollInfo: {
    width: null,
    height: null,
  },
  withArchiveArticles: false
};

document.addEventListener("DOMContentLoaded", () => {
  const table = document.getElementById("main-table");
  table.addEventListener("scroll", (event) => {
    getScrollTable();
  });
  getUserInfoRequest();
  renderTableRequest();
});

const archiveContractOnClick = () => {
  const archiveCheckbox = document.getElementById("archive-article");
  state.withArchiveArticles = archiveCheckbox.checked;
  renderTableRequest();
}

const getUserInfoRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getUserInfoRequest",
      
    })
  );
  xhr.onload = function () {
    state.userInfo = JSON.parse(xhr.response);
  };
};

const renderTableRequest = () => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "renderTableRequest",
      withArchiveArticles: state.withArchiveArticles
    })
  );
  xhr.onload = function () {
    document.querySelector("#main-table").innerHTML = xhr.response;
    setScrollTable();
  };
};

const getMainSectionsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
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
  const requestURL = "budget-articles-directory.php";
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

const getSubsectionsRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getSubsectionsRequest",
      sectionId: modalState.sectionIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.subsectionsData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getOldCodesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
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

const getNewCodesRequest = (
  modalState,
  action,
  newCodeId,
  callbackFunction
) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  let section = modalState.articleData.section || "";
  if (!modalState.articleData.section) {
    if (modalState.sectionsData)
      modalState.sectionsData.forEach((element) => {
        if (element.id == modalState.sectionIdSelected) section = element.name;
      });
  }
  xhr.open("POST", requestURL);

  xhr.send(
    JSON.stringify({
      typeRequest: "getNewCodesRequest",
      lowLimitSymbolsNewCode: modalState.lowLimitSymbolsNewCode,
      section: section,
      oldCodeId: modalState.oldCodeIdSelected,
      action: action,
      newCodeId: newCodeId,
    })
  );
  xhr.onload = () => {
    modalState.newCodesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getFundholdersRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getFundholdersRequest",
    })
  );
  xhr.onload = () => {
    modalState.fundholdersData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getServicesRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getServicesRequest",
      fundholderId: modalState.fundholderIdSelected,
    })
  );
  xhr.onload = () => {
    modalState.servicesData = JSON.parse(xhr.response);
    callbackFunction();
  };
};

const getBudgetArticleRequest = (modalState, callbackFunction) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-articles-directory.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getBudgetArticleRequest",
      id: modalState.id,
    })
  );
  xhr.onload = () => {
    modalState.articleData = JSON.parse(xhr.response);
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
