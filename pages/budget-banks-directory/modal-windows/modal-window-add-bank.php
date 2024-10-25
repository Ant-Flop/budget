<div id="modal-window-add-bank" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAddBankCloseOnClick()">&times;</span>
        <div class="text-2">Створення банку</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Код</span>
            <input type="number" id="modal-code-add" placeholder="Код" title="Код" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Розрахунковий рахунок</span>
            <input type="number" id="modal-current-account-add" placeholder="Розрахунковий рахунок" title="Розрахунковий рахунок" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Найменування банку</span>
            <input id="modal-name-add" placeholder="Найменування банку" title="Найменування банку" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">МФО</span>
            <input type="number" id="modal-mfo-add" placeholder="МФО" title="МФО" />
        </div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Розрахунковий рахунок IBAN</span>
            <input id="modal-iban-add" placeholder="Розрахунковий рахунок IBAN" title="Розрахунковий рахунок IBAN" />
        </div>
        <div class="modal-button">
            <button id="modal-button-save" onclick="modalAddBankSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>