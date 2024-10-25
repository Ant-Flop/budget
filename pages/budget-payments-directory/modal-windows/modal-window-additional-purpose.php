<div id="modal-window-additional-purpose" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAdditionalPurposeCloseOnClick()">&times;</span>
        <div class="text-2">Створення додаткового призначення</div>
        <div class="modal-header-content"></div>
        <div class="modal-bar-content">
            <div class="modal-input modal-tooltip-wrapper">
                <button id="modal-add__button">+</button>
            </div>
            <div class="modal-input modal-tooltip-wrapper">
                <input type="number" id="modal-sum__input" placeholder="Сума" title="Сума" />
            </div>
            <div class="modal-input modal-tooltip-wrapper">
                <span class='modal-tooltip' id="modal-purpose__span"></span>
                <input id="modal-purpose__input" placeholder="Призначення платежу" title="Призначення платежу"
                    disabled />
            </div>
            <div class="modal-input modal-tooltip-wrapper modal-old-code-wrapper__select">
                <select id="modal-old-code__select" title="Старий код"></select>
            </div>
            <div class="modal-input modal-tooltip-wrapper modal-new-code-wrapper__select">
                <select id="modal-new-code__select" title="Новий код"></select>
            </div>
        </div>
        <div class="modal-main-content">

        </div>
        <div class="modal-footer-content">
            <div class="modal-button">
                <button id="modal-button-save" class="disabled" onclick="modalSaveAdditionalPurposeOnClick()"
                    disabled>Зберегти</button>
            </div>
            <div class="modal-upper-save-panel">
                <label id="modal-label-save-indicator" hidden></label>
            </div>
        </div>

    </div>
</div>