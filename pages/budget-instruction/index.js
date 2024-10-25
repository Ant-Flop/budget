const state = {
    sideBarItemIdSelected: 1,
    pointsData: [{
            id: 1,
            name: "Entry",
            title: "Вступ",
            path: "entry",
        },
        {
            id: 2,
            name: "description_roles",
            title: "Опис ролей користувачів ПК 'Бюджет'",
            path: "description_roles",
        },
        {
            id: 3,
            name: "authentication",
            title: "Вхід у програму",
            path: "authentication",
        },
        {
            id: 4.1,
            name: "main_page",
            title: "Головна сторінка",
            path: "main_page",
        },
        {
            id: 4.2,
            name: "main_page_director",
            title: "Головна сторінка для директора за напрямком",
            path: "main_page_director",
        },
        {
            id: 4.3,
            name: "main_page_financier",
            title: "Головна сторінка для фінансиста",
            path: "main_page_financier",
        },
        {
            id: 5.1,
            name: "conterparties_directory_director",
            title: "Довідник контрагентів",
            path: "conterparties_directory_director",
        },
        {
            id: 5.2,
            name: "budget_articles_directory_director",
            title: "Довідник статей бюджету",
            path: "budget_articles_directory_director",
        },
        {
            id: 5.3,
            name: "codes_directory_financier",
            title: "Довідник коду",
            path: "codes_directory_financier",
        },
        {
            id: 5.4,
            name: "fundholders_directory_financier",
            title: "Довідник фондоутримувачів",
            path: "fundholders_directory_financier",
        },
        {
            id: 5.5,
            name: "services_directory_financier",
            title: "Довідник служб бюджету",
            path: "services_directory_financier",
        },
        {
            id: 5.6,
            name: "events_names_directory_financier",
            title: "Довідник найменувань заходів",
            path: "events_names_directory_financier",
        },
        {
            id: 5.7,
            name: "budget_articles_directory_financier",
            title: "Довідник статей бюджету",
            path: "budget_articles_directory_financier",
        },
        {
            id: 5.8,
            name: "banks_directory_financier",
            title: "Довідник банків",
            path: "banks_directory_financier",
        },
        {
            id: 6.1,
            name: "planned_indicators_director",
            title: "Планові показники",
            path: "planned_indicators_director",
        },
        {
            id: 6.2,
            name: "budget_plan_director",
            title: "План бюджету",
            path: "budget_plan_director",
        },
        {
            id: 6.3,
            name: "budget-plan-implementation_director",
            title: "Виконання бюджету",
            path: "budget-plan-implementation_director",
        },

        {
            id: 6.4,
            name: "planned_indicators_financier",
            title: "Планові показники",
            path: "planned_indicators_financier",
        },
        {
            id: 6.5,
            name: "budget_plan_financier",
            title: "План бюджету",
            path: "budget_plan_financier",
        },
        {
            id: 6.6,
            name: "budget-plan-implementation_financier",
            title: "Виконання бюджету",
            path: "budget-plan-implementation_financier",
        },
        {
            id: 6.7,
            name: "banks_register_financier",
            title: "Реєстр банку",
            path: "banks_register_financier",
        },
        {
            id: 7.1,
            name: "register_of_costs_under_the_contract_report_director",
            title: "Реєстр витрат по договору",
            path: "register_of_costs_under_the_contract_report_director",
        },
        {
            id: 7.2,
            name: "register_of_expenditures_by_budget_article_report_director",
            title: "Реєстр витрат по статті бюджету",
            path: "register_of_expenditures_by_budget_article_report_director",
        },
        {
            id: 7.3,
            name: "daily_report_financier",
            title: "Щоденка",
            path: "daily_report_financier",
        },

    ],
}


document.addEventListener("DOMContentLoaded", () => {
    sideBarAddEventListener();
    fillContent();

})

const sideBarAddEventListener = () => {
    const sideBarUl = [...document.getElementById("side-bar-ul").children];
    sideBarUl.forEach((element) => {
        if (element.classList.contains("side-bar-item"))
            element.onclick = sideBarItemOnClick.bind(this, element);
    });
    const sideBarItemUl = [...document.getElementsByClassName("side-bar-subitem")];
    sideBarItemUl.forEach(element => {
        element.onclick = sideBarItemUlOnClick.bind(this, element);
    })
}

const sideBarItemOnClick = (target) => {
    const isSelected = document.getElementsByClassName("side-bar-item-selected")[0];
    if (isSelected)
        isSelected.classList.remove("side-bar-item-selected");
    state.sideBarItemIdSelected = JSON.parse(target.getAttribute("data-id"));
    target.classList.add("side-bar-item-selected");
    const subitem = document.getElementById("side-bar-item-ul-" + state.sideBarItemIdSelected);
    if (subitem)
        subitem.hidden = !subitem.hidden
        fillContent()
}

const sideBarItemUlOnClick = (target) => {
    const isSelected = document.getElementsByClassName("side-bar-item-selected")[0];
    if (isSelected)
        isSelected.classList.remove("side-bar-item-selected");
    state.sideBarItemIdSelected = JSON.parse(target.getAttribute("data-id"));
    target.classList.add("side-bar-item-selected");
    fillContent()
}

const fillContent = () => {
    const mainContentBlock = document.getElementsByClassName("main-content")[0];
    const subTitle = document.getElementsByClassName("sub-title")[0];
    const contentArray = state.pointsData.filter(element => state.sideBarItemIdSelected === element.id);
    if (contentArray.length > 0) {
        subTitle.innerHTML = contentArray[0].title;
        loadMainContent(mainContentBlock, contentArray[0]);
    }
}

const loadMainContent = (mainContentBlock, contentArray) => {
    const xhr = new XMLHttpRequest();
    const requestURL = "budget-instruction.php";
    xhr.open("POST", requestURL);
    xhr.send(JSON.stringify({
        typeRequest: "getMainContentRequest",
        path: contentArray.path,
    }));
    xhr.onload = () => {
        mainContentBlock.innerHTML = xhr.response;
    }
}
