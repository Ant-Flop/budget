
<div id='modal-window-add-budget-article' class='modal-window unselectable'>
    <div class='modal-window-content'>
        <span class='modal-close' onclick='modalAddBudgetArticleCloseOnClick()'>&times;</span>
        <div class='text-2'>Створення статті бюджету</div>
        <div class='modal-input modal-tooltip-wrapper'>
            <span class='modal-tooltip'>Головний розділ бюджету</span>
            <div id='modal-add-budget-article-main-section-wrapper-select'>
                <select id='modal-add-budget-article-main-section__select' class='main-section-select' title='Головний розділ бюджету'>
                </select>
            </div>
        </div><br />
        <div class='modal-input modal-tooltip-wrapper'>
            <span class='modal-tooltip'>Розділ бюджету</span>
            <div id='modal-add-budget-article-section-wrapper-select'>
                <select id='modal-add-budget-article-section__select' class='section-select' title='Розділ бюджету'>
                </select>
            </div>
        </div><br />
        <div class='modal-input modal-tooltip-wrapper'>
            <span class='modal-tooltip'>Підрозділ бюджету</span>
            <div id='modal-add-budget-article-subsection-wrapper-select'>
                <select id='modal-add-budget-article-subsection__select' class='subsection-select' title='Підрозділ бюджету'>
                </select>
            </div>
        </div><br />
        <?php  
            if($financier_role) {
                echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Фондоутримувач</span>
                        <div id='modal-add-budget-article-fundholder-wrapper-select'>
                            <select id='modal-add-budget-article-fundholder__select' class='fundholder-select' title='Фондоутримувач'>
                            </select>
                        </div>
                     </div><br />";
            }
        ?>
        
        <div class='modal-input modal-tooltip-wrapper'>
            <span class='modal-tooltip'>Служба</span>
            <div id='modal-add-budget-article-service-wrapper-select'>
                <select id='modal-add-budget-article-service__select' class='service-select' title='Служба'>
                </select>
            </div>
        </div><br />
        <?php  
            if($financier_role) {
                echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Старий код</span>
                        <div id='modal-add-budget-article-old-code-wrapper-select'>
                            <select id='modal-add-budget-article-old-code__select' class='old-code-select' title='Старий код'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Новий код</span>
                        <div id='modal-add-budget-article-new-code-wrapper-select'>
                            <select id='modal-add-budget-article-new-code__select' class='new-code-select' title='Новий код'>
                            </select>
                        </div>
                      </div><br />";
            }
        ?>
        
        <div class='modal-input modal-tooltip-wrapper'>
            <span class='modal-tooltip'>Назва статті</span>
            <input id='modal-add-budget-article-name' placeholder='Назва статті' title='Назва статті' />
        </div>
        <div class='modal-button'>
            <button id='modal-button-save' onclick='modalAddBudgetArticleSaveOnClick()'>Зберегти</button>
        </div>
    </div>
</div>