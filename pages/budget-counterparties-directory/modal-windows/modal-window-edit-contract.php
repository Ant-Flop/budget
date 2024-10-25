<div id="modal-window-edit-contract" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalEditContractCloseOnClick()">&times;</span>
        <div class="text-2">Редагування договору</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Номер договору</span>
            <input id="modal-number-contract-edit" placeholder="Номер договору" title="Номер договору" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Статус договору</span>
            <div id="modal-status-contract-edit">
                <select id="modal-status-contract-edit-select" class="contract-select" title="Статус договору">
                    <option selected hidden>Обрати статус договору</option>
                    <option value="Відкритий">Відкритий</option>
                    <option value="Закритий">Закритий</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Ознака ПДВ</span>
            <div id="modal-sign-vat-contract-edit">
                <select id="modal-sign-vat-contract-edit-select" class="contract-select" title="Ознака ПДВ">
                    <option selected hidden>Обрати ознаку ПДВ</option>
                    <option value="З ПДВ">З ПДВ</option>
                    <option value="Без ПДВ">Без ПДВ</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Назва договору</span>
            <input id="modal-name-contract-edit" placeholder="Назва договору" title="Назва договору" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Термін дії договору</span>
            <input type='date' id="modal-term-contract-edit" placeholder="Термін дії договору"
                title="Термін дії договору" />
        </div>

        <div class="modal-button">
            <button id="modal-button-save" onclick="modalEditContractSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>