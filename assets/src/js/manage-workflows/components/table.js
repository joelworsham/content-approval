import React from 'react';

/**
 * Presentational component for the Workflows table.
 *
 * @since {{VERSION}}
 *
 * @constructor
 */
const Table = ({
    children,
}) => (
    <table className="wfm-workflows-table">
        <tbody>
        {children}
        </tbody>
    </table>
);

export default Table;