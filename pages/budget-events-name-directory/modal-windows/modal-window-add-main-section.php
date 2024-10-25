<div id="modal-window-add-main-section" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAddMainSectionCloseOnClick()">&times;</span>
        <div class="text-2">Створення головного розділу</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Старий код</span>
            <div id="modal-add-main-section-old-code-wrapper-select">
                <select id="modal-add-main-section-old-code__select" class="old-code-select" title="Старий код">
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Новий код</span>
            <div id="modal-add-main-section-new-code-wrapper-select">
                <select id="modal-add-main-section-new-code__select" class="new-code-select" title="Новий код">
                    <option selected hidden>Обрати новий код</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Головний розділ</span>
            <input id="modal-name-main-section-add" placeholder="Головний розділ" title="Головний розділ" />
        </div>
        <div class="modal-button">
            <button id="modal-button-save" onclick="modalAddMainSectionSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>