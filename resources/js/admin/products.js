document.addEventListener('DOMContentLoaded', () => {
    const licensePeriod = document.getElementById('license_period');
    const licensePeriodUnit = document.getElementById('license_period_unit');

    // toggle license period when unit is lifetime
    if (licensePeriod && licensePeriodUnit) {
        licensePeriodUnit.addEventListener('change', e => {
            if (licensePeriodUnit.value === 'lifetime') {
                licensePeriod.value = '';
                licensePeriod.classList.add('hidden');
            } else {
                licensePeriod.classList.remove('hidden');
            }
        });
    }

    const formsWithConfirmations = document.querySelectorAll('form.delete');
    if (formsWithConfirmations) {
        formsWithConfirmations.forEach(form => {
            form.addEventListener('submit', e => {
                if (! confirm('Are you sure?')) {
                    e.preventDefault();
                }
            })
        })
    }
});
