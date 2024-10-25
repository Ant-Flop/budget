<div id='modal-window-transfer-planned-indicator' class='modal-window unselectable'>
    <div class='modal-window-content'>
        <span class='modal-close' onclick='modalTransferPlannedIndicatorCloseOnClick()'>&times;</span>
        <div class='text-2'>Перенесення статей бюджету на обраний рік</div>
        <div class='modal-input modal-tooltip-wrapper'>
            <span class='modal-tooltip'>З року</span>
            <div id='modal-transfer-planned-indicator-start-year-wrapper-select'>
                <select id='modal-transfer-planned-indicator-start-year__select' class='year-select'
                    title='Рік'>
                </select>
            </div>
        </div><br />
        <div class='modal-input modal-tooltip-wrapper'>
            <span class='modal-tooltip'>На рік</span>
            <div id='modal-transfer-planned-indicator-end-year-wrapper-select'>
                <select id='modal-transfer-planned-indicator-end-year__select' class='year-select'
                    title='Рік'>
                </select>
            </div>
        </div><br />
        <div class='modal-button'>
            <button id='modal-button-save' onclick='modalTransferPlannedIndicatorSaveOnClick()'>Зберегти</button>
        </div>
    </div>
</div>