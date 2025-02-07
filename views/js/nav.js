document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('check-icon');
    const navSlider = document.getElementById('menu-slide');
    const label = document.querySelector('.hamburger');
    const container = document.querySelector('.container'); // Container chính
    
    label.addEventListener('click', function () {
        if (navSlider.classList.contains('show')) {
            navSlider.classList.remove('show'); // Tắt menu
            container.classList.remove('menu-close'); // Quay lại vị trí ban đầu
            container.classList.remove('position_ct');

        } else {
            navSlider.classList.add('show'); // Bật menu
            container.classList.add('menu-close'); // Đẩy nội dung sang phải
            container.classList.add('position_ct');

        }
    });

    checkbox.addEventListener('change', function () {
        if (checkbox.checked) {
            navSlider.classList.add('show'); // Bật menu
            container.classList.add('menu-close'); // Đẩy nội dung sang phải
            container.classList.remove('position_ct');

        } else {
            navSlider.classList.remove('show'); // Tắt menu
            container.classList.remove('menu-close'); // Quay lại vị trí ban đầu
            container.classList.add('position_ct');
        }
    });

    // Dropdown menu logic
    var dropdowns = document.querySelectorAll('.button-menu-body-item');
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function() {
            var content = this.nextElementSibling;
            if (content.style.display === 'block') {
                content.style.display = 'none';
                this.classList.remove('active');
                var paths = this.querySelectorAll('svg path');
                paths.forEach(function(path) {
                    path.style.stroke = '#888888'; 
                });
            } else {
                dropdowns.forEach(function(item) {
                    var otherContent = item.nextElementSibling;
                    if (otherContent !== content) {
                        otherContent.style.display = 'none';
                        item.classList.remove('active');
                        var otherPaths = item.querySelectorAll('svg path');
                        otherPaths.forEach(function(path) {
                            path.style.stroke = '#888888'; 
                        });
                    }
                });
                content.style.display = 'block';
                this.classList.add('active');
                var paths = this.querySelectorAll('svg path');
                paths.forEach(function(path) {
                    path.style.stroke = '#0D99FF'; 
                });
            }
        });
    });
    
    // Close dropdowns when clicking outside
    window.addEventListener('click', function(event) {
        dropdowns.forEach(function(dropdown) {
            var content = dropdown.nextElementSibling;
            if (!dropdown.contains(event.target) && !content.contains(event.target)) {
                content.style.display = 'none';
                dropdown.classList.remove('active');
                var paths = dropdown.querySelectorAll('svg path');
                paths.forEach(function(path) {
                    path.style.stroke = '#888888'; 
                });
            }
        });
    });
});
