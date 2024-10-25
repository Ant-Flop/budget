<div id="modal-window-add-new-code" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAddNewCodeCloseOnClick()">&times;</span>
        <div class="text-2">Створення нового коду</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Старий код</span>
            <div id="modal-add-old-code-wrapper-select">
                <select id="modal-add-old-code__select" class="old-code-select" title="Старий код">
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Новий код</span>
            <input type="number" id="modal-new-code-add" placeholder="Новий код" title="Новий код" />
        </div>
        <div class="modal-button">
            <button id="modal-button-save" onclick="modalAddNewCodeSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>