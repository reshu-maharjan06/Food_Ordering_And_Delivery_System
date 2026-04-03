/**
 * Sauni – Rating Modal & Page Logic
 * Handles star interaction and submission for order reviews.
 * Works both as a popup or a standalone page.
 */

document.addEventListener('DOMContentLoaded', () => {
    const starContainer = document.getElementById('starRating');
    if (!starContainer) return; // Exit if not on a rating interface

    const stars = starContainer.querySelectorAll('i');
    const submitBtn = document.getElementById('submitRating');
    const cancelBtn = document.getElementById('cancelRating');
    const commentInput = document.getElementById('ratingComment');
    const modal = document.getElementById('ratingModal');
    
    let currentRating = 0;

    // --- Star Interaction ---

    stars.forEach(star => {
        // Hover to preview
        star.addEventListener('mouseenter', () => {
            const val = parseInt(star.dataset.value);
            highlightStars(val, 'hover');
        });

        star.addEventListener('mouseleave', () => {
            resetHover();
        });

        // Click to set
        star.addEventListener('click', () => {
            currentRating = parseInt(star.dataset.value);
            setActiveStars(currentRating);
        });
    });

    function highlightStars(count, className) {
        stars.forEach(s => {
            const val = parseInt(s.dataset.value);
            s.classList.toggle(className, val <= count);
        });
    }

    function resetHover() {
        stars.forEach(s => s.classList.remove('hover'));
    }

    function setActiveStars(count) {
        highlightStars(count, 'active');
    }

    // --- Submission ---

    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            if (currentRating === 0) {
                // Shake effect for visual feedback
                starContainer.style.animation = 'none';
                void starContainer.offsetWidth; // trigger reflow
                starContainer.style.animation = 'successPulse 0.4s ease';
                return;
            }

            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending Review...';

            // Simulate network delay
            setTimeout(() => {
                submitBtn.textContent = 'Thank You!';
                
                // Add success class to card
                const card = document.querySelector('.rating-card');
                if (card) card.classList.add('rating-success');
                
                const title = document.querySelector('.rating-title');
                if (title) title.textContent = 'Review Received';

                // Flow after success
                setTimeout(() => {
                    if (modal) {
                        // If it's a modal, close it
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                    } else {
                        // If it's a standalone page, redirect back to history
                        window.location.href = 'settings.html?tab=history';
                    }
                }, 1800);
            }, 1200);
        });
    }

    // --- Modal Only Logic ---
    if (modal) {
        // Close on click outside card
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
    }
});
