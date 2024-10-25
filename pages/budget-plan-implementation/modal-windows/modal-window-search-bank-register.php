<div id='modal-window-search-bank-register' class='modal-window unselectable'>
    <div class='modal-window-content'>
        <span class='modal-close' onclick='modalSearchBankRegisterCloseOnClick()'>&times;</span>
        <div class='text-2'>Пошук оплат в реєстрі банків</div>
        <div class="modal-input modal-tooltip-wrapper">
            <div class="modal-header-bar">
                <div>
                    <span>Код статті</span><br />
                    <div id="modal-search-bank-register-new-code-wrapper-select">
                        <select id="modal-search-bank-register-new-code__select" class="new-code-select" title="Новий код" onchange="modalNewCodesSelectOnChange(this)">
                        </select>
                    </div>
                </div>
                <div id="modal-search-bank-register-start-date-wrapper-input">
                    <span>Період з</span><br />
                    <input type="date" id="modal-search-bank-register-start-date__input" onchange="modalStartDateOnChange(this)" />
                </div>
                <div id="modal-search-bank-register-end-date-wrapper-input">
                    <span>по</span><br />
                    <input type="date" id="modal-search-bank-register-end-date__input" onchange="modalEndDateOnChange(this)" />
                </div>
                <div id="modal-search-bank-register-sum-wrapper-input">
                    <span>Сума, тис. грн</span><br />
                    <input type="input" id="modal-search-bank-register-sum__input" onchange="" disabled />
                </div>
            </div>
        </div>
        <div id='modal-window-search__table'>
            <table id='modal-window-search-banks-register-table'>
                <thead id="modal-main-table-thead" class="unselectable" data-sort="operDate ASC">
                    <tr>
                        <th id="main-table-th-id" class="main-table-th modal-table-column-id">№</th>
                        <th class="main-table-th modal-table-column-oper-number">Номер доручення</th>
                        <th id="main-table-th-date" class="main-table-th modal-table-column-date modal-sort-th" data-column='a.date'>Дата</th>
                        <th class="main-table-th modal-table-column-old-code">Код статті (старий)</th>
                        <th class="main-table-th modal-table-column-new-code">Код статті (новий)</th>
                        <th class="main-table-th modal-table-column-sum">Сума, грн</th>
                        <th class="main-table-th modal-table-column-counterparty">Контрагент</th>
                        <th class="main-table-th modal-table-column-payment-type">Призначення платежу</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>