<div id='modal-window-set-sum-planned-indicator' class='modal-window unselectable'>
    <div class='modal-window-content'>
        <span class='modal-close' onclick='modalSetSumPlannedIndicatorCloseOnClick()'>&times;</span>
        <div class='text-2'>Встановлення планового показника</div>
        <div class='modal-window-set-sum__table'>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Січень</th>
                        <th>Лютий</th>
                        <th>Березень</th>
                        <th>Квітень</th>
                        <th>Травень</th>
                        <th>Червень</th>
                        <th>Липень</th>
                        <th>Серпень</th>
                        <th>Вересень</th>
                        <th>Жовтень</th>
                        <th>Листопад</th>
                        <th>Грудень</th>
                        <th>Сума</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Оплата, тис. грн. з ПДВ</th>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 0)'
                                onchange='modalTableMonthInputOnChange(this, 0, true, 0)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 1)'
                                onchange='modalTableMonthInputOnChange(this, 1, true, 1)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 2)'
                                onchange='modalTableMonthInputOnChange(this, 2, true, 2)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 3)'
                                onchange='modalTableMonthInputOnChange(this, 3, true, 3)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 4)'
                                onchange='modalTableMonthInputOnChange(this, 4, true, 4)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 5)'
                                onchange='modalTableMonthInputOnChange(this, 5, true, 5)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 6)'
                                onchange='modalTableMonthInputOnChange(this, 6, true, 6)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 7)'
                                onchange='modalTableMonthInputOnChange(this, 7, true, 7)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 8)'
                                onchange='modalTableMonthInputOnChange(this, 8, true, 8)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 9)'
                                onchange='modalTableMonthInputOnChange(this, 9, true, 9)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 10)'
                                onchange='modalTableMonthInputOnChange(this, 10, true, 10)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 11)'
                                onchange='modalTableMonthInputOnChange(this, 11, true, 11)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 12)'
                                onchange='modalTableMonthInputOnChange(this, "sum", true, 12)' disabled /></td>
                    </tr>
                    <tr>
                        <th>Спис. витрат, акт вик. роб., тис. грн. без ПДВ </th>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 13)'
                                onchange='modalTableMonthInputOnChange(this, 0, false, 13)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 14)'
                                onchange='modalTableMonthInputOnChange(this, 1, false, 14)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 15)'
                                onchange='modalTableMonthInputOnChange(this, 2, false, 15)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 16)'
                                onchange='modalTableMonthInputOnChange(this, 3, false, 16)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 17)'
                                onchange='modalTableMonthInputOnChange(this, 4, false, 17)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 18)'
                                onchange='modalTableMonthInputOnChange(this, 5, false, 18)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 19)'
                                onchange='modalTableMonthInputOnChange(this, 6, false, 19)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 20)'
                                onchange='modalTableMonthInputOnChange(this, 7, false, 20)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 21)'
                                onchange='modalTableMonthInputOnChange(this, 8, false, 21)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 22)'
                                onchange='modalTableMonthInputOnChange(this, 9, false, 22)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 23)'
                                onchange='modalTableMonthInputOnChange(this, 10, false, 23)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 24)'
                                onchange='modalTableMonthInputOnChange(this, 11, false, 24)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumPlannedIndicatorOnKeyDown(event.keyCode, 25)'
                                onchange='modalTableMonthInputOnChange(this, "sum", false, 25)' disabled /></td>
                    </tr>
                    <tr>
                        <th>Ознака ПДВ</th>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 0)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 1)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 2)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 3)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 4)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 5)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 6)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 7)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 8)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 9)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 10)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 11)' checked></td>
                        <td class='modal-input'><input type='checkbox' class='modal__table__input modal-vat-sign__input'
                                onchange='modalTableCheckBoxInputOnChange(this, 12)' checked></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class='modal-button'>
            <button id='modal-button-sum-save' onclick='modalSetSumPlannedIndicatorSaveOnClick()'>Встановити</button>
        </div>
    </div>
</div>