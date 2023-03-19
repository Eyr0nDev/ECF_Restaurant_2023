document.addEventListener('DOMContentLoaded', function () {
    const dateField = document.getElementById('booking_date');
    const restaurantField = document.getElementById('booking_restaurant');
    const timeField = document.getElementById('booking_time');
    if (dateField && restaurantField && timeField) {
        dateField.addEventListener('change', updateOpeningHoursChoices);
        restaurantField.addEventListener('change', updateOpeningHoursChoices);
    }

    function updateOpeningHoursChoices() {
        console.log('updateOpeningHoursChoices called');
        const dateValue = new Date(dateField.value);
        const dayOfWeek = getFrenchDayOfWeek(dateValue.getDay());

        fetch(`/api/opening_hours?day_of_week=${dayOfWeek}&restaurant=${restaurantField.value}`)
            .then(response => response.json())
            .then(allowedTimes => {
                console.log('allowedTimes:', allowedTimes);

                // Get the current selected value
                const currentValue = timeField.value;

                // Hide options that are not in allowedTimes
                Array.from(timeField.options).forEach(option => {
                    if (option.value !== '' && !allowedTimes.includes(option.textContent)) {
                        option.hidden = true;
                    } else {
                        option.hidden = false;
                    }
                });

                // Check if the current value is still valid, if not select the first available time
                if (!allowedTimes.includes(currentValue)) {
                    timeField.value = allowedTimes[0];
                }
            });
    }

    function getFrenchDayOfWeek(day) {
        const days = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
        return days[day];
    }
});