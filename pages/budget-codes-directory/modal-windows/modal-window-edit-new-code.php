<div id="modal-window-edit-new-code" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalEditNewCodeCloseOnClick()">&times;</span>
        <div class="text-2">Редагування нового коду</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Старий код</span>
            <div id="modal-edit-old-code-wrapper-select">
                <select id="modal-edit-old-code__select" class="old-code-select" title="Старий код">
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Новий код</span>
            <input type="number" id="modal-new-code-edit" placeholder="Новий код" title="Новий код" />
        </div>
        <div class="modal-button">
            <button id="modal-button-save" onclick="modalEditNewCodeSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>