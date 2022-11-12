/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*!
 * [KIMAI] KimaiContextMenu: help to create, position and display context menus
 */

export default class KimaiContextMenu {

    /**
     * @param {string} id
     */
    constructor(id)
    {
        this.id = id;
    }

    /**
     * @returns {HTMLElement}
     */
    getContextMenuElement()
    {
        if (document.getElementById(this.id) === null) {
            const temp = document.createElement('div');
            temp.id = this.id;
            temp.classList.add('dropdown-menu', 'd-none');
            document.body.appendChild(temp);
        }

        return document.getElementById(this.id);
    }

    /**
     * @param {MouseEvent} event
     * @param {object} json
     */
    createFromApi(event, json)
    {
        let html = '';

        for (const options of json) {
            if (options['divider'] === true) {
                html += '<div class="dropdown-divider"></div>';
            }

            if (options['url'] !== null) {
                html += '<a class="dropdown-item ' + (options['class'] !== null ? options['class'] : '') + '" href="' + options['url'] + '"';

                if (options['attr'] !== undefined) {
                    for (const attrName in options['attr']) {
                        html += ' ' + attrName + '="' + options['attr'][attrName].replaceAll('"', '&quot;') + '"';
                    }
                }
                html += '>' + options['title'] + '</a>';
            }
        }

        this.createFromClickEvent(event, html);
    }

    /**
     * @param {MouseEvent} event
     * @param {string} html
     */
    createFromClickEvent(event, html)
    {
        const dropdownElement = this.getContextMenuElement();

        dropdownElement.style.zIndex = '1021'; // stay on top of sticky elements (like table header)
        dropdownElement.innerHTML = html;
        dropdownElement.style.position = 'fixed';
        dropdownElement.style.top = (event.clientY) + 'px';
        dropdownElement.style.left = (event.clientX) + 'px';

        const dropdownListener = () => {
            dropdownElement.classList.remove('d-block');
            if (!dropdownElement.classList.contains('d-none')) {
                dropdownElement.classList.add('d-none');
            }
            dropdownElement.removeEventListener('click', dropdownListener);
            document.removeEventListener('click', dropdownListener);
        }

        dropdownElement.addEventListener('click', dropdownListener);
        document.addEventListener('click', dropdownListener);

        dropdownElement.classList.remove('d-none');
        if (!dropdownElement.classList.contains('d-block')) {
            dropdownElement.classList.add('d-block');
        }
    }
}
