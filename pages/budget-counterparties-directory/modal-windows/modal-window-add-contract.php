<div id="modal-window-add-contract" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAddContractCloseOnClick()">&times;</span>
        <div class="text-2">Створення договору</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Контрагент</span>
            <select id="modal-counterparty-select" class="counterparty-select" title="Контрагент">
                <option selected hidden>Обрати контрагента</option>
            </select>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Номер договору</span>
            <input id="modal-number-contract-add" placeholder="Номер договору" title="Номер договору" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Статус договору</span>
            <select id="modal-status-contract-select" class="contract-select" title="Статус договору">
                <option value="Відкритий" selected hidden>Обрати статус договору</option>
                <option value="Відкритий">Відкритий</option>
                <option value="Закритий">Закритий</option>
            </select>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Ознака ПДВ</span>
            <select id="modal-sign-vat-contract-select" class="contract-select" title="Ознака ПДВ">
                <option selected hidden>Обрати ознаку ПДВ</option>
                <option value="З ПДВ">З ПДВ</option>
                <option value="Без ПДВ">Без ПДВ</option>
            </select>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Назва договору</span>
            <input id="modal-name-contract-add" placeholder="Назва договору" title="Назва договору" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Термін дії договору</span>
            <input type='date' id="modal-term-contract-add" placeholder="Термін дії договору"
                title="Термін дії договору" />
        </div>

        <div class="modal-button">
            <button id="modal-button-save" onclick="modalAddContractSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>