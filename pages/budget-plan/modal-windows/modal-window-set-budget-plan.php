<div id='modal-window-set-budget-plan' class='modal-window unselectable'>
    <div class='modal-window-content'>
        <span class='modal-close' onclick='modalSetBudgetPlanCloseOnClick()'>&times;</span>
        <div class='text-2'>Встановлення планового показника</div>
        <div class='modal-window-set-sum__table'>
            <table id='modal-window-set-budget-plan-table'>
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
                        <td>Оплата, тис. грн. з ПДВ</td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 0)'
                                onchange='modalSetTableMonthInputOnChange(this, 0, true, 0)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 1)'
                                onchange='modalSetTableMonthInputOnChange(this, 1, true, 1)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 2)'
                                onchange='modalSetTableMonthInputOnChange(this, 2, true, 2)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 3)'
                                onchange='modalSetTableMonthInputOnChange(this, 3, true, 3)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 4)'
                                onchange='modalSetTableMonthInputOnChange(this, 4, true, 4)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 5)'
                                onchange='modalSetTableMonthInputOnChange(this, 5, true, 5)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 6)'
                                onchange='modalSetTableMonthInputOnChange(this, 6, true, 6)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 7)'
                                onchange='modalSetTableMonthInputOnChange(this, 7, true, 7)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 8)'
                                onchange='modalSetTableMonthInputOnChange(this, 8, true, 8)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 9)'
                                onchange='modalSetTableMonthInputOnChange(this, 9, true, 9)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 10)'
                                onchange='modalSetTableMonthInputOnChange(this, 10, true, 10)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 11)'
                                onchange='modalSetTableMonthInputOnChange(this, 11, true, 11)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 12)'
                                onchange='modalSetTableMonthInputOnChange(this, "sum", true, 12)' disabled /></td>
                    </tr>
                    <tr>
                        <td>Спис. витрат, акт вик. роб., тис. грн. без ПДВ </td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 13)'
                                onchange='modalSetTableMonthInputOnChange(this, 0, false, 13)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 14)'
                                onchange='modalSetTableMonthInputOnChange(this, 1, false, 14)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 15)'
                                onchange='modalSetTableMonthInputOnChange(this, 2, false, 15)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 16)'
                                onchange='modalSetTableMonthInputOnChange(this, 3, false, 16)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 17)'
                                onchange='modalSetTableMonthInputOnChange(this, 4, false, 17)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 18)'
                                onchange='modalSetTableMonthInputOnChange(this, 5, false, 18)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 19)'
                                onchange='modalSetTableMonthInputOnChange(this, 6, false, 19)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 20)'
                                onchange='modalSetTableMonthInputOnChange(this, 7, false, 20)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 21)'
                                onchange='modalSetTableMonthInputOnChange(this, 8, false, 21)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 22)'
                                onchange='modalSetTableMonthInputOnChange(this, 9, false, 22)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 23)'
                                onchange='modalSetTableMonthInputOnChange(this, 10, false, 23)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 24)'
                                onchange='modalSetTableMonthInputOnChange(this, 11, false, 24)' /></td>
                        <td class='modal-input'><input class='modal__table__input' placeholder='0.00000'
                                onkeydown='modalSetSumOnKeyDown(event.keyCode, 25)'
                                onchange='modalSetTableMonthInputOnChange(this, "sum", false, 25)' disabled /></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class='modal-button'>
            <button id='modal-button-sum-save' onclick='modalSetBudgetPlanSaveOnClick()'>Зберегти</button>
        </div>
    </div>
</div>