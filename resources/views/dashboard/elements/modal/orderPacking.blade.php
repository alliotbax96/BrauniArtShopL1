<div class="modal fade" id="addNewTasks" tabindex="-1">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Сборка заказа № <span id="OrderID"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="container mt-4">
                    <div id="boxesContainer"></div>
                    <div class="mt-3">
                        <button id="addBoxBtn" class="btn btn-success">Добавить коробку</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отменить</button>
                <button type="button" id="savePackingBtn" class="btn btn-primary">Собрать</button>
            </div>
        </div>
    </div>
</div>
