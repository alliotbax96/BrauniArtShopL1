
    $(".perPageSelect").change(function (){
        $("#filters").submit();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.category-toggle');

        toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                // Не блокируем клик по радио-кнопке
                if (e.target.type === 'radio') return;

                e.preventDefault();
                const parentLi = this.parentElement;
                parentLi.classList.toggle('expanded');
            });
        });
    });
