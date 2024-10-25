<div id="modal-window" class="modal-window-background">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalCloseOnClick()">&times;</span>
        <div class="text-2">Створення послуги</div>
        <div class="wrapper-modal-form">
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Контрагент</span>
                <select id="modal-treaty-counterparty-select" class="treaty-counterparty-select" title="Контрагент"
                    onchange="modalSelectCounterpartiesOnChange(this, 'modal-treaty-number-contract-select')">
                    <option selected hidden>Обрати контрагента</option>
                </select>
            </div><br />
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Номер договору</span>
                <select id="modal-treaty-number-contract-select" class="treaty-number-contract-select"
                    title="Номер договору" onchange="modalSelectTreatyOnChange()">
                    <option selected hidden>Обрати номер договору</option>
                </select>
            </div><br />
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Стаття бюджету</span>
                <select id="modal-kind-service-select" class="treaty-article-select" title="Стаття бюджету">
                    <option selected hidden>Обрати статтю бюджета</option>
                </select>
            </div><br />
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Найменування послуги</span>
                <textarea id="modal-name-service-add" placeholder="Найменування послуги"
                    title="Найменування послуги"></textarea>
            </div>
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Тип обладнання</span>
                <input id="modal-type-equipment-add" placeholder="Тип обладнання" title="Тип обладнання" />
            </div>
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Кількість</span>
                <input type="number" id="modal-amount-add" placeholder="Кількість" title="Кількість" />
            </div>
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Ціна послуги, грн без ПДВ</span>
                <input type="number" id="modal-price-service-add" placeholder="Ціна послуги, грн без ПДВ"
                    title="Ціна послуги, грн без ПДВ" />
            </div>
            <div class="modal-input modal-tooltip-treaty">
                <span class="modal-tooltip">Вартість матеріалів, грн без ПДВ</span>
                <input type="number" id="modal-cost-materials-add" placeholder="Вартість матеріалів, грн без ПДВ"
                    title="Вартість матеріалів, грн без ПДВ" />
            </div>
        </div>

        <div class="modal-button">
            <button id="modal-button-save" onclick="modalSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>