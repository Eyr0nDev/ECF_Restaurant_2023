 document.addEventListener('DOMContentLoaded', function () {
     const dateField = document.getElementById('booking_date');
     const timeField = document.getElementById('booking_time');
     const restaurantField = document.getElementById('booking_restaurant');
     const allTimeChoices = Array.from(document.querySelectorAll('input[type="radio"][id^="time_"]'));

     if (dateField && timeField && restaurantField) {
         dateField.addEventListener('change', updateOpeningHoursChoices);
         restaurantField.addEventListener('change', updateOpeningHoursChoices);
     }
     function updateOpeningHoursChoices() {
         const dateValue = new Date(dateField.value);
         const dayOfWeek = getFrenchDayOfWeek(dateValue.getDay());

         fetch(`/api/opening_hours?day_of_week=${dayOfWeek}&restaurant=${restaurantField.value}`)
             .then(response => response.json())
             .then(allowedTimes => {
                 const allowedChoices = allTimeChoices.filter(choice => allowedTimes.includes(choice.getAttribute('data-time')));
                 const allowedIds = allowedChoices.map(choice => choice.getAttribute('id'));

                 allTimeChoices.forEach(choice => {
                     const formCheck = choice.closest('.form-check');
                     if (allowedIds.includes(choice.getAttribute('id'))) {
                         formCheck.style.display = 'inline-block';
                     } else {
                         formCheck.style.display = 'none';
                     }
                 });
             });
     }


     function getFrenchDayOfWeek(day) {
         const days = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
         return days[day];
     }
 });
