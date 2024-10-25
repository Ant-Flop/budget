<div id='modal-window-edit-planned-indicator' class='modal-window unselectable'>
  <div class='modal-window-content'>
    <span class='modal-close' onclick='modalEditPlannedIndicatorCloseOnClick()'>&times;</span>
    <div class='text-2'>Редагування запису</div>
    <?php
    if ($fin_dir_role) {
      echo "<div class='modal-input modal-tooltip-wrapper'>
                      <span class='modal-tooltip'>Контрагент</span>
                      <div id='modal-edit-planned-indicator-counterparty-wrapper-select'>
                        <select id='modal-edit-planned-indicator-counterparty__select' class='counterparty-select js-example-basic-multiple' title='Контрагент' name='states[]' multiple='multiple'>
                        </select>
                      </div>
                      </div>
                      
                      <div class='modal-input modal-tooltip-wrapper' id='modal-edit-contracts-wrapper-id' hidden>
                        <div class='modal-name-multiselect'>Договори</div>
                        <span class='modal-tooltip'>Договори</span>
                        <div id='modal-edit-planned-indicator-contracts-wrapper-select'>
                            <select id='modal-edit-planned-indicator-contracts__select' class='contract-select contracts-select js-example-basic-multiple' title='Договори' name='states[]' multiple='multiple'>
                            <option>x1</option>
                            <option>x2</option>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Назва статті бюджета</span>
                        <input id='modal-edit-planned-indicator-article-name' placeholder='Стаття бюджета' title='Стаття бюджета'/>
                    </div>
                    <div id='modal-edit-budget-plan-wrapper-id' class='modal-input modal-tooltip-wrapper' hidden>
                        <span class='modal-tooltip'>Плановий показник</span>
                        <div id='modal-add-planned-indicator-sum-wrapper'>
                            <button onclick='modalEditBudgetPlanOnClick()'>Редагувати плановий показник</button>
                        </div>
                        
                      </div><br />";
      echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Старий код</span>
                        <div id='modal-edit-planned-indicator-old-code-wrapper-select'>
                            <select id='modal-edit-planned-indicator-old-code__select' class='old-code-select' title='Старий код'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Новий код</span>
                        <div id='modal-edit-planned-indicator-new-code-wrapper-select'>
                            <select id='modal-edit-planned-indicator-new-code__select' class='new-code-select' title='Новий код'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Плановий показник, грн. з ПДВ</span>
                        <input type='number' id='modal-edit-planned-indicator-sum-with-vat' placeholder='Плановий показник, грн. з ПДВ' title='Плановий показник, грн. з ПДВ' onchange='editModalPlannedIndicatorSumWithVATOnChange()'/>
                      </div>";
    } elseif ($director_role) {
      echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Контрагент</span>
                        <div id='modal-edit-planned-indicator-counterparty-wrapper-select'>
                            <select id='modal-edit-planned-indicator-counterparty__select' class='counterparty-select' title='Контрагент'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper' id='modal-edit-contracts-wrapper-id' hidden>
                      <div class='modal-name-multiselect'>Договори</div>
                      
                        <span class='modal-tooltip'>Договори</span>
                        <div id='modal-edit-planned-indicator-contracts-wrapper-select'>
                            <select id='modal-edit-planned-indicator-contracts__select' class='counterparty-select contracts-select js-example-basic-multiple' title='Договори' name='states[]' multiple='multiple'>
                            <option>x1</option>
                            <option>x2</option>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Назва статті бюджета</span>
                        <input id='modal-edit-planned-indicator-article-name' placeholder='Стаття бюджета' title='Стаття бюджета'/>
                      </div>
                      <div id='modal-edit-budget-plan-wrapper-id' class='modal-input modal-tooltip-wrapper' hidden>
                        <span class='modal-tooltip'>Плановий показник</span>
                        <div id='modal-add-planned-indicator-sum-wrapper'>
                            <button onclick='modalEditBudgetPlanOnClick()'>Редагувати плановий показник</button>
                        </div>
                        
                      </div><br />";
    } elseif ($financier_role) {
      echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Старий код</span>
                        <div id='modal-edit-planned-indicator-old-code-wrapper-select'>
                            <select id='modal-edit-planned-indicator-old-code__select' class='old-code-select' title='Старий код'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Новий код</span>
                        <div id='modal-edit-planned-indicator-new-code-wrapper-select'>
                            <select id='modal-edit-planned-indicator-new-code__select' class='new-code-select' title='Новий код'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Плановий показник, грн. з ПДВ</span>
                        <input type='number' id='modal-edit-planned-indicator-sum-with-vat' placeholder='Плановий показник  , грн. з ПДВ' title='Плановий показник, грн. з ПДВ' onchange='editModalPlannedIndicatorSumWithVATOnChange()'/>
                      </div>";
    }
    ?>
    <div class='modal-button'>
      <button id='modal-button-save' onclick='modalEditPlannedIndicatorSaveOnClick()'>Зберегти</button>
    </div>
  </div>
</div>