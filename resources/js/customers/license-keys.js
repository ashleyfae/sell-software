document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = document.querySelectorAll('.download--toggle-licenses');
    if (! toggleButtons) {
        return;
    }

    toggleButtons.forEach(button => {
        button.addEventListener('click', e => {
            e.preventDefault();

            const wrapperNode = button.parentElement;
            if (! wrapperNode) {
                return;
            }

            const responseWrapper = wrapperNode.querySelector('.download--licenses');
            if (! responseWrapper) {
                return;
            }

            responseWrapper.classList.toggle('hidden');

            if (wrapperNode.getAttribute('data-loaded')) {
                return;
            }

            const route = button.getAttribute('data-route');
            if (! route) {
                return;
            }

            axios.get(route)
                .then(response => {
                    console.log(response);
                    wrapperNode.setAttribute('data-loaded', 'true');

                    if (response.data.length === 0) {
                        responseWrapper.innerHTML = '<p>No licenses.</p>';
                    } else {
                        appendLicenseTemplate(responseWrapper, wrapperNode.querySelector('template'), response.data);
                    }
                })
        })
    });
});

/**
 *
 * @param {HTMLElement} wrapperNode
 * @param {HTMLElement|null} template
 * @param {object[]} licenses
 */
function appendLicenseTemplate(wrapperNode, template, licenses)
{
    if (! template) {
        return;
    }

    licenses.forEach(license => {
        const thisLicenseTemplate = template.content.firstElementChild.cloneNode(true);
        const input = thisLicenseTemplate.querySelector('input');
        if (input && license.license_key) {
            input.setAttribute('value', license.license_key);
        }
        const anchor = thisLicenseTemplate.querySelector('a');
        if (anchor && license.path) {
            anchor.setAttribute('href', license.path);
        }

        wrapperNode.appendChild(thisLicenseTemplate);
    })
}
