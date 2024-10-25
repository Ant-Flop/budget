<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="text-1 unselectable">Картка акту</div>
            <div class="above-table-bar unselectable">
                <div id="counterparty-act-card-id">
                    <label for='act-card-counterparty-select'>Контрагент</label><br />
                    <div class="above-table-bar-element">
                        <select id='act-card-counterparty-select'
                            onchange="selectCounterpartiesOnChange(this, 'act-card-number-contract-select'); activateSaveButton()">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="number-contract-act-card-id">
                    <label for='act-card-number-contract-select'>Номер договору</label><br />
                    <div class="above-table-bar-element">
                        <select id='act-card-number-contract-select'
                            onchange="selectTreatyOnChange(this); activateSaveButton()">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="number-of-act-edit-act-card-id">
                    <label for='act-card-edit-number-of-act-select'>Номер акту</label><br />
                    <div class="above-table-bar-element">
                        <select id='act-card-edit-number-of-act-select' onchange="selectNumberActEditOnChange(this)">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="number-of-act-act-card-id">
                    <label for='act-card-number-of-act-input'>Номер акту</label><br />
                    <div class="above-table-bar-element">
                        <input type="text" id='act-card-number-of-act-input' placeholder="Ввести"
                            onchange="activateSaveButton()" />
                    </div>
                </div>
                <div id="number_account-of-act-act-card-id">
                    <label for='act-card-number-account-of-act-input'> Номер рахунку-акту</label><br />
                    <div class="above-table-bar-element">
                        <input type="text" id='act-card-number-account-of-act-input' placeholder="Ввести" />
                    </div>
                </div>
                <div id="account-of-act-act-card-id">
                    <label for='act-card-account-of-act-input'>Рахунок-акт</label><br />
                    <div class="above-table-bar-element">
                        <input type="text" id='act-card-account-of-act-input' placeholder="Ввести суму" />
                    </div>
                </div>

                <div id="date-of-act-act-card-id">
                    <label for='act-card-date-of-act-input'>Дата акту</label><br />
                    <div class="above-table-bar-element">
                        <input type="date" id='act-card-date-of-act-input' onchange="activateSaveButton()" />
                    </div>
                </div>
            </div>
            <div id="card-act-table">
                <table id='table-main'>
                    <thead class="unselectable">
                        <tr>
                            <th rowspan='2' class='th-width-auto th-get-width-for-footer th-lower-child th-low-border'>№
                            </th>
                            <th rowspan='2'
                                class='th-get-width-for-footer th-lower-child th-low-border th-name-service'>
                                Найменування послуг
                            </th>
                            <th rowspan='2'
                                class='th-get-width-for-footer th-lower-child th-low-border type-of-equipment__th'>Тип
                                обладнання
                            </th>
                            <th rowspan='2' class='th-get-width-for-footer th-lower-child th-low-border'>Кількість</th>
                            <th colspan='3' class='table-th-colspan-head-color table-th-colspan-head-background'>
                                Вартість
                                послуг
                            </th>
                        </tr>
                        <tr>
                            <th
                                class='table-th-colspan-foot-color table-th-sticky-second-row th-get-width-for-footer table-th-colspan-foot-background th-lower-child th-low-border'>
                                Ціна послуги, грн. без ПДВ</th>
                            <th
                                class='table-th-colspan-foot-color table-th-sticky-second-row th-get-width-for-footer table-th-colspan-foot-background th-lower-child th-low-border'>
                                Вартість матеріалів, грн. без ПДВ</th>
                            <th
                                class='table-th-colspan-foot-color table-th-sticky-second-row th-get-width-for-footer table-th-colspan-foot-background th-lower-child th-low-border'>
                                Сума, грн. без ПДВ</th>
                        </tr>
                    </thead>
                    <tbody id='table-tbody-id'>
                    </tbody>
                    <tfoot class="unselectable">
                        <tr>
                            <td rowspan='3' id='td-lower-border'></td>
                            <td colspan='2' class='table-td-colspan-blue-font'>Всього, грн. без ПДВ</td>
                            <td class='table-td-colspan-red-font' id="td-lower-amount-sum"></td>
                            <td class='table-td-colspan-blue-font' id='td-lower-price-no-vat'></td>
                            <td class='table-td-colspan-blue-font' id='td-lower-cost-no-vat'></td>
                            <td class='table-td-colspan-red-font' id='td-lower-sum-main-no-val'></td>
                        </tr>
                        <tr>
                            <td colspan='2' class='table-td-colspan-blue-font'>ПДВ, 20%</td>
                            <td></td>
                            <td class='table-td-colspan-blue-font' id='td-lower-price-vat'></td>
                            <td class='table-td-colspan-blue-font' id='td-lower-cost-vat'></td>
                            <td class='table-td-colspan-red-font' id='td-lower-sum-val'></td>
                        </tr>
                        <tr>
                            <td colspan='2' class='table-td-colspan-blue-font'>Всього, грн. з ПДВ</td>
                            <td></td>
                            <td class='table-td-colspan-blue-font' id='td-lower-price-with-vat'></td>
                            <td class='table-td-colspan-blue-font' id='td-lower-cost-with-vat'></td>
                            <td class='table-td-colspan-red-font' id='td-lower-sum-main-with-val'></td>
                        </tr>

                    </tfoot>
                </table>
            </div>
            <div class="lower-art-card-save-panel">
                <label for="act-card-spend-button" id="label-act-card-spend-button" hidden></label>
                <button class="save-button" id="act-card-spend-button" onclick="actCardConductOnClick(this)"
                    disabled>Провести</button>
            </div>
            <div class="lower-art-card-save-panel">
                <label for="act-card-save-button" id="label-act-card-save-button" hidden></label>
                <button class="save-button" id="act-card-save-button" onclick="actCardSaveOnClick(this)"
                    disabled>Зберегти</button>
            </div>

        </div>
        <?php require("../../templates/layout/footer.php") ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

        <link href="../../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <script src="../../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
        $('select').select2();
        </script>
        <script src="index.js"></script>
    </div>
</body>

</html>