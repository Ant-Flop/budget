<div id="modal-window-add-subsection" class="modal-window unselectable">
    <div class="modal-window-content">
        <span class="modal-close" onclick="modalAddSubsectionCloseOnClick()">&times;</span>
        <div class="text-2">Створення підрозділу</div>
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Старий код</span>
            <div id="modal-add-subsection-old-code-wrapper-select">
                <select id="modal-add-subsection-old-code__select" class="old-code-select" title="Старий код">
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Новий код</span>
            <div id="modal-add-subsection-new-code-wrapper-select">
                <select id="modal-add-subsection-new-code__select" class="new-code-select" title="Новий код">
                    <option selected hidden>Обрати новий код</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Головний розділ</span>
            <div id="modal-add-subsection-main-section-wrapper-select">
                <select id="modal-add-subsection-main-section__select" class="main-section-select" title="Головний розділ">
                    <option selected hidden>Обрати головний розділ</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Розділ</span>
            <div id="modal-add-subsection-section-wrapper-select">
                <select id="modal-add-subsection-section__select" class="section-select" title="Розділ">
                    <option selected hidden>Обрати розділ</option>
                </select>
            </div>
        </div><br />
        <div class="modal-input modal-tooltip-wrapper">
            <span class="modal-tooltip">Підрозділ</span>
            <input id="modal-name-subsection-add" placeholder="Підрозділ" title="Підрозділ" />
        </div>
        <div class="modal-button">
            <button id="modal-button-save" onclick="modalAddSubsectionSaveOnClick()">Зберегти</button>
        </div>
    </div>
</div>