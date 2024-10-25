<div id='modal-window-edit-budget-article' class='modal-window unselectable'>
    <div class='modal-window-content'>
        <span class='modal-close' onclick='modalEditBudgetArticleCloseOnClick()'>&times;</span>
        <div class='text-2'>Редагування статті бюджету</div>
        <?php  
            if($financier_role) {
                echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Старий код</span>
                        <div id='modal-edit-budget-article-old-code-wrapper-select'>
                            <select id='modal-edit-budget-article-old-code__select' class='old-code-select' title='Старий код'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Новий код</span>
                        <div id='modal-edit-budget-article-new-code-wrapper-select'>
                            <select id='modal-edit-budget-article-new-code__select' class='new-code-select' title='Новий код'>
                            </select>
                        </div>
                      </div><br />";
            } 
            if($director_role) {
                echo "<div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Служба</span>
                        <div id='modal-edit-budget-article-service-wrapper-select'>
                            <select id='modal-edit-budget-article-service__select' class='service-select' title='Служба'>
                            </select>
                        </div>
                      </div><br />
                      <div class='modal-input modal-tooltip-wrapper'>
                        <span class='modal-tooltip'>Назва статті</span>
                        <input id='modal-edit-budget-article-name' placeholder='Назва статті' title='Назва статті' />
                     </div>";
            }
        ?>
        <div class='modal-button'>
            <button id='modal-button-save' onclick='modalEditBudgetArticleSaveOnClick()'>Зберегти</button>
        </div>
    </div>
</div>