<button id="topButton" class="btn bg-primary rounded-full position-fixed" 
        style="display: none; z-index: 10001;">
    <i class="fas fa-arrow-up text-white"></i>
</button>

<style>
    #topButton {
        width: 45px;
        height: 45px;
        transition: all 0.3s ease;
        bottom: 16px !important;
        right: 24px !important;
        opacity: 1 !important;
        visibility: visible !important;
        z-index: 10001 !important;
        pointer-events: auto !important;
        position: fixed !important;
    }
    
    #topButton:hover {
        opacity: 1 !important;
        transform: translateY(-5px);
    }

    @media (max-width: 767px) {
        #topButton {
            width: 36px !important;
            height: 36px !important;
            bottom: 4px !important;
            right: 20px !important;
        }

        #topButton i {
            font-size: 0.875rem !important;
        }
    }

    @media (min-width: 768px) {
        #topButton {
            bottom: 12px !important;
            right: 24px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topButton = document.getElementById('topButton');
        
        if (!topButton) return;
        
        function checkScroll() {
            if (window.scrollY > 300) {
                topButton.style.display = 'block';
                topButton.style.visibility = 'visible';
                topButton.style.opacity = '1';
                topButton.style.zIndex = '10001';
            } else {
                topButton.style.display = 'none';
            }
        }
        
        checkScroll();
        
        window.addEventListener('scroll', checkScroll);
        
        topButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>