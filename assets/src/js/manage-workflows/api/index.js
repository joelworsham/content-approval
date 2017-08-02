const APINonce = WFM_ManageWorkflows['nonce'];

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Fetches all workflows from the database.
 *
 * @since {{VERSION}}
 */
export const fetchWorkflows = () => (
    fetch('/wp-json/wp/v2/workflow/', {
        method: 'GET',
        credentials: 'same-origin',
        headers: new Headers({
            'Content-Type': 'application/json',
            'X-WP-Nonce': APINonce,
        }),
    })
        .then(response => {

            // Make sure we have a success code
            if ( !response.status || response.status !== 200 ) {

                throw new Error(l10n['server_returned_code'].replace('%s', response.status || l10n['reason_unknown']));
            }

            response = response.json();

            return response;
        })
);

/**
 * Adds a new workflow to the database.
 *
 * @since {{VERSION}}
 *
 * @param {{}} fields
 */
export const addWorkflow = (fields) => {

    // Make sure ID is not set, the API does not like that when creating an item. *sigh*
    delete fields.id;

    return fetch('/wp-json/wp/v2/workflow/', {
        method: 'POST',
        credentials: 'same-origin',
        headers: new Headers({
            'Content-Type': 'application/json',
            'X-WP-Nonce': APINonce,
        }),
        body: JSON.stringify(fields),
    })
        .then(response => {

            // Make sure we have a success code
            if ( !response.status || response.status !== 201 ) {

                throw new Error(l10n['server_returned_code'].replace('%s', response.status || l10n['reason_unknown']));
            }

            response = response.json();

            return response;
        })
};

/**
 * Edits an existing workflow in the database.
 *
 * @since {{VERSION}}
 *
 * @param {int} id
 * @param {{}} fields
 */
export const editWorkflow = (id, fields) => {

    // Make sure ID is not set, the API does not like that when creating an item. *sigh*
    delete fields.id;

    return fetch('/wp-json/wp/v2/workflow/' + id, {
        method: 'POST',
        credentials: 'same-origin',
        headers: new Headers({
            'Content-Type': 'application/json',
            'X-WP-Nonce': APINonce,
        }),
        body: JSON.stringify(fields)
    })
        .then(response => {

            // Make sure we have a success code
            if ( !response.status || response.status !== 200 ) {

                throw new Error(l10n['server_returned_code'].replace('%s', response.status || l10n['reason_unknown']));
            }

            response = response.json();

            return response;
        })
};

/**
 * Deletes a workflow from the database.
 *
 * @since {{VERSION}}
 *
 * @param {int} id
 */
export const deleteWorkflow = (id) => (
    fetch('/wp-json/wp/v2/workflow/' + id, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: new Headers({
            'Content-Type': 'application/json',
            'X-WP-Nonce': APINonce,
        }),
    })
        .then(response => {

            // Make sure we have a success code
            if ( !response.status || response.status !== 200 ) {

                throw new Error(l10n['server_returned_code'].replace('%s', response.status || l10n['reason_unknown']));
            }

            response = response.json();

            return response;
        })
);