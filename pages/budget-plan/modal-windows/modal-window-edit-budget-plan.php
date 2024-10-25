<div id='modal-window-edit-budget-plan' class='modal-window unselectable'>
        <div class='modal-window-content'>
                <span class='modal-close' onclick='modalEditBudgetPlanCloseOnClick()'>&times;</span>
                <div class='text-2'>Редагування планового показника</div>
                <div class='modal-window-set-sum__table'>
                        <table id='modal-window-edit-budget-plan-table'>
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
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 0)' onchange='modalEditTableMonthInputOnChange(this, 0, true, 0)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 1)' onchange='modalEditTableMonthInputOnChange(this, 1, true, 1)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 2)' onchange='modalEditTableMonthInputOnChange(this, 2, true, 2)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 3)' onchange='modalEditTableMonthInputOnChange(this, 3, true, 3)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 4)' onchange='modalEditTableMonthInputOnChange(this, 4, true, 4)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 5)' onchange='modalEditTableMonthInputOnChange(this, 5, true, 5)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 6)' onchange='modalEditTableMonthInputOnChange(this, 6, true, 6)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 7)' onchange='modalEditTableMonthInputOnChange(this, 7, true, 7)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 8)' onchange='modalEditTableMonthInputOnChange(this, 8, true, 8)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 9)' onchange='modalEditTableMonthInputOnChange(this, 9, true, 9)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 10)' onchange='modalEditTableMonthInputOnChange(this, 10, true, 10)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 11)' onchange='modalEditTableMonthInputOnChange(this, 11, true, 11)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 12)' onchange='modalEditTableMonthInputOnChange(this, "sum", true, 12)' disabled /></td>
                                        </tr>
                                        <tr>
                                                <td>Спис. витрат, акт вик. роб., тис. грн. без ПДВ </td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 13)' onchange='modalEditTableMonthInputOnChange(this, 0, false, 13)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 14)' onchange='modalEditTableMonthInputOnChange(this, 1, false, 14)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 15)' onchange='modalEditTableMonthInputOnChange(this, 2, false, 15)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 16)' onchange='modalEditTableMonthInputOnChange(this, 3, false, 16)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 17)' onchange='modalEditTableMonthInputOnChange(this, 4, false, 17)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 18)' onchange='modalEditTableMonthInputOnChange(this, 5, false, 18)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 19)' onchange='modalEditTableMonthInputOnChange(this, 6, false, 19)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 20)' onchange='modalEditTableMonthInputOnChange(this, 7, false, 20)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 21)' onchange='modalEditTableMonthInputOnChange(this, 8, false, 21)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 22)' onchange='modalEditTableMonthInputOnChange(this, 9, false, 22)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 23)' onchange='modalEditTableMonthInputOnChange(this, 10, false, 23)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 24)' onchange='modalEditTableMonthInputOnChange(this, 11, false, 24)' /></td>
                                                <td class='modal-input'><input class='modal__table__input' placeholder='0.00000' onkeydown='modalEditSumOnKeyDown(event.keyCode, 25)' onchange='modalEditTableMonthInputOnChange(this, "sum", false, 25)' disabled /></td>
                                        </tr>
                                </tbody>
                        </table>
                </div>

                <div class='modal-button'>
                        <button id='modal-button-sum-save' onclick='modalEditBudgetPlanSaveOnClick()'>Зберегти</button>
                </div>
        </div>
</div>