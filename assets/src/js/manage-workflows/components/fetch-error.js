import React from 'react';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Displays the API fetch error message.
 *
 * @since {{VERSION}}
 *
 * @param {string} message The error message.
 * @param {func} onRetry The retry callback.
 * @constructor
 */
const FetchError = ({message, onRetry}) => (
    <div>
        <p>
            {message}

            &nbsp;

            <button onClick={onRetry} className="button">
                {l10n['retry']}
            </button>
        </p>
    </div>
);

export default FetchError;