<div id='modal-window-add-planned-indicator' class='modal-window unselectable'>
    <div class='modal-window-content'>
        <span class='modal-close' onclick='modalAddPlannedIndicatorCloseOnClick()'>&times;</span>
        <div class='text-2'>Створення запису</div>
        <?php
    if ($admin_role || $director_role) {
      echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Головний розділ бюджету</span>
                        <div id='modal-add-planned-indicator-main-section-wrapper-select'>
                            <select id='modal-add-planned-indicator-main-section__select' class='main-section-select' title='Головний розділ бюджету'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Розділ бюджету</span>
                        <div id='modal-add-planned-indicator-section-wrapper-select'>
                            <select id='modal-add-planned-indicator-section__select' class='section-select' title='Розділ бюджету'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Підрозділ бюджету</span>
                        <div id='modal-add-planned-indicator-subsection-wrapper-select'>
                            <select id='modal-add-planned-indicator-subsection__select' class='subsection-select' title='Підрозділ бюджету'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Служба</span>
                        <div id='modal-add-planned-indicator-service-wrapper-select'>
                            <select id='modal-add-planned-indicator-service__select' class='service-select' title='Служба'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Назва статті бюджету</span>
                        <div id='modal-add-planned-indicator-article-wrapper-select'>
                            <select id='modal-add-planned-indicator-article__select' class='article-select' title='Назва статті бюджету'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                       <div class='modal-name-multiselect'>Контрагент</div>
                        <span class='modal-tooltip'>Контрагент</span>
                        <div id='modal-add-planned-indicator-counterparty-wrapper-select'>
                            <select id='modal-add-planned-indicator-counterparty__select' class='counterparty-select' title='Контрагент' js-example-basic-multiple' name='states[]' multiple='multiple'>
                            <option>x1</option>
                            <option>x2</option>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper' id='modal-add-contracts-wrapper-id' hidden>
                        <div class='modal-name-multiselect'>Договори</div>
                        <span class='modal-tooltip'>Договори</span>
                        <div id='modal-add-planned-indicator-contracts-wrapper-select'>
                            <select id='modal-add-planned-indicator-contracts__select' class='contract-select contracts-select js-example-basic-multiple' title='Договори' name='states[]' multiple='multiple'>
              
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Плановий показник</span>
                        <div id='modal-add-planned-indicator-sum-wrapper'>
                            <button onclick='modalSetSumPlannedIndicatorOnClick()'>Встановити плановий показник</button>
                            <span id='sign-planned-indicator'>
                                <div class='cross-element-1'>
                                    <div class='cross-element-2'></div>
                                </div>
                            </span>
                        </div>
                        
                      </div><br />";
    }
    ?>
        <div class='modal-button'>
            <button id='modal-button-save' onclick='modalAddPlannedIndicatorSaveOnClick()'>Зберегти</button>
        </div>
    </div>
</div>