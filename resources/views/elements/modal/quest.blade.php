<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Бронирование квеста</h2>
        <form id="bookingForm">
            <div id="bookingAlert" class="alert" style="display: none;"></div>
            <input type="hidden" id="timeslotId" name="timeslot_id">
            <input type="hidden" id="date" name="date">
            <input type="hidden" id="totalPrice" name="total_price">
            <input type="hidden" id="basePlayerCount" name="base_player_count">
            <input type="hidden" id="additionalPlayerPrice" name="additional_player_price">
            <input type="hidden" id="baseSlotPrice" name="base_slot_price">

            <div class="form-group">
                <label for="playerCount">Количество игроков:</label>
                <input type="number" id="playerCount" name="player_count" min="1" required>
            </div>

            <div class="form-group">
                <label for="customerName">Имя:</label>
                <input type="text" id="customerName" name="customer_name">
            </div>

            <div class="form-group">
                <label for="customerPhone">Телефон:</label>
                <input type="tel" class="mask_phone" id="customerPhone" name="customer_phone" required>
            </div>

            <div class="form-group">
                <label for="additionalServices">Дополнительные услуги:</label>
                <select id="additionalServices" name="selected_services[]" multiple size="4">
                    <!-- Опции будут заполняться динамически -->
                </select>
                <small>Зажмите Ctrl для выбора нескольких услуг</small>
            </div>

            <div class="total-price-info">
                Итоговая цена: <span id="displayTotalPrice">0</span> руб.
            </div>
            <button type="submit" class="submit-btn">Забронировать</button>
        </form>
    </div>
</div>

