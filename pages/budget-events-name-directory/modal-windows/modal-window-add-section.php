<div id="modal-window-add-section" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAddSectionCloseOnClick()">&times;</span>
        <div class="text-2">Створення розділу</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Старий код</span>
            <div id="modal-add-section-old-code-wrapper-select">
                <select id="modal-add-section-old-code__select" class="old-code-select" title="Старий код">
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Новий код</span>
            <div id="modal-add-section-new-code-wrapper-select">
                <select id="modal-add-section-new-code__select" class="new-code-select" title="Новий код">
                    <option selected hidden>Обрати новий код</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Головний розділ</span>
            <div id="modal-add-section-main-section-wrapper-select">
                <select id="modal-add-section-main-section__select" class="main-section-select" title="Головний розділ">
                    <option selected hidden>Обрати головний розділ</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Розділ</span>
            <input id="modal-name-section-add" placeholder="Розділ" title="Розділ" />
        </div>
        <div class="modal-button">
            <button id="modal-button-save" onclick="modalAddSectionSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>