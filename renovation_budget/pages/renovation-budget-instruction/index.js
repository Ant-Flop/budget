const state = {
  sideBarItemIdSelected: 1,
  pointsData: [
    {
      id: 1,
      name: "Entry",
      title: "Вступ",
      path: "entry",
    },
    {
      id: 2.1,
      name: "treaty-directory",
      title: "Довідник договору",
      path: "treaty-directory",
    },
    {
      id: 2.2,
      name: "counterparties-directory",
      title: "Довідник контрагентів",
      path: "counterparties-directory",
    },
    {
      id: 2.3,
      name: "act-card",
      title: "Картка акту",
      path: "act-card",
    },

    {
      id: 2.4,
      name: "fact-of-contracts",
      title: "Факт по договорах",
      path: "fact-of-contracts",
    },
    {
      id: 2.5,
      name: "fact-of-articles",
      title: "Факт по статтям",
      path: "fact-of-articles",
    },
    {
      id: 3.1,
      name: "common-report",
      title: "Загальний звіт",
      path: "common-report",
    },
    {
      id: 3.2,
      name: "writing-off-costs-report",
      title: "Списання витрат",
      path: "writing-off-costs-report",
    },
    {
      id: 4,
      name: "clear-filters-with-reload",
      title: "Очистка фільтрів з перезагрузкою",
      path: "clear-filters-with-reload",
    },
  ],
};

document.addEventListener("DOMContentLoaded", () => {
  sideBarAddEventListener();
  fillContent();
});

const sideBarAddEventListener = () => {
  const sideBarUl = [...document.getElementById("side-bar-ul").children];
  sideBarUl.forEach((element) => {
    if (element.classList.contains("side-bar-item"))
      element.onclick = sideBarItemOnClick.bind(this, element);
  });
  const sideBarItemUl = [
    ...document.getElementsByClassName("side-bar-subitem"),
  ];
  sideBarItemUl.forEach((element) => {
    element.onclick = sideBarItemUlOnClick.bind(this, element);
  });
};

const sideBarItemOnClick = (target) => {
  const isSelected = document.getElementsByClassName(
    "side-bar-item-selected"
  )[0];
  if (isSelected) isSelected.classList.remove("side-bar-item-selected");
  state.sideBarItemIdSelected = JSON.parse(target.getAttribute("data-id"));
  target.classList.add("side-bar-item-selected");
  const subitem = document.getElementById(
    "side-bar-item-ul-" + state.sideBarItemIdSelected
  );
  if (subitem) subitem.hidden = !subitem.hidden;
  fillContent();
};

const sideBarItemUlOnClick = (target) => {
  const isSelected = document.getElementsByClassName(
    "side-bar-item-selected"
  )[0];
  if (isSelected) isSelected.classList.remove("side-bar-item-selected");
  state.sideBarItemIdSelected = JSON.parse(target.getAttribute("data-id"));
  target.classList.add("side-bar-item-selected");
  fillContent();
};

const fillContent = () => {
  const mainContentBlock = document.getElementsByClassName("main-content")[0];
  const subTitle = document.getElementsByClassName("sub-title")[0];
  const contentArray = state.pointsData.filter(
    (element) => state.sideBarItemIdSelected === element.id
  );
  if (contentArray.length > 0) {
    subTitle.innerHTML = contentArray[0].title;
    loadMainContent(mainContentBlock, contentArray[0]);
  }
};

const loadMainContent = (mainContentBlock, contentArray) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "budget-instruction.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      typeRequest: "getMainContentRequest",
      path: contentArray.path,
    })
  );
  xhr.onload = () => {
    mainContentBlock.innerHTML = xhr.response;
  };
};
