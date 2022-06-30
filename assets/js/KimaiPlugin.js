/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*!
 * [KIMAI] KimaiPlugin: base class for all plugins
 */

import KimaiContainer from "./KimaiContainer";

export default class KimaiPlugin {

    /**
     * Overwrite this method to initialize your plugin.
     *
     * It is called AFTER setContainer() and AFTER DOMContentLoaded was fired.
     * You don't have access to the container before this method!
     */
    init() {
    }

    /**
     * If you return an ID, you indicate that your plugin can be used by other plugins.
     *
     * @returns {string|null}
     */
    getId() {
        return null;
    }

    /**
     * @param {KimaiContainer} core
     */
    setContainer(core) {
        if (!(core instanceof KimaiContainer)) {
            throw new Error('Plugin was given an invalid KimaiContainer');
        }
        this._core = core;
    }

    /**
     * This function returns null, if you call it BEFORE init().
     *
     * @returns {KimaiContainer}
     */
    getContainer() {
        return this._core;
    }

    /**
     * @param {string} name
     * @returns {(string|number|boolean)}
     */
    getConfiguration(name) {
        return this.getContainer().getConfiguration().get(name);
    }

    /**
     * @return {KimaiConfiguration}
     */
    getConfigurations() {
        return this.getContainer().getConfiguration();
    }

    /**
     * @returns {KimaiDateUtils}
     */
    getDateUtils() {
        return this.getPlugin('date');
    }

    /**
     * @param {string} name
     * @returns {KimaiPlugin}
     */
    getPlugin(name) {
        return this.getContainer().getPlugin(name);
    }

    /**
     * @returns {KimaiTranslation}
     */
    getTranslation() {
        return this.getContainer().getTranslation();
    }

    /**
     * @param {string} title
     * @returns {string}
     */
    escape(title) {
        return this.getPlugin('escape').escapeForHtml(title);
    }

    /**
     * @param {string} name
     * @param {string} details
     */
    trigger(name, details) {
        this.getPlugin('event').trigger(name, details);
    };

    /**
     * @param {string} url
     * @param {object} options
     * @returns {Promise<Response>}
     */
    fetch(url, options = {}) {
        return this.getPlugin('fetch').fetch(url, options);
    };

    /**
     * @param {HTMLFormElement} form
     * @param {object} options
     * @returns {Promise<Response>}
     */
    fetchForm(form, options = {}) {
        let url = form.action;
        const method = form.method.toUpperCase();

        if (method === 'GET') {
            const data = this.getPlugin('form').convertFormDataToQueryString(form, {}, true);
            url = url + (url.includes('?') ? '&' : '?') + data;
            options = {...{method: 'GET'}, ...options};
        } else if (method === 'POST') {
            options = {...{
                method: 'POST',
                body: this.getPlugin('form').convertFormDataToQueryString(form)
            }, ...options};
        }

        return this.fetch(url, options);
    };
}
