document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[type=radio][name=tabletoggle]');
    
    radioButtons.forEach(function(radioButton) {
        radioButton.addEventListener('change', function() {
            const table = document.querySelector('.ft-table');
            
            if (this.value === 'simple-view') {
                table.classList.add('simple-view');
                table.classList.remove('advanced-view');
            } else if (this.value === 'advanced-view') {
                table.classList.add('advanced-view');
                table.classList.remove('simple-view');
            }
        });
    });
});
