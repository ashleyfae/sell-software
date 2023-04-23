document.addEventListener('DOMContentLoaded', () => {
   const deactivateButtons = document.querySelectorAll('.deactivate-site');

   if (deactivateButtons) {
       deactivateButtons.forEach(initDeactivateSiteButton);
   }
});

/**
 *
 * @param {HTMLElement} button
 */
function initDeactivateSiteButton(button) {
    button.addEventListener('click', e => {
        e.preventDefault();

        const route = button.getAttribute('data-route');
        const domain = button.getAttribute('data-domain');

        if (! route || ! domain) {
            return;
        }

        axios.delete(route, {
            data: {
                url: domain
            }
        }).then(response => {
            const successNode = document.createElement('span');
            successNode.classList.add('tag', 'success');
            successNode.textContent = 'Successfully deactivated';
            button.parentElement.appendChild(successNode);
            button.remove();
        }).catch(error => {
            console.log('Error', error);
        })
    })
}
