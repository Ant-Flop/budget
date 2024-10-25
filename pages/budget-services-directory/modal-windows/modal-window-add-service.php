<div id="modal-window-add-service" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAddServiceCloseOnClick()">&times;</span>
        <div class="text-2">Створення служби</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Фондоутримувач</span>
            <div id="modal-add-fundholder-wrapper-select">
                <select id="modal-add-fundholder__select" class="fundholder-select" title="Фондоутримувач">
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Назва служби</span>
            <input id="modal-service-add" placeholder="Назва служби" title="Назва служби" />
        </div>
        <div class="modal-button">
            <button id="modal-button-save" onclick="modalAddServiceSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>